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
?>
<div class="alert alert-info p-2 px-3">
	<?php
		printf(
			esc_html__( 'This %s is privated.', 'streamtube' ),
			get_post_type()
		);
	?>
</div>