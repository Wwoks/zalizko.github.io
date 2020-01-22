<?php

namespace BlueSpice\Calumma;

use BlueSpice\Services;
use Config;
use MediaWiki\Linker\LinkRenderer;

abstract class BreadcrumbRootNodeBase implements IBreadcrumbRootNode {

	/**
	 *
	 * @var Config
	 */
	protected $config = null;

	/**
	 *
	 * @var LinkRenderer
	 */
	protected $linkRenderer = null;

	/**
	 *
	 * @param Config $config
	 * @param LinkRenderer $linkRenderer
	 */
	public function __construct( $config, $linkRenderer ) {
		$this->config = $config;
		$this->linkRenderer = $linkRenderer;
	}

	/**
	 *
	 * @param Config $config
	 * @return IBreadCrumpRootNode
	 */
	public static function factory( $config ) {
		$linkRenderer = Services::getInstance()->getLinkRenderer();

		return new static( $config, $linkRenderer );
	}
}
