<?php
/**
 * The videojs template file
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

if( ! defined( 'STREAMTUBE_IS_PLAYER' ) ){
	return;
}

wp_enqueue_style( 'streamtube-player' );

$is_single_video 	= is_singular( 'video' );
$is_embed 			= is_embed();

extract( $args );

$ratio = ! empty( $ratio ) ? 'ratio ratio-' . sanitize_html_class( $ratio ) : 'ratio-default';

?>
<?php printf(
	'<div class="player-wrapper %s" data-player-wrap-id="%s">',
	$is_single_video && ! $is_embed && get_option( 'floating_player' ) ? 'jsappear' : 'no-jsappear',
	$post_id ? esc_attr( $post_id ) : $args['uniqueid']
)?>
	<?php printf(
		'<div class="player-wrapper-ratio %s">',
		$ratio
	);?>

		<?php if( ! empty( $source ) ): ?>

			<div class="player-container">
		
			<?php if( $is_single_video && ! $is_embed ): ?>

				<div class="player-header p-3">
					<div class="d-flex align-items-center">

						<?php the_title( '<h5 class="post-title post-title-md h5">', '</h5>' );?>

						<div class="ms-auto">
							<?php printf(
								'<button type="button" class="btn-close outline-none shadow-none" aria-label="%s"></button>',
								esc_html__( 'Close', 'streamtube' )
							);?>
						</div>
					</div>
				</div>	

			<?php endif;?>

		    <?php printf( 
			    	'<div class="player-embed overflow-hidden bg-black %s">', 
			    	$ratio
		    	); ?>
		    	<?php

		    		$techOrder = array();

		    		$src = $type = $ads_tag_url = '';

		    		$skin = get_option( 'player_skin', 'forest' );

		    		if( $skin == 'custom' ){
		    			$skin = get_option( 'player_skin_custom' );
		    		}

		    		if( wp_attachment_is( 'video', $source  ) || wp_attachment_is( 'audio', $source  ) ){
		    			$is_attachment = true;

		    			$src = wp_get_attachment_url( $source );

		    			if( strpos( $src , '.m3u8' ) !== false ){
		    				$type = 'application/x-mpegURL';
		    			}
		    			else{
		    				$type = get_post_mime_type( $source );
		    			}
		    		}

		    		if( false != $maybe_youtube_url = streamtube_get_youtube_url( $source ) ){
		    			$src 			= $maybe_youtube_url;
		    			$type 			= 'video/youtube';
		    			$techOrder[] 	= 'youtube';
		    		}

		    		if( streamtube_is_hls_url( $source ) ){
		    			$src 			= $source;
		    			$type 			= 'application/x-mpegURL';
		    		}

		    		if( empty( $src ) ){
		    			$maybe_ext_url = pathinfo( $source );

		    			if( is_array( $maybe_ext_url ) && array_key_exists( 'extension', $maybe_ext_url ) ){
		    				if( in_array( strtolower( $maybe_ext_url['extension'] ), wp_get_video_extensions() ) ){
		    					$src = $source;
		    					$type = 'video/' . strtolower( $maybe_ext_url['extension'] );
		    				}
		    			}
		    		}

			    	if( ! empty( $src ) ):

			    		if( in_array( $skin, array( 'city', 'forest', 'fantasy', 'sea' ) ) ){
			    			wp_enqueue_style( 'videojs-theme-' . $skin );	
			    		}

			    		if( $type == 'application/x-mpegURL' ){
		    				wp_enqueue_script( 'videojs-contrib-quality-levels' );
		    				wp_enqueue_script( 'videojs-hls-quality-selector' );
			    		}

			    		if( $type == 'video/youtube' ){
			    			wp_enqueue_script( 'videojs-youtube' );
			    		}

			    		$setup = array(
			    			'mediaid'			=>	$post_id ? $post_id : $args['uniqueid'],
			    			'classes'			=>	array( 'position-absolute', 'videojs-streamtube' ),
			    			'controls'			=>	$controls,
			    			'preload'			=>	'auto',
			    			'poster'			=>	$poster ? $poster : '',
			    			'inactivityTimeout'	=>	0,
			    			'sources'			=>	array(
			    				array(
			    					'src'		=>	$src,
			    					'type'		=>	$type
			    				)
			    			),
			    			'plugins'			=>	array()
			    		);

			    		$setup = array_merge( $setup, compact( 'controls', 'muted', 'autoplay', 'loop' ) );

			    		if( $type == 'application/x-mpegURL' ){
			    			$setup['plugins']['playerhlsQualitySelector'] = array(
			    				'displayCurrentQuality'	=>	true
			    			);
			    		}

			    		if( ! empty( $skin ) ){
			    			$setup['classes'][] = 'vjs-theme-' . sanitize_html_class( $skin );	
			    		}
			    		
			    		if( $share && $post_id ){
			    			$setup['plugins']['playerShareBox'] = array(
		    					'id'			=>	'share_box_' . $post_id,
		    					'url'			=>	streamtube_get_share_permalink( $post_id ),
		    					'embed_url'		=>	get_post_embed_url( $post_id ),
		    					'embed_width'	=>	560,
		    					'embed_height'	=>	315,
		    					'label_url'		=>	esc_html__( 'Link', 'streamtube' ),
		    					'label_iframe'	=>	esc_html__( 'Iframe', 'streamtube' )
			    			);
			    		}

			    		if( $techOrder ){
			    			$setup['techOrder'] = $techOrder;
			    		}

			    		if( ! $share ){
			    			unset( $setup['plugins']['playerShareBox'] );
			    		}

			    		if( ! $logo ){
			    			unset( $setup['plugins']['playerLogo'] );
			    		}

			    		/**
			    		 *
			    		 * filter the player setup
			    		 *
			    		 * @param  array $setup
			    		 *
			    		 * @since  1.0.0
			    		 * 
			    		 */
			    		$setup = apply_filters( 'streamtube/player/file/setup', $setup, $source );

			    		if( $setup['plugins'] ){
			    			$setup['jplugins'] = $setup['plugins'];
			    			unset( $setup['plugins'] );
			    		}    	

			    		$player = sprintf(
			    			'<video-js data-player-id="%1$s" id="player_%1$s" class="%2$s" data-setup="%3$s"></video-js>',
			    			$post_id ? $post_id : $args['uniqueid'],
			    			esc_attr( join(' ', $setup['classes'] ) ),
			    			esc_attr( json_encode( $setup ) )
			    		);

			    		/**
			    		 * Check if file is being encoded.
			    		 */
			    		if( function_exists( 'wp_video_encoder' ) && "" !== ( $queue = wpve_is_attachment_queue( $source ) ) ){
			    			
			    			if( $queue && in_array( $queue['status'], array( 'waiting', 'encoding' ) ) ){

			    				$post_title = get_the_title( $source );

			    				if( $post_id ){
			    					$post_title = get_the_title( $post_id );
			    				}

				    			do_action( "wp_video_encoder_running", $queue );
			    						    				
			    				ob_start();
			    				?>
			    				<div class="progress-wrap">
			    					<div class="w-50 top-50 start-50 translate-middle position-absolute">
			    						<h3 class="text-white h5 mb-4 fw-normal d-none d-sm-block">
			    							<?php printf(
			    								esc_html__( '%s is being encoded, please wait a minute.', 'streamtube' ),
			    								'<strong class="text-info">'. $post_title .'</strong>'
			    							);?>
			    						</h3>
			    						<?php wp_video_encoder()->get()->post->get_encode_status( $source );?>
			    					</div>
			    				</div>
			    				<?php
			    				$player = ob_get_clean();
			    			}
			    		}

			    		wp_enqueue_style( 'videojs' );	
			    		wp_enqueue_script( 'player' );

			    		/**
			    		 *
			    		 * Fires before videojs loaded
			    		 *
			    		 * @since 1.0.9
			    		 * 
			    		 */
			    		do_action( 'streamtube/player/videojs/loaded', $player, $setup, $source );

			    		/**
			    		 *
			    		 * filter the player output
			    		 *
			    		 * @param  HTML $player
			    		 * @param  array $setup
			    		 * @param string $source
			    		 *
			    		 * @since  1.0.0
			    		 * 
			    		 */
			    		echo apply_filters( 'streamtube/player/file/output', $player, $setup, $source );

			    	else:
				    	$oembed_html = wp_oembed_get( $source  );

				    	if( ! $oembed_html ){
				    		$oembed_html = do_shortcode( $source  );
				    	}

			    		/**
			    		 *
			    		 * filter the oembed_html output
			    		 *
			    		 * @param  HTML $oembed_html
			    		 * @param  array $source
			    		 *
			    		 * @since  1.0.0
			    		 * 
			    		 */
			    		$oembed_html = apply_filters( 'streamtube/player/embed/output', $oembed_html, $source );

				    	printf(
				    		'<div class="%s">%s</div>',
				    		$ratio != 'ratio-default' ? 'embed-responsive' : 'embed-custom-responsive',
				    		$oembed_html
				    	);

				    endif;

		    		/**
		    		 *
		    		 * Fires after player loaded
		    		 *
		    		 * @since 1.0.9
		    		 * 
		    		 */
		    		do_action( 'streamtube/player/loaded', $source );

			    ?>
		    </div>

		    </div><!--.player-container-->
		<?php
			else:
				printf(
					'<div class="video-not-found"><h3 class="position-absolute top-50 start-50 translate-middle text-white">%s</h3></div>',
					esc_html__( 'Video unavailable', 'streamtube' )
				);	
			endif;// end check if source is empty
		?>
		
	</div><!--.player-wrapper-ratio-->
</div><!--.player-wrapper-->