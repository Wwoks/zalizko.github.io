<?php

namespace BlueSpice\Calumma\BreadcrumbRootNode;

use BlueSpice\Calumma\BreadcrumbRootNodeBase;
use SpecialPageFactory;
use Title;

class SpecialSpecialPages extends BreadcrumbRootNodeBase {

	/**
	 *
	 * @param Title $title
	 * @return string
	 */
	public function getHtml( $title ) {
		if ( !$title->isSpecialPage() ) {
			return '';
		}

		$specialSpecialpages = SpecialPageFactory::getTitleForAlias( 'Specialpages' );
		if ( $title->equals( $specialSpecialpages ) ) {
			return '';
		}

		return $this->linkRenderer->makeLink(
			$specialSpecialpages,
			$specialSpecialpages->getText(),
			[
				'class' => 'btn',
				'role' => 'button'
			]
		);
	}

}
