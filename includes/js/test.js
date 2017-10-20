
var APPLICATION_ID = 'C1L9Z9O6E8';
var SEARCH_ONLY_API_KEY = '417a20424b4227b7032ecd009dbb1643';
var INDEX_NAME = 'Prod_Inteum_TechnologyPublisher_wsu';
var MAIN_URL = 'http://wsu.testtechnologypublisher.com/';



// NOTE - When using a free Algolia account, this variable should be set to true;
var SHOW_ALGOLIA_LABEL = true;
var SEARCH_BOX_REQUIRES_ENTER = false;

try {
	var search = instantsearch({
		appId: 'C1L9Z9O6E8',
		apiKey: '417a20424b4227b7032ecd009dbb1643',
		indexName: 'Prod_Inteum_TechnologyPublisher_wsu'
		,
		urlSync: true
	});
} catch (err) {
	/* Perhaps a wrong App ID or API Key or Algolia's site is throwing an exception */
	alert(err.message);
}



search.addWidget(
	instantsearch.widgets.searchBox({
		container: '#search-box',
		placeholder: 'Search',
		poweredBy: true
	})
);
/* Hit Container */
search.addWidget(
	instantsearch.widgets.hits({
		container: '#hits-container',
		templates: {
			item: '<div class="hit">' +
			'    <div class="hit-content">' +
			'        <a href="{{{Url}}}" class="hit-name">{{{title}}}</a>' +
			'        <div style="height:5px;"></div>' +
			'        <div class="hit-description" style="font-size: normal;">Inventors: {{{finalPathInventors}}}</div>' +
			'        <div style="height:0px;"></div>' +
			'        <span class="hit-description">Summary: </span><span>{{{descriptionTruncated}}}</span>' +
			'        <div style="height:5px;"></div>' +
			'        <span class="hit-description">Disclosure Date: </span><span>{{{disclosureDate}}} </span>' +
			'        <div style="height:5px;"></div>' +
			'        <div class="hit-description" style="font-size: normal;">Categories: {{{finalPathCategories}}}</div>' +
			'        <div style="height:5px;"></div>' +
			'    </div>' +
			'</div>'
		},
		transformData: {
			item: function(refinement) {
				var addCommas = true;
				refinement = refineInventors(refinement, addCommas);
				refinement = refineCategories(refinement, addCommas);
				return refinement;
			} // end of item
		} // end of tranformData function
	})
);

function refineInventors(refinement, addCommas) {
	if (refinement === null || refinement.finalPathInventors === null)
		return refinement;

	var partsOfStr = refinement.finalPathInventors.split(',');
	if (partsOfStr.length <= 0) {
		return refinement;
	}

	refinement.finalPathInventors = "";

	// Sort the inventors alphabetically and trim
	var items = [];
	for (index = 0; index < partsOfStr.length; ++index) {
		items.push(partsOfStr[index].trim());
	}
	items.sort(compareInventors);

	// generate links
	for (index = 0; index < items.length; ++index) {
		var part = items[index];
		var url = MAIN_URL + '/?q=&hPP=20&idx=' + INDEX_NAME + '&fR%5Binventors%5D%5B0%5D=' + encodeURIComponent(part) + '&is_v=1';
		var link = '<a href="' + url + '" class="hit-name">' + part + '</a>';
		if (addCommas) {
			if (index + 1 < items.length) {
				link = link + ', ';
			}
		} else {
			link = '<div>' + link + '</div>';
		}
		refinement.finalPathInventors = refinement.finalPathInventors + link;
	}

	return refinement;
}

function compareInventors(a, b) {
	if (a < b)
		return -1;
	if (a > b)
		return 1;
	return 0;
}

function refineCategories(refinement, addCommas) {
	// Pre-processing to convert categories into links
	// Temporarily change specific commas to double chevron
	var tempComma = '<> ';
	var tempRefinement = refinement.finalPathCategories.replace('Computers, Electronics', 'Computers' + tempComma + 'Electronics');

	var partsOfStr = tempRefinement.split(',');

	if (partsOfStr.length > 0) {
		refinement.finalPathCategories = "";

		var items = [];
		for (index = 0; index < partsOfStr.length; ++index) {
			// Change double chevron back to comma
			partsOfStr[index] = partsOfStr[index].replace('<> ', ', ');

			var classification = '';
			var firstChevron = partsOfStr[index].indexOf('>');
			var part = null;
			if (firstChevron > 0) {
				if (classification == '') {
					classification = partsOfStr[index].substring(0, firstChevron).trim();
				}
				part = partsOfStr[index].replace(classification + ' > ', '');

				var chevronsInStr = part.split('>');
				for (inner = 0; inner < chevronsInStr.length; ++inner) {
					part = chevronsInStr[inner].trim();
					var encoded = null;
					for (i2 = 0; i2 <= inner; ++i2) {
						if (encoded === null) {
							encoded = chevronsInStr[i2].trim();
						} else {
							encoded += ' > ';
							encoded += chevronsInStr[i2].trim();
						}
					}
					encoded = encodeURIComponent(encoded.trim());

					var url = MAIN_URL + '/?q=&hPP=20&idx=' + INDEX_NAME + '&p=0&hFR%5B' + classification + '.lvl0%5D%5B0%5D=' + encoded + '&is_v=1';
					var link = '<a href="' + url + '" class="hit-name">' + part + '</a>';

					items.push({
						part: part,
						link: link
					});
				} // end of for inner
			} // end of if (firstChevron)
		} // end of for index

		// Sort the urls
		items.sort(compareCategoryPart);
		for (i = 0; i < items.length; ++i) {
			var url = items[i].link;
			if (refinement.finalPathCategories.indexOf(url) >= 0) {
				continue;
			}
			if (addCommas)
			{
				if (i + 1 < items.length) {
					url = url + ", ";
				}
			}
			else
			{
				url = '<div>' + url + '</div>';
			}
			refinement.finalPathCategories = refinement.finalPathCategories + url;
		}

	} // end of if partsOfStr

	return refinement;
}

