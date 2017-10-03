<?php
global $is_top_feature;
?>
<article class="content-syndicate-item">
	<span class="content-item-categories"><?php
		$category_html = '';
		foreach ( get_the_category() as $category ) {
			$category_html .= ' <a href="' . esc_url( get_category_link( $category->cat_ID ) ) . '">' . esc_html( $category->cat_name ) . '</a>,';
		}
		$category_html = trim( $category_html );
		$category_html = rtrim( $category_html, ',' );
		echo $category_html;
		?></span>

	<?php if( spine_has_featured_image() ) { ?>
	<figure class="content-item-image">
		<a href="<?php the_permalink(); ?>"><?php if ( $is_top_feature ) { the_post_thumbnail( 'spine-large_size' ); } else { the_post_thumbnail( 'spine-small_size' ); } ?></a>
	</figure>
	<?php } ?>

	<header class="content-item-title">
		<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
	</header>

	<span class="content-item-byline">
		<span class="content-item-byline-date"><?php echo get_the_date(); ?></span>
	</span>
	<span class="content-item-excerpt">
		<?php the_excerpt(); ?>
	</span>
</article>
