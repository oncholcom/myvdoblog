<?php
/**
 *
 * @link       https://1.envato.market/mgXE4y
 * @since      1.0.0
 *
 * @package    WordPress
 * @subpackage StreamTube
 * @author     phpface <nttoanbrvt@gmail.com>
 */
if( ! defined( 'ABSPATH' ) ){
    exit;
}

/**
 *
 * Get streamtube core instance
 * 
 * @return object|false
 * 
 */
function streamtube_get_core(){
    global $streamtube;

    if( class_exists( 'Streamtube_Core' ) && $streamtube instanceof Streamtube_Core ){
        return $streamtube;
    }

    return false;
}

/**
 * Adds custom classes to the array of body classes.
 *
 * @since 1.0.0
 *
 * @param array $classes Classes for the body element.
 *
 * @return array
 */
function streamtube_filter_body_classes( $classes ) {

	$classes[] = 'd-flex flex-column h-100vh';

	// Helps detect if JS is enabled or not.
	$classes[] = 'no-js';

	// Adds `singular` to singular pages, and `hfeed` to all other pages.
	$classes[] = is_singular() ? 'singular' : 'hfeed';

	if( has_nav_menu( 'primary' ) ){
		$classes[] = 'has-primary-menu';
	}

	if( has_nav_menu( 'secondary' ) ){
		$classes[] = 'has-secondary-menu';
	}

    $classes[] = 'header-template-' . get_option( 'header_template', '2' );

    $classes[] = 'content-' . sanitize_html_class( streamtube_get_site_content_width() );

    if( get_option( 'preloader' ) ){
        $classes[] = 'has-preloader';
    }

    if( function_exists( 'WC' ) && (is_woocommerce() || is_search() ) ){
        $classes[] = 'woocommerce';
    }

    if( is_admin_bar_showing() && is_user_logged_in() ){
        $classes[] = 'admin-bar';
    }

    if( get_option( 'single_video' ) == 'page-templates/single-video-v3.php' ){
        $classes[] = 'video-template-single-video-v3';
    }

    if( is_embed() ){
        $classes[] = 'is-embed';
    }

    if( function_exists( 'WC' ) ){
        $classes[] = 'woocommerce';
    }

	return $classes;
}
add_filter( 'body_class', 'streamtube_filter_body_classes' );
add_filter( 'login_body_class', 'streamtube_filter_body_classes', 10, 2 );

// Enqueue login scripts.
add_action( 'login_enqueue_scripts', 'streamtube_enqueue_scripts' );

/**
 *
 * Load the login header
 *
 * @since 1.0.0
 * 
 */
function streamtube_load_login_header(){
	get_template_part( 'template-parts/header/header' );
}
add_action( 'login_header', 'streamtube_load_login_header', 10, 1 );

/**
 *
 * Load the login header
 *
 * @since 1.0.0
 * 
 */
function streamtube_load_login_footer(){
	get_template_part( 'template-parts/footer/footer' );
}
add_action( 'login_footer', 'streamtube_load_login_footer', 10, 1 );

/**
 *
 * Ask if current page is login page
 * 
 * @return true|false
 *
 * @since 1.0.0
 * 
 */
function streamtube_is_login_page(){
    if ( $GLOBALS['pagenow'] === 'wp-login.php' ) {
        return true;
    }

    return false;
}

/**
 *
 * Get default theme mode: dark or light
 * 
 * @return string
 *
 * @since 1.0.0
 * 
 */
function streamtube_get_theme_mode(){
    $theme_mode = get_option( 'theme_mode', 'light' );

    /**
     *
     * Delete theme mode cookies if custom option is disabled
     * 
     */
    if( ! get_option( 'custom_theme_mode', 'on' ) ){
        unset( $_COOKIE['theme_mode'] );
    }

    if( isset( $_COOKIE['theme_mode'] ) && in_array( $_COOKIE['theme_mode'], array( 'light', 'dark' ) ) ){
        $theme_mode = $_COOKIE['theme_mode'];
    }    

    return $theme_mode;
}

