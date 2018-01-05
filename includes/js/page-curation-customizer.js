jQuery( document ).ready( function( $ ) {
	"use strict";

	let page_curation = $( ".page-curation" );

	page_curation.sortable();
	page_curation.disableSelection();

	let process_value = function() {
		let sections = {};

		page_curation.find( "li" ).each( function( i, e ) {
			let section_id = $( this ).attr( "id" ).substr( 8 );

			sections[ section_id ] = {};
			sections[ section_id ].count = $( this ).find( ".section-count input" ).val();
			sections[ section_id ].classes = $( this ).find( ".section-classes input" ).val();
		} );

		sections = JSON.stringify( sections );
		$( "input[data-customize-setting-link='page_curation']" ).attr( "value", sections ).trigger( "change" );
	};

	page_curation.bind( "sortstop", process_value );
	$( ".page-curation input" ).on( "change", process_value );

	let process_featured_posts = function( ) {
		let post_ids = [];
		featured_posts.find( ".featured-post-single" ).each( function() {
			post_ids.push( $( this ).data( "featured-post-id" ) );
		} );

		$( "input[data-customize-setting-link='featured_posts']" ).attr( "value", post_ids ).trigger( "change" );
	};
	let featured_posts = $( ".selected-featured-posts" );

	featured_posts.sortable( {
		start: function( e, ui ) {
			ui.placeholder.height( ui.item.height() );
		}
	} );

	featured_posts.bind( "sortstop", process_featured_posts );

	$( featured_posts ).on( "click", ".remove-featured", function( e ) {
		e.preventDefault();
		$( this ).parent().remove();
		process_featured_posts();
		featured_posts.append( "<div class=\"featured-post-empty\">No featured post selected for this area.</div>" );
	} );
} );
