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
 * Fires before header
 *
 * @since 1.0.0
 * 
 */
do_action( 'streamtube/header/before' );

$content_width = streamtube_get_site_content_width();

$header_template = sanitize_file_name( streamtube_get_header_template() );

if( empty( $header_template ) ){
    $header_template = 1;
}

switch ( $content_width ) {
    case 'container':
    case 'container-wide':
        get_template_part( 'template-parts/header/header-'. $header_template .'-boxed' );
    break;
    
    default:
        get_template_part( 'template-parts/header/header-'. $header_template .'-fullwidth' );
    break;
}

/**
 *
 * Fires after header
 *
 * @since 1.0.0
 * 
 */
do_action( 'streamtube/header/after' );