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
<?php if( has_term( null, $args['taxonomy'], get_the_ID()  ) ): ?>
    <div class="post-categories post-terms d-inline-block">
        <span class="btn__icon icon-folder-open-empty"></span>
        <?php the_terms( get_the_ID(), $args['taxonomy'], null, '<span class="sep mx-2">/</span>' );?>
    </div>
<?php endif;