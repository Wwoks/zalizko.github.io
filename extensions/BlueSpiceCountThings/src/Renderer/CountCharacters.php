<?php

namespace BlueSpice\CountThings\Renderer;

use Config;
use IContextSource;
use BlueSpice\Utility\CacheHelper;
use MediaWiki\Linker\LinkRenderer;
use BlueSpice\TemplateRenderer;
use BlueSpice\Renderer\Params;

class CountCharacters extends TemplateRenderer {

	const TITLE = 'title';
	const TITLELINK = 'titlelink';

	/**
	 * Constructor
	 * @param Config $config
	 * @param Params $params
	 * @param LinkRenderer|null $linkRenderer
	 * @param IContextSource|null $context
	 * @param string $name | ''
	 * @param CacheHelper|null $cacheHelper
	 */
	protected function __construct( Config $config, Params $params,
		LinkRenderer $linkRenderer = null, IContextSource $context = null,
		$name = '', CacheHelper $cacheHelper = null ) {
		parent::__construct(
			$config,
			$params,
			$linkRenderer,
			$context,
			$name,
			$cacheHelper
		);

		$this->args[static::TITLE] = $params->get( static::TITLE, '' );
		$this->args[static::TITLELINK] = $params->get( static::TITLELINK, '' );
	}

	public function getTemplateName() {
		return 'BlueSpiceCountThings.CountCharacters';
	}

}