function compareCategoryPart(a, b) {
	if (a.part < b.part)
		return -1;
	if (a.part > b.part)
		return 1;
	return 0;
}
/* Hit Container */
search.addWidget(
	instantsearch.widgets.hits({
		container: '#hits-container',
		templates: {
			item: '<div class="hit">' +
			'    <div class="hit-content">' +
			'        <a href="{{{Url}}}" class="hit-name">{{{_highlightResult.title.value}}}</a>' +
			'        <div style="height:5px;"></div>' +
			'        <div class="hit-description" style="font-size: normal;">Inventors: {{{finalPathInventors}}}</div>' +
			'        <div style="height:0px;"></div>' +
			'        <span class="hit-description">Summary: </span><span>{{{_highlightResult.descriptionTruncated.value}}}</span>' +
			'        <div style="height:5px;"></div>' +
			'        <span class="hit-description">Disclosure Date: </span><span>{{{disclosureDate}}} </span>' +
			'        <div style="height:5px;"></div>' +
			'        <div class="hit-description" style="font-size: normal;">Categories: {{{finalPathCategories}}}</div>' +
			'        <div style="height:5px;"></div>' +
			'    </div>' +
			'</div>'
		},
		transformData: {
			item: function(refinement) {
				var addCommas = true;
				refinement = refineInventors(refinement, addCommas);
				refinement = refineCategories(refinement, addCommas);
				return refinement;
			} // end of item
		} // end of tranformData function
	})
);








/* Technology Categories */
search.addWidget(
	instantsearch.widgets.hierarchicalMenu({
		container: '#technologyCategories',
		attributes: ['Technologies.lvl0','Technologies.lvl1'],
		operator: 'or',
		collapsible: {
			collapsed: false
		},
		templates: {
			header: '<div style="font-size: 18px; color: #717171;">Technology Categories</div>',
			item: '<a href="javascript:void(0);" class="facet-item {{#isRefined}}active{{/isRefined}}"><span class="facet-name"><em class="fa fa-angle-right"></em> {{name}}</span class="facet-name"><span>&nbsp;({{count}})</span></a>'
		},
		limit: 50
	})
);
search.addWidget(
	instantsearch.widgets.refinementList({
		container: '#inventors',
		attributeName: 'inventors',
		operator: 'and',
		sortBy: ['isRefined', 'count:desc', 'name:asc'],
		collapsible: {
			collapsed: true
		},
		templates: {
			header: '<div style="font-size: 18px; color: #717171;">Inventors</div>',
			item: '<label class="ais-refinement-list–label">' +
			'<input class="ais-refinement-list–checkbox" value="{{name}}" {{#isRefined}}checked=""{{/isRefined}} type="checkbox">' +
			'<span >{{name}}&nbsp;({{count}})</span>' +
			'</label>'
		},
		searchForFacetValues: {
			placeholder: 'Search for inventors',
			templates: {
				noResults: '<div class="sffv_no-results">No matching inventors.</div>'
			}
		},limit: 7,
		showMore: {
			limit: 50
		}
	})
);
search.addWidget(
	instantsearch.widgets.pagination({
		container: '#pagination-container'
	})
);
search.addWidget(
	instantsearch.widgets.pagination({
		container: '#pagination-container-top'
	})
);


/* ClientDept */
search.addWidget(
	instantsearch.widgets.refinementList({
		container: '#clientDepartments',
		attributeName: 'clientDepartments',

		sortBy: ['isRefined', 'count:desc', 'name:asc'],
		operator: 'or',
		collapsible: {
			collapsed: true
		},
		templates: {
			header: '<div style="font-size: 18px; color: #717171;">Colleges</div>',
			item: '<label class="ais-refinement-list--clientdept" style="width: 250px; padding-left: 1rem; font-weight: 400; overflow: hidden; text-overflow: ellipsis;">' +
			'<input style="margin: 0 10px 0 -10px;" class="ais-refinement-list--checkbox" value="{{name}}" {{#isRefined}}checked=""{{/isRefined}} type="checkbox">' +
			'<span>{{name}}&nbsp;({{count}})</span>' +
			'</label>'
		},

		transformData: {
			item: function(data) {
				console.log(data);
				data.name=data.name.split('(')[0];
				return data;
			}
		}
})
);

/* Search Stats */
search.addWidget(
	instantsearch.widgets.stats({
		container: '#statsContainer'
	})
);

/* This is an alternative to the ClearAll Widget */
search.addWidget(
	instantsearch.widgets.currentRefinedValues({
		container: '#refinedValues',
		clearAll: 'after',
		templates: {
			item: '<div>{{name}}</div>'
		}
	})
);





var list_item = jQuery( ".ais-refinement-list--item" );

list_item.each( function() {
	if ( "COLLEGE" !== $(this).find('span').text().substr(0,7 ) ) {
		$(this).css('display', 'none' );
	}
} );

search.start();
