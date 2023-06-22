<?php
/**
 *
 *	WP User Follow plugin compatiblity file
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
 * Load the button after the avatar
 * 
 * @since 1.0.0
 * 
 */
function streamtube_wpuf_load_profile_button_follow(){
    if( function_exists( 'wpuf_button_follow' ) ){
        wpuf_button_follow( array(
            'user_id'   =>  get_queried_object_id(),
            'echo'      =>  true
        ) );
    }   
}
add_action( 'streamtube/core/user/navigation/right', 'streamtube_wpuf_load_profile_button_follow', 10 );

/**
 * 
 * Load the follow button
 * 
 * @return wpuf_button_follow()
 *
 * @since 1.0.0
 * 
 */
function streamtube_wpuf_load_single_button_follow(){
	if( function_exists( 'wpuf_button_follow' ) ){
		wpuf_button_follow( array(
			'user_id'	=>	get_the_author_meta( 'ID' ),
            'btn_size'  =>  'lg'
		) );
	}
}

// add to the user section in single video page
add_action( 'streamtube/single/video/author/after', 'streamtube_wpuf_load_single_button_follow' );


/**
 * 
 * Load the follow button
 * 
 * @return wpuf_button_follow()
 *
 * @since 1.0.0
 * 
 */
function streamtube_wpuf_load_user_nav_button_follow(){
	if( function_exists( 'wpuf_button_follow' ) ){
		wpuf_button_follow( array(
			'user_id'	    =>	get_queried_object_id()
		) );
	}
}
// add to the user's navigation
add_action( 'streamtube/user/nav/right', 'streamtube_wpuf_load_user_nav_button_follow' );


/**
 * 
 * Load the follow button
 * 
 * @return wpuf_button_follow()
 *
 * @since 1.0.0
 * 
 */
function streamtube_wpuf_load_user_card_button_follow( $user ){
	if( function_exists( 'wpuf_button_follow' ) ){
		wpuf_button_follow( array(
			'user_id'	=>	$user->ID
		) );
	}
}
add_action( 'streamtube/core/user/card/name/after', 'streamtube_wpuf_load_user_card_button_follow', 10, 1 );


/**
 *
 * Load the followers count
 * 
 * @param  object WP_User $user
 *
 * @since 1.0.0
 * 
 */
function streamtube_wpuf_load_user_card_followers_count( $user ){

	if( function_exists( 'wpuf_get_following_count' ) ):

		?>
	    <div class="member-info__item flex-fill">
	        <div class="member-info__item__count">
	        	<?php echo number_format_i18n( wpuf_get_following_count( $user->ID ) ); ?>
	        </div>
	        <div class="member-info__item__label">
	        	<?php esc_html_e( 'followers', 'streamtube' ); ?>
	        </div>
	    </div>
		<?php

	endif;
}
add_action( 'streamtube/core/user/card/info/item', 'streamtube_wpuf_load_user_card_followers_count', 10, 1 );