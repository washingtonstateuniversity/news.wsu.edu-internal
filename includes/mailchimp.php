<?php

namespace WSU\News\Internal\MailChimp;

add_shortcode( 'insider_subscribe_home', 'WSU\News\Internal\MailChimp\display_insider_home_subscription_form' );

/**
 * Display a subscription form on the home page.
 *
 * @since 0.2.0
 *
 * @return string
 */
function display_insider_home_subscription_form() {
	ob_start();
	?>
	<div id="mc_embed_signup">
		<form action="//wsu.us3.list-manage.com/subscribe/post?u=02b3cd67989caec2eec47f036&amp;id=21d653f16c" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
			<div id="mc_embed_signup_scroll">
				<label for="mce-EMAIL">Sign up for the WSU Insider daily email newsletter</label>
				<input type="email" value="" name="EMAIL" class="email" id="mce-EMAIL" placeholder="email address" required>
				<!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
				<div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_02b3cd67989caec2eec47f036_21d653f16c" tabindex="-1" value=""></div>
				<input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button">
			</div>
		</form>
	</div>
	<?php
	$content = ob_get_contents();
	ob_end_clean();

	return $content;
}
