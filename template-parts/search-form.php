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

$filter = get_option( 'search_filter', 'post_types' );

/**
 *
 * Filter search filter
 */
$filter = apply_filters( 'streamtube/searchform/search_filter', $filter );
?>
<form action="<?php echo esc_url( home_url( '/' ) ); ?>" class="search-form d-flex" method="get">
	<button class="toggle-search btn btn-sm border-0 shadow-none d-block d-lg-none p-2" type="button">
		<span class="icon-left-open"></span>
	</button>	
	<div class="input-group-wrap position-relative w-100">

		<?php 
		if( $filter ){
			get_template_part( 'template-parts/search-form/dropdown-' . sanitize_file_name( $filter ) );	
		}
		?>

		<?php printf(
			'<input id="search-input" class="form-control shadow-none ps-4 %s" autocomplete="off" aria-label="%s" name="s" placeholder="%s" type="text" value="%s">',
			get_option( 'search_autocomplete', 'on' ) ? 'autocomplete' : '',
			esc_attr__( 'Search', 'streamtube' ),
			esc_attr__( 'Search here...', 'streamtube' ),
			esc_attr( streamtube_get_search_query_value() )
		);?>

		<input type="hidden" name="search">

		<?php 
		if( $filter ){
			printf(
				'<input type="hidden" name="search_filter" value="%s">',
				esc_attr( $filter )
			);
		}?>

		<?php if( $filter && $filter == 'taxonomy' && "" != $taxonomy = get_option( 'search_taxonomy', 'categories' ) ){
			printf(
				'<input type="hidden" name="taxonomy" value="%s">',
				esc_attr( $taxonomy )
			);			
		}?>

		<?php wp_nonce_field( '_wpnonce', '_wpnonce', false, true );?>

		<button class="btn btn-outline-secondary px-4 btn-main shadow-none" type="submit">
			<span class="btn__icon icon-search"></span>
		</button>
	</div>
</form>