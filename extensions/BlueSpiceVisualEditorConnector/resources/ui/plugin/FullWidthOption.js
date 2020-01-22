bs.util.registerNamespace( 'bs.ui.plugin' );

// extend document model
ve.dm.MWTableNode.static.classAttributes['tablefullwidth'] = {tablefullwidth: true};

bs.ui.plugin.FullWidthOption = function BsFullWidthblUiFullWidthOption( config ) {
	bs.ui.plugin.FullWidthOption.super.call( this, config );
};

OO.inheritClass( bs.ui.plugin.FullWidthOption, bs.vec.ui.plugin.MWTableDialog );

bs.ui.plugin.FullWidthOption.prototype.initialize = function () {
	var fullWidthField;

	this.component.fullWidthToggle = new OO.ui.ToggleSwitchWidget();
	fullWidthField = new OO.ui.FieldLayout( this.component.fullWidthToggle, {
		align: 'left',
		label: ve.msg( 'bs-visualeditorconnector-ve-fullwidth-option' )
	} );

	this.component.fullWidthToggle.connect( this.component, {change: 'updateActions'} );
	this.component.panel.$element.append( fullWidthField.$element );
};

bs.ui.plugin.FullWidthOption.prototype.getValues = function ( values ) {
	return ve.extendObject( values, {
		tablefullwidth: this.component.fullWidthToggle.getValue()
	} );
};

bs.ui.plugin.FullWidthOption.prototype.getSetupProcess = function ( parentProcess, data ) {
	parentProcess.next( function () {
		var fragment = this.component.getFragment();
		if ( !fragment ) {
			return;
		}
		var tableNode = this.component.getFragment().getSelection().getTableNode(),
			tablefullwidth = !!tableNode.getAttribute( 'tablefullwidth' );

		this.component.fullWidthToggle.setValue( tablefullwidth );

		ve.extendObject( this.component.initialValues, {
			tablefullwidth: tablefullwidth
		} );
	}, this );
	return parentProcess;
};

bs.ui.plugin.FullWidthOption.prototype.getActionProcess = function ( parentProcess, action ) {
	parentProcess.next( function () {
		var surfaceModel, fragment, initialFragment;
		if ( action === 'done' ) {
			initialFragment = this.component.getFragment();
			if ( !initialFragment ) {
				return;
			}
			surfaceModel = initialFragment.getSurface();
			fragment = surfaceModel.getLinearFragment(
				initialFragment.getSelection().tableRange, true
			);
			fragment.changeAttributes( {
				tablefullwidth: this.component.fullWidthToggle.getValue()
			} );
		}
	}, this );
	return parentProcess;
};

bs.vec.registerComponentPlugin(
		bs.vec.components.TABLE_DIALOG,
		function ( component ) {
			return new bs.ui.plugin.FullWidthOption( component );
		}
);