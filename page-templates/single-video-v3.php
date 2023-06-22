<?php
/**
 * Template Name: Single Video V3
 *
 * Template Post Type: Video
 *
 * The template for displaying single video
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

get_header();
?>

	<?php 
	if( have_posts() ): the_post(); 
		
	$show_comments = ! comments_open() && ! get_comments_number() ? false : true;

	/**
	 *
	 * Filter show_comments
	 *
	 * @since 2.1.7
	 * 
	 */
	$show_comments = apply_filters( 'streamtube/single/show_comments', $show_comments );
	?>

	<div class="page-main">

		<?php printf(
			'<div class="single-main %s">',
			$show_comments ? 'overflow-hidden' : 'pt-4'
		)?>

			<?php printf(
				'<div class="container%s">',
				$show_comments ? '-fluid' : ''
			)?>

		        <div class="row">

		            <?php printf(
		            	'<div class="single-video__body pt-4 col-12 %s">',
		            	$show_comments ? 'col-lg-8 col-xl-9 col-xxl-9 no-scroll' : 'h-auto'
		            );?>

		            	<?php get_template_part( 'template-parts/alert/content', get_post_status() );?>

	                	<?php
	                	/**
	                	 *
	                	 * Fires before content wrapper
	                	 *
	                	 * @since  1.0.0
	                	 * 
	                	 */
	                	do_action( 'streamtube/single/content/wrap/before' );
	                	?>		            	

		                <div class="single-video__body__main bg-white rounded mb-4">

		                	<?php
		                	/**
		                	 *
		                	 * Fires before main content
		                	 *
		                	 * @since  1.0.0
		                	 * 
		                	 */
		                	do_action( 'streamtube/single/content/before' );
		                	?>

		                	<?php get_template_part( 
		                		'template-parts/content/content-single', 
		                		'video-v3',
                                apply_filters( 'streamtube/single/video/part_args', array(
                                    'author_avatar' =>  'on'
                                ) )
		                	); ?>

		                	<?php
		                	/**
		                	 *
		                	 * Fires after main content
		                	 *
		                	 * @since  1.0.0
		                	 * 
		                	 */
		                	do_action( 'streamtube/single/content/after' );
		                	?>
		                </div>

	                	<?php
	                	/**
	                	 *
	                	 * Fires after content wrapper
	                	 *
	                	 * @since  1.0.0
	                	 * 
	                	 */
	                	do_action( 'streamtube/single/content/wrap/after' );
	                	?>			                

		                <?php if( is_active_sidebar( 'content-bottom' ) ): ?>

		                	<?php add_filter( 'sidebars_widgets', 'streamtube_remove_comments_template_widget', 10, 1 ); ?>

			                <div class="single-video__body__bottom mb-4">
			                	<?php get_sidebar( 'content-bottom' )?>
			                </div>

		            	<?php endif;?>

		            </div>

		            <?php if( $show_comments ): ?>

			            <div class="col-12 col-lg-4 col-xl-3 col-xxl-3 comments--fixed comments-template-v3">
			                <?php comments_template();?>
			            </div>

		        	<?php endif;?>

		        </div>

			</div>

		</div>

	</div>

	<?php endif;?>

<?php 
get_footer();