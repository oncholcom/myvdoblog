<?php
/**
 *
 * Woocommerce plugin compatibility file
 * 
 */
if( ! defined( 'ABSPATH' ) ){
	exit;
}

/**
 * Register widget area.
 *
 * @since 1.0.0
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 *
 * @return void
 */

if( ! get_option( 'woocommerce_enable', 'on' ) ){
    return;
} 

function streamtube_woo_widgets_init() {

    register_sidebar(
        array(
            'name'          => esc_html__( 'Woocommerce Primary', 'streamtube' ),
            'id'            => 'woocommerce',
            'description'   => esc_html__( 'Add widgets here to appear in Woocommerce primary sidebar.', 'streamtube' ),
            'before_widget' => '<div id="%1$s" class="widget widget-primary widget-woocommerce shadow-sm %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<div class="widget-title-wrap"><h2 class="widget-title d-flex align-items-center">',
            'after_title'   => '</h2></div>',
        )
    );
}
add_action( 'widgets_init', 'streamtube_woo_widgets_init' );

// Hide default shop page title
add_filter( 'woocommerce_show_page_title', '__return_null' );

/**
 *
 * Filter my-account endpoints
 *
 * @since 1.0.5
 * 
 */
function streamtube_woo_filer_account_endpoint_url( $url, $endpoint, $value = '', $permalink = '' ){

    $myaccount_endpoints = array(
        'orders',
        'view-order',
        'downloads',
        'edit-address',
        'edit-account'
    );

    if( in_array( $endpoint, $myaccount_endpoints ) && is_user_logged_in() ){
        $url = trailingslashit( get_author_posts_url( get_current_user_id() ) ) . 'dashboard/shop/' . $endpoint;

        if( $value ){
            $url= trailingslashit( $url ) . $value;
        }
    }

    return $url;

}
add_filter( 'woocommerce_get_endpoint_url', 'streamtube_woo_filer_account_endpoint_url', 10, 4 );

/**
 *
 * Redirect default WC myaccount to dashboard
 * 
 * @since 1.0.5
 */
function streamtube_woo_redirect_myaccount_page(){

    $redirect_url = wp_login_url();

    if( is_user_logged_in() ){
        $redirect_url = trailingslashit( get_author_posts_url( get_current_user_id() ) ) . 'dashboard/shop';
    }

    if( is_page( get_option( 'woocommerce_myaccount_page_id' ) ) ){

        if( array_key_exists( 'lost-password', $GLOBALS['wp']->query_vars ) ){
            $redirect_url = wp_lostpassword_url();
        }

        wp_redirect( $redirect_url );
        exit;
    }

}
add_action( 'wp', 'streamtube_woo_redirect_myaccount_page' );

/**
 *
 * Save address
 * 
 * @since 1.0.5
 * 
 */
function streamtube_woo_save_address(){

    if ( ! isset( $_POST['address_type'] ) || ! in_array( $_POST['address_type'], array( 'billing', 'shipping' ) )  ) {
        return;
    }    

    $form = new WC_Form_Handler();

    $GLOBALS['wp']->query_vars = array_merge( (array)$GLOBALS['wp']->query_vars, array(
        'edit-address'  =>  $_POST['address_type']
    ) );

    $form->save_address();

}
add_action( 'init', 'streamtube_woo_save_address' );