<?php

namespace BlueSpice\WhoIsOnline\Tag;

use Config;
use Html;
use RequestContext;
use Parser;
use PPFrame;
use BlueSpice\Services;
use BlueSpice\Tag\Handler;
use BlueSpice\Renderer\Params;
use BlueSpice\WhoIsOnline\Renderer\UserList;
use BlueSpice\WhoIsOnline\Tracer;

class PopUpHandler extends Handler {

	/**
	 *
	 * @var int
	 */
	protected static $idCounter = 0;

	/**
	 *
	 * @var Tracer
	 */
	protected $tracer = null;

	/**
	 *
	 * @param string $processedInput
	 * @param array $processedArgs
	 * @param Parser $parser
	 * @param PPFrame $frame
	 * @param Tracer|null $tracer
	 */
	public function __construct( $processedInput, array $processedArgs,
		Parser $parser, PPFrame $frame, Tracer $tracer = null ) {
		parent::__construct( $processedInput, $processedArgs, $parser, $frame );
		if ( !$tracer ) {
			$tracer = Services::getInstance()->getService( 'BSWhoIsOnlineTracer' );
		}
		$this->tracer = $tracer;
	}

	/**
	 *
	 * @return string
	 */
	public function handle() {
		$recordSet = $this->tracer->getTracedRecords();

		$this->parser->getOutput()->setProperty( 'bs-tag-userscount', 1 );
		$targetId = $this->getTargetId();
		$maxHeigt = $this->getConfig()->get( 'WhoIsOnlineLimitCount' ) * 20;
		$anchor = Html::element( 'a', [
				'class' => 'bs-tooltip-link',
				'data-bs-tt-title' => wfMessage( 'bs-whoisonline-widget-title' ),
				'data-bs-tt-target' => "$targetId-target",
				'data-bs-tt-maxheight' => $maxHeigt,
				'id' => $targetId,
			],
			( empty( $this->processedArgs[PopUp::PARAM_ANCHOR_TEXT] )
				? wfMessage( 'bs-whoisonline-widget-title' )
				: $this->processedArgs[PopUp::PARAM_ANCHOR_TEXT] )
		);

		$portlet = Services::getInstance()->getBSRendererFactory()->get(
			'whoisonline-userlist',
			new Params( [
				UserList::PARAM_RECORD_SET => $recordSet
			] ),
			RequestContext::getMain()
		);

		$target = Html::rawElement( 'div', [
				'class' => 'bs-tooltip-body bs-whoisonline-portlet',
				'id' => "$targetId-target"
			],
			$portlet->render()
		);

		return $anchor . Html::rawElement(
			'div',
			[ 'class' => 'bs-tooltip' ],
			$target
		);
	}

	/**
	 *
	 * @return string
	 */
	protected function getTargetId() {
		return 'bs-wo-link-' . static::$idCounter++;
	}

	/**
	 *
	 * @return Config
	 */
	protected function getConfig() {
		return Services::getInstance()->getConfigFactory()->makeConfig( 'bsg' );
	}
}
