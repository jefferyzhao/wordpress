<?php
/**
 * You can extend it with new icons.
 * Please see the icon list from here, http://fortawesome.github.io/Font-Awesome/cheatsheet/
 * And extend following array with name and hex code.
 */
global $tt_social_icons;
$tt_social_icons = array(
    'facebook' => 'fa fa-facebook',
    'twitter' => 'fa fa-twitter',
    'googleplus' => 'fa fa-google-plus',
    'email' => 'fa fa-envelope',
    'pinterest' => 'fa fa-pinterest',
    'linkedin' => 'fa fa-linkedin',
    'youtube' => 'fa fa-youtube',
    'vimeo' => 'fa fa-vimeo-square',
    'dribbble' => 'fa fa-dribbble',
    'instagram' => 'fa fa-instagram',
    'flickr' => 'fa fa-flickr',
    'skype' => 'fa fa-skype'
);


add_action('admin_enqueue_scripts', 'admin_common_render_scripts');

function admin_common_render_scripts() {
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_style('themeton-admin-common-style', file_require(get_template_directory_uri().'/framework/admin-assets/common.css', true) );

    wp_enqueue_script('jquery');
    wp_enqueue_script('wp-color-picker');
    wp_enqueue_script('themeton-admin-common-js', file_require(get_template_directory_uri().'/framework/admin-assets/common.js', true), false, false, true);
}


/* Validate URL
========================================================*/
function validateURL($url){
    return filter_var($url, FILTER_VALIDATE_URL);

    if(!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $url)){
        return false;
    }
    return true;
}


/**
 * The function returns brightness value from 0 to 255
 */
function get_brightness($hex) {
    $hex = str_replace('#', '', $hex);

    if (strlen($hex) < 6) {
        $hex = substr($hex, 0, 1) . substr($hex, 0, 1) .
                substr($hex, 1, 2) . substr($hex, 1, 2) .
                substr($hex, 2, 3) . substr($hex, 2, 3);
    }

    $c_r = hexdec(substr($hex, 0, 2));
    $c_g = hexdec(substr($hex, 2, 2));
    $c_b = hexdec(substr($hex, 4, 2));

    return (($c_r * 299) + ($c_g * 587) + ($c_b * 114)) / 1000;
}


function themeton_admin_post_type() {
    global $post, $typenow, $current_screen;

    // Check to see if a post object exists
    if ($post && $post->post_type)
        return $post->post_type;

    // Check if the current type is set
    elseif ($typenow)
        return $typenow;

    // Check to see if the current screen is set
    elseif ($current_screen && $current_screen->post_type)
        return $current_screen->post_type;

    // Finally make a last ditch effort to check the URL query for type
    elseif (isset($_REQUEST['post_type']))
        return sanitize_key($_REQUEST['post_type']);

    return '-1';
}

function tt_getmeta($meta, $post_id = NULL) {
    global $post;
    if ($post_id != NULL && (int) $post_id > 0) {
        return get_post_meta($post_id, '_' . $meta, true);
    } else if (isset($post->ID)) {
        return get_post_meta($post->ID, '_' . $meta, true);
    }
    return '';
}


function get_post_like($post_id){
    return '<a href="javascript:;" data-pid="'. $post_id .'" class="'. blox_post_liked($post_id) .'"><i class="fa fa-heart"></i> <span>'. (int)blox_getmeta($post_id, 'post_like') .'</span></a>';
}


function get_external_sliders($type){
    global $wpdb;
    $sliders = array();

    if( $type == 'layerslider' ){
        /* SLIDER VALUES */

        if( class_exists('LS_Sliders') ){
            $layer_sliders = LS_Sliders::find(array('data'=>false));
            foreach ($layer_sliders as $item) {
                $sliders = array_merge($sliders, array("layerslider_" . $item['id'] => "LayerSlider - " . $item['name']));
            }
        }
    }
    else if( $type == 'revslider' ){
        if( class_exists('RevSlider') ){
            $revo = new RevSlider();
            $revo_sliders = $revo->getArrSlidersShort();
            foreach ($revo_sliders as $rev_id => $rev_title) {
                $s = new RevSlider();
                $s->initByID($rev_id);
                $alias = $s->getAlias();
                $sliders = array_merge($sliders, array("revslider_" . $alias => "Revolution Slider - " . $rev_title));
            }

        }
    }
    else if( $type == 'masterslider' ){
        if( function_exists('get_mastersliders') ){
            $master_sliders = get_mastersliders();
            foreach ($master_sliders as $slider) {
                $sliders = array_merge($sliders, array("masterslider_" . $slider['ID'] => "Master Slider - " . $slider['title']));
            }

        }
    }

    return $sliders;
}