/**
 *
 * Hide the float sidebar on WP login page
 *
 * @since 1.0.0
 * 
 */
function streatube_hide_float_sidebar_on_login(){
    if ( streamtube_is_login_page() ) {
        add_filter( 'sidebar_float', '__return_false' );   
    }
}
add_action( 'init', 'streatube_hide_float_sidebar_on_login' );

/**
 *
 * Filter post excerpt more link
 * 
 * @param  string $more
 * @return string
 *
 * @since 1.0.0
 * 
 */
function streamtube_filter_excerpt_more_link( $more ){
    return sprintf( '<div class="more-link-wrap mt-3"><a href="%1$s" class="more-link">%2$s</a></div>',
        esc_url( get_permalink( get_the_ID() ) ),
        esc_html__( 'Continue reading', 'streamtube' )
    );
}
add_filter( 'excerpt_more', 'streamtube_filter_excerpt_more_link', 10, 1 );

/**
 *
 * Filter the archive title
 * Remove taxonomy name on archive pages
 *
 * @since 1.0.0
 * 
 */
function streamtube_filter_archive_title( $title, $original_title, $prefix ){

    if( is_category() || is_tag() || is_tax( 'categories' ) || is_tax( 'video_tag' ) ){
        $title = single_term_title( '', false );
    }

    return $title;
}
add_filter( 'get_the_archive_title', 'streamtube_filter_archive_title', 10, 4 );

/**
 *
 * Filter the post password form
 * 
 * @param  string $output
 * @param  object $post
 * @return string
 *
 * @since 1.0.0
 * 
 */
function streamtube_filter_the_post_password_form( $output, $post ){

    $output = '<div class="post-password-form-wrapper">';

        $output .= sprintf(
            '<form action="%s" class="post-password-form text-center border p-4" method="post">',
            esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) )
        );

            $output .= sprintf(
                '<p>%s</p>',
                esc_html__( 'Please enter your password to unlock this content', 'streamtube' )
            );

            $output .= '<div class="input-group mx-auto">';
                $output .= '<input type="password" name="post_password" class="form-control form-control-sm">';
                $output .= sprintf(
                    '<button type="submit" class="btn btn-danger p-2">%s</button>',
                    esc_html__( 'Unlock', 'streamtube' )
                );
            $output .= '</div>';

        $output .= '</form>';

    $output .= '</div>';

    return $output;

}
add_filter( 'the_password_form', 'streamtube_filter_the_post_password_form', 10, 2 );

/**
 *
 * Filter widget archive link
 *
 * Add span tag for the count
 * 
 * @param  string $links
 * @return string
 *
 * @since 1.0.0
 * 
 */
function sreamtube_filter_get_archives_link( $links ) {
    $links = str_replace( '</a>&nbsp;(', '</a><span class="li-post-count">(', $links );
    $links = str_replace( ')', ')</span>', $links );
    return $links;
}
add_filter('get_archives_link', 'sreamtube_filter_get_archives_link', 10, 1 );

/**
 *
 * Filter widget category link
 *
 * Add span tag for the count
 * 
 * @param  string $links
 * @return string
 *
 * @since 1.0.0
 * 
 */
function sreamtube_filter_wp_list_categories( $links ) {
    $links = str_replace( '</a> (', '</a><span class="li-post-count">(', $links );
    $links = str_replace( ')', ')</span>', $links );
    return $links;
}
add_filter( 'wp_list_categories', 'sreamtube_filter_wp_list_categories', 10, 1 );

/**
 *
 * Add the preloader
 * 
 * @since 1.0.0
 * 
 */
function streamtube_add_preloader(){
    if( get_option( 'preloader' ) ){
        get_template_part( 'template-parts/preloader' );
    }
}
add_action( 'streamtube/header/before', 'streamtube_add_preloader', 1 );

