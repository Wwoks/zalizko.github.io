/**
 * Feature: Change link label
 * Backported changes: update this.renderBody with updateLabelPreview call
 */

ve.ui.MWInternalLinkContextItem.prototype.renderBody = function () {
	this.$body.empty().append( this.constructor.static.generateBody(
		ve.init.platform.linkCache,
		this.model,
		this.context.getSurface().getModel().getDocument().getHtmlDocument(),
		this.context
	), this.$labelLayout );
	this.updateLabelPreview();
};

// Need to re-inherit class, as LinkContextItem was also backported
OO.inheritClass( ve.ui.MWInternalLinkContextItem, ve.ui.LinkContextItem );