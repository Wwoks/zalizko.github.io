/**
 * Feature: Change link label
 * Backported changes: added annotationsAtFocus()
 */
ve.ce.Surface.prototype.annotationsAtFocus = function () {
	var annotations = [];
	// Note: in the original change, the class was .ve-ce-annotation. Had to change this because
	// the respective class is not present in the old version
	$( this.nativeSelection.focusNode ).parents( '.ve-ce-linkAnnotation' ).addBack( '.ve-ce-linkAnnotation' ).each( function () {
		var view = $( this ).data( 'view' );
		if ( view /*&& view.canBeActive()*/ ) {
			annotations.push( view );
		}
	} );
	return annotations;
};