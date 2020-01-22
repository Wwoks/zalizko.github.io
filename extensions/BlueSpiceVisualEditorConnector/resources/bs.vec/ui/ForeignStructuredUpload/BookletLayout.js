bs.util.registerNamespace( 'bs.vec.ui.ForeignStructuredUpload' );

bs.vec.ui.ForeignStructuredUpload.BookletLayout = function BsVecUiForeignStructuredUploadBookletLayout( config ) {
	bs.vec.ui.ForeignStructuredUpload.BookletLayout.parent.call( this, config );
};

OO.inheritClass( bs.vec.ui.ForeignStructuredUpload.BookletLayout, mw.ForeignStructuredUpload.BookletLayout );

bs.vec.ui.ForeignStructuredUpload.BookletLayout.prototype.getFilename = function() {
	var filename = bs.vec.ui.ForeignStructuredUpload.BookletLayout.parent.prototype.getFilename.call( this );

	var data = {
		filename: filename
	}
	this.emit( 'getfilename', this, data );

	return data.filename;
};
