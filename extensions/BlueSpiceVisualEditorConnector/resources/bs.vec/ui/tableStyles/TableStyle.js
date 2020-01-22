bs.util.registerNamespace( 'bs.vec.ui' );
bs.util.registerNamespace( 'bs.vec.util' );

bs.vec.ui.TableStyle = function() {
	this.section = '';
	this.applyTo = '';
};

OO.initClass( bs.vec.ui.TableStyle );

bs.vec.ui.TableStyle.static.SECTION_ROW = 'row';
bs.vec.ui.TableStyle.static.SECTION_COLUMN = 'col';
bs.vec.ui.TableStyle.static.SECTION_CELL = 'cell';

bs.vec.ui.TableStyle.static.ELEMENT_CELL = 'cell';
bs.vec.ui.TableStyle.static.ELEMENT_ROW = 'row';

bs.vec.ui.TableStyle.static.UNIT_PIXEL = 'px';
bs.vec.ui.TableStyle.static.UNIT_PERCENT = '%';
bs.vec.ui.TableStyle.static.UNIT_NONE = '';

bs.vec.ui.TableStyle.static.TYPE_QUICK = 'quick';
bs.vec.ui.TableStyle.static.TYPE_ADDITIONAL = 'additional';
bs.vec.ui.TableStyle.static.TYPE_BOTH = 'both';

bs.vec.ui.TableStyle.prototype.decorate = function ( $element ) {
	if ( !this.getAttribute() ) {
		return;
	}
	if ( !this.value ) {
		return;
	}

	$element.css( this.getAttribute(), this.getValue() );
};

bs.vec.ui.TableStyle.prototype.getValue = function() {
	return this.value + this.getUnit();
};

bs.vec.ui.TableStyle.prototype.getSection = function() {
	return this.section || null;
};

bs.vec.ui.TableStyle.prototype.splitValue = function( value ) {
	return value.replace( this.getUnit(), '' );
};

/**
 * Name of CSS attribute as it will be applied to the element
 */
bs.vec.ui.TableStyle.prototype.getAttribute = function() {
	// STUB
};

/**
 * Unit for style attribute value (px, %...)
 * @returns string
 */
bs.vec.ui.TableStyle.prototype.getUnit = function() {
	return '';
};

bs.vec.ui.TableStyle.prototype.setValue = function( value ) {
	this.value = value;
};

/**
 * Does this style tool applies to current section
 *
 * @param string section
 * @returns boolean
 */
bs.vec.ui.TableStyle.prototype.applies = function( section ) {
	if ( section !== this.section ) {
		return false;
	}
	return true;
};

bs.vec.ui.TableStyle.prototype.getTool = function() {
	return {};
};

/**
 * FIXME: Point of confusion. This will get called with different "subject"s.
 * Depending on section it will be cellNode or a rowNode
 * Also, return values depend on the section
 *
 * @param subject
 * @param params
 * @return mixed
 */
bs.vec.ui.TableStyle.prototype.executeAction = function( subject, params ) {
	// STUB
};

bs.vec.ui.TableStyle.prototype.toDataElement = function( section, domElement, result ) {
	// STUB
};

bs.vec.ui.TableStyle.prototype.toDomElements = function( section, dataElement, domElement, attributes ) {
	// STUB
};

bs.vec.ui.TableStyle.prototype.getModelProperty = function() {
	// STUB
};

bs.vec.ui.TableStyle.prototype.toDataElement = function( section, domElement, result ) {
	var style, styleParser, modelProperty;
	modelProperty = this.getModelProperty();

	if ( section !== this.applyTo ) {
		return;
	}
	style = domElement.getAttribute( 'style' );
	if ( !style ) {
		return;
	}
	styleParser = new bs.vec.util.StyleAttributeParser( style );
	if ( styleParser.getValueForAttr( this.getAttribute() ) ) {
		result[modelProperty] = this.splitValue(
			styleParser.getValueForAttr( this.getAttribute() )
		);
	}
};

bs.vec.ui.TableStyle.prototype.toDomElements = function( section, dataElement, domElement, attributes ) {
	var value, style, styleParser;

	if ( section !== this.applyTo ) {
		return;
	}

	if ( !dataElement.hasOwnProperty( this.getModelProperty() ) ) {
		return;
	}

	value = dataElement[this.getModelProperty()];
	this.setValue( value );

	style = domElement.getAttribute( 'style' );
	if ( !style ) {
		style = '';
	}
	styleParser = new bs.vec.util.StyleAttributeParser( style );
	styleParser.addToStyle( this.getAttribute(), this.getValue() );
	domElement.setAttribute( 'style', styleParser.toString() );
};



bs.vec.util.StyleAttributeParser = function( value ) {
	if ( typeof value !== 'undefined') {
		// Parse even if empty style is passed
		this.setStyle( value );
	}
};

OO.initClass( bs.vec.util.StyleAttributeParser );

/**
 * This class enabled operations on style attribute of a DOM element
 *
 * @param string style
 */
bs.vec.util.StyleAttributeParser.prototype.setStyle = function( style ) {
	this.value = style;
	this.formatted = {};
	this.parseStyle();
};

bs.vec.util.StyleAttributeParser.prototype.parseStyle = function() {
	var pairs, pair, pairBits, attr, value, i;
	if ( typeof this.value !== 'string' ) {
		return {};
	}
	pairs = this.value.split( ';' );
	for( i = 0; i < pairs.length; i++ ) {
		pair = pairs[i];
		if ( pair.trim() === '' ) {
			continue;
		}
		pairBits = pair.split( ':' );
		value = pairBits.pop().trim();
		attr = pairBits.pop().trim();
		this.formatted[attr] = value;
	}
	return this.formatted;
};

bs.vec.util.StyleAttributeParser.prototype.addToStyle = function( attr, value ) {
	this.formatted[attr] = value;
};

bs.vec.util.StyleAttributeParser.prototype.removeFromStyle = function( attr ) {
	if ( this.formatted.hasOwnProperty( attr ) ) {
		delete( this.formatted[attr] );
	}
};

bs.vec.util.StyleAttributeParser.prototype.getValueForAttr = function( attr ) {
	if ( this.formatted.hasOwnProperty( attr ) ) {
		return this.formatted[attr];
	}
	return null;
};

bs.vec.util.StyleAttributeParser.prototype.toString = function() {
	var attr, str = '';
	for( attr in this.formatted ) {
		if ( !this.formatted.hasOwnProperty( attr ) ) {
			continue;
		}
		str += attr + ':' + this.formatted[attr] + ';';
	}
	return str;
};