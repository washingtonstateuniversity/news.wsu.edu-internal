<?php

namespace WSU\News\Internal\Theme_Images;

add_filter( 'spine_post_supports_background_image', '__return_false' );
add_filter( 'spine_page_supports_background_image', '__return_false' );
add_filter( 'spine_post_supports_thumbnail_image', '__return_false' );
add_filter( 'spine_page_supports_thumbnail_image', '__return_false' );
