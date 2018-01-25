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
			<h1>Announcements</h1>
			<div class="description">
				<p><?php echo esc_html( get_the_date() ); ?></p>
			</div>
		</header>

		<section class="row single gutter news-river">

			<div class="column one">

				<div class="deck deck--list">

					<?php

					if ( have_posts() ) {
						while ( have_posts() ) {
							the_post();

							get_template_part( 'parts/card-content' );
						}
					} else {
						$previous_query = WSU\News\Internal\Announcements\get_previous_day_archive_posts();
						if ( $previous_query && $previous_query->have_posts() ) {
							while ( $previous_query->have_posts() ) {
								$previous_query->the_post();

								get_template_part( 'parts/card-content' );
							}
						}
						wp_reset_postdata();
					}

					?>

				</div>
			</div>
		</section>

		<?php
		$pagination = WSU\News\Internal\Announcements\get_date_archive_pagination_urls( get_the_date() );
		?>

		<footer class="main-footer archive-footer">
			<section class="row single pager prevnext gutter bottom-divider">
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
			<section class="row side-right pad-top gutter">
				<div class="column one">
					<h2>Submit an announcement</h2>
					<p>Announcements is a free service available to the WSU community for posting notices of general University interest. It’s a way to share everything from upcoming activities and notable developments to employee awards, special events and much more. For questions or help with the submission form contact Brenda Campbell at <a href="mailto:bcampbell@wsu.edu">bcampbell@wsu.edu</a>.</p>
					<a class="card-cta button" href="https://insider.wsu.edu/submit-announcement/">Submit Announcement</a>
				</div>
				<div class="column two"></div>
			</section>
		</footer>

		<?php get_template_part( 'parts/footers' ); ?>

	</main>
<?php

get_footer();