/**
 *
 * Get customizer URl with url param
 *
 * @return string
 *
 * @since  1.0.0
 * 
 */
function streamtube_get_customize_url(){
	return add_query_arg( array(
		'url'	=>	 home_url( $GLOBALS['wp']->request )
	), wp_customize_url() );
}

/**
 *
 * Check if given URL is youtube
 * 
 * @param  string $url
 * @return true|false
 *
 * @since  1.0.0
 * 
 */
function streamtube_get_youtube_url( $url ){
    if( class_exists( 'Streamtube_Core_oEmbed' ) && method_exists( 'Streamtube_Core_oEmbed' , 'get_youtube_url' ) ){
        return Streamtube_Core_oEmbed::get_youtube_url( $url );
    }
    return false;
}

/**
 *
 * Check if given URL is a HLS with .m3u8 extension
 * 
 * @param  string $url
 * @return true|false
 *
 * @since 1.1
 * 
 */
function streamtube_is_hls_url( $url ){

    $fileinfo = pathinfo( $url );

    if( is_array( $fileinfo ) && array_key_exists( 'extension', $fileinfo ) && strtolower( $fileinfo['extension'] ) == 'm3u8' ){
        return true;
    }

    return false;
}

/**
 *
 * Convert seconds to video length
 * 
 * @param  int|string $seconds
 * @return string
 *
 * @since 1.0.0
 * 
 */
function streamtube_seconds_to_length( $seconds ){

    $length = '';

    $maybe_string = explode( ':', $seconds );

    if( is_array( $maybe_string ) && count( $maybe_string ) > 1 ){
        return apply_filters( 'streamtube_seconds_to_length', join( ':', $maybe_string ), $seconds );
    }

    if( $seconds >= 3600 ){
        $length = gmdate( "H:i:s", $seconds%86400);
    }else{
        $length = gmdate( "i:s", $seconds%86400);
    }

    /**
     *
     * Filter the length string
     * 
     */
    return apply_filters( 'streamtube_seconds_to_length', $length, $seconds );
}

/**
 *
 * Get header template
 *
 * @return string
 * 
 * @since 1.0.0
 */
function streamtube_get_header_template(){
    $template = get_option( 'header_template', '1' );

    /**
     *
     * Filter and return the template
     *
     * @since 1.0.0
     * 
     */
    return apply_filters( 'streamtube_get_header_template', $template );
}

/**
 *
 * Get container classes
 * 
 * @param  string $class
 * @return array
 *
 * @since 1.0.0
 * 
 */
function streamtube_get_container_classes( $class = '' ){
    $classes = array();

    if( $class == 'container-wide' ){
        $classes[] = 'container';
        $classes[] = $class;
    }
    else{
        $classes[] = $class;
    }

    return array_unique( $classes );
}

/**
 *
 * Get header container classes
 * 
 * @return string
 *
 * @since 1.0.0
 * 
 */
function streamtube_get_container_header_classes(){
    return join( ' ', array_merge(
        streamtube_get_container_classes( streamtube_get_site_content_width() ),
        array(
            'container-header'
        )
    ) );
}

/**
 *
 * Get footer container classes
 * 
 * @return string
 *
 * @since 1.0.0
 * 
 */
function streamtube_get_container_footer_classes(){
    return join( ' ', array_merge(
        streamtube_get_container_classes( streamtube_get_footer_content_width() ),
        array(
            'container-footer'
        )
    ) );
}

/**
 *
 * Get site content width
 * 
 * @return string
 *
 * @since 1.0.0
 * 
 */
function streamtube_get_site_content_width(){

    $content_width = get_option( 'site_content_width', 'container' );

    if( isset( $GLOBALS['wp_query']->query_vars['dashboard'] ) ){
        $content_width = 'container-fluid';
    }

    /**
     *
     * Filter site content width
     *
     * @since 1.00
     * 
     */
    return apply_filters( 'streamtube_get_site_content_width', $content_width );
}

