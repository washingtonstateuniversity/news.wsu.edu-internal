<?php

namespace WSU\News\Internal\Curation;

add_action( 'homepage', 'WSU\News\Internal\Curation\display_section' );
add_filter( 'homepage_control_title', 'WSU\News\Internal\Curation\homepage_control_title', 10, 2);

function homepage_control_title( $title, $id ) {
	return $id;
}

function display_section() {
	echo 'A category section';
}
