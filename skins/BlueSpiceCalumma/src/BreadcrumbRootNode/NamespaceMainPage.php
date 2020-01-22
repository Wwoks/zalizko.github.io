<?php

namespace BlueSpice\Calumma\BreadcrumbRootNode;

use BlueSpice\Calumma\BreadcrumbRootNodeBase;
use Title;
use Message;

class NamespaceMainPage extends BreadcrumbRootNodeBase {

	/**
	 *
	 * @param Title $title
	 * @return string
	 */
	public function getHtml( $title ) {
		if ( $title->isSpecialPage() ) {
			return '';
		}

		$namespaceMainPage = Title::makeTitle(
			$title->getNamespace(),
			Title::newMainPage()->getText()
		);

		$nsText = str_replace( '_', ' ', $title->getNsText() );
		if ( $title->getNamespace() === NS_MAIN ) {
			$nsText = Message::newFromKey( 'bs-ns_main' );
		}

		return $this->linkRenderer->makeLink(
			$namespaceMainPage,
			$nsText,
			[
				'class' => 'btn',
				'role' => 'button'
			]
		);
	}

}
