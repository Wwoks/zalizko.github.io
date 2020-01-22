<?php

namespace BlueSpice\Calumma\Renderer\PageHeader;

use Html;
use Config;
use HtmlArmor;
use WikiPage;
use User;
use IContextSource;
use RequestContext;
use BlueSpice\Services;
use BlueSpice\UtilityFactory;
use BlueSpice\Renderer;
use BlueSpice\Renderer\Params;
use MediaWiki\Linker\LinkRenderer;
use Revision;

class LastEdit extends Renderer {

	/**
	 *
	 * @var UtilityFactory
	 */
	protected $util = null;

	/**
	 * LastEdit constructor.
	 * @param Config $config
	 * @param Params $params
	 * @param LinkRenderer|null $linkRenderer
	 * @param IContextSource|null $context
	 * @param string $name
	 * @param UtilityFactory|null $util
	 */
	protected function __construct( Config $config, Params $params,
		LinkRenderer $linkRenderer = null, IContextSource $context = null,
		$name = '', UtilityFactory $util = null ) {
		parent::__construct( $config, $params, $linkRenderer, $context, $name );

		$this->util = $util;
	}

	/**
	 *
	 * @param string $name
	 * @param Services $services
	 * @param Config $config
	 * @param Params $params
	 * @param IContextSource|null $context
	 * @param LinkRenderer|null $linkRenderer
	 * @param UtilityFactory|null $util
	 * @return Renderer
	 */
	public static function factory( $name, Services $services, Config $config, Params $params,
		IContextSource $context = null, LinkRenderer $linkRenderer = null,
		UtilityFactory $util = null ) {
		if ( !$context ) {
			$context = $params->get(
				static::PARAM_CONTEXT,
				false
			);
			if ( !$context instanceof IContextSource ) {
				$context = RequestContext::getMain();
			}
		}
		if ( !$linkRenderer ) {
			$linkRenderer = $services->getLinkRenderer();
		}
		if ( !$util ) {
			$util = $services->getBSUtilityFactory();
		}

		return new static( $config, $params, $linkRenderer, $context, $name, $util );
	}

	/**
	 * @return string
	 */
	public function render() {
		$html = '';
		$title = $this->getContext()->getTitle();
		if ( !$title || $title->isSpecialPage() ) {
			return $html;
		}

		$wikiPage = WikiPage::factory( $title );
		$currentRevision = $this->getCurrentRevision( $wikiPage );

		if ( !$currentRevision ) {
			return '';
		}

		$diffLink = $this->makeLastEditDiffLink( $wikiPage, $currentRevision );
		$userLink = $this->makeLastEditorLink( $wikiPage, $currentRevision );

		$lastEditText = $this->msg(
			'bs-calumma-page-last-edit-text',
			$diffLink,
			$userLink
		)->text();

		return Html::rawElement(
			'span',
			[ 'class' => "bs-page-last-edit" ],
			$lastEditText
		);
	}

	/**
	 *
	 * @param WikiPage $wikiPage
	 * @param Revision $currentRevision
	 * @return string
	 */
	protected function makeLastEditDiffLink( WikiPage $wikiPage, $currentRevision ) {
		$rawTimestamp = $currentRevision->getTimestamp();
		$formattedDate = $this->getContext()->getLanguage()->date( $rawTimestamp );

		$unixTS = wfTimestamp( TS_UNIX, $rawTimestamp );
		$period = time() - $unixTS;

		$chosenIntervals = [];
		/* interval: 'years', 'days', 'hours', 'minutes' */

		/* more than one year */
		if ( ( $period > ( 365 * 24 * 60 * 60 ) ) ) {
			$chosenIntervals = [ 'years' ];
		}

		/* between one day and one year */
		if ( ( $period > ( 24 * 60 * 60 ) ) && ( $period < ( 365 * 24 * 60 * 60 ) ) ) {
			$chosenIntervals = [ 'days' ];
		}

		/* between one day and one hour */
		if ( ( $period > ( 60 * 60 ) ) && ( $period < ( 24 * 60 * 60 ) ) ) {
			$chosenIntervals = [ 'hours' ];
		}

		/* less than one hour */
		if ( ( $period < ( 60 * 60 ) ) ) {
			$chosenIntervals = [ 'minutes' ];
		}

		if ( $period < 60 ) {
			$chosenIntervals[] = 'seconds';
		}

		$formattedPeriod = $this->getContext()->getLanguage()->formatDuration(
			$period,
			$chosenIntervals
		);

		$diffLink = $this->linkRenderer->makeLink(
			$wikiPage->getTitle(),
			new HtmlArmor( $formattedPeriod ),
			[
				'title' => $formattedDate
			],
			[
				'oldid' => $currentRevision->getId(),
				'diff' => 'prev'
			]
		);

		return $diffLink;
	}

	/**
	 *
	 * @param WikiPage $wikiPage
	 * @param Revision $currentRevision
	 * @return string
	 */
	protected function makeLastEditorLink( WikiPage $wikiPage, $currentRevision ) {
		/* Main_page is created with user id 0 */
		if ( !$currentRevision->getUser() ) {
			return $this->msg( 'bs-calumma-page-last-edit-by-system-user' )->plain();
		}

		$user = User::newFromId( $currentRevision->getUser() );

		$userLink = $this->linkRenderer->makeLink(
			$user->getUserPage(),
			$this->util->getUserHelper( $user )->getDisplayName()
		);

		return $userLink;
	}

	/**
	 * @param WikiPage $wikiPage
	 * @return Revision|null
	 */
	protected function getCurrentRevision( WikiPage $wikiPage ) {
		return $wikiPage->getRevision();
	}
}
