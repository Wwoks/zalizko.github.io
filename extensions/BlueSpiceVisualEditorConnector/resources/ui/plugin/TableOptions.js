bs.util.registerNamespace( 'bs.ui.plugin' );

var styleclasses = mw.config.get( 'bsgVisualEditorConnectorTableStyleRegistry' );

bs.ui.plugin.TableOptions = function ( config ) {
	bs.ui.plugin.TableOptions.super.call( this, config );
};

OO.inheritClass( bs.ui.plugin.TableOptions, bs.vec.ui.plugin.MWTableDialog );



// object styleclass has properties displayname->classname
for ( var style in styleclasses ) {
	if ( !styleclasses.hasOwnProperty( style ) ) {
		continue;
	}
	var classname = styleclasses[style];
	var obj = {};
	obj[classname] = true;
	ve.dm.MWTableNode.static.classAttributes[classname] = obj;
}

bs.ui.plugin.TableOptions.prototype.initialize = function () {
	var TableOptions;

	var options = [];
	for ( var style in styleclasses ) {
		if ( !styleclasses.hasOwnProperty( style ) ) {
			continue;
		}
		var classname = styleclasses[style];
		var option = new OO.ui.MenuOptionWidget( {
			data: classname, label: style
		} );
		options.push( option );
	}
	this.component.TableOptions = new OO.ui.DropdownWidget( {
		label: ve.msg( 'bs-visualeditorconnector-ve-style-option-description' ),
		menu: {
			items: options
		}
	} );
	TableOptions = new OO.ui.FieldLayout( this.component.TableOptions, {
		align: 'left',
		label: ve.msg( 'bs-visualeditorconnector-ve-style-option' )
	} );

	this.component.TableOptions.connect( this.component, {change: 'updateActions'} );
	this.component.panel.$element.append( TableOptions.$element );

};

bs.ui.plugin.TableOptions.prototype.getValues = function ( values ) {

	var tableNode = this.component.getFragment().getSelection().getTableNode();
	var tableoption = tableNode.getAttributes();
	var activeclass = '';
	var originalClasses = tableoption.originalClasses;

	if ( originalClasses ) {
		var classes = originalClasses.split( " " );
		for ( var style in styleclasses ) {
			if ( !styleclasses.hasOwnProperty( style ) ) {
				continue;
			}
			var classname = styleclasses[style];
			if ( classes.indexOf( classname ) !== -1 ) {
				if ( !activeclass ) {
					activeclass = classname;
				}
			}
		}
	}
	return ve.extendObject( values, {tableoption: activeclass} );
};

bs.ui.plugin.TableOptions.prototype.getSetupProcess = function ( parentProcess, data ) {
	parentProcess.next( function () {
		var tableNode = this.component.getFragment().getSelection().getTableNode();
		var tableoption = tableNode.getAttributes();
		var originalClasses = tableoption.originalClasses;

		if ( originalClasses ) {
			var classes = originalClasses.split( " " );
			var activeclass;
			for ( var style in styleclasses ) {
				if ( !styleclasses.hasOwnProperty( style ) ) {
					continue;
				}
				var classname = styleclasses[style];
				if ( classes.indexOf( classname ) !== -1 ) {
					if ( activeclass ) {
						var index = classes.indexOf( classname );
						if ( index !== -1 ) {
							classes.splice( index, 1 );
						}
					} else {
						activeclass = classname;
					}
				}
			}
			if ( activeclass ) {
				var selected = this.component.TableOptions.getMenu().findItemFromData( activeclass );
				this.component.TableOptions.getMenu().selectItem( selected );
			}
			ve.extendObject( this.component.initialValues, {
				tableoption: activeclass
			} );
		}
	}, this );
	return parentProcess;
};

bs.ui.plugin.TableOptions.prototype.getActionProcess = function ( parentProcess, action ) {
	parentProcess.next( function () {
		var initialFragment, surfaceModel, fragment;

		if ( action === 'done' ) {
			initialFragment = this.component.getFragment();
			if ( !initialFragment ) {
				return;
			}
			surfaceModel = initialFragment.getSurface();
			fragment = surfaceModel.getLinearFragment(
				initialFragment.getSelection().tableRange, true
			);
			var selectedItem = this.component.TableOptions.getMenu().findSelectedItem();
			if ( !selectedItem ) {
				return;
			}
			var selectedClass = this.component.TableOptions.getMenu().findSelectedItem().getData();
			var allItems = this.component.TableOptions.getMenu().getItems();

			allItems.forEach( function ( thisClass ) {
				var thisName = thisClass.data;
				var obj = {};
				if ( thisName === selectedClass ) {
					obj[thisName] = true;
					fragment.changeAttributes( obj );
				} else {
					obj[thisName] = false;
					fragment.changeAttributes( obj );
				}
			} );
		}
	}, this );
	return parentProcess;
};

bs.vec.registerComponentPlugin(
		bs.vec.components.TABLE_DIALOG,
		function ( component ) {
			return new bs.ui.plugin.TableOptions( component );
		}
);