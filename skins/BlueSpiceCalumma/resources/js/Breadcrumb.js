( function( d, $, mw ) {
	var api = new mw.Api();

	$( d ).on( 'click', '.bs-page-breadcrumbs .dropdown-toggle', function( e ) {
		var $buttonGroup = $(this).parent( '.btn-group' );
		if( $buttonGroup.data( 'loaded' ) === true ) {
			return;
		}

		var $mainButton = $buttonGroup.find( '.btn' ).first();
		var path = $mainButton.data( 'bs-path' );
		if( !path ) {
			return;
		}

		var $dropDownMenu = $buttonGroup.find( '.dropdown-menu > ul' ).first();
		api.abort();
		api.get( {
				"format": "json",
				"action": "bs-wikisubpage-treestore",
				"node": path,
				"limit": "-1"
		})
		.done( function( response ){
			for ( var i = 0; i < response.children.length; i++ ) {
				$dropDownMenu.append( '<li>' + response.children[i].page_link + '</li>' );
			};

			$buttonGroup.data( 'loaded', true );
		});
	});
})( document, jQuery, mediaWiki );
