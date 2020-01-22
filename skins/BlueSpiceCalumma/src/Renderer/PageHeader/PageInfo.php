<?php

namespace BlueSpice\Calumma\Renderer\PageHeader;

use Config;
use IContextSource;
use RequestContext;
use Html;
use MediaWiki\Linker\LinkRenderer;
use BlueSpice\Services;
use BlueSpice\PageInfoElementFactory;
use BlueSpice\Renderer\Params;
use BlueSpice\Renderer;
use BlueSpice\Calumma\PageInfoSentenceBuilder;

class PageInfo extends Renderer {

	/**
	 *
	 * @var PageInfoElementFactory
	 */
	protected $factory = null;

	/**
	 *
	 * @var PageInfoSentenceBuilder
	 */
	protected $builder = null;

	/**
	 * Constructor
	 * @param Config $config
	 * @param Params $params
	 * @param LinkRenderer|null $linkRenderer
	 * @param IContextSource|null $context
	 * @param string $name | ''
	 * @param PageInfoElementFactory|null $factory
	 * @param PageInfoSentenceBuilder|null $builder
	 */
	protected function __construct( Config $config, Params $params,
		LinkRenderer $linkRenderer = null, IContextSource $context = null,
		$name = '', PageInfoElementFactory $factory = null,
		PageInfoSentenceBuilder $builder = null ) {
		parent::__construct( $config, $params, $linkRenderer, $context, $name );

		$this->factory = $factory;
		$this->builder = $builder;
	}

	/**
	 *
	 * @param string $name
	 * @param Services $services
	 * @param Config $config
	 * @param Params $params
	 * @param IContextSource|null $context
	 * @param LinkRenderer|null $linkRenderer
	 * @param PageInfoElementFactory|null $factory
	 * @param PageInfoSentenceBuilder|null $builder
	 * @return Renderer
	 */
	public static function factory( $name, Services $services, Config $config, Params $params,
		IContextSource $context = null, LinkRenderer $linkRenderer = null,
		PageInfoElementFactory $factory = null, PageInfoSentenceBuilder $builder = null ) {
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
		if ( !$factory ) {
			$factory = $services->getBSPageInfoElementFactory();
		}
		if ( !$builder ) {
			$builder = new PageInfoSentenceBuilder(
				$context
			);
		}
		return new static(
				$config, $params, $linkRenderer,
				$context, $name, $factory, $builder
			);
	}

	/**
	 * @return string
	 */
	public function render() {
		$title = $this->getContext()->getTitle();
		if ( !$title || !$title->exists() || $title->isSpecialPage() ) {
			return '';
		}

		$elements = $this->factory->getAll( $this->getContext() );
		if ( empty( $elements ) ) {
			return '';
		}

		$html = Html::openElement( 'ul' );

		$html .= Html::openElement(
				'li',
				[
					'class' => 'bs-page-info-element-button-col'
				]
		);
		$html .= $this->makeActionButton();
		$html .= Html::closeElement( 'li' );

		$html .= Html::openElement(
				'li',
				[
					'class' => 'bs-page-info-element-text-col'
				]
		);
		$html .= $this->builder->build(
			$elements,
			$this->getContext()->getTitle(),
			$this->getContext()->getUser()
		);
		$html .= Html::closeElement( 'li' );

		$html .= Html::closeElement( 'ul' );

		return $html;
	}

	/**
	 *
	 * @return string
	 */
	private function makeActionButton() {
		$html = '';

		$html .= Html::openElement( 'a', [
			'id' => 'pageinfo-qm-panel',
			'class' => 'bs-page-info-element-button',
			'title' => $this->msg( 'bs-calumma-pageinfo-qm-button-tooltip' )->plain(),
			'aria-label' => $this->msg( 'bs-calumma-pageinfo-qm-button-tooltip' )->plain()
		] );

		$html .= Html::element( 'i' );
		$html .= Html::closeElement( 'a' );

		return $html;
	}

}
