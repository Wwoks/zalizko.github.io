<?php

namespace BlueSpice;

use IContextSource;
use MessageLocalizer;
use Message;
use Config;

abstract class PageInfoElement implements IPageInfoElement, MessageLocalizer {

	/**
	 *
	 * @var IContextSource
	 */
	protected $context = null;

	/**
	 *
	 * @var Config
	 */
	protected $config = null;

	/**
	 *
	 * @param IContextSource $context
	 * @param Config $config
	 * @return IPageInfoElement
	 */
	public static function factory( IContextSource $context, Config $config ) {
		return new static( $context, $config );
	}

	/**
	 *
	 * @param IContextSource $context
	 * @param Config $config
	 */
	public function __construct( IContextSource $context, Config $config ) {
		$this->context = $context;
		$this->config = $config;
	}

	/**
	 *
	 * @return string
	 */
	public function getUrl() {
		return '';
	}

	/**
	 *
	 * @return int
	 */
	public function getPosition() {
		return 100;
	}

	/**
	 *
	 * @return string
	 */
	public function getType() {
		return IPageInfoElement::TYPE_TEXT;
	}

	/**
	 *
	 * @return string
	 */
	public function getHtmlClass() {
		return '';
	}

	/**
	 *
	 * @return string
	 */
	public function getHtmlId() {
		return '';
	}

	/**
	 * @return array with html attributes data-*
	 */
	public function getHtmlDataAttribs() {
		return [];
	}

	/**
	 *
	 * @return string
	 */
	public function getMenu() {
		return '';
	}

	/**
	 * Get a Message object with context set
	 * Parameters are the same as wfMessage()
	 *
	 * @param string|string[]|MessageSpecifier $key Message key, or array of keys,
	 *   or a MessageSpecifier.
	 * @param mixed $args,...
	 * @return Message
	 */
	public function msg( $key ) {
		return call_user_func_array(
			[ $this->context, 'msg' ],
			func_get_args()
		);
	}
}
