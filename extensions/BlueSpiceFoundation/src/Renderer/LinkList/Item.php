<?php

namespace BlueSpice\Renderer\LinkList;

use Config;
use IContextSource;
use HtmlArmor;
use MediaWiki\Linker\LinkRenderer;
use MediaWiki\Linker\LinkTarget;
use BlueSpice\Renderer\Params;

class Item extends \BlueSpice\Renderer\SimpleList\Item {
	const PARAM_TARGET = 'target';

	/**
	 * Constructor
	 * @param Config $config
	 * @param Params $params
	 * @param LinkRenderer|null $linkRenderer
	 * @param IContextSource|null $context
	 * @param string $name | ''
	 */
	protected function __construct( Config $config, Params $params,
		LinkRenderer $linkRenderer = null, IContextSource $context = null,
		$name = '' ) {
		parent::__construct( $config, $params, $linkRenderer, $context, $name );
		$this->args[static::PARAM_TARGET] = $params->get(
			static::PARAM_TARGET,
			false
		);
	}

	protected function makeTagContent() {
		$content = '';
		$text = new HtmlArmor( $this->args[static::PARAM_TEXT] );
		if ( $this->args[static::PARAM_TARGET] instanceof LinkTarget ) {
			$content .= $this->linkRenderer->makeLink(
				$this->args[static::PARAM_TARGET],
				$text
			);
		} else {
			$content .= HtmlArmor::getHtml( $text );
		}
		return $content;
	}
}
