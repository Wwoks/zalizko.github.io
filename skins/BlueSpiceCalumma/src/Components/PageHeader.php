<?php

namespace BlueSpice\Calumma\Components;

use Html;
use Title;
use Sanitizer;
use MediaWiki\MediaWikiServices;
use BlueSpice\SkinData;
use BlueSpice\RendererFactory;
use BlueSpice\Renderer\Params;
use BlueSpice\Renderer\NullRenderer;
use BlueSpice\Calumma\Renderer\PageHeader\LastEdit;
use BlueSpice\Calumma\Renderer\PageHeader\Category;
use BlueSpice\Calumma\TemplateComponent;
use BlueSpice\Services;

class PageHeader extends TemplateComponent {

	/**
	 *
	 * @return string
	 */
	protected function getTemplatePathName() {
		return 'Calumma.Components.PageHeader';
	}

	/**
	 *
	 * @return array
	 */
	protected function getTemplateArgs() {
		$args = parent::getTemplateArgs();

		$showContextBand = $this->showContextBand();
		$showQualificationBand = $this->showQualificationBand();
		$showMetaBand = $this->showMetaBand();

		$args = $this->getDefaultArgs();
		if ( $this->isEditMode() ) {
			$args['firstheading'] = $this->getFirstHeading();
			return $args;
		}

		$args = [
			'sitenotice' => $this->getSiteNotice(),
			'indicators' => $this->getIndicators(),
			'firstheading' => $this->getFirstHeading(),
			'lang' => $this->getPageLanguageCode(),
			'headerlinks' => $this->getHeaderLinks(),
			'breadcrumbs' => $showContextBand ? $this->getBreadCrumbs() : '',
			'namespaces' => $showContextBand ? $this->getSiteNamespaces() : '',
			'categories' => $showQualificationBand ? $this->getCategories() : '',
			'pageinfoelements' => $showMetaBand ? $this->getPageInfoElement() : '',
			'lastedit' => $showMetaBand ? $this->getLastEdit() : '',
			'watchaction' => $showMetaBand ? $this->getWatchAction() : '',
			'contextband' => $showContextBand,
			'qualificationband' => $showQualificationBand,
			'metaband' => $showMetaBand

		] + $args;

		return $args;
	}

	/**
	 *
	 * @return array
	 */
	protected function getDefaultArgs() {
		return [
			'sitenotice' => '',
			'indicators' => '',
			'firstheading' => '',
			'lang' => '',
			'headerlinks' => '',
			'breadcrumbs' => '',
			'namespaces' => '',
			'categories' => '',
			'pageinfoelements' => '',
			'lastedit' => '',
			'watchaction' => '',
			'edit' => '',
			'contextband' => false,
			'qualificationband' => false,
			'metaband' => false,
			'actionband' => false
		];
	}

	/**
	 *
	 * @return string
	 */
	protected function getSiteNamespaces() {
		return $this->getRendererFactory()->get(
			'pageheader-context',
			new Params( parent::getTemplateArgs() ),
			$this->getSkin()->getContext()
		)->render();
	}

	/**
	 *
	 * @return string
	 */
	protected function getPageInfoElement() {
		return $this->getRendererFactory()->get(
			'pageheader-pageinfo',
			new Params( parent::getTemplateArgs() ),
			$this->getSkin()->getContext()
		)->render();
	}

	/**
	 * @return string
	 */
	protected function getLastEdit() {
		$renderer = $this->getRendererFactory()->get(
			'pageheader-lastedit',
			new Params( parent::getTemplateArgs() ),
			$this->getSkin()->getContext()
		);

		if ( $renderer instanceof NullRenderer ) {
			$renderer = LastEdit::factory(
				'pageheader-lastedit',
				Services::getInstance(),
				MediaWikiServices::getInstance()->getConfigFactory()->makeConfig( 'bsg' ),
				new Params( parent::getTemplateArgs() )
			);
		}

		return $renderer->render();
	}

	/**
	 *
	 * @return string
	 */
	protected function getEditButton() {
		$params = parent::getTemplateArgs();
		$params[SkinData::FEATURED_ACTIONS] = $this->getSkinTemplate()->get(
			SkinData::FEATURED_ACTIONS
		);
		return $this->getRendererFactory()->get(
			'pageheader-editbutton',
			new Params( $params ),
			$this->getSkin()->getContext()
		)->render();
	}

	/**
	 *
	 * @return string
	 */
	protected function getSiteNotice() {
		return $this->getSkinTemplate()->get( 'sitenotice' );
	}

	/**
	 *
	 * @return string
	 */
	protected function getIndicators() {
		$out = '';
		$indicators = $this->getSkinTemplate()->get( 'indicators' );
		// Logic from `BaseTemplate`
		foreach ( $indicators as $id => $content ) {
			$out .= Html::rawElement(
				'div',
				[
					'id' => Sanitizer::escapeIdForAttribute( "mw-indicator-$id" ),
					'class' => 'mw-indicator',
				],
				$content
			) . "\n";
		}
		return $out;
	}

	/**
	 *
	 * @return string
	 */
	protected function getFirstHeading() {
		$titleText = $this->getSkinTemplate()->get( 'title' );
		// Might contain HTML elements e.g. by BlueSpiceRating
		// e.g. "<span>...</span>Some Page<a>...</a>"
		$strippedTitleText = strip_tags( $titleText );

		$currentTitle = $this->getSkin()->getTitle();
		$title = Title::newFromText( $strippedTitleText );
		// Only shorten if not already overwritten by another extension or `{{DISPLAYTITLE:...}}`

		if ( $title && $title->equals( $currentTitle ) ) {
			$titleText = str_replace(
				$strippedTitleText,
				$currentTitle->getSubpageText(),
				$titleText
			);
		}

		return $titleText;
	}

