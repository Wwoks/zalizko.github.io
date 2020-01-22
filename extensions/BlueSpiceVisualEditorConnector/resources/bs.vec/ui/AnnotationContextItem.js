/**
 * Feature: Change link label
 * Backported changes: added getAnnotationView()
 */
/**
 * Find the CE view for the annotation related to this context item
 *
 * Problem: Going from the model to the view is difficult, as there can be many views of any given model.
 * Assumption: an active annotation context item must mean the focus is within the relevant annotation.
 *
 * @return {ve.ce.Annotation|undefined} The annotation view, if it's found, or undefined if not
 */
ve.ui.AnnotationContextItem.prototype.getAnnotationView = function () {
	var annotation,
		model = this.model;

	this.context.getSurface().getView().annotationsAtFocus().some( function ( ann ) {
		if ( model === ann.model ) {
			annotation = ann;
			return true;
		}
	} );
	return annotation;
};