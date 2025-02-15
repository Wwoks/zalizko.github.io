(function( mw, $, bs ){

	var _instances = {};

	function createEditor( id, cfg ) {
		cfg.floatable = cfg.floatable || false;
		cfg.format = cfg.format || 'wikitext';
		cfg.value = cfg.value || '';

		var dfd = $.Deferred();
		if ( cfg.format === 'html' ) {
			_makeEditor( id, cfg ).done( function( target ){
				dfd.resolve( target );
			});
		} else if ( cfg.format === 'wikitext' ) {
			_transformToHtml( cfg.value ).done( function( html ) {
				cfg.value = html;
				_makeEditor( id, cfg ).done( function( target ){
					dfd.resolve( target );
				});
			});
		}

		return dfd.promise();
	}

	function getInstance( id ) {
		return _instances[id];
	}

	function _makeEditor( id, cfg ) {
		var dfd = $.Deferred();

		ve.init.platform.initialize().done( function () {
			var target = new ve.init.sa.BlueSpiceTarget();
			target.addSurface(
				ve.dm.converter.getModelFromDom(
					ve.createDocumentFromHtml( cfg.value )
				)
			);
			// Editor toolbar should not float when there are more than one instances
			target.toolbar.floatable = cfg.floatable;

			// save for future reference
			_instances[id] = target;

			// mysteriously, parent is needed here to actually render to the
			// element, otherwise the toolbar finds no element to attach to,
			// issueing the error message "jQuery.fn.offset() requires an
			// element connected to a document"
			$( cfg.renderTo ).parent().append( target.$element );

			target.getHtml = function() {
				var dfd = $.Deferred();
				var value = this.getSurface().getHtml();
				dfd.resolve( value );
				return dfd.promise();
			};

			target.getWikiText = function() {
				var dfd = $.Deferred();
				var value = this.getSurface().getHtml();
				_transformToWikiText( value ).done( function( data ) {
						dfd.resolve( data );
				});
				return dfd.promise();
			};

			// some extensions, e.g. PageForms, do not support asynchronous
			// calls. Hence, we need to provide a synchronous funcion here.
			target.getWikiTextSync = function() {
				var textToSubmit = this.getSurface().getHtml();

				var requestWikiText = $.ajax({
					type: "POST",
					url: mw.config.get('wgScriptPath') + '/api.php?action=bs-vec-transformtowikitext',
					async: false,
					data: {
						html: textToSubmit,
						format: 'json',
						token: mw.user.tokens.get( 'csrfToken' )
					}
				});
				if ( requestWikiText.status != "200" ) {
					return false;
				} else {
					return requestWikiText.responseJSON.wikitext;
				}
			};

			dfd.resolve( target );
		});

		return dfd.promise();
	}

	function _transformToWikiText( fromHtml ) {
		var dfd = $.Deferred();
		var api = new mw.Api();
		api.postWithToken( 'csrf', {
			action: 'bs-vec-transformtowikitext',
			html: fromHtml
		} )
		.done( function( response ) {
			dfd.resolve( response.wikitext );
		})
		.fail( function() {
			dfd.reject();
		});
		return dfd.promise();
	}

	function _transformToHtml( fromWikiText ) {
		var dfd = $.Deferred();
		var api = new mw.Api();
		api.postWithToken( 'csrf', {
			action: 'bs-vec-transformtohtml',
			wikitext: fromWikiText
		} )
		.done( function( response ) {
			dfd.resolve( response.html );
		})
		.fail( function() {
			dfd.reject();
		});
		return dfd.promise();
	}

	/**
	 * HINT:https://www.mediawiki.org/w/index.php?title=VisualEditor/Gadgets&oldid=2776161#Checking_if_VisualEditor_is_in_regular_'visual'_mode_or_'source'_mode
	 * @param {VeInitTarget} target
	 * @returns {Array}
	 */
	function getCategoriesFromTarget( target ) {
		var surface, model, document, metadata;

		surface = target.getSurface();

		if ( surface.getMode() === 'visual' ) {
			model = surface.getModel();
			document = model.getDocument();
			metadata = document.getMetadata();
			return getCategoriesFromMetadata( metadata );
		} else if ( surface.getMode() === 'source' ) {
			return getCategoriesFromHtml( surface.getHtml() );
		}
		return [];
	}

	/**
	 *
	 * @param {VeDmMetaItem[]} metadata
	 * @returns {Array}
	 */
	function getCategoriesFromMetadata( metadata ) {
		var categories, i, item, prefixedCategory, categoryParts;

		categories = [];
		for( i = 0; i < metadata.length; i++ ) {
			item = metadata[i];
			if( !item || item.type !== 'mwCategory' ) {
				continue;
			}
			//Yes, this is the way to read out categories!
			//HINT: https://github.com/wikimedia/mediawiki-extensions-VisualEditor/blob/ffaab335cee2d9a45cf6767fc40d3463f599b175/modules/ve-mw/ui/pages/ve.ui.MWCategoriesPage.js#L209
			prefixedCategory = item.element.attributes.category;
			categoryParts = prefixedCategory.split( ':', 2 );
			categories.push( categoryParts[1] );
		}

		return categories;
	}

	function getCategoriesFromHtml( html ) {
		var categories = [],
			regex = new RegExp( '\\[\\[([^\\[\\]]*)\\]\\]', 'gm' ),
			singleMatch,
			title,
			categoryName;

		while ( ( singleMatch = regex.exec( html ) ) !== null ) {
			if ( singleMatch.length !== 2 ) {
				continue;
			}
			categoryName = singleMatch.pop();
			categoryName = categoryName.split( '|' ).shift();
			title = mw.Title.newFromText( categoryName );
			if ( title === null || title.getNamespaceId() !== 14 ) {
				continue;
			}
			categories.push( title.getNameText() );
		}

		return categories;
	}

	bs.util.registerNamespace( 'bs.vec' );
	bs.vec.createEditor = createEditor;
	bs.vec.getInstance = getInstance;
	bs.vec.getCategoriesFromTarget = getCategoriesFromTarget;
	bs.vec.getCategoriesFromMetadata = getCategoriesFromMetadata;
	bs.vec.getCategoriesFromHtml = getCategoriesFromHtml;

	// Hide QM tab
	var $qmControls;

	mw.hook( 've.activationComplete' ).add( function () {
		if( !$qmControls ) {
			$qmControls = $( '#bs-qualitymanagement-panel, a[href="#bs-qualitymanagement-panel"]' );
		}
		$qmControls.hide();
	} );

	mw.hook( 've.deactivationComplete' ).add( function () {
		if( $qmControls ) {
			$qmControls.show();
		}
	} );

})( mediaWiki, jQuery, blueSpice );