/* Get Site Logo */
function tt_site_logo() {
    global $smof_data;
    $hide = '';
    echo '<div class="logo site-brand">';

    //Normal logo
    $logo = get_theme_mod('logo');
    if ( !empty($logo) ) {

        $logo = str_replace('[site_url]', site_url(), $logo);
        $logo = str_replace('[site_url_secure]', site_url(), $logo);
        $logo = strpos($logo, 'http')!==false ? $logo : home_url('/') . $logo;
        echo "<a href=" . home_url() . "><img src='" . $logo . "' alt='" . get_bloginfo('name') . "' class='normal'/>";

        // Retina logo
        $retina_logo = get_theme_mod('logo_retina');
        if( !empty($retina_logo) ){
            $logo_width = abs(get_theme_mod('logo-width'));
            $logo_height = abs(get_theme_mod('logo-height'));
            $retina_style = '';
            if( $logo_width>0 && $logo_height>0 ) {
                $retina_style = 'width:'. $logo_width.'px;max-height:'. $logo_height.'px; height: auto !important';
            }
            $retina_logo = str_replace('[site_url]', site_url(), $retina_logo);
            $retina_logo = str_replace('[site_url_secure]', site_url(), $retina_logo);
            echo '<img src="'.$retina_logo.'" alt="'.get_bloginfo('name').'" style="'.$retina_style.'" class="retina" />';
        }
        echo "</a>";

        // Hide site title text if logo image is defined
        $hide = "style='display:none'";
    }
    echo "<h1 $hide class='navbar-brand'><a href=" . home_url() . ">" . get_bloginfo('name') . "</a></h1>";
    echo '</div>';
}

/* Get Ultimate Page Logo */
function tt_up_site_logo() {
    global $smof_data;
    $up_logo = tt_getmeta('up_logo');

    if( !empty($up_logo) ){
        echo '<div class="logo site-brand">';
            echo "<a href=" . home_url() . "><img src='$up_logo' alt='" . get_bloginfo('name') . "' /></a>";
        echo '</div>';
        return;
    }
    $hide = '';
    echo '<div class="logo site-brand">';
    if ( isset($smof_data['logo']) && $smof_data['logo'] != '') {
        echo "<a href=" . home_url() . "><img src='" . $smof_data['logo'] . "' alt='" . get_bloginfo('name') . "' class='normal'/>";

        // Retina logo
        if(isset($smof_data['logo_retina']) && $smof_data['logo_retina'] !='' ) {
            if(isset($smof_data['logo_retina_width']) && isset($smof_data['logo_retina_height'])) {
                        $pixels ="";
                if(is_numeric($smof_data['logo_retina_width']) && is_numeric($smof_data['logo_retina_height'])){
                    $pixels ="px";
                }
                echo '<img src="'. $smof_data["logo_retina"].'" alt="'.get_bloginfo('name').'" style="width:'. $smof_data["logo_retina_width"].$pixels.';max-height:'. $smof_data["logo_retina_height"].$pixels.'; height: auto !important" class="retina" />';
            }
        }
        echo "</a>";

        // Hide site title text if logo image is defined
        $hide = "style='display:none'";
    }
    echo "<h1 $hide class='navbar-brand'><a href=" . home_url() . ">" . get_bloginfo('name') . "</a></h1>";
    echo '</div>';
}

/*
 * Favicon and Touch Icons
 */

function tt_icons() {
    global $smof_data;

    /*
     * Favicon
     */
    $url = get_template_directory_uri() . "/assets/images/favicon.png";
    $url_custom = get_theme_mod('icon_favicon');
    if ( !empty($url_custom) ) {
        $url = $url_custom;
    }
    $url = str_replace('[site_url]', site_url(), $url);
    echo "<link rel='shortcut icon' href='$url'/>";

    /*
     * Apple Devices Touch Icons
     */
    if (isset($smof_data['icon_iphone']) && $smof_data['icon_iphone'])
        echo '<link rel="apple-touch-icon" href="' . $smof_data['icon_iphone'] . '">';
    if (isset($smof_data['icon_iphone_retina']) && $smof_data['icon_iphone_retina'])
        echo '<link rel="apple-touch-icon" sizes="114x114" href="' . $smof_data['icon_iphone_retina'] . '">';
    if (isset($smof_data['icon_ipad']) && $smof_data['icon_ipad'])
        echo '<link rel="apple-touch-icon" sizes="72x72" href="' . $smof_data['icon_ipad'] . '">';
    if (isset($smof_data['icon_ipad_retina']) && $smof_data['icon_ipad_retina'])
        echo '<link rel="apple-touch-icon" sizes="144x144" href="' . $smof_data['icon_ipad_retina'] . '">';
}

