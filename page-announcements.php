<?php get_header(); ?>

<main id="wsuwp-main">
	<header class="page-header">
		<h1>Announcements</h1>
	</header>
	<section class="row single gutter pad-top news-river">
		<div class="column one">
			<div class="deck deck--list">
				<?php
				$announcements = wp_remote_get( 'https://news.wsu.edu/wp-json/insider/v1/announcements' );
				if ( is_wp_error( $announcements ) ) {
					$announcements = array();
				} else {
					$announcements = json_decode( wp_remote_retrieve_body( $announcements ) );
				}

				foreach ( $announcements as $announcement ) {
					$excerpt = strip_tags( $announcement->excerpt );
					$excerpt = '<p>' . substr( $excerpt, 0, 280 ) . '...</p>';

					?>
					<article class="card card-news">
						<header class="card-title">
							<a href="<?php echo esc_url( $announcement->url ); ?>"><?php echo esc_html( $announcement->title ); ?></a>
						</header>
						<div class="card-date"><?php echo esc_html( $announcement->date ); ?></div>
						<div class="card-excerpt"><?php echo wp_kses_post( $excerpt ); ?></div>
					</article>
					<?php
				}
				?>
			</div>
		</div>
	</section>

	<?php get_template_part( 'parts/footers' ); ?>

</main><!--/#page-->

<?php get_footer();
