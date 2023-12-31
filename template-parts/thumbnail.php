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

if( ! isset( $args ) ){
    $args = array();
}

$ratio = '16x9';

$args = wp_parse_args( $args, array(
    'thumbnail_size'    =>  'streamtube-image-medium',
    'thumbnail_ratio'   =>  '16x9',
    'overlay'           =>  ''
) );

if( $args['thumbnail_ratio'] == '9x16' ){
    $args['thumbnail_size'] = 'full';
}

?>
<?php 
if( $args['thumbnail_ratio'] == 'default' ){
    printf(
        '<div class="post-thumbnail ratio-%s rounded overflow-hidden bg-dark">',
        esc_attr( $args['thumbnail_ratio'] )
    );
}
else{
    printf(
        '<div class="post-thumbnail ratio ratio-%s rounded overflow-hidden bg-dark">',
        esc_attr( $args['thumbnail_ratio'] )
    );    
}
?>
    <?php 
    /**
     *
     * Fires before post thumbnail
     *
     * @since  1.0.0
     * 
     */
    do_action( 'streamtube/post/thumbnail/before' );
    ?>    

    <?php if( has_post_thumbnail() ):

        the_post_thumbnail( $args['thumbnail_size'], array(
            'class' =>  'img-fluid'
        ) );

    endif;?>

    <?php 
    /**
     *
     * Fires after post thumbnail
     *
     * @since  1.0.0
     * 
     */
    do_action( 'streamtube/post/thumbnail/after' );
    ?>
    <?php if( $args['show_post_like'] && is_user_logged_in() ){
        get_template_part( 'template-parts/post-like' );
    }?>
    <?php if( get_post_type() == 'video' || get_post_format() == 'video' ):?>
   
        <?php get_template_part( 'template-parts/play-icon' ); ?>

        <?php get_template_part( 'template-parts/video-length' ); ?>

    <?php endif;?>

    <?php if( $args['overlay'] ): ?>
        <div class="bg-overlay"></div>
    <?php endif; ?>

</div><!--.post-thumbnail-->