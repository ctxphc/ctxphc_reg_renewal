<?php
/**
 * The template for displaying code related
 * error messages instead of breaking
 *
 * @since CTXPHC Beach Holiday 3.1
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">

			<header class="page-header">
				<h1 class="page-title"><?php _e( 'There was a problem...', 'ctxphcbeachholiday' );
				?></h1>
			</header>

			<div class="page-wrapper">
				<div class="page-content">
					<h2><?php _e( 'This is somewhat embarrassing, isn’t it?', 'ctxphcbeachholiday' ); ?></h2>
					<p><?php _e( 'It looks like a problem has temporarily broken the website.',
							'ctxphcbeachholiday'
						); ?></p>
					<p><?php _e( 'A message has been sent to our support team and they will fixing this as quickly as soon as they return from the beach....',
							'ctxphcbeachholiday'
						); ?></p>
					<p><?php _e( 'Feel free to try this again, if you feel is could be something you did to cause this.',
							'ctxphcbeachholiday'
						); ?></p>
				</div><!-- .page-content -->
			</div><!-- .page-wrapper -->

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_footer(); ?>