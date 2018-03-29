<?php while ( have_posts() ) : the_post(); ?>

<section class="row single gutter pad-ends primary-categories">

	<div class="column one">

		<?php
		$default_category = absint( get_option( 'default_category' ) );
		$post_categories = wp_get_post_categories( get_the_ID() );

		// Output a category list if categories are assigned and if the default category is not the only category.
		if ( $post_categories && ! ( 1 === count( $post_categories ) && $default_category !== $post_categories[0] ) ) {
			?>
			<ul>
			<?php
			foreach ( $post_categories as $category ) {
				// Don't show the default category (Uncategorized) on features.
				if ( $default_category === $category ) {
					continue;
				}
				?>
				<li>
					<a href="<?php echo esc_url( get_category_link( $category ) ); ?>"><?php echo esc_html( get_cat_name( $category ) ); ?></a>
				</li>
				<?php
			}
			?>
			</ul>
			<?php
		}
		?>

	</div>

</section>

<section class="row single gutter pad-top article-container">

	<div class="column one">

		<?php get_template_part( 'articles/post', get_post_type() ) ?>

	</div><!--/column-->

</section>

<?php endwhile; ?>
