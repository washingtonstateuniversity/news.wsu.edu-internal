<?php

get_header();
global $is_top_feature;
?>
	<main id="wsuwp-main" class="spine-category-index">

		<header class="page-header">
			<h1><?php echo esc_html( single_cat_title( '', false ) ); ?></h1>
		</header>
		<?php

		$skip_post_id = array();
		if ( have_posts() ) {
			?>
			<section class="row single gutter pad-top news-features">

				<div class="column one">

					<div class="content-syndicate-wrapper">

					<?php while ( have_posts() ) : the_post(); ?>

						<?php
						$skip_post_id[] = get_post()->ID;
						$is_top_feature = true;
						get_template_part( 'parts/category-post-content' );
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
			'category_name' => get_query_var( 'category_name' ),
			'post__not_in' => $skip_post_id,
		) );

		if ( $archive_query->have_posts() ) {
			$output_post_count = 0;

			while ( $archive_query->have_posts() ) {
				$archive_query->the_post();

				// 4 posts are output in the secondary section.
				if ( 0 === $output_post_count ) {
					?>
					<section class="row single gutter pad-top top-four">
						<div class="column one">
							<div class="content-syndicate-wrapper">
					<?php
				}

				// Remaining posts are output as a river.
				if ( 4 === $output_post_count ) {
					?>
							</div>
						</div>
					</section>
					<section class="row single gutter pad-top news-river">
						<div class="column one">
							<div class="content-syndicate-wrapper">
					<?php
				}

				get_template_part( 'parts/category-post-content' );

				$output_post_count++;
			}
			?>
					</div>
				</div>
			</section>
			<?php
		}

		/* @type WP_Query $wp_query */
		global $wp_query;

		$big = 99164;
		$args = array(
			'base'         => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
			'format'       => 'page/%#%',
			'total'        => $wp_query->max_num_pages, // Provide the number of pages this query expects to fill.
			'current'      => max( 1, get_query_var( 'paged' ) ), // Provide either 1 or the page number we're on.
		);
		?>
		<footer class="main-footer archive-footer">
			<section class="row side-right pager prevnext gutter">
				<div class="column one">
					<?php echo paginate_links( $args ); ?>
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
