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
<div class="social-share-bar">
	<div>
		<span>Let's Share</span>
		<ul>
			<li class="linkedin">
				<a href="https://www.linkedin.com/shareArticle?mini=true&amp;url=<?php echo esc_url( $post_share_url ); ?>&amp;summary=<?php echo esc_attr( $post_share_title ); ?>&amp;source=undefined" target="_blank">
					<span class="screen-reader-text">Share on Linkedin</span>
					<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20">
						<use xlink:href="#social-media-icon_linkedin" />
					</svg>
				</a>
			</li>
			<li class="twitter">
				<a href="https://twitter.com/intent/tweet?text=<?php echo esc_attr( $post_share_title ); ?>&amp;url=<?php echo esc_url( $post_share_url ); ?>&amp;via=<?php echo esc_attr( $twitter_handle ); ?>" target="_blank">
					<span class="screen-reader-text">Share on Twitter</span>
					<svg xmlns="http://www.w3.org/2000/svg" width="20" height="16">
						<use xlink:href="#social-media-icon_twitter" />
					</svg>
				</a>
			</li>
			<li class="facebook">
				<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo esc_url( $post_share_url ); ?>" target="_blank">
					<span class="screen-reader-text">Share on Facebook</span>
					<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20">
						<use xlink:href="#social-media-icon_facebook" />
					</svg>
				</a>
			</li>
		</ul>
	</div>
</div>
