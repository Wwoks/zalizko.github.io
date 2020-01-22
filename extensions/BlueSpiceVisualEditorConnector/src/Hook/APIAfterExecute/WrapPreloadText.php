<?php

namespace BlueSpice\VisualEditorConnector\Hook\APIAfterExecute;

use BlueSpice\Hook\APIAfterExecute;

class WrapPreloadText extends APIAfterExecute {

	protected function doProcess() {
		$data = $this->module->getResult()->getResultData();

		// IE11 requires a root element. We need to use an element that is not supported in
		// wikitext. This is stripped by VE after the preload content is loaded.
		$newContent = \Html::rawElement(
				'body',
				[],
				$data['visualeditor']['content']
		);

		$this->module->getResult()->removeValue( 'visualeditor', 'content' );
		$this->module->getResult()->addValue( 'visualeditor', 'content', $newContent );
	}

	protected function skipProcessing() {
		if ( parent::skipProcessing() ) {
			return true;
		}

		if ( $this->module instanceof \ApiVisualEditor === false ) {
			return true;
		}

		$params = $this->module->extractRequestParams();
		if ( isset( $params['paction'] ) && $params['paction'] !== 'metadata' ) {
			return true;
		}

		$data = $this->module->getResult()->getResultData();
		if ( empty( $data['visualeditor'] ) ) {
			return true;
		}
	}

}
