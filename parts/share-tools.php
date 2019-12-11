<?php
$post_share_url = esc_url( get_permalink() );
$post_share_title = rawurlencode( trim( wp_title( '', false ) ) );
$spine_social_options = spine_social_options();

if ( ! empty( $spine_social_options['twitter'] ) ) {
	$twitter_array = explode( '/', $spine_social_options['twitter'] );
	$twitter_handle = esc_attr( array_pop( $twitter_array ) );
} else {
	$twitter_handle = 'wsupullman';
}
?>
<div class="card-share">
	<p>Let's Share</p>
	<a href="<?php echo esc_url( 'https://www.linkedin.com/shareArticle?mini=true&amp;url=' . $post_share_url . '&amp;summary=' . $post_share_title ); ?>">
		<span class="screen-reader-text">Share this article on Linkedin</span>
		<svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" width="16" height="16">
			<use xlink:href="#social-media-icon_linkedin" fill="#007bb6"/>
		</svg>
	</a>
	<a href="<?php echo esc_url( 'https://twitter.com/intent/tweet?text=' . $post_share_title . '&amp;url=' . $post_share_url . '&amp;via=' . $twitter_handle ); ?>">
		<span class="screen-reader-text">Share on Twitter</span>
		<svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" width="16" height="16" viewBox="0 -2 20 20">
			<use xlink:href="#social-media-icon_twitter" fill="#00aced"/>
		</svg>
	</a>
	<a href="<?php echo esc_url( 'https://www.facebook.com/sharer/sharer.php?u=' . $post_share_url ); ?>">
		<span class="screen-reader-text">Share on Facebook</span>
		<svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" width="16" height="16">
			<use xlink:href="#social-media-icon_facebook" fill="#3b5998"/>
		</svg>
	</a>
	<a href="mailto:?subject=<?php echo esc_url( $post_share_title ); ?>&amp;body=Check out this WSU Insider story: <?php echo esc_url( $post_share_url ); ?>">
		<span class="screen-reader-text">Share with email</span>
		<svg id="Layer_1" width="16" height="16" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><defs><style>.cls-1{fill:none;}.cls-2{fill:#404040;}</style></defs><rect class="cls-1" x="-0.03" width="16" height="16" transform="translate(16 0.03) rotate(90)"/>
		<path class="cls-2" d="M15,2H1A1,1,0,0,0,0,3V13a1,1,0,0,0,1,1H15a1,1,0,0,0,1-1V3A1,1,0,0,0,15,2ZM8,7.7,3.64,4.09H12.3ZM2.06,11.91V5.37l5.27,4.4.15.08a.6.6,0,0,0,.11.06A.88.88,0,0,0,8,10H8a.85.85,0,0,0,.38-.09.6.6,0,0,0,.11-.06l.15-.08,5.27-4.4v6.54Z" transform="translate(0.03 0)"/></svg>
	</a>
</div>
