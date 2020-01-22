<?php

namespace BlueSpice\Calumma\Panel;

use BlueSpice\SkinData;
use BlueSpice\Calumma\Components\SimpleLinkListGroup;
use BlueSpice\Calumma\Components\CollapsibleGroup;

class GlobalActions extends BasePanel {

	/**
	 *
	 * @var \SkinTemplate
	 */
	protected $skintemplate = null;

	/**
	 *
	 * @var string
	 */
	protected $sectionId = '';

	/**
	 *
	 * @param SkinTemplate $skintemplate
	 */
	public function __construct( $skintemplate ) {
		$this->skintemplate = $skintemplate;
	}

	/**
	 *
	 * @return bool
	 */
	public function isEmpty() {
		return empty( $this->skintemplate->get( SkinData::GLOBAL_ACTIONS ) )
			&& empty( $this->skintemplate->get( SkinData::ADMIN_LINKS ) );
	}

	/**
	 *
	 * @return string
	 */
	public function getBody() {
		$globalActions = $this->sortLinksAlphabetically(
				$this->skintemplate->get( SkinData::GLOBAL_ACTIONS )
			);

		$sections = [
			'bs-sitenav-globalactions-section-globalactions' => $globalActions
		];

		if ( $this->skintemplate->getSkin()->getUser()->isLoggedIn() ) {
			$management = $this->sortLinksAlphabetically(
					$this->skintemplate->get( SkinData::ADMIN_LINKS )
				);

			$sections += [
				'bs-sitenav-globalactions-section-management' => $management
			];
		}

		$html = '';

		foreach ( $sections as $section => $links ) {
			$linklistgroup = new SimpleLinkListGroup( array_values( $links ) );

			$this->sectionId = str_replace( ' ', '-', $section );

			$collapsibleGroup = new CollapsibleGroup( [
				'id' => $this->sectionId,
				'title' => wfMessage( $section ),
				'content' => $linklistgroup->getHtml(),
				'collapse' => $this->getPanelCollapseState()
			] );

			$html .= $collapsibleGroup->getHtml();
		}

		return $html;
	}

	/**
	 *
	 * @return string
	 */
	public function getHtmlId() {
		return 'bs-globalactions';
	}

	/**
	 *
	 * @return \Message
	 */
	public function getTitleMessage() {
		return wfMessage( 'bs-sitenav-globalactions-title' );
	}

	/**
	 *
	 * @return array
	 */
	public function getContainerClasses() {
		return [ 'calumma-navigation-mobile-hidden' ];
	}

	/**
	 *
	 * @param array $links
	 * @return array
	 */
	protected function sortLinksAlphabetically( $links ) {
		$helper = [];

		foreach ( $links as $link ) {
			if ( $link['text'] instanceof \Message ) {
				$text = $link['text']->text();
				$helper[$text] = $link;
			} else {
				$helper[$link['text']] = $link;
			}
		}

		ksort( $helper );

		return array_values( $helper );
	}

	/**
	 *
	 * @return bool
	 */
	public function getPanelCollapseState() {
		if ( $this->sectionId === 'bs-sitenav-globalactions-section-globalactions' ) {
			return false;
		}
		if ( $this->sectionId === 'bs-sitenav-globalactions-section-management' ) {
			return false;
		}
	}
}
