<?php

get_header();

global $is_top_feature, $is_river, $is_good_to_know;

// Explicitly set globals - they're compared strictly in parts/card-content.php
$is_top_feature = false;
$is_good_to_know = false;
$is_river = true;

$main_class = '';
$title = '';
$description = '';

if ( is_home() ) {
	$main_class = 'spine-main-index';
	$title = single_post_title( '', false );
} elseif ( is_author() ) {
	$main_class = 'spine-author-index';
	$title = get_the_author();
} elseif ( is_tag() ) {
	$main_class = 'spine-tag-index';
	$title = single_tag_title( '', false );
	$description = term_description();
} elseif ( is_tax() ) {
	$main_class = 'spine-tax-index';
	$title = single_term_title( '', false );
	$description = term_description();
} elseif ( is_search() ) {
	$main_class = 'spine-search-index';
	$title = 'Search Results';
}
?>
	<main id="wsuwp-main" class="<?php echo esc_attr( $main_class ); ?>">

		<header class="page-header">

			<h1><?php echo esc_html( $title ); ?></h1>

			<?php if ( $description ) { ?>
			<div class="description"><?php echo esc_html( $description ); ?></div>
			<?php } ?>

		</header>

		<?php if ( have_posts() ) { ?>
		<section class="row single gutter pad-top news-river">
			<div class="column one">
				<div class="deck deck--list">
					<?php while ( have_posts() ) : the_post(); ?>
						<?php get_template_part( 'parts/card-content' ); ?>
					<?php endwhile; ?>
				</div>
			</div>
		</section>
		<?php } ?>

		<footer class="main-footer archive-footer">
			<section class="row side-right pager prevnext gutter">
				<div class="column one">
					<?php
					global $wp_query;

					$args = array(
						'base'               => str_replace( 99164, '%#%', esc_url( get_pagenum_link( 99164 ) ) ),
						'format'             => 'page/%#%',
						'total'              => $wp_query->max_num_pages, // Provide the number of pages this query expects to fill.
						'type'               => 'array',
						'current'            => max( 1, get_query_var( 'paged' ) ), // Provide either 1 or the page number we're on.
						'prev_text'          => 'Previous <span class="screen-reader-text">page</span>',
						'next_text'          => 'Next <span class="screen-reader-text">page</span>',
						'before_page_number' => '<span class="screen-reader-text">Page </span>',
					);

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