	/**
	 *
	 * @return string
	 */
	protected function getHeaderLinks() {
		return $this->getSkinTemplate()->get( 'headerlinks' );
	}

	/**
	 *
	 * @return string
	 */
	protected function getPageLanguageCode() {
		return $this->getSkin()->getTitle()->getPageViewLanguage()->getHtmlCode();
	}

	/**
	 *
	 * @return string
	 */
	protected function getBreadCrumbs() {
		return $this->getRendererFactory()->get(
			'pageheader-breadcrumb',
			new Params( parent::getTemplateArgs() ),
			$this->getSkin()->getContext()
		)->render();
	}

	/**
	 *
	 * @return string
	 */
	protected function getCategories() {
		if ( $this->hideCategories() ) {
			return '';
		}

		$args = parent::getTemplateArgs();
		$args[Category::PARAM_CATEGORY_NAMES] = $this->getSkin()->getOutput()
			->getCategories( 'normal' );
		$renderer = $this->getRendererFactory()->get(
			'pageheader-category',
			new Params( $args ),
			$this->getSkin()->getContext()
		);
		if ( $renderer instanceof NullRenderer ) {
			$renderer = Category::factory(
				'pageheader-category',
				Services::getInstance(),
				MediaWikiServices::getInstance()->getConfigFactory()->makeConfig( 'bsg' ),
				new Params( $args )
			);
		}
		return $renderer->render();
	}

	/**
	 *
	 * @return string
	 */
	protected function getWatchAction() {
		$contentNavigation = $this->getSkinTemplate()->get( 'content_navigation' );

		$watch = [];
		$text = '';
		$title = '';

		if ( isset( $contentNavigation['actions']['watch'] ) ) {
			$watch = $contentNavigation['actions']['watch'];
			$iconClass = 'bs-icon-star-empty';
			$text = wfMessage( 'bs-calumma-page-watch-text',
					$this->getSkin()->getTitle()->getPrefixedText() )->plain();
			$title = wfMessage( 'bs-calumma-page-watch-title',
					$this->getSkin()->getTitle()->getPrefixedText() )->plain();
		}

		if ( isset( $contentNavigation['actions']['unwatch'] ) ) {
			$watch = $contentNavigation['actions']['unwatch'];
			$iconClass = 'bs-icon-star-full';
			$text = wfMessage( 'bs-calumma-page-unwatch-text',
					$this->getSkin()->getTitle()->getPrefixedText() )->plain();
			$title = wfMessage( 'bs-calumma-page-unwatch-title',
					$this->getSkin()->getTitle()->getPrefixedText() )->plain();
		}

		if ( empty( $watch ) ) {
			return '';
		}

		$html = Html::openElement(
				'span',
				[
					'id' => $watch['id'],
					'class' => 'bs-page-watch'
				] );

		$html .= Html::openElement(
				'a',
				[
					'href' => $watch['href'],
					'class' => $watch['class'],
					'title' => $title
				]
			);
		$html .= Html::element(
				'i',
				[
					'class' => $iconClass
				] );
		$html .= Html::element(
				'span',
				[],
				$text
				);
		$html .= Html::closeElement( 'a' );

		$html .= Html::closeElement( 'span' );

		return $html;
	}

	/**
	 *
	 * @return RendererFactory
	 */
	protected function getRendererFactory() {
		return Services::getInstance()->getBSRendererFactory();
	}

	/**
	 *
	 * @return bool
	 */
	protected function showContextBand() {
		return true;
	}

	/**
	 *
	 * @return bool
	 */
	protected function showQualificationBand() {
		if ( $this->getSkin()->getTitle()->exists() === false ) {
			return false;
		}
		return true;
	}

	/**
	 *
	 * @return bool
	 */
	protected function showMetaBand() {
		$title = $this->getSkin()->getTitle();
		if ( $title->exists() === false ) {
			return false;
		}
		if ( $title->isTalkPage() ) {
			return false;
		}
		if ( $this->isHistoryView() ) {
			return false;
		}
		if ( $this->isDiffView() ) {
			return false;
		}

		return true;
	}

	/**
	 *
	 * @return bool
	 */
	protected function hideCategories() {
		if ( $this->getSkin()->getTitle()->isTalkPage() ) {
			return true;
		}
		if ( $this->isHistoryView() ) {
			return true;
		}
		if ( $this->isDiffView() ) {
			return true;
		}

		return false;
	}

	/**
	 *
	 * @return bool
	 */
	private function isDiffView() {
		return $this->getSkin()->getRequest()->getVal( 'diff', '-1' ) !== '-1';
	}

	/**
	 *
	 * @return bool
	 */
	private function isHistoryView() {
		return $this->getSkin()->getRequest()->getVal( 'action', 'view' ) === 'history';
	}

	/**
	 *
	 * @return bool
	 */
	private function isEditMode() {
		$action = $this->getSkin()->getRequest()->getVal( 'action', 'view' );
		$veAction = $this->getSkin()->getRequest()->getVal( 'veaction', '' );
		return $action === 'edit' || $veAction === 'edit';
	}

}
