<?php

namespace BlueSpice\VisualEditorConnector\Hook\BeforePageDisplay;

use BlueSpice\Hook\BeforePageDisplay;
use BlueSpice\ExtensionAttributeBasedRegistry;

class AddModules extends BeforePageDisplay {

	/**
	 * Skip if not in visual edit mode or Extension disabled
	 * @return bool
	 */
	protected function skipProcessing() {
		if ( $this->getConfig()->get( 'VisualEditorConnectorEnableVisualEditor' ) ) {
			return false;
		}
		return true;
	}

	protected function doProcess() {
		$registry = new ExtensionAttributeBasedRegistry(
			'BlueSpiceVisualEditorConnectorPluginModules'
		);

		$pluginModules = [];
		foreach ( $registry->getAllKeys() as $key ) {
			$moduleName = $registry->getValue( $key );
			$pluginModules[] = $moduleName;
		}

		$this->out->addModules( [
			"ext.bluespice.visualEditorConnector",
			"ext.bluespice.visualEditorConnector.styleInspector",
			'ext.bluespice.visualEditorConnector.textColor'
		] );
		$this->out->addJsConfigVars(
			'bsVECColorPickerColors',
			$this->getConfig()->get( 'VisualEditorConnectorColorPickerColors' )
		);
		$this->out->addJsConfigVars(
			'bsVECColorPickerColorsBackground',
			$this->getConfig()->get( 'VisualEditorConnectorColorPickerColorsBackground' )
		);

		$this->out->addModules(
			'ext.bluespice.visualEditorConnector.overrides'
		);
		$this->out->addJsConfigVars( 'bsVECPluginModules', $pluginModules );

		$tableStyles = $this->getConfig()->get( 'VisualEditorConnectorTableStyleRegistry' );
		$this->out->addJsConfigVars( 'bsgVisualEditorConnectorTableStyleRegistry', $tableStyles );

		$tagRegistry = new ExtensionAttributeBasedRegistry(
			'BlueSpiceVisualEditorConnectorTagDefinitions'
		);
		$tagDefinitions = [];
		foreach ( $tagRegistry->getAllKeys() as $key ) {
			$moduleName = $tagRegistry->getValue( $key );
			$tagDefinitions[] = $moduleName;
		}

		$this->out->addModules(
			'ext.bluespice.visualEditorConnector.tags'
		);
		$this->out->addJsConfigVars( 'bsVECTagDefinitions', $tagDefinitions );

		$uploadType = $this->getConfig()->get( 'VisualEditorConnectorUploadDialogType' );
		$this->out->addJsConfigVars( 'bsVECUploadType', $uploadType );
	}

}
