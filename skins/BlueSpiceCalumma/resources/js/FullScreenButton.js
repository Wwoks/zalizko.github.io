( function( d, $, mw ) {
	$( d ).on( 'click', '.calumma-full-screen-button', function( e ){
		e.preventDefault();

		var target =  $( this ).attr( 'data-toggle' );

		if ( $( this ).hasClass( target ) ){
			$( this ).removeClass( target );
			$( 'body.mediawiki .wrapper .bs-content.container' ).removeClass( target );

		} else{
			if ( !( $( 'body' ).hasClass( 'navigation-main-collapse' ) ) ){
				$( '.navigation-main .sidebar-toggle' ).trigger( 'click' );
			}
			if ( !( $( 'body' ).hasClass( 'sitetools-main-collapse' ) ) ){
				$( '.sitetools-main .sidebar-toggle' ).trigger( 'click' );
			}
			$( 'body.mediawiki .wrapper .bs-content.container' ).addClass( target );
			$( this ).addClass( target );
		}
	});
})( document, jQuery, mediaWiki );

