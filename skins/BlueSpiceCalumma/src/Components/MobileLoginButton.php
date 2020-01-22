<?php
namespace BlueSpice\Calumma\Components;

use SpecialPageFactory;

class MobileLoginButton extends \Skins\Chameleon\Components\Structure {

	/**
	 * The resulting HTML
	 * @return string
	 */
	public function getHtml() {
		if ( $this->getSkin()->getUser()->isLoggedIn()
			|| $this->getSkin()->getUser()->isAllowed( 'read' ) ) {
			return '';
		}

		$class = $this->getDomElement()->getAttribute( 'class' );
		$title = SpecialPageFactory::getTitleForAlias( 'Userlogin' );

		$html = \Html::openElement( 'a', [
			'href' => $title->getFullURL(),
			'title' => $title->getText(),
			'class' => ' ' . $class,
			'role' => 'button'
		] );

		$html .= \Html::openElement( 'i', [ 'class' => $class ] );
		$html .= \Html::closeElement( 'i' );

		$html .= \Html::closeElement( 'a' );

		return $html;
	}
}
