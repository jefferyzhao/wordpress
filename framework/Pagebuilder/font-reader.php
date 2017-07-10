<?php

global $blox_fonts;

$blox_fonts = array();

/* Simple Line Icons */
@ $font_pointer = fopen( get_template_directory()."/assets/plugins/simple-line-icons/icons.txt", "r");
if ($font_pointer) {
    while (!feof($font_pointer)) {
        $font_item = fgets($font_pointer, 999);
        $font_item = trim($font_item . '');
        $blox_fonts[] = $font_item;
    }
    fclose($font_pointer);
}

/* Font Awesome */
@ $font_pointer = fopen( get_template_directory()."/assets/plugins/font-awesome/less/icons.less", "r");
if ($font_pointer) {
    while (!feof($font_pointer)) {
        $font_item = fgets($font_pointer, 999);
        $font_item = trim($font_item . '');
        if( substr($font_item, 0, 2) == '.@' ){
            $chars = explode(':', $font_item);
            $font_item = $chars[0];
            $font_item = str_replace('.@{fa-css-prefix}', 'fa', $font_item);
            $blox_fonts[] = $font_item;
        }
    }
    fclose($font_pointer);
}



add_action('admin_enqueue_scripts', 'blox_font_render_scripts');
add_action('wp_enqueue_scripts', 'blox_font_render_scripts');
function blox_font_render_scripts(){
    /* Fontawesome */
    wp_enqueue_style('font-awesome', file_require(get_template_directory_uri().'/assets/plugins/font-awesome/css/font-awesome.min.css', true));

    /* Simple Line Icons */
    wp_enqueue_style('simple-line-icons', file_require(get_template_directory_uri().'/assets/plugins/simple-line-icons/simple-line-icons.css', true));
}


?>