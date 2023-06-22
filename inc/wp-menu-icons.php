<?php
/**
 *
 * WP Menu Icons plugin compatiblity file
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

if( ! function_exists( 'streamtube_filter_wp_menu_icons_item_title' ) ){
	/**
	 *
	 * Filter the wp icon title
	 *
	 * 
	 * @param  string $new_title
	 * @param  int $menu_item_id
	 * @param  $wpmi
	 * @param  $title        [description
	 * @return string $title
	 *
	 * @since 1.0.0
	 * 
	 */
	function streamtube_filter_wp_menu_icons_item_title( $new_title, $menu_item_id, $wpmi, $title ){
		return $title;
	}

	add_filter( 'wp_menu_icons_item_title' , 'streamtube_filter_wp_menu_icons_item_title', 10, 4 );	
}

if( ! function_exists( 'streamtube_filter_wp_menu_item_title' ) ){
	/**
	 *
	 * Filter the menu title
	 *
	 * 
	 * @param  string $title
	 * @param  WP Post Object $item
	 * @param  array $args
	 * @param  int $depth
	 * @return string
	 *
	 *
	 * @since 1.0.0
	 * 
	 */
	function streamtube_filter_wp_menu_item_title( $title, $item, $args, $depth ){

		$_title = '';

		$wpmi = get_post_meta( $item->ID , WPMI_DB_KEY, true );

		$icon = '';

		if ( isset( $wpmi['icon'] ) && $wpmi['icon'] != '' ) {
			$icon = sprintf( 
				'<span class="menu-icon %s" data-bs-toggle="tooltip" data-bs-placement="%s" title="%s"></span>',
				esc_attr( $wpmi['icon'] ),
				! is_rtl() ? 'right' : 'left',
				esc_attr( $title )
			);
		}

		$title = sprintf( 
			'<span class="menu-title menu-text">%s</span>',
			 $title
		);

		if ( isset($wpmi['position']) && $wpmi['position'] == 'after' ) {
			$_title = $title . $icon;
		}
		else{
			$_title = $icon . $title;
		}

		return sprintf( '<span class="menu-icon-wrap">%s</span>', $_title );

	}

	add_filter( 'nav_menu_item_title' , 'streamtube_filter_wp_menu_item_title', 10, 4 );
}