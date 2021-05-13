<?php
global $is_top_feature, $is_river, $is_good_to_know, $is_read_more;

$card_id = get_the_ID();

$card_class = 'card-' . $card_id;

if ( is_front_page() && $is_top_feature ) {
	$default_category = absint( get_option( 'default_category' ) );

	$category_html = array();
	foreach ( get_the_category() as $category ) {
		// Don't show the default category (Uncategorized) on features.
		if ( \WSU\News\Internal\Taxonomy\is_term_clerical( $category->cat_ID ) ) {
			continue;
		}

		$category_html[] = esc_html( $category->cat_name );
	}

	// Mark cards with multiple categories so that proper spacing can be applied.
	if ( 1 < count( $category_html ) ) {
		$card_class = ' card--multi-category';
	}

	// Enforce a maximum of 2 displayed categories on featured items.
	if ( 2 < count( $category_html ) ) {
		$category_html = array_slice( $category_html, 0, 2 );
	}

	$category_html = array_map( 'trim', $category_html );
	$category_html = implode( ',<br />', $category_html );
}

$item_cats = get_the_category();

foreach ( $item_cats as $item_cat ) {

	$card_class .= ' category-' . $item_cat->slug;

}

?>
<article class="card card--news<?php echo esc_attr( $card_class ); ?>">
	<?php if ( is_front_page() && $is_top_feature ) { ?>
	<span class="card-categories"><?php echo $category_html; // @codingStandardsIgnoreLine ?></span>
	<?php } ?>

	<?php if ( ( ! is_archive() && ! is_home() && ! is_front_page() ) ) { ?>
	<span class="card-categories"><?php
	$category_html = '';
	foreach ( get_the_category() as $category ) {
		$category_html .= ' <a href="' . esc_url( get_category_link( $category->cat_ID ) ) . '">' . esc_html( $category->cat_name ) . '</a>,';
	}
	$category_html = trim( $category_html );
	$category_html = rtrim( $category_html, ',' );
	echo $category_html; // @codingStandardsIgnoreLine
	?></span>
	<?php } ?>

	<?php if ( spine_has_featured_image() && false === $is_river && false === $is_good_to_know ) { ?>
	<figure class="card-image">
		<a href="<?php the_permalink(); ?>"><?php if ( $is_top_feature ) { the_post_thumbnail( 'spine-large_size' ); } else { the_post_thumbnail( 'spine-small_size' ); } ?></a>
	</figure>
	<?php } ?>

	<header class="card-title">
		<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
	</header>

	<?php if ( false === $is_good_to_know ) { ?>
	<div class="card-date"><?php echo get_the_date(); ?></div>
	<?php } ?>

	<?php if ( false === $is_good_to_know ) { ?>
	<div class="card-excerpt">
		<?php the_excerpt(); ?>
	</div>
	<?php } ?>

	<?php if ( $is_read_more ) { ?>
	<a href="<?php the_permalink(); ?>" class="card-cta button">Read more</a>
	<?php } ?>
</article>
