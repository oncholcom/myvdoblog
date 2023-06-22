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
 * The custom logo
 * 
 * @return HTML
 *
 * @since 1.0.0
 * 
 */
function streamtube_the_custom_logo(){

	$output = get_custom_logo();

	$default_mode = get_option( 'theme_mode', 'light' );

	if( isset( $_COOKIE['theme_mode'] ) && in_array( $_COOKIE['theme_mode'], array( 'light', 'dark' ) ) ){
		$default_mode = $_COOKIE['theme_mode'];
	}

	if( $default_mode == 'dark' ){
		$dark_logo = get_option( 'dark_logo' );

		if( ! empty( $dark_logo ) ){
			$output = preg_replace(  '/<img(.*?)src=/is' , '<img$1src="' . esc_attr( $dark_logo ) . '" data-light-src=' , $output );
		}
	}

	echo wp_kses_post( $output );
}

/**
 *
 * The posts navigation
 * 
 * @param  array $args
 * @param  boolean $echo
 * @return HTML
 */
function streamtube_posts_navigation( $args = array(), $echo = true ){

	$args = wp_parse_args( $args, array(
		'prev_text'          => esc_html__( 'Older posts', 'streamtube' ) . '<span class="icon-angle-right"></span>',
		'next_text'          => '<span class="icon-angle-left"></span>' . esc_html__( 'Newer posts', 'streamtube' )
	) );

	$output = get_the_posts_navigation( $args );

	if( $output ){
		$output = sprintf(
			'<div class="navigation-wrap">%s</div>',
			$output
		);
	}

	if( $echo ){
		echo wp_kses_post( $output );
	}
	else{
		return wp_kses_post( $output );
	}
}

/**
 *
 * The posts pag
 * 
 * @param  array $args
 * @param  boolean $echo
 * @return HTML
 *
 * @since 1.0.0
 * 
 */
function streamtube_posts_pagination( $args = array(), $echo = true ){

	$args = wp_parse_args( $args, array(
		'type'		=>	'list',
		'prev_text' => sprintf(
			'<span class="icon-angle-%s"></span>',
			! is_rtl() ? 'left' : 'right'
		),
		'next_text' => sprintf(
			'<span class="icon-angle-%s"></span>',
			! is_rtl() ? 'right' : 'left'	
		),
		'echo'		=>	false,
		'el_class'	=>	''
	) );

	$output = get_the_posts_pagination( $args );

	if( $output ){
		$output = sprintf(
			'<div class="navigation-wrap %s">%s</div>',
			sanitize_html_class( $args['el_class'] ),
			$output
		);
	}

	if( $echo ){
		echo wp_kses_post( $output );
	}
	else{
		return wp_kses_post( $output );
	}
}

/**
 *
 * The comments pagination
 * 
 * @param  array   $args
 * @param  boolean $echo
 * @return HTML
 *
 * @since 1.0.0
 * 
 */
function streamtube_comments_pagination( $args = array(), $echo = true ){
	$output = get_the_comments_pagination( $args );

	if( $output ){
		$output = sprintf(
			'<div class="navigation-wrap">%s</div>',
			$output
		);
	}

	if( $echo ){
		echo wp_kses_post( $output );
	}
	else{
		return wp_kses_post( $output );
	}	
}

/**
 *
 * The comments navigation
 * 
 * @param  array   $args
 * @param  boolean $echo
 * @return HTML
 *
 * @since 1.0.0
 * 
 */
function streamtube_comments_navigation( $args = array(), $echo = true ){
	$output = get_the_comments_navigation( $args );

	if( $output ){
		$output = sprintf(
			'<div class="navigation-wrap p-3 mb-3">%s</div>',
			$output
		);
	}

	if( $echo ){
		echo wp_kses_post( $output );
	}
	else{
		return wp_kses_post( $output );
	}	
}

/**
 *
 * Get current user roles
 * 
 * @return string
 *
 * @since 1.0.0
 * 
 */
function streamtube_user_roles( $user_id = 0, $exclude_roles = array() ){

	$output = '';

	if( ! $user_id ){
		$user_id = get_current_user_id();
	}

	if( $user_id ){
		$roles = get_userdata( $user_id )->roles;
	}

	if( $roles ){
		for ( $i=0;  $i < count( $roles );  $i++) {

			if( is_array( $exclude_roles ) && ! in_array( $roles[$i] , $exclude_roles ) ){
				$name = apply_filters( 'streamtube/role/display_name', $roles[$i], $user_id );
				$output .= sprintf(
					'<span class="user-role role-%s badge bg-secondary">%s</span>',
					esc_attr( $roles[$i] ),
					$name 
				);
			}
		}
	}

	if( ! empty( $output ) ){
		printf(
			'<div class="user-roles d-block justify-content-center gap-2">%s</div>',
			$output
		);
	}
}