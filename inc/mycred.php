<?php
/**
 *
 * myCred plugin compatiblity file
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
 * Load the followers count
 * 
 * @param  object WP_User $user
 *
 * @since 1.0.0
 * 
 */
function streamtube_mycred_load_user_card_points_count( $user ){

	if( function_exists( 'mycred_display_users_balance' ) && apply_filters( 'streamtube/userlist/balance', false ) === true ):

		?>
	    <div class="member-info__item flex-fill">
	        <div class="member-info__item__count">
	        	<?php echo mycred_display_users_balance( $user->ID ); ?>
	        </div>
	        <div class="member-info__item__label">
	        	<?php esc_html_e( 'points', 'streamtube' ); ?>
	        </div>
	    </div>
		<?php

	endif;
}
add_action( 'streamtube/core/user/card/info/item', 'streamtube_mycred_load_user_card_points_count', 20, 1 );