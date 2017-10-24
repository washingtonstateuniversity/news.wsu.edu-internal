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
} );
