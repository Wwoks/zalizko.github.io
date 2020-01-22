<?php

namespace BlueSpice\VisualEditorConnector\Hook\BeforePageDisplay;

use BlueSpice\Hook\BeforePageDisplay;

class AddSaveProcessOverride extends BeforePageDisplay {

	protected function doProcess() {
		$this->out->addModules(
			"ext.bluespice.visualEditorConnector.SaveProcessOverride"
		);

		$this->out->addJsConfigVars(
			'bsVECSimpleSaveProcess',
			$this->getConfig()->get( 'VisualEditorConnectorSimpleSaveProcess' )
		);
	}
}
