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
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <div class="post-body single-body">

        <div class="post-main">
        	<?php
        	/**
        	 *
        	 * Fires before player
        	 *
        	 * @since  1.0.0
        	 * 
        	 */
        	do_action( 'streamtube/player/before' );
        	?>

            <?php get_template_part( 'template-parts/player' );?>

            <?php
            if( class_exists( 'Streamtube_Core_Widget_Playlist_Content' ) ){
                the_widget( 'Streamtube_Core_Widget_Playlist_Content', array(), array(
                    'before_widget' => '<div class="widget widget-content-bottom %1$s">',
                    'after_widget'  => '</div>'
                ) );
            }
            ?>

        	<?php
        	/**
        	 *
        	 * Fires after player
        	 *
        	 * @since  1.0.0
        	 * 
        	 */
        	do_action( 'streamtube/player/after' );
        	?>

        </div>

        <div class="post-bottom shadow-sm">

            <div class="post-bottom__meta border-bottom p-4">

                <div class="d-lg-flex align-items-start gap-4">

                    <div class="d-flex flex-column">
                        <?php the_title(
                            '<h1 class="post-title post-title-xl text-body">',
                            '</h1>'
                        )?>
                        <div class="post-meta">
                            <div class="post-meta__items">
                                <?php
                                /**
                                 * @since 1.0.8
                                 */
                                do_action( 'streamtube/single/video/meta' );
                                ?>       
                            </div>
                        </div>
                    </div>

                    <?php get_template_part( 'template-parts/video-small-controls' );?>
                </div>

                <?php
                /**
                 * @since 1.0.8
                 */
                do_action( 'streamtube/single/video/single/meta/before' );
                ?>                

                <?php get_template_part( 'template-parts/post-single', 'meta', $args );?>

            </div>

            <?php if( get_option( 'author_box', 'on' ) ): ?>
                <?php get_template_part( 'template-parts/author', 'box', $args );?>
            <?php endif;?>

            <?php get_template_part( 'template-parts/post', 'content', $args );?>
          
        </div>

    </div>

</article>