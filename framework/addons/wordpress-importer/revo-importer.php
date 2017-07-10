<?php

function tt_revo_fix_bg_url($url){
    $imgExts = array("gif", "jpg", "jpeg", "png", "tiff", "tif", "bmp");
    $exp = explode(".", $url);
    if( in_array($exp[count($exp)-1], $imgExts) ){
        $sites_url = "/wp-content/uploads/sites/";
        $uploads_url = "/wp-content/uploads/";
        if( strpos($url, $sites_url)!==false ){
            $ex = explode($sites_url, $url);
            $site_id_str = explode("/", $ex[1]);
            $uri = $ex[0].$sites_url.$site_id_str[0];
            return trailingslashit($uri);
        }
        else if( strpos($url, $uploads_url)!==false ){
            $ex = explode($uploads_url, $url);
            $uri = $ex[0].$uploads_url;
            return trailingslashit($uri);
        }
        else{
            return false;
        }
    }
    return trailingslashit($url);
}
function tt_revo_fix_img_url($bg_image, $image){
    if( empty($image) ) return "";

    $bg = tt_revo_fix_bg_url($bg_image);
    if($bg){
        if( strpos($image, "://")===false )
            return $bg.$image;
        return $image;
    }
    return $image;
}
function tt_revo_importer(){
    if( class_exists('UniteFunctionsRev') ){
        
        global $wpdb;
        
        $revo_directory = get_stylesheet_directory().'/framework/addons/wordpress-importer/files/revsliders/';
        $revo_files = array();
        $db = new UniteDBRev();

        $revo_obj = new RevSlider();
        $aliases = $revo_obj->getAllSliderAliases();

        foreach( glob( $revo_directory . '*.txt' ) as $filename ) {
            $filename = basename($filename);
            $revo_files[] = get_stylesheet_directory_uri().'/framework/addons/wordpress-importer/files/revsliders/'.$filename;
        }
        foreach( $revo_files as $rev_file ){
            $get_revo_file = wp_remote_get( $rev_file );
            $ncd = $get_revo_file['body'];
            
            if( base64_decode($ncd, true) ){
                $slider_data = @unserialize(base64_decode($ncd));
            }
            else{
                ob_start();
                $ncd = preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", trim($ncd)); //clear errors in string
                ob_end_clean();
                
                $slider_data = @unserialize($ncd);
            }

            if( empty($slider_data) )
                continue;
            $slider_params = $slider_data["params"];
            if( in_array($slider_params['alias'], $aliases) )
                continue;

            $serialized_content = serialize($slider_data);

            /* Detecting Animations and Styles */
            $animations = isset($slider_data["custom_animations"]) ? $slider_data["custom_animations"] : array();
            if(!empty($animations)){
                foreach($animations as $key => $animation){ //$animation['id'], $animation['handle'], $animation['params']
                    $exist = $db->fetch(GlobalsRevSlider::$table_layer_anims, "handle = '".$animation['handle']."'");
                    if(!empty($exist)){ //update the animation, get the ID
                        $arrUpdate = array();
                        $arrUpdate['params'] = stripslashes(json_encode(str_replace("'", '"', $animation['params'])));
                        $db->update(GlobalsRevSlider::$table_layer_anims, $arrUpdate, array('handle' => $animation['handle']));
                        $id = $exist['0']['id'];
                    }else{ //insert the animation, get the ID
                        $arrInsert = array();
                        $arrInsert["handle"] = $animation['handle'];
                        $arrInsert["params"] = stripslashes(json_encode(str_replace("'", '"', $animation['params'])));
                        $id = $db->insert(GlobalsRevSlider::$table_layer_anims, $arrInsert);
                    }
                    $serialized_content = str_replace(array('customin-'.$animation['id'], 'customout-'.$animation['id']), array('customin-'.$id, 'customout-'.$id), $serialized_content); 
                }
            }

            // Static Captions
            $static = isset($slider_data["static_captions"]) ? $slider_data["static_captions"] : "";
            if(!empty($static)){
                RevOperations::updateStaticCss($static);
            }

            //overwrite/create dynamic-captions.css
            //parse css to classes
            if( isset($slider_data["dynamic_captions"]) && !empty($slider_data["dynamic_captions"]) ){
                $dynamicCss = UniteCssParserRev::parseCssToArray($slider_data["dynamic_captions"]);
                if(is_array($dynamicCss) && $dynamicCss !== false && count($dynamicCss) > 0){
                    foreach($dynamicCss as $class => $styles){
                        //check if static style or dynamic style
                        $class = trim($class);
                        
                        if((strpos($class, ':hover') === false && strpos($class, ':') !== false) || //before, after
                            strpos($class," ") !== false || // .tp-caption.imageclass img or .tp-caption .imageclass or .tp-caption.imageclass .img
                            strpos($class,".tp-caption") === false || // everything that is not tp-caption
                            (strpos($class,".") === false || strpos($class,"#") !== false) || // no class -> #ID or img
                            strpos($class,">") !== false){ //.tp-caption>.imageclass or .tp-caption.imageclass>img or .tp-caption.imageclass .img
                            continue;
                        }
                        
                        //is a dynamic style
                        if(strpos($class, ':hover') !== false){
                            $class = trim(str_replace(':hover', '', $class));
                            $arrInsert = array();
                            $arrInsert["hover"] = json_encode($styles);
                            $arrInsert["settings"] = json_encode(array('hover' => 'true'));
                        }else{
                            $arrInsert = array();
                            $arrInsert["params"] = json_encode($styles);
                        }
                        //check if class exists
                        $result = $db->fetch(GlobalsRevSlider::$table_css, "handle = '".$class."'");
                        
                        if(!empty($result)){ //update
                            $db->update(GlobalsRevSlider::$table_css, $arrInsert, array('handle' => $class));
                        }else{ //insert
                            $arrInsert["handle"] = $class;
                            $db->insert(GlobalsRevSlider::$table_css, $arrInsert);
                        }
                    }
                }
            }

            $slider_data = unserialize($serialized_content);
            $slider_params = $slider_data["params"];

            /*
            if(isset($slider_params["background_image"])) {
                $slider_params["background_image"] = UniteFunctionsWPRev::getImageUrlFromPath($slider_params["background_image"]);
            }
            */

            $json_params = json_encode($slider_params);

            $revoSliderInstance = array();
            $revoSliderInstance["params"] = $json_params;
            $revoSliderInstance["title"] = UniteFunctionsRev::getVal($slider_params, "title", $slider_params['title']);
            $revoSliderInstance["alias"] = UniteFunctionsRev::getVal($slider_params, "alias", $slider_params['alias']);

            $sliderID = $db->insert(GlobalsRevSlider::$table_sliders, $revoSliderInstance);

            //create all slides
            $revoSlides = $slider_data["slides"];
            foreach($revoSlides as $slide){
                
                $params = $slide["params"];
                $layers = $slide["layers"];
                
                //convert params images:
                if(isset($params["image"])) {
                    // $params["image"] = UniteFunctionsWPRev::getImageUrlFromPath($params["image"]);
                    $params["image"] = tt_revo_fix_img_url($slider_params["background_image"], $params["image"]);
                }
                
                //convert layers images:
                foreach($layers as $key=>$layer){                   
                    if(isset($layer["image_url"])){
                        // $layer["image_url"] = UniteFunctionsWPRev::getImageUrlFromPath($layer["image_url"]);
                        $layer["image_url"] = tt_revo_fix_img_url($slider_params["background_image"], $layer["image_url"]);
                        $layers[$key] = $layer;
                    }
                }
                
                //create new slide
                $SlideInstance = array();
                $SlideInstance["slider_id"] = $sliderID;
                $SlideInstance["slide_order"] = $slide["slide_order"]; 

                $my_layers = json_encode($layers);
                if(empty($my_layers))
                    $my_layers = stripslashes(json_encode($layers));
                $my_params = json_encode($params);
                if(empty($my_params))
                    $my_params = stripslashes(json_encode($params));

                $SlideInstance["layers"] = $my_layers;
                $SlideInstance["params"] = $my_params;

                $db->insert(GlobalsRevSlider::$table_slides,$SlideInstance);
            }


            //check if static slide exists and import
            if(isset($slider_data['static_slides']) && !empty($slider_data['static_slides'])){
                $static_slide = $slider_data['static_slides'];
                foreach($static_slide as $slide){
                    
                    $params = $slide["params"];
                    $layers = $slide["layers"];
                    
                    //convert params images:
                    if(isset($params["image"])){
                        $params["image"] = tt_revo_fix_img_url($slider_params["background_image"], $params["image"]);
                    }
                    
                    //convert layers images:
                    foreach($layers as $key=>$layer){
                        if(isset($layer["image_url"])){
                            $layer["image_url"] = tt_revo_fix_img_url($slider_params["background_image"], $layer["image_url"]);
                            $layers[$key] = $layer;
                        }
                    }
                    
                    //create new slide
                    $arrCreate = array();
                    $arrCreate["slider_id"] = $sliderID;
                    
                    $my_layers = json_encode($layers);
                    if(empty($my_layers))
                        $my_layers = stripslashes(json_encode($layers));
                    $my_params = json_encode($params);
                    if(empty($my_params))
                        $my_params = stripslashes(json_encode($params));
                        
                        
                    $arrCreate["layers"] = $my_layers;
                    $arrCreate["params"] = $my_params;
                    
                    $this->db->insert(GlobalsRevSlider::$table_static_slides, $arrCreate);
                }
            }
            


        }

    }
}

?>