/**
 *
 * Get single content width
 * 
 * @return string
 *
 * @since 1.0.0
 * 
 */
function streamtube_get_single_content_width(){

    $content_width = get_option( 'single_video_content_width', 'container-fluid' );

    $site_content_width = streamtube_get_site_content_width();

    if( in_array( $site_content_width, array( 'container', 'container-wide' ) ) ){
         $content_width = $site_content_width;
    }

    /**
     *
     * Filter site content width
     *
     * @since 1.00
     * 
     */
    return apply_filters( 'streamtube_get_single_content_width', $content_width );
}

/**
 *
 * Get container single classes
 * 
 * @return string
 *
 * @since 1.0.0
 * 
 */
function streamtube_get_container_single_classes(){
    return join( ' ', streamtube_get_container_classes( streamtube_get_single_content_width() ) );
}

/**
 *
 * Get footer content width
 * 
 * @return string
 *
 * @since 1.0.0
 * 
 */
function streamtube_get_footer_content_width(){

    $content_width = get_option( 'footer_content_width', 'container' );

    $site_content_width = streamtube_get_site_content_width();

    if( in_array( $site_content_width, array( 'container', 'container-wide' ) ) ){
         $content_width = $site_content_width;
    }    

    /**
     *
     * Filter content width
     *
     * @since 1.00
     * 
     */
    return apply_filters( 'streamtube_get_footer_content_width', $content_width );
}

/**
 *
 * Get the blog template settings
 * 
 * @return array
 *
 * 
 */
function streamtube_get_blog_template_settings(){
    return apply_filters( 'streamtube_get_blog_template_settings', array(
        'post_author'       =>  'on',
        'post_date'         =>  'normal',
        'post_category'     =>  'on',
        'post_comment'      =>  'on',
        'post_views'        =>  'on',
        'thumbnail_size'    =>  get_option( 'blog_thumbnail_size', 'post-thumbnails' )
    ) );
}

/**
 *
 * Get the archive template settings
 * 
 * @return array
 *
 * @since 1.0.0
 * 
 */
function streamtube_get_archive_template_settings(){
    $options = array(
        'content_width'     =>  get_option( 'archive_content_width', 'container-fluid' ),
        'posts_per_column'  =>  get_option( 'archive_posts_per_column', 5 ),
        'col_xl'            =>  get_option( 'archive_col_xl', 4 ),
        'col_lg'            =>  get_option( 'archive_col_lg', 4 ),
        'col_md'            =>  get_option( 'archive_col_md', 2 ),
        'col_sm'            =>  get_option( 'archive_col_sm', 2 ),
        'col'               =>  get_option( 'archive_col_xs', 1 ),
        'rows_per_page'     =>  get_option( 'archive_rows_per_page', 4 ),
        'post_comment'      =>  get_option( 'archive_post_comment', 'on' ),
        'post_date'         =>  get_option( 'archive_post_date', 'normal' ),
        'author_name'       =>  get_option( 'archive_author_name', 'on' ),
        'author_avatar'     =>  get_option( 'archive_author_avatar', 'on' ),
        'pagination'        =>  get_option( 'archive_pagination', 'click' )
    );

    $site_content_width = streamtube_get_site_content_width();

    if( in_array( $site_content_width, array( 'container', 'container-wide' ) ) ){
        $options['content_width'] = $site_content_width;
    }

    return $options;    
}

/**
 *
 * Get the search template settings
 * 
 * @return array
 *
 * @since 1.0.0
 * 
 */
function streamtube_get_search_template_settings(){
    $options = array(
        'content_width'         =>  get_option( 'search_content_width', 'container' ),
        'layout'                =>  get_option( 'search_layout', 'list_xxl' ),
        'posts_per_column'      =>  (int)get_option( 'search_posts_per_column', 1 ),
        'col_xl'                =>  get_option( 'search_col_xl', 1 ),
        'col_lg'                =>  get_option( 'search_col_lg', 1 ),
        'col_md'                =>  get_option( 'search_col_md', 1 ),
        'col_sm'                =>  get_option( 'search_col_sm', 1 ),
        'col'                   =>  get_option( 'search_col_xs', 1 ),        
        'rows_per_page'         =>  (int)get_option( 'search_rows_per_page', get_option( 'posts_per_page' ) ),
        'post_excerpt_length'   =>  (int)get_option( 'search_post_excerpt_length', 20 ),
        'author_avatar'         =>  get_option( 'search_author_avatar', 'on' ),
        'hide_empty_thumbnail'  =>  get_option( 'search_hide_empty_thumbnail', 'on' ),
        'pagination'            =>  get_option( 'search_pagination', 'click' ),
        'post_date'             =>  get_option( 'search_post_date', 'normal' ),
        'post_views'            =>  'on',
        'post_comment'          =>  'on',
        'thumbnail_size'        =>  get_option( 'blog_thumbnail_size', 'post-thumbnails' )
    );

    if( $options['posts_per_column'] <= 0 ){
        $options['posts_per_column'] = 1;
    }

    if( $options['rows_per_page'] <= 0 ){
        $options['rows_per_page'] = get_option( 'posts_per_page' );
    }

    $site_content_width = streamtube_get_site_content_width();

    if( in_array( $site_content_width, array( 'container', 'container-wide' ) ) ){
        $options['content_width'] = $site_content_width;
    }

    return $options;
}

/**
 *
 * Get the user template settings
 * 
 * @return array
 *
 * @since 1.0.0
 * 
 */
function streamtube_get_user_template_settings(){
    return array(
        'content_width'     =>  get_option( 'user_content_width', 'container' ),
        'posts_per_column'  =>  get_option( 'user_posts_per_column', 4 ),
        'col_xl'            =>  get_option( 'user_col_xl', 4 ),
        'col_lg'            =>  get_option( 'user_col_lg', 4 ),
        'col_md'            =>  get_option( 'user_col_md', 2 ),
        'col_sm'            =>  get_option( 'user_col_sm', 2 ),
        'col'               =>  get_option( 'user_col_xs', 1 ),         
        'rows_per_page'     =>  get_option( 'user_rows_per_page', 4 ),
        'pagination'        =>  get_option( 'user_pagination', 'click' ),
        'post_date'         =>  get_option( 'user_post_date', 'normal' )
    );
}

/**
 *
 * Get search post type
 * 
 * @return array
 *
 * @since 1.1.9
 * 
 */
function streamtube_get_search_post_types(){
    $post_types = array(
        'any'   =>  esc_html__( 'All', 'streamtube' ),
    );

    $has_user_search        = false;
    $has_collection_search  = false;

    $_post_types = get_option( 'search_post_types', 'video,post' );

    if( is_string( $_post_types ) && ! empty( $_post_types ) ){
        $_post_types = array_map( "trim", explode(',', $_post_types ));
    }

    if( is_array( $_post_types ) ){
        for ( $i = 0; $i < count( $_post_types ); $i++ ) {

            if( trim( $_post_types[$i] ) == 'user' ){
                $has_user_search = true;
            }

            if( trim( $_post_types[$i] ) == 'collection' ){
                $has_collection_search = true;
            }            

            if( post_type_exists( ( $_post_types[$i] ) ) ){
                $post_type_object = get_post_type_object( $_post_types[$i] );

                $post_types[$_post_types[$i]] = $post_type_object->label;
            }
        }
    }

    if( array_key_exists( 'topic', $post_types ) ){
        if( ! function_exists( 'bbpress' ) || ! bbp_allow_search() ){
            unset( $post_types['topic'] );
        }
    }

    if( $has_collection_search ){
        $post_types['collection'] = esc_html__( 'Collections', 'streamtube' );
    }

    if( $has_user_search ){
        $post_types['user'] = esc_html__( 'Users', 'streamtube' );
    }    

    /**
     *
     * Filter the post types
     *
     * @param array $post_type
     *
     * @since 1.1.9
     * 
     */
    return apply_filters( 'streamtube_get_search_post_types', $post_types );
}

