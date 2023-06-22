<?php
/**
 *
 * The template for displaying single blog post
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

$has_sidebar = is_active_sidebar( 'sidebar-1' );

$args = streamtube_get_blog_template_settings();

get_header();
?>

    <div class="page-main pt-4">

        <div class="container">

    		<div class="row">

                <?php printf(
                    '<div class="col-xl-%1$s col-lg-%1$s col-md-12 col-12">',
                    $has_sidebar ? '8' : '12'
                );?>

				    <?php if( have_posts() ): the_post();?>

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
                    
                	<div class="shadow-sm bg-white mb-4">

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
                            'template-parts/content/content', 
                            get_post_format(),
                            $args
                        )?>

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

                    <?php if( get_option( 'blog_author_box' ) ): ?>
                        <div class="shadow-sm bg-white mb-4">

                            <?php get_template_part( 'template-parts/author', 'box', array(
                                'author_avatar' =>  'on'
                            ) );?>

                        </div>
                    <?php endif;?>                        

                    <?php
                    /**
                     *
                     * Fires before content wrapper
                     *
                     * @since  1.0.0
                     * 
                     */
                    do_action( 'streamtube/single/content/wrap/after' );
                        ?>                        

                	<?php endif;?>

                    <?php get_sidebar( 'content-bottom' )?>

                    <?php comments_template(); ?>

            	</div>

                <?php if( $has_sidebar ): ?>
                    <div class="col-xl-4 col-lg-4 col-md-12 col-12">
                        <?php get_sidebar();?>
                    </div>
                <?php endif;?>

            </div>

    	</div>

    </div>

<?php 
get_footer();