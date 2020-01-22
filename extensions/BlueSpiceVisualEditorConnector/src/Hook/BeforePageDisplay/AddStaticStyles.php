<?php

namespace BlueSpice\VisualEditorConnector\Hook\BeforePageDisplay;

use BlueSpice\Hook\BeforePageDisplay;

class AddStaticStyles extends BeforePageDisplay {

	protected function doProcess() {
		$this->out->addModuleStyles( 'ext.bluespice.visualEditorConnector.colors' );
		return true;
	}

}
