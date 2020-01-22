<?php


namespace BlueSpice\WhoIsOnline\Hook\BeforePageDisplay;

class AddModules extends \BlueSpice\Hook\BeforePageDisplay {

	/**
	 *
	 * @return bool
	 */
	protected function skipProcessing() {
		return !$this->out->getTitle()->userCan( 'read' );
	}

	/**
	 *
	 * @return bool
	 */
	protected function doProcess() {
		$this->out->addModules( 'ext.bluespice.whoisonline' );
		return true;
	}

}
