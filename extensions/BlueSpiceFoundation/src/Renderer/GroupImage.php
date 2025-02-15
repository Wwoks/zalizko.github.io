<?php
namespace BlueSpice\Renderer;

use Config;
use IContextSource;
use BlueSpice\Utility\CacheHelper;
use MediaWiki\Linker\LinkRenderer;
use BlueSpice\DynamicFileDispatcher\Params as DFDParams;
use BlueSpice\DynamicFileDispatcher\GroupImage as DFDGroupImage;
use BlueSpice\Services;

class GroupImage extends \BlueSpice\TemplateRenderer {
	const PARAM_WIDTH = 'width';
	const PARAM_HEIGHT = 'height';
	const PARAM_GROUP = 'group';

	/**
	 *
	 * @var string
	 */
	protected $group = '';

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

		$this->group = $params->get( static::PARAM_GROUP, 'unknown' );
		$this->args[static::PARAM_WIDTH] = $params->get( static::PARAM_WIDTH, 50 );
		$this->args[static::PARAM_HEIGHT] = $params->get( static::PARAM_HEIGHT, 50 );

		if ( empty( $this->args[static::PARAM_CLASS] ) ) {
			$this->args[static::PARAM_CLASS] = '';
		}
		$this->args[static::PARAM_CLASS] .= ' bs-groupminiprofile';

		$message = $this->msg( 'group-' . $this->group );
		if ( !$message->exists() ) {
			$message = new \RawMessage( $this->group );
		}

		$this->args['groupname'] = $this->group;
		$this->args['imagesrc'] = $this->group;
		$this->args['imagetitle'] = $message->text();
		$this->args['imagealt'] = $message->text();
	}

	/**
	 *
	 * @return string
	 */
	public function getGroup() {
		return $this->group;
	}

	/**
	 * Returns the template's name
	 * @return string
	 */
	public function getTemplateName() {
		return "BlueSpiceFoundation.GroupImage";
	}

	/**
	 *
	 * @param string $val
	 * @return string
	 */
	protected function render_imagesrc( $val ) {
		$params = [
			DFDParams::MODULE => DFDGroupImage::MODULE_NAME,
			DFDGroupImage::GROUP => $val,
			DFDGroupImage::WIDTH => $this->args[static::PARAM_WIDTH],
			DFDGroupImage::HEIGHT => $this->args[static::PARAM_HEIGHT]
		];

		$dfdUrlBuilder = Services::getInstance()
			->getBSDynamicFileDispatcherUrlBuilder();
		return $dfdUrlBuilder->build( new DFDParams( $params ) );
	}

	/**
	 *
	 * @return string
	 */
	protected function getCacheKey() {
		return $this->getCacheHelper()->getCacheKey(
			$this->getTemplateName(),
			$this->group,
			$this->args[static::PARAM_WIDTH],
			$this->args[static::PARAM_HEIGHT]
		);
	}
}