/**
 *
 * Get search query value
 * 
 * @return string|null
 *
 * @since 1.1.9
 * 
 */
function streamtube_get_search_query_value(){

    global $wp_query;

    $search_query = get_search_query();

    if( streamtube_is_bbp_search() ){
        $search_query = streamtube_is_bbp_search();
    }

    return $search_query;    
}

/**
 *
 * Check if is bbpress search
 * 
 * @return string|false
 *
 * @since 1.1.9
 * 
 */
function streamtube_is_bbp_search(){

    global $wp_query;

    if( array_key_exists( 'bbp_search', $wp_query->query_vars ) ){
        return $wp_query->query_vars['bbp_search'];
    }    

    return false;
}

/**
 *
 * Get share permalink URL
 * 
 * @return string
 *
 * @since 1.1.7.2
 * 
 */
function streamtube_get_share_permalink( $post_id = 0 ){
    
    if( ! $post_id ){
        $post_id = get_the_ID();
    }

    $url = get_permalink( $post_id );

    if( get_option( 'share_permalink' ) == 'shorturl' ){
        $url = wp_get_shortlink( $post_id );
    }

    return apply_filters( 'streamtube_get_share_embed_permalink', $url, $post_id );
}

/**
 *
 * Check if Google Site Kit Analytics module is activated
 *
 * @return true|false
 * 
 * @since 1.0.8
 */
function streamtube_is_google_analytics_connected(){

    if( class_exists( 'Streamtube_Core_GoogleSiteKit_Analytics' ) ){
        return streamtube_core()->get()->googlesitekit->analytics->is_connected();
    }

    return false;
}

/**
 *
 * Remove comments template widget from the Single V3
 * 
 * @param  array $sidebars_widgets
 * @return array $sidebars_widgets
 *
 * @since 2.1.5
 * 
 */
function streamtube_remove_comments_template_widget( $sidebars_widgets ){
    $widgets = false;

    if( array_key_exists( 'content-bottom' , $sidebars_widgets ) ){
        $widgets = $sidebars_widgets['content-bottom'];

        if( is_array( $widgets ) ){
            for ( $i=0;  $i < count( $widgets );  $i++) { 
                if( isset( $widgets[$i] ) && strpos( $widgets[$i] , 'comments-template-widget' ) !== false ){
                    unset( $widgets[$i] );
                }
            }
        }

        $sidebars_widgets['content-bottom'] = $widgets;
    }
    return $sidebars_widgets;
}

/**
 *
 * Get page template options
 * 
 * @return array
 *
 * @since 2.2
 * 
 */
function streamtube_get_page_template_options( $post_id = 0 ){

    $fallback_default = array(
        'disable_title'                 =>  '',
        'disable_thumbnail'             =>  '',
        'header_alignment'              =>  'default',
        'header_padding'                =>  '5',
        'remove_content_box'            =>  '',
        'disable_content_padding'       =>  '',
        'disable_primary_sidebar'       =>  '',
        'disable_bottom_sidebar'        =>  '',
        'disable_comment_box'           =>  ''        
    );

    if( ! $post_id ){
        $post_id = get_the_ID();
    }

    $streamtube = streamtube_get_core();

    if( ! $streamtube ){
        return $fallback_default;
    }

    if( ! method_exists( $streamtube->get()->metabox, 'get_template_options' ) ){
        return $fallback_default;
    }

    return $streamtube->get()->metabox->get_template_options( $post_id );
}