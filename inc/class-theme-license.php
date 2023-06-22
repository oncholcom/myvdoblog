<?php

/**
 * The update-specific functionality of the plugin.
 *
 * @link       https://themeforest.net/user/phpface
 * @since      1.0.0
 *
 * @package    Streamtube_Core
 * @subpackage Streamtube_Core/admin
 */

/**
 * The update-specific functionality of the plugin.
 *
 * @package    Streamtube_Core
 * @subpackage Streamtube_Core/admin
 * @author     phpface <nttoanbrvt@gmail.com>
 */

if( ! defined('ABSPATH' ) ){
    exit;
}

class Class_StreamTube_Theme_License{

    const ITEM_ID                   = 33821786;

    const ITEM_CHECK_VERSION_URL    = 'https://api.marstheme.com/version.txt';

    /**
     *
     * Holds the Item URL
     * 
     * @var string
     */
    const ENVATO_ITEM_URL           = 'https://1.envato.market/qny3O5';

    /**
     *
     * Holds the theme API URL
     * 
     * @var string
     */
    const THEME_API_URL             = 'https://api.marstheme.com/wp-json/licenser/v1';

    /**
     *
     * Holds the admin page slug
     * 
     * @var string
     */    
    const ADMIN_PAGE_SLUG           = 'license-verification';

    /**
     * The instance of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     */ 
    protected static $instance = null;

    /**
     *
     * Class instance
     */
    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }    

    /**
     * Class loader
     */
    public function init() {
        add_action( 'admin_menu', array( $this , 'admin_menu' ) );

        add_action( 'admin_notices', array( $this , 'check_form_verification' ) );

        add_action( 'admin_notices', array( $this , 'check_form_verification_load' ) );

        add_action( 'admin_notices', array( $this, 'admin_notices' ) );

        add_action( 'wp_ajax_dismiss_verify_purchase', array( $this , 'ajax_dismiss_verify_purchase' ) );

        add_filter( 'pre_set_site_transient_update_themes', array( $this , 'check_for_theme_update' ), 9999, 1 );

        add_action( 'wp_update_plugins', array( $this, 'check_for_plugins_update' ) );

        add_action( 'wp_update_themes', array( $this, 'check_for_license_update' ) );
    }

    /**
     *
     * Prepare query args
     * 
     * @param  array  $args
     * @return array  $args
     *
     * @since 1.1
     * 
     */
    private function prepare_query_args( $args = array() ){
        return array_merge( $args, array(
            'site_url'          =>  home_url( '/' ),
            'admin_email'       =>  get_bloginfo( 'admin_email' ),
            'envato_username'   =>  $this->get_envato_username()
        ) );        
    }

    /**
     *
     * Clean up data
     * 
     * @since 1.1
     */
    public function cleanup_data(){
        delete_option( 'envato_' . self::ITEM_ID );

        delete_option( 'license_check_times_' . self::ITEM_ID );

        delete_transient( 'sample_contents_' . self::ITEM_ID );

        delete_transient( 'plugins_' . self::ITEM_ID );

        delete_option( 'envato_error_messages' );
    }    

    /**
     *
     * Get item ID
     * 
     * @return int
     *
     * @since 1.1
     * 
     */
    public function get_item_id(){
        return self::ITEM_ID;
    }

    /**
     *
     * Get access token
     * 
     * @return string
     *
     * @since 1.0.5
     * 
     */
    public function get_access_token(){
        return get_option( 'access_token' );
    }

    /**
     *
     * Get purchase code
     * 
     * @return string
     *
     * @since 1.0.5
     * 
     */
    public function get_purchase_code(){
        return get_option( 'envato_purchase_code_' . self::ITEM_ID );
    }

    public function get_envato_username(){
        return get_option( 'envato_username_' . self::ITEM_ID );
    }

    /**
     * @since 1.1
     */
    public function is_verified(){
        return get_option( 'envato_' . self::ITEM_ID );
    }

    /**
     *
     * Get installable WP theme
     * 
     * @return string
     *
     * @since 1.0.5
     * 
     */
    public function get_download_theme_url(){

        $url = add_query_arg( array(
            'purchase_code' =>  $this->get_purchase_code(),
            'shorten_url'   =>  true
        ), 'https://api.envato.com/v3/market/buyer/download' );

        $args = array(
            'headers'  =>   array(
                'authorization' =>  'Bearer ' . $this->get_access_token()
            )
        );

        $response = wp_remote_get( $url, $args );

        if( is_wp_error( $response ) ){
            return $response;
        }

        if( wp_remote_retrieve_response_code( $response ) != 200 ){
            return new WP_Error(
                wp_remote_retrieve_response_code( $response ),
                wp_remote_retrieve_response_message( $response )
            );
        }

        $data = json_decode( wp_remote_retrieve_body( $response ), true );

        if( is_array( $data ) && array_key_exists( 'wordpress_theme', $data ) ){
            return $data['wordpress_theme'];
        }

        return new WP_Error( 'undefined', esc_html__( 'Undefined Error, please try again.', 'streamtube' ) );
    }

    /**
     *
     * Get plugin list
     * 
     * @return false|array
     *
     * @since 1.1
     * 
     */
    public function get_plugins(){

        if( ! $this->is_verified() ){
            return array();
        }

        $cache = get_transient( 'plugins_' . self::ITEM_ID );

        if( ! $cache ){
            return array();
        }

        return $cache;
    }

    /**
     *
     * Get required plugins
     * 
     * @return array
     *
     * @since 1.0.5
     * 
     */
    public function request_plugins(){

        if( ! $this->is_verified() ){
            return new WP_Error( 'not_verified', esc_html__( 'Not verified yet', 'streamtube' ) );
        }

        $args = array(
            'purchase_code' =>  $this->get_purchase_code(),
            'access_token'  =>  $this->get_access_token()
        );

        $request_url = add_query_arg( $this->prepare_query_args( $args ), self::THEME_API_URL . '/get_plugins' );

        $response = wp_remote_get( $request_url );

        if( is_wp_error( $response ) ){
            return $response;
        }

        if( wp_remote_retrieve_response_code( $response ) != 200 ){
            return new WP_Error(
                wp_remote_retrieve_response_code( $response ),
                wp_remote_retrieve_response_message( $response )
            );
        }

        $response = json_decode( wp_remote_retrieve_body( $response ), true );

        set_transient( 'plugins_' . self::ITEM_ID, $response, 60*1*60*24 );

        return $response;
    }

    /**
     *
     * Get required plugins
     * 
     * @return array
     *
     * @since 1.0.5
     * 
     */
    public function request_sample_contents(){

        if( ! $this->is_verified() ){
            return new WP_Error( 'not_verified', esc_html__( 'Not verified yet', 'streamtube' ) );
        }

        $args = array(
            'purchase_code' =>  $this->get_purchase_code(),
            'access_token'  =>  $this->get_access_token()
        );

        $request_url = add_query_arg( $this->prepare_query_args( $args ), self::THEME_API_URL . '/get_sample_contents' );

        $response = wp_remote_get( $request_url );

        if( is_wp_error( $response ) ){
            return $response;
        }

        if( wp_remote_retrieve_response_code( $response ) != 200 ){
            return new WP_Error(
                wp_remote_retrieve_response_code( $response ),
                wp_remote_retrieve_response_message( $response )
            );
        }

        $response = json_decode( wp_remote_retrieve_body( $response ), true );

        set_transient( 'sample_contents_' . self::ITEM_ID, $response, 60*1*60*365 );

        return $response;
    }

    /**
     *
     * Get sample content list
     * 
     * @return false|array
     *
     * @since 1.1
     * 
     */
    public function get_sample_content(){

        if( ! $this->is_verified() ){
            return array();
        }

        $cache = get_transient( 'sample_contents_' . self::ITEM_ID );

        if( ! $cache ){
            return array();
        }

        return $cache;
    }   

    /**
     * @since 1.1
     */
    public function output_message( $messages = array() ){

        if( ! $messages ){
            return;
        }

        if( is_array( $messages ) && count( $messages ) > 0 ){
            return join( '<br/>', $messages );
        }

        return $messages;
    }

    private function deregister_license(){

        $response = wp_remote_post( self::THEME_API_URL . '/deregister', array(
            'body'  =>  $this->prepare_query_args( array(
                'purchase_code' =>  $this->get_purchase_code(),
                'access_token'  =>  $this->get_access_token()
            ) )
        ) );

        if( is_wp_error( $response ) ){
            return $response;
        }

        if( wp_remote_retrieve_response_code( $response ) != 200 ){
            return new WP_Error(
                wp_remote_retrieve_response_code( $response ),
                wp_remote_retrieve_response_message( $response )
            );
        }

        $this->cleanup_data();

        return $response;
    } 

    /**
     *
     * Verify purchase
     * 
     * @param  array $args
     * @return WP_Error|Array
     *
     * @since 1.1
     * 
     */
    public function verify_purchase( $args ){

        $args = wp_parse_args( $args, array(
            'purchase_code' =>  '',
            'access_token'  =>  ''
        ) );

        if ( ! preg_match("/^([a-f0-9]{8})-(([a-f0-9]{4})-){3}([a-f0-9]{12})$/i", $args['purchase_code'] ) ){
            return new WP_Error(
                'invalid_purchase_code',
                esc_html__( 'Invalid Purchase Code', 'streamtube' )
            );
        }

        $response = wp_remote_post( self::THEME_API_URL . '/verification', array(
            'body'  =>  $this->prepare_query_args( $args )
        ) );

        if( is_wp_error( $response ) ){
            return $response;
        }

        if( wp_remote_retrieve_response_code( $response ) != 200 ){

            $body = json_decode( wp_remote_retrieve_body( $response ), true );

            $message = is_array( $body ) && array_key_exists( 'message', $body ) ? $body['message'] : wp_remote_retrieve_response_message( $response );

            return new WP_Error(
                wp_remote_retrieve_response_code( $response ),
                $message
            );
        }

        return json_decode( wp_remote_retrieve_body( $response ), true );
    }    

    /**
     *
     * Form Verification handler
     * 
     * @since 1.1
     */
    public function check_form_verification(){

        if ( ! isset( $_POST['submit'] ) || ! isset( $_POST['verify_form_check'] ) || ! wp_verify_nonce( $_POST['verify_form_check'], 'verify_form_check' )){
            return;
        }

        if( ! current_user_can( 'administrator' ) ){
            return;
        }        

        if( $_POST['submit'] == 'deregister' ){
            return $this->deregister_license();
        }

        $access_token       = sanitize_text_field( $_POST['access_token'] );
        $purchase_code      = sanitize_text_field( $_POST['purchase_code'] );
        $envato_username    = sanitize_text_field( $_POST['envato_username'] );

        update_option( 'access_token', $access_token );
        update_option( 'envato_purchase_code_' . self::ITEM_ID, $purchase_code );
        update_option( 'envato_username_' . self::ITEM_ID, $envato_username );

        $this->cleanup_data();

        $response = $this->verify_purchase( compact( 'access_token', 'purchase_code' ) );

        if( is_wp_error( $response ) ){

            update_option( 'envato_error_messages', $response->get_error_messages() );
            ?>
                <div class="notice notice-error settings-error">
                    <?php printf( '<p>%s</p>', $this->output_message( $response->get_error_messages() ) ); ?>
                </div>
            <?php
            return;
        }

        update_option( 'envato_' . self::ITEM_ID, $response );

        $this->request_plugins();

        $this->request_sample_contents();

        ?>
        <div class="notice notice-success">
            <p>
                <?php
                printf(
                    __( 'Congratulations, you have verified <strong>%s - %s</strong> successfully', 'streamtube' ),
                    wp_get_theme()->name,
                    wp_get_theme()->description
                );
                ?>
            </p>
        </div>
        <?php
    }

    /**
     *
     * Check verification
     *
     * @since 1.0.5
     * 
     */
    public function check_form_verification_load(){

        if( ! $this->get_access_token() || ! $this->get_purchase_code() || isset( $_POST['verify_form_check'] ) ){
            return;
        }

        if( ! isset( $_GET['page'] ) || $_GET['page'] != self::ADMIN_PAGE_SLUG ){
            return;
        }

        if( get_option( 'envato_error_messages' ) ){

            ?>
                <div class="notice notice-error settings-error">
                    <?php printf( '<p>%s</p>', $this->output_message( get_option( 'envato_error_messages' ) ) ); ?>
                </div>
            <?php
            return;          
        }

        if( $this->is_verified() ){
            ?>
            <div class="notice notice-success">
                <p>
                    <?php
                    printf(
                        __( 'Congratulations, you verified <strong>%s - %s</strong> successfully.', 'streamtube' ),
                        wp_get_theme()->name,
                        wp_get_theme()->description
                    );
                    ?>
                </p>
            </div>
            <?php  
        }
    }

    public function ajax_dismiss_verify_purchase(){

        if( ! current_user_can( 'administrator' ) ){
            wp_send_json_error( esc_html__( 'No permission', 'streamtube' ) );
        }

        $user_id = get_current_user_id();

        $itemId = self::ITEM_ID;

        set_transient( "dismiss_verify_{$itemId}_{$user_id}", 'on', 60*60*24 );

        wp_send_json_success( esc_html__( 'Dismissed', 'streamtube' ) );
    }  

    public function admin_notices(){

        $user_id = get_current_user_id();
        $itemId = self::ITEM_ID;

        if( ! get_option( 'envato_' . $itemId ) && ! get_transient( "dismiss_verify_{$itemId}_{$user_id}" ) ):
            if( ! isset( $_GET['page'] ) || $_GET['page'] != self::ADMIN_PAGE_SLUG ):
                ?>
                    <div class="notice notice-success notice-verify-purchase is-dismissible">
                        <p><?php printf(
                            esc_html__( 'Would you like to receive automatic updates and unlock premium support? Please %s of %s.', 'streamtube' ),
                            '<strong><a href="'. esc_url( admin_url( 'themes.php?page=' . self::ADMIN_PAGE_SLUG ) ) .'">'. esc_html__( 'activate your copy', 'streamtube' ) .'</a></strong>',
                            sprintf(
                                '<strong><a href="'. self::ENVATO_ITEM_URL .'">%s - %s</a></strong>',
                                wp_get_theme()->name,
                                wp_get_theme()->description
                            )
                        ) ?></p>
                    </div>
                <?php
            endif;
        endif;
    }

    /**
     *
     * Check or theme update
     *
     * @since 1.0.5
     * 
     */
    public function check_for_theme_update( $transient ){
        $current_theme_name         =   '';
        $current_theme_version      =   '';
        $new_version                =   '';
        $package                    =   '';
        $url                        =   '';

        $theme = wp_get_theme();
        
        if( $theme->parent() ){
            $current_theme_name     =   $theme->parent()->get('Name');
            $current_theme_version  =   $theme->parent()->get('Version');
        }
        else{
            $current_theme_name     =   $theme->get('Name');
            $current_theme_version  =   $theme->get('Version');
        }

        if ( empty ( $transient->checked ) ){
            return $transient;
        }

        $check = $this->is_verified();

        if( ! $check || empty( $check ) || ! is_array( $check ) ){
            $new_version = $this->check_for_theme_version();
        }else{
            $new_version = $check['item']['wordpress_theme_metadata']['version'];

            $package = $this->get_download_theme_url();

            if( is_wp_error( $package ) ){
                $package = '';
            }
        }

        if( $new_version && version_compare( $new_version, $current_theme_version, '>' ) ){
            $transient->response[ trim( get_template() ) ] = compact( 'new_version', 'url', 'package' );
        }

        return $transient;
    }

    /**
     *
     * Check for theme version
     *
     * @since 2.0
     * 
     */
    private function check_for_theme_version(){

        $check = wp_remote_get( self::ITEM_CHECK_VERSION_URL, array(
            'headers'       => array(
                'referer'   => home_url()
            )
        ) );

        if( ! is_wp_error( $check ) ){
            return wp_remote_retrieve_body( $check );
        }

        return false;
    }

    /**
     *
     * Check for updating latest plugins
     * 
     * @since 1.0.8
     */
    public function check_for_plugins_update(){
        return $this->request_plugins();
    }

    /**
     *
     * Check for License Update
     * 
     * @since 1.1
     */
    public function check_for_license_update(){

        $check_times    = (int)get_option( 'license_check_times_' . self::ITEM_ID, 1 );
        $access_token   = $this->get_access_token();
        $purchase_code  = $this->get_purchase_code();

        $response = $this->verify_purchase( compact( 'access_token', 'purchase_code' ) );

        if( is_wp_error( $response ) ){
            if( $check_times == 10 ){
                $this->cleanup_data();
            }else{
                $check_times++;
                update_option( 'license_check_times_' . self::ITEM_ID, $check_times );
            }
        }else{
            delete_option( 'license_check_times_' . self::ITEM_ID );
        }
    }

    /**
     *
     * Add update page.
     *
     * @since 1.0.5
     * 
     */
    public function admin_menu(){
        add_theme_page( 
            esc_html__( 'Verify License', 'streamtube' ), 
            esc_html__( 'Verify License', 'streamtube' ), 
            'administrator', 
            self::ADMIN_PAGE_SLUG, 
            array( $this , 'admin_menu_callback' ) 
        );
    }    

    /**
     *
     * Load update page content
     * 
     * @since 1.0.5
     */
    public function admin_menu_callback(){
        get_template_part( 'template-parts/admin/verify-license' );
    }

}

if( ! function_exists( 'StreamTube_Theme_License' ) ):
    /**
     * 
     * @since 1.0.5
     */
    function StreamTube_Theme_License(){
        $GLOBALS['StreamTube_Theme_License'] = Class_StreamTube_Theme_License::get_instance();

        return $GLOBALS['StreamTube_Theme_License'];
    }
    StreamTube_Theme_License()->init();
endif;