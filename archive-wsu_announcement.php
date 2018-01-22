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
			<h1><?php echo esc_html( ' Announcements ' . get_the_date() ); ?></h1>
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

					?>

				</div>
			</div>
		</section>

		<?php
		$pagination = WSU\News\Internal\Announcements\get_date_archive_pagination_urls( get_the_date() );
		?>

		<footer class="main-footer archive-footer">
			<section class="row single pager prevnext gutter">
				<div class="column one">
					<div class="pagination">
						<div class="pagination-previous">
							<?php if ( $pagination['previous'] ) : ?><a href="<?php echo esc_url( $pagination['previous'] ); ?>">Previous day</a><?php endif; ?>
						</div>
						<div class="pagination-next">
							<?php if ( $pagination['next'] ) : ?><a href="<?php echo esc_url( $pagination['next'] ); ?>">Next day</a><?php endif; ?>
						</div>
					</div>
				</div>
			</section><!--pager-->
		</footer>

		<?php get_template_part( 'parts/footers' ); ?>

	</main>
<?php

get_footer();
