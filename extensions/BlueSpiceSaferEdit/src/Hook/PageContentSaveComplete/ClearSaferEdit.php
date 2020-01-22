<?php

namespace BlueSpice\SaferEdit\Hook\PageContentSaveComplete;

use BlueSpice\Hook\PageContentSaveComplete;

class ClearSaferEdit extends PageContentSaveComplete {

	protected function doProcess() {
		$seManager = $this->getServices()->getService( 'BSSaferEditManager' );
		$title = $this->wikipage->getTitle();
		return $seManager->doClearSaferEdit( $this->user, $title );
	}
}
