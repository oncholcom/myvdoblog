<?php
/**
 *
 * The template for displaying search form
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
<div class="search-form-wrap">
	<form role="search" method="get" id="search-form" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<div class="input-group-wrap position-relative w-100">
			<label class="screen-reader-text" for="s"><?php esc_html_e( 'Search for:', 'streamtube' )?></label>
			<?php printf(
				'<input type="text" name="s" id="s" class="form-control shadow-none" value="%s" placeholder="%s">',
				get_search_query(),
				esc_attr__( 'Search here...', 'streamtube' )

			);?>
	
			<button class="btn border-0 shadow-none btn-main text-secondary" type="submit">
			    <span class="icon-search"></span>
			</button>
		</div>
	</form>
</div>