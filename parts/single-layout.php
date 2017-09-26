<?php while ( have_posts() ) : the_post(); ?>

<section class="row single gutter pad-ends primary-categories">

	<div class="column one">

		<?php
		$post_categories = wp_get_post_categories( get_the_ID() );

		if ( $post_categories ) {
			?>
			<ul>
			<?php
			foreach ( $post_categories as $category ) {
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
