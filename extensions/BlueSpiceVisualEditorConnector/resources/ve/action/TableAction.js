bs.util.registerNamespace( 'bs.vec.ui' );

bs.vec.ui.TableAction = function () {
	// Parent constructor
	ve.ui.TableAction.super.apply( this, arguments );
	this.tableStyleRegistry =  bs.vec.registry.TableStyle.registry;

	var action = this;
	for( var key in this.tableStyleRegistry ) {
		if ( !this.tableStyleRegistry.hasOwnProperty( key ) ) {
			continue;
		}
		var handler = this.tableStyleRegistry[key];
		// This will create a function for each command
		bs.vec.ui.TableAction.prototype[key] = function( args ) {
			action.executeAction.call( this, action, args );
		}.bind( handler );
	}
};

/* Inheritance */

OO.inheritClass( bs.vec.ui.TableAction, ve.ui.TableAction );

/* Static Properties */

bs.vec.ui.TableAction.static.name = 'bs-vec-table';

bs.vec.ui.TableAction.static.methods = Object.keys( bs.vec.registry.TableStyle.registry ) + [
	'duplicateRow', 'duplicateColumn'
];

/**
 * Gets the selection and runs each appropriate node( row, cell ) though
 * the handler's executeAction method, afterwards, restores the original selection
 *
 * @param bs.vec.ui.TableAction action
 * @param array args
 */
bs.vec.ui.TableAction.prototype.executeAction = function( action, args ) {
	var surfaceModel = action.surface.getModel(),
		selection = surfaceModel.getSelection(),
		tableNode = selection.getTableNode(),
		oldSelection = [ selection.fromCol, selection.fromRow, selection.toCol, selection.toRow ],
		section;

	section = this.getSection();
	if ( section ) {
		action.forEach( section, selection, this.executeAction.bind( action ), args );
	}

	action.restoreSelection( surfaceModel, selection, tableNode, oldSelection );
};

bs.vec.ui.TableAction.prototype.forEach = function( what, selection, cb, cbParams ) {
	var tableNode = selection.getTableNode(),
		matrix = tableNode.getMatrix(),
		cellIndex = 0, rowIndex, colIndex, newRow, rowNode, cells;

	if ( what === 'col' ) {
		cells = [];
		for( colIndex = selection.startCol; colIndex <= selection.endCol; colIndex++ ) {
			cells = matrix.getColumn( colIndex );
			for ( cellIndex = 0; cellIndex < cells.length; cellIndex++ ) {
				cells[cellIndex] = cb( cells[cellIndex], cbParams, this );
			}
			this.replace( selection, what, colIndex, { cells: cells } );
		}
		return;
	} else {
		for( rowIndex = selection.startRow; rowIndex <= selection.endRow; rowIndex++ ) {
			cells = matrix.getRow( rowIndex );
			rowNode = matrix.getRowNode( rowIndex );
			if ( what === 'row' ) {
				newRow = {
					row: cb( rowNode, cbParams, this ),
					cells: cells
				};
			} else if ( what === 'cell' ) {
				for ( cellIndex = 0; cellIndex < cells.length; cellIndex++ ) {
					if ( cellIndex >= selection.startCol && cellIndex <= selection.endCol ) {
						cells[cellIndex] = cb( cells[cellIndex], cbParams, this );
					}
				}

				newRow = {
					row: ve.dm.TableRowNode.static.applyStylings( matrix.getRowNode( rowIndex ), {} ),
					cells: cells
				};
			}
			this.replace( selection, 'row', rowIndex, newRow );
		}
	}

	return true;
};

bs.vec.ui.TableAction.prototype.restoreSelection = function( surfaceModel, selection, tableNode, oldSelection ) {
	surfaceModel.setSelection( new ve.dm.TableSelection(
		selection.getDocument(),
		// tableNode range was changed by deletion
		tableNode.getOuterRange(),
		oldSelection[ 0 ], oldSelection[ 1 ], oldSelection[ 2 ], oldSelection[ 3 ]
	) );
};

bs.vec.ui.TableAction.prototype.hasMutliRowCells = function( cells ) {
	var cellIndex, cell;

	for( cellIndex = 0; cellIndex < cells.length; cellIndex++ ) {
		cell = cells[cellIndex];
		if ( !cell.node.element.attributes.hasOwnProperty( 'rowspan' ) ) {
			continue;
		}
		if ( cell.node.element.attributes.rowspan < 2 ) {
			continue;
		}
		return true;
	}
	return false;
};

bs.vec.ui.TableAction.prototype.duplicate = function( mode ) {
	var surfaceModel = this.surface.getModel(),
		selection = surfaceModel.getSelection(),
		tableNode = selection.getTableNode(),
		matrix = tableNode.getMatrix(),
		oldSelection = [ selection.fromCol, selection.fromRow, selection.toCol, selection.toRow ],
		newRow, cells, itemIndex, indexAfterAddditions;

	if ( !( selection instanceof ve.dm.TableSelection ) ) {
		return false;
	}

	if ( mode === 'row' ) {
		indexAfterAddditions = selection.toRow;
		for ( itemIndex = selection.fromRow; itemIndex <= indexAfterAddditions; itemIndex++ ) {
			cells = matrix.getRow( itemIndex );
			// If row contains cells that span over multiple rows, we cannot duplicate - alert user
			if ( this.hasMutliRowCells( cells ) ) {
				OO.ui.alert( OO.ui.deferMsg( 'bs-vec-row-duplicate-has-merged' ) );
				return;
			}
			newRow = {
				row: ve.dm.TableRowNode.static.applyStylings( matrix.getRowNode( itemIndex ), {} ),
				cells: cells
			};
			// Don't ask...
			this.replace( selection, 'row', itemIndex, newRow );
			this.insertRowOrCol( tableNode, 'row', itemIndex, 'after', null, newRow );
			// Number of rows changed
			itemIndex++;
			indexAfterAddditions++;
		}
	} else if ( mode === 'col' ) {
		indexAfterAddditions = selection.toCol;
		for ( itemIndex = selection.fromCol; itemIndex <= indexAfterAddditions; itemIndex++ ) {
			cells = matrix.getColumn( itemIndex );
			this.replace( selection, mode, itemIndex, { cells: cells }  );
			this.insertRowOrCol( tableNode, mode, itemIndex, 'after', null, { cells: cells } );
			itemIndex++;
			indexAfterAddditions++;
		}
	}
	this.restoreSelection( surfaceModel, selection, tableNode, oldSelection );
};

bs.vec.ui.TableAction.prototype.replace = function( selection, mode, index, replacement ) {
	var tableNode = selection.getTableNode(),
		matrix = tableNode.getMatrix(),
		position;

	if ( !( selection instanceof ve.dm.TableSelection ) ) {
		return false;
	}
	this.deleteRowsOrColumns( matrix, mode, index, index );


	position = 'after';
	if ( index === 0 ) {
		position = 'before';
	} else {
		index--;
	}

	this.insertRowOrCol( tableNode, mode, index, position, null, replacement );
};

ve.ui.actionFactory.register( bs.vec.ui.TableAction );
