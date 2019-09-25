<?php

get_header();
global $is_top_feature, $is_river, $is_good_to_know;

$page = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
?>
	<main id="wsuwp-main" class="spine-category-index">

		<header class="page-header">
			<h1><?php echo esc_html( single_cat_title( '', false ) ); ?></h1>
			<?php if ( category_description() ) { ?>
			<div class="description"><?php echo category_description(); ?></div>
			<?php } ?>
		</header>
		<?php

		// Explicitly set globals - they're compared strictly in parts/card-content.php
		$is_top_feature = false;
		$is_good_to_know = false;
		$is_river = false;

		$skip_post_id = array();

		// Display the category's most recent featured story on the category's first page.
		if ( have_posts() && 1 === $page ) {
			?>
			<section class="row single gutter pad-top news-features">

				<div class="column one">

					<div class="deck deck--featured">

					<?php while ( have_posts() ) : the_post(); ?>

						<?php
						$skip_post_id[] = get_post()->ID;
						$is_top_feature = true;
						get_template_part( 'parts/card-content' );
						?>

					<?php endwhile; ?>

					</div>

				</div><!--/column-->

			</section>
			<?php
		}

		$is_top_feature = false;

		$archive_query = new WP_Query( array(
			'posts_per_page' => 20, // Start with a high number until pagination.
			'category_name'  => get_query_var( 'category_name' ),
			'post__not_in'   => $skip_post_id,
			'paged'          => absint( $page ),
			'post_type'      => array( 'post', 'wsu_announcement' ),
		) );

		if ( $archive_query->have_posts() ) {
			$output_post_count = 0;

			while ( $archive_query->have_posts() ) {
				$archive_query->the_post();

				// 4 posts are output in the secondary section.
				if ( 0 === $output_post_count ) {
					$is_river = false;
					?>
					<section class="row single gutter pad-top bottom-divider">
						<div class="column one">
							<div class="deck">
					<?php
				}

				// Remaining posts are output as a river.
				if ( 4 === $output_post_count ) {
					$is_river = true;
					?>
							</div>
						</div>
					</section>
					<section class="row single gutter pad-top news-river">
						<div class="column one">
							<header>
								<h2>More <?php echo esc_html( single_cat_title( '', false ) ); ?></h2>
							</header>
							<div class="deck deck--list">
					<?php
				}

				get_template_part( 'parts/card-content' );

				$output_post_count++;
			}
			?>
					</div>
				</div>
			</section>
			<?php
		}

		$args = array(
			'base'               => str_replace( 99164, '%#%', esc_url( get_pagenum_link( 99164 ) ) ),
			'format'             => 'page/%#%',
			'total'              => $archive_query->max_num_pages, // Provide the number of pages this query expects to fill.
			'type'               => 'array',
			'current'            => max( 1, get_query_var( 'paged' ) ), // Provide either 1 or the page number we're on.
			'prev_text'          => 'Previous <span class="screen-reader-text">page</span>',
			'next_text'          => 'Next <span class="screen-reader-text">page</span>',
			'before_page_number' => '<span class="screen-reader-text">Page </span>',
		);
		?>
		<footer class="main-footer archive-footer">
			<section class="row side-right pager prevnext gutter">
				<div class="column one">
					<?php
					$paginate_links = paginate_links( $args );

					if ( ! empty( $paginate_links ) ) {
						?>
						<nav role="navigation" aria-label="Pagination navigation">
							<ul>
								<?php
								foreach ( $paginate_links as $paginate_link ) {
									echo '<li>' . $paginate_link . '</li>'; // @codingStandardsIgnoreLine
								}
								?>
							</ul>
						</nav>
						<?php
					}
					?>
				</div>
				<div class="column two">
					<!-- intentionally empty -->
				</div>
			</section><!--pager-->
		</footer>

		<?php get_template_part( 'parts/footers' ); ?>

	</main>
<?php

get_footer();