/*
 * Site Tracking Code
 */

function tt_trackingcode() {
    global $smof_data;
    if ( isset($smof_data['site_analytics']) && $smof_data['site_analytics']!='') {
        echo $smof_data['site_analytics'];
    }
}

function add_video_radio($embed) {
    if (strstr($embed, 'http://www.youtube.com/embed/')) {
        return str_replace('?fs=1', '?fs=1&rel=0', $embed);
    } else {
        return $embed;
    }
}

add_filter('oembed_result', 'add_video_radio', 1, true);

if (!function_exists('custom_upload_mimes')) {
    add_filter('upload_mimes', 'custom_upload_mimes');

    function custom_upload_mimes($existing_mimes = array()) {
        $existing_mimes['ico'] = "image/x-icon";
        return $existing_mimes;
    }

}


if (!function_exists('format_class')) {

    // Returns post format class by string
    function format_class($post_id) {
        $format = get_post_format($post_id);
        if ($format === false)
            $format = 'standard';
        return 'format_' . $format;
    }
}


/**
 * Comment Count Number
 * @return html
 */
function comment_count_text() {
    $comment_count = get_comments_number('0', '1', '%');
    $comment_text = $comment_count . ' ' . __('Comments', 'themeton');
    if( (int)$comment_count == 1 ){
        $comment_text = $comment_count . ' ' . __('Comment', 'themeton');
    }
    else if( (int)$comment_count < 1 ){
        $comment_text = __('No Comment', 'themeton');
    }
    return "<a href='" . get_comments_link() . "' title='" . $comment_text . "'> " . $comment_text . "</a>";
}

function comment_count() {
    $comment_count = get_comments_number('0', '1', '%');
    $comment_trans = '<i class="fa fa-comment"></i> ' . $comment_count;
    return "<a href='" . get_comments_link() . "' title='" . $comment_trans . "'> " . $comment_trans . "</a>";
}

/**
 * Returns Author link
 * @return html
 */
function get_author_posts_link() {
    $output = '';
    ob_start();
    the_author_posts_link();
    $output .= ob_get_contents();
    ob_end_clean();
    return $output;
}






/**
 * This code filters the Categories archive widget to include the post count inside the link
 */
add_filter('wp_list_categories', 'cat_count_span');

function cat_count_span($links) {
    $links = str_replace('</a> (', ' <span>', $links);
    $links = str_replace('<span class="count">(', '<span>', $links);
    $links = str_replace(')', '</span></a>', $links);
    return $links;
}

/**
 * This code filters the Archive widget to include the post count inside the link
 */
add_filter('get_archives_link', 'archive_count_span');

function archive_count_span($links) {
    $links = str_replace('</a>&nbsp;(', ' <span>', $links);
    $links = str_replace(')</li>', '</span></a></li>', $links);
    return $links;
}

/**
 * Prints social links on top bar & sub footer area
 * @global array $tt_social_icons
 * @param type $footer : Sign of footer layout
 */
function social_links_by_icon($footer = false) {
    global $tt_social_icons, $smof_data;
    $sign = false;
    $pref = 'social_';
    if ($footer)
        $pref = 'footer_' . $pref;
    $result = '<ul class="top-bar-list list-inline">';
    foreach ($tt_social_icons as $key => $hex) {
        if (isset($smof_data[$pref . $key]) && $smof_data[$pref . $key] != '') {
            $url = $smof_data[$pref . $key];
            if ($key != 'email' && $key != 'skype') {
                if (!preg_match_all('!https?://[\S]+!', $url, $matches))
                    $url = "http://" . $url;
            } elseif($key =='skype') {
                $url = $url;
            } else {
                $url = 'mailto:' . $url . '?subject=' . get_bloginfo('name') . '&amp;body='.__('Your%20message%20here!', 'themeton');
            }
            $result .= '<li><a class="social-link ' . $key . '" href="' . $url . '" target="_blank"><i class="fa ' . $hex . '"></i></a></li>';
            $sign = true;
        }
    }
    $result .= '</ul>';
    echo $sign ? $result : __('Please add your socials.', 'themeton');
}

/**
 * Prints Top Bar content
 * @param type $type : Menu type
 * @param type $position : Right or Left
 */
function tt_bar_content($bar_content = 'text1', $footer = false) {
    global $smof_data;

    $splitedValues = explode(',', trim($bar_content));

    foreach ($splitedValues as $value) {

        $type = trim($value);

        $pref = 'top_';
        if($footer) {
            $pref = 'footer_';
        }

        if ($type == 'social') {
            ob_start();
            social_links_by_icon($footer);
            $result = ob_get_clean();
            echo '<div class="topbar-item">'. $result .'</div>';
        }
        elseif ($type == 'shop') {
            global $woocommerce;
            if (isset($woocommerce->cart)) {
                $cart = $woocommerce->cart;

                // Get mini cart
                ob_start();
                woocommerce_mini_cart();
                $mini_cart = ob_get_clean();

                echo '<div class="woocommerce-shcart woocommerce topbar-item hidden-sm hidden-xs">
                        <div class="shcart-display">
                            <i class="fa fa-shopping-cart"></i>'. __('Cart', 'themeton') .'
                            <span class="total-cart">'. $cart->cart_contents_count .'</span>
                        </div>
                        <div class="shcart-content">
                            <div class="widget_shopping_cart_content">' . $mini_cart . '</div>
                        </div>
                      </div>';
            }
            else{
                echo '<div class="topbar-item">'. __('Please install Woocommerce.', 'themeton') .'</div>';;
            }
        }
        elseif ($type == 'lang') {
            global $wp_filter;
            if( isset($wp_filter['icl_language_selector']) ){
                ob_start();
                do_action('icl_language_selector');
                $result = ob_get_clean();
                echo '<div class="topbar-item">'. $result .'</div>';
            }
            else{
                echo '<div class="topbar-item">'. __('Please install WPML.', 'themeton') .'</div>';;
            }
        }
        elseif ($type == 'menu') {
            ob_start();
            wp_nav_menu(array('theme_location' => $pref.'bar-menu', 'fallback_cb' => '', 'depth'=>1, 'menu_class'=>'list-inline'));
            $result = ob_get_clean();
            echo '<div class="topbar-item">'. $result .'</div>';
        }
        elseif ($type == 'text1' || $type == 'text2') {
            if (isset($smof_data[$pref.'bar_'.$type])) {
                $result = '<span class="bar-text">'. do_shortcode($smof_data[$pref.'bar_'.$type]) .'</span>';
                echo '<div class="topbar-item">'. $result .'</div>';
            }
        }
        else if( $type=='login' ){
            $link = get_edit_user_link();
            $text = __('Login / Register', 'themeton');

            if( function_exists('is_shop') ){
                $link = get_permalink( get_option('woocommerce_myaccount_page_id') );
            }
            else if( !is_user_logged_in() ){
                $link = wp_login_url();
            }

            if( is_user_logged_in() ){
                $text = __('My Account','themeton');
            }
            $result = '<div class="topbar-item login-item">
                            <a href="'. $link .'">'. $text .'</a>
                       </div>';
            echo $result;
        }
    }

}



if (!function_exists('tt_comment_form')) :

    function tt_comment_form($fields) {
        global $id, $post_id;
        if (null === $post_id)
            $post_id = $id;
        else
            $id = $post_id;

        $commenter = wp_get_current_commenter();

        $req = get_option('require_name_email');
        $aria_req = ( $req ? " aria-required='true'" : '' );
        $fields = array(
            'author' => '<p class="comment-form-author">' . '<label for="author">' . __('Name', 'themeton') . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
            '<input placeholder="' . __('Name', 'themeton') . '" id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="30"' . $aria_req . ' /></p>',
            'email' => '<p class="comment-form-email"><label for="email">' . __('Email', 'themeton') . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
            '<input placeholder="' . __('Email', 'themeton') . '" id="email" name="email" type="text" value="' . esc_attr($commenter['comment_author_email']) . '" size="30"' . $aria_req . ' /></p>',
            'url' => '<p class="comment-form-url"><label for="url">' . __('Website', 'themeton') . '</label>' .
            '<input placeholder="' . __('Website', 'themeton') . '" id="url" name="url" type="text" value="' . esc_attr($commenter['comment_author_url']) . '" size="30" /></p>',
        );
        return $fields;
    }
    add_filter('comment_form_default_fields', 'tt_comment_form');
endif;



if (!function_exists('about_author')) {

    function about_author() {
        ?>
        <div class="item-author clearfix">
            <?php
            $author_email = get_the_author_meta('email');
            echo get_avatar($author_email, $size = '60');
            ?>
            <h3><?php _e("Written by ", "themeton"); ?><?php if (is_author()) the_author(); else the_author_posts_link(); ?></h3>
            <div class="author-title-line"></div>
            <p>
                <?php
                $description = get_the_author_meta('description');
                if ($description != '')
                    echo $description;
                else
                    _e('The author didnt add any Information to his profile yet', 'themeton');
                ?>
            </p>
        </div>
        <?php
    }

}

if (!function_exists('social_share')) {

    /**
     * Prints Social Share Options
     * @global array $tt_social_icons
     * @global type $post : Current post
     */
    function social_share() {
        global $smof_data, $tt_social_icons, $post;

        echo '<span class="sf_text">' . __('Share', 'themeton') . ': </span>';
        echo '<ul class="post_share list-inline">';
        if (isset($smof_data['share_buttons']['facebook']) && $smof_data['share_buttons']['facebook'] == 1) {
            echo '<li><a href="https://www.facebook.com/sharer/sharer.php?u=' . get_permalink() . '" title="Facebook" target="_blank"><i class="fa ' . $tt_social_icons['facebook'] . '"></i></a></li>';
        }
        if (isset($smof_data['share_buttons']['twitter']) && $smof_data['share_buttons']['twitter'] == 1) {
            echo '<li><a href="https://twitter.com/share?url=' . get_permalink() . '" title="Twitter" target="_blank"><i class="fa ' . $tt_social_icons['twitter'] . '"></i></a></li>';
        }
        if (isset($smof_data['share_buttons']['googleplus']) && $smof_data['share_buttons']['googleplus'] == 1) {
            echo '<li><a href="https://plus.google.com/share?url='.get_permalink().'" title="GooglePlus" target="_blank"><i class="fa ' . $tt_social_icons['googleplus'] . '"></i></a></li>';
        }
        if (isset($smof_data['share_buttons']['pinterest']) && $smof_data['share_buttons']['pinterest'] == 1) {
            $image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'large');
            echo '<li><a href="//pinterest.com/pin/create/button/?url=' . get_permalink() . '&media=' . $image[0] . '&description=' . get_the_title() . '" title="Pinterest" target="_blank"><i class="fa ' . $tt_social_icons['pinterest'] . '"></i></a></li>';
        }
        if (isset($smof_data['share_buttons']['email']) && $smof_data['share_buttons']['email'] == 1) {
            echo '<li><a href="mailto:?subject=' . get_the_title() . '&body=' . strip_tags(get_the_excerpt()) . get_permalink() . '" title="Email" target="_blank"><i class="fa ' . $tt_social_icons['email'] . '"></i></a></li>';
        }
        echo '</ul>';

    }

}

/**
 * Prints Related Posts
 * @global type $post : Current post
 */
if( !function_exists('tt_related_posts') ):
function tt_related_posts( $options=array() ) {

    $options = array_merge(array(
                    'per_page'=>'3'
                    ),
                    $options);

    global $post, $smof_data;

    $args = array(
        'post__not_in' => array($post->ID),
        'posts_per_page' => $options['per_page']
    );
    $grid_class = 'col-md-4 col-sm-6 col-xs-12';
    $post_type_class = 'blog';

    $categories = get_the_category($post->ID);
    if ($categories) {
        $category_ids = array();
        foreach ($categories as $individual_category) {
            $category_ids[] = $individual_category->term_id;
        }
        $args['category__in'] = $category_ids;
    }

    // For portfolio post and another than Post
    if($post->post_type != 'post') {
        $tax_name = 'portfolio_entries'; //should change it to dynamic and for any custom post types
        $args['post_type'] =  get_post_type(get_the_ID());
        $args['tax_query'] = array(
            array(
                'taxonomy' => $tax_name,
                'field' => 'id',
                'terms' => wp_get_post_terms($post->ID, $tax_name, array('fields'=>'ids'))
            )
        );
        if( $options['per_page']=='4' ) {
            $grid_class = 'col-md-3 col-sm-6 col-xs-12';
        }
        $post_type_class = 'portfolio';
    }

    if(isset($args)) {
        $my_query = new wp_query($args);
        if ($my_query->have_posts()) {

            $html = '';
            while ($my_query->have_posts()) {
                $my_query->the_post();

                $html .= '<div class="'.$grid_class.' loop-item">
                                <article itemscope="" itemtype="http://schema.org/BlogPosting" class="entry">
                                    '. hover_featured_image(array('overlay'=>'permalink')) .'

                                    <div class="relative">
                                        <div class="entry-title">
                                            <h2 itemprop="headline">
                                                <a itemprop="url" href="'. get_permalink() .'">'.get_the_title().'</a>
                                            </h2>
                                        </div>
                                        <ul class="entry-meta list-inline">
                                            <li itemprop="datePublished" class="meta-date">'. date_i18n(get_option('date_format'), strtotime(get_the_date())) .'</li>
                                            <li class="meta-like">'. get_post_like(get_the_ID()) .'</li>
                                        </ul>
                                    </div>
                                </article>
                            </div>';
            }

            echo '<div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <h3 class="related-posts">' . __('Related Posts', 'themeton') . '</h3>
                        <div class="blox-element related-posts '.$post_type_class.' grid-loop">
                            <div class="row">
                                <div class="loop-container">'. $html .'</div>
                            </div>
                        </div>
                    </div>
                  </div>';
        }
    }
    wp_reset_query();
}
endif;


// ADDING ADMIN BAR MENU
if (!function_exists('tt_admin_bar_menu')) {
    add_action('admin_bar_menu', 'tt_admin_bar_menu', 90);

    function tt_admin_bar_menu() {

        if (!current_user_can('manage_options'))
            return;

        global $wp_admin_bar;

        $admin_url = admin_url('admin.php');

        $options = array(
            'id' => 'theme-options',
            'title' => __('Theme Options', 'themeton'),
            'href' => $admin_url . "?page=theme-options",
        );
        $wp_admin_bar->add_menu($options);

        $color = array(
            'id' => 'color-options',
            'title' => __('Site Customize', 'themeton'),
            'href' => admin_url() . "customize.php",
        );
        $wp_admin_bar->add_menu($color);
    }

}


/**
 * Prints Custom Logo Image for Login Page
 */
function custom_login_logo() {
    global $smof_data;
    $logo = get_theme_mod('logo_admin');
    if (!empty($logo)) {
        $logo = str_replace('[site_url]', site_url(), $logo);
        echo '<style type="text/css">.login h1 a { background: url(' . $logo . ') center center no-repeat !important;width: auto !important;}</style>';
    }
}

add_action('login_head', 'custom_login_logo');


/*
 * Random order
 * Preventing duplication of post on paged
 */

function register_tt_session(){
    if( !session_id() ){
        session_start();
    }
}

if(!is_admin() && true) {

    function edit_posts_orderby($orderby_statement) {

        add_action('init', 'register_tt_session');
        //add_filter('posts_orderby', 'edit_posts_orderby');

        if (isset($_SESSION['expiretime'])) {
            if ($_SESSION['expiretime'] < time()) {
                session_unset();
            }
        } else {
            $_SESSION['expiretime'] = time() + 300;
        }

        $seed = rand();
        if (isset($_SESSION['seed'])) {
            $seed = $_SESSION['seed'];
        } else {
            $_SESSION['seed'] = $seed;
        }
        $orderby_statement = 'RAND(' . $seed . ')';
        return $orderby_statement;
    }
}




function get_parallax_class($param){
    return $param=='parallax' ? 'parallax-section' : '';
}
function get_parallax_attr($param){
    return $param['type']=='parallax' ? 'data-stellar-background-ratio="0.5"' : '';
}


function get_post_filter_cats(){
    global $post;
    $filter_classes = '';
    $post_categories = wp_get_post_categories( get_the_ID() );
    foreach($post_categories as $c){
        $cat = get_category( $c );
        $filter_classes .= 'filter-'.$cat->slug.' ';
    }
    return $filter_classes;
}



/*
    Post Like Event
    =================================
*/
add_action('wp_ajax_blox_post_like', 'blox_post_like_hook');
add_action('wp_ajax_nopriv_blox_post_like', 'blox_post_like_hook');
function blox_post_like_hook() {
    try {
        $post_id = (int)$_POST['post_id'];
        $count = (int)blox_getmeta($post_id, 'post_like');
        if( $post_id>0 ){
            blox_setmeta($post_id, 'post_like', $count+1);
        }
        echo "1";
    } catch (Exception $e) {
        echo "-1";
    }
    exit;
}

function blox_post_liked($post_id){
    $cookie_id = '';
    if( isset($_COOKIE['liked']) ){
        $cookie_id = $_COOKIE['liked'];
        $ids = explode(',', $cookie_id);
        foreach ($ids as $value) {
            if( $value+'' == $post_id+'' ){
                return 'liked';
            }
        }
    }
    return '';
}


add_action('wp_ajax_reset_post_likes', 'reset_post_likes_hook');
add_action('wp_ajax_nopriv_reset_post_likes', 'reset_post_likes_hook');
function reset_post_likes_hook() {
    if( isset($_GET['hook']) && $_GET['hook']=='reset' ){
        if( current_user_can( 'manage_options' ) ){
            $args = array( 'posts_per_page' => -1);
            $posts = get_posts( $args );
            if ($posts){
                foreach ( $posts as $post ){
                    //setup_postdata($post);
                    blox_setmeta($post->ID, 'post_like', '0');
                }
            }

            $args = array( 'post_type'=>'portfolio', 'posts_per_page' => -1 );
            $posts = get_posts( $args );
            if ($posts){
                foreach ( $posts as $post ){
                    blox_setmeta($post->ID, 'post_like', '0');
                }
            }

            $_COOKIE['post_like'] = '';

            echo 'SUCCESS. :)';
        }
    }
    else{
        echo "<form method='get'>
                <input type='hidden' name='action' value='reset_post_likes' />
                <input type='hidden' name='hook' value='reset' />
                <input type='submit' value='Reset Post LIKES' />
              </form>";
    }

    exit;
}





function blox_getmeta($post_id, $meta){
    return get_post_meta($post_id, '_'.$meta, true);
}

function blox_setmeta($post_id, $meta, $value){
    if(count(get_post_meta($post_id , '_'.$meta)) == 0){
        add_post_meta($post_id , '_'.$meta, trim($value), true);
    }
    else{
        update_post_meta($post_id , '_'.$meta, trim($value));
    }
}






/* Pager functions
====================================================*/
if (!function_exists('themeton_pager')) :

    function themeton_pager($query = null) {
        global $wp_query;
        $current_query = $query!=null ? $query : $wp_query;
        $pages = (int)$current_query->max_num_pages;
        $paged = get_query_var('paged') ? (int)get_query_var('paged') : 1;
        if (is_front_page()){
            $paged = get_query_var('page') ? (int)get_query_var('page') : $paged;
        }

        if (empty($pages)) {
            $pages = 1;
        }

        if ( $pages!=1 ) {
            if ($paged > 1) {
                $prevlink = get_pagenum_link($paged - 1);
            }
            if ($paged < $pages) {
                $nextlink = get_pagenum_link($paged + 1);
            }


            $big = 9999; // need an unlikely integer
            echo "<div class='row'><div class='col-xs-12 col-sm-12 col-md-12 col-lg-12'><div class='pagination-container clearfix'>";

            $args = array(
                'current' => 0,
                'show_all' => false,
                'prev_next' => true,
                'add_args' => false, // array of query args to add
                'add_fragment' => '',
                'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                'end_size' => 3,
                'mid_size' => 1,
                'format' => '?paged=%#%',
                'current' => max(1, $paged),
                'total' => $current_query->max_num_pages,
                'type' => 'list',
                'prev_text' => '<i class="icon-arrow-left"></i>',
                'next_text' => '<i class="icon-arrow-right"></i>',
            );

            extract($args, EXTR_SKIP);

            // Who knows what else people pass in $args
            $total = (int) $total;
            if ($total < 2)
                return;
            $current = (int) $current;
            $end_size = 0 < (int) $end_size ? (int) $end_size : 1; // Out of bounds?  Make it the default.
            $mid_size = 0 <= (int) $mid_size ? (int) $mid_size : 2;
            $add_args = is_array($add_args) ? $add_args : false;
            $r = '';
            $page_links = array();
            $next_link = '<li class="disabled"><a href="#">'. __('Prev', 'themeton') .'</a></li>';
            $prev_link = '<li class="disabled"><a href="#">'. __('Next', 'themeton') .'</a></li>';
            $n = 0;
            $dots = false;

            // Next link
            if ($prev_next && $current && 1 < $current) :
                $link = str_replace('%_%', 2 == $current ? '' : $format, $base);
                $link = str_replace('%#%', $current - 1, $link);
                if ($add_args)
                    $link = wp_nonce_url(add_query_arg($add_args, $link));
                $link .= $add_fragment;
                $next_link = '<li><a href="'. esc_url(apply_filters('paginate_links', $link)) .'">'. __('Prev', 'themeton') .'</a></li>';
            endif;

            // Pager links
            for ($n = 1; $n <= $total; $n++) :
                $n_display = number_format_i18n($n);
                if ($n == $current) :
                    $page_links[] = "<li class='active'><a href='#'>$n_display <span class='sr-only'>(current)</span></a></li>";
                    $dots = true;
                else :
                    if ($show_all || ( $n <= $end_size || ( $current && $n >= $current - $mid_size && $n <= $current + $mid_size ) || $n > $total - $end_size )) :
                        $link = str_replace('%_%', 1 == $n ? '' : $format, $base);
                        $link = str_replace('%#%', $n, $link);
                        if ($add_args)
                            $link = wp_nonce_url(add_query_arg($add_args, $link));
                        $link .= $add_fragment;
                        $page_links[] = "<li><a href='" . esc_url(apply_filters('paginate_links', $link)) . "'>$n_display</a></li>";
                        $dots = true;
                    elseif ($dots && !$show_all) :
                        $page_links[] = '<li><span class="page-numbers dots">&hellip;</span></li>';
                        $dots = false;
                    endif;
                endif;
            endfor;

            // Prev links
            if ($prev_next && $current && ( $current < $total || -1 == $total )) :
                $link = str_replace('%_%', $format, $base);
                $link = str_replace('%#%', $current + 1, $link);
                if ($add_args)
                    $link = wp_nonce_url(add_query_arg($add_args, $link));
                $link .= $add_fragment;
                $prev_link = '<li><a href="'. esc_url(apply_filters('paginate_links', $link)) .'">'. __('Next', 'themeton') .'</a></li>';
            endif;

            $r .= "<ul class='pagination pull-left'>";
            $r .= join("\n\t", $page_links);
            $r .= "</ul>\n";
            $r .= '<ul class="pagination pull-right">
                    '. $next_link .'
                    '. $prev_link .'
                 </ul>
                 <div class="clearfix"></div>';
            echo $r;
            echo "</div></div></div>";
        }
    }

endif;



if ( ! function_exists( 'themeton_theme_comment' ) ) :

function themeton_theme_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'themeton' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( 'Edit', 'themeton' ), '<span class="edit-link">', '</span>' ); ?></p>
	<?php
			break;
		default :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment">
			<footer class="comment-meta">
				<div class="comment-author vcard">
					<?php
						$avatar_size = 68;
						if ( '0' != $comment->comment_parent )
							$avatar_size = 39;

						echo get_avatar( $comment, $avatar_size );

						/* translators: 1: comment author, 2: date and time */
						printf( __( '%1$s on %2$s <span class="says">said:</span>', 'themeton' ),
							sprintf( '<span class="fn">%s</span>', get_comment_author_link() ),
							sprintf( '<a href="%1$s"><time datetime="%2$s">%3$s</time></a>',
								esc_url( get_comment_link( $comment->comment_ID ) ),
								get_comment_time( 'c' ),
								/* translators: 1: date, 2: time */
								sprintf( __( '%1$s at %2$s', 'themeton' ), get_comment_date(), get_comment_time() )
							)
						);
					?>

					<?php edit_comment_link( __( 'Edit', 'themeton' ), '<span class="edit-link">', '</span>' ); ?>
				</div><!-- .comment-author .vcard -->

				<?php if ( $comment->comment_approved == '0' ) : ?>
					<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'themeton' ); ?></em>
					<br />
				<?php endif; ?>

			</footer>

			<div class="comment-content"><?php comment_text(); ?></div>

			<div class="reply">
				<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply <span>&darr;</span>', 'themeton' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div><!-- .reply -->
		</article><!-- #comment-## -->

	<?php
			break;
	endswitch;
}
endif;

// Search form customizing

function tt_search_form( $form ) {
    $form = '<div class="search-form">
                <form method="get" id="searchform" action="'.esc_url( home_url( '/' ) ).'">
                    <div class="input-group">
                        <input type="text" class="form-control" name="s" placeholder="'. __('Type & Enter ...', 'themeton'). '">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="submit">'. __('Go!', 'themeton'). '</button>
                        </span>
                    </div>
                </form>
            </div>';

    return $form;
}
function tt_product_search_form( $form ) {
    $form = '<div class="search-form">
                <form method="get" id="searchform" action="'.esc_url( home_url( '/' ) ).'">
                    <div class="input-group">
                        <input type="text" class="form-control" name="s" placeholder="'. __('Search for products ...', 'themeton'). '">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="submit">'. __('Go!', 'themeton'). '</button>
                        </span>
                    </div>
                    <input type="hidden" name="post_type" value="product" />
                </form>
            </div>';

    return $form;
}

add_filter( 'get_search_form', 'tt_search_form' );
add_filter( 'get_product_search_form', 'tt_product_search_form' );
