<?php
get_header();

?>

<main>
	<?php

	foreach ( WSU\News\Internal\Page_Curation\get_sections() as $section_slug => $front_section ) {
		$is_top_feature = false;
		$is_river = false;
		$section_query = new WP_Query( array(
			'posts_per_page' => (int) $front_section['count'],
			'category_name' => $section_slug,
		) );

		if ( $section_query->have_posts() ) {
			?>
			<section class="row single gutter pad-top cat-sec">
				<div class="column one">
					<header>
						<h2><?php echo esc_html( $front_section['name'] ); ?></h2>
					</header>
					<div class="deck">
					<?php
						while ( $section_query->have_posts() ) {
							$section_query->the_post();

							get_template_part( 'parts/card-content' );

						}
					?>
					</div>
				</div>
			</section>
			<?php
		}
		wp_reset_postdata();
	}

	?>
</main>

<?php
get_footer();
