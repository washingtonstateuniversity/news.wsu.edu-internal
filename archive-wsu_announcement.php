<?php

get_header();
global $is_top_feature, $is_river, $is_good_to_know, $is_read_more;

$is_top_feature = false;
$is_river = false;
$is_good_to_know = false;
$is_read_more = false;
?>
	<main id="wsuwp-main" class="spine-category-index">

		<header class="page-header">
			<h1><?php echo esc_html( 'Announcements Archive' ); ?></h1>
		</header>

		<section class="row single gutter pad-top news-river">

			<div class="column one">

				<div class="deck deck--list">

				<?php

				if ( have_posts() ) {
					while ( have_posts() ) {
						the_post();

						get_template_part( 'parts/card-content' );
					}
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
				</div>
			</div>
		</section>

		<footer class="main-footer archive-footer">
			<section class="row side-right pager prevnext gutter">
				<div class="column one">
					<?php echo paginate_links( $args ); // @codingStandardsIgnoreLine ?>
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
