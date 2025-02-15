<?php

namespace BlueSpice\TagCloud\Data\TagCloud\Category;

use BlueSpice\TagCloud\Data\TagCloud\IStore as ITagCloudStore;
use BlueSpice\TagCloud\Context;
use BlueSpice\Services;

class Store implements ITagCloudStore {

	/**
	 *
	 * @var Context
	 */
	protected $context = null;

	/**
	 *
	 * @param Context $context
	 */
	public function __construct( Context $context ) {
		$this->context = $context;
		$this->loadBalancer = Services::getInstance()->getDBLoadBalancer();
	}

	/**
	 *
	 * @return Reader
	 */
	public function getReader() {
		return new Reader( $this->loadBalancer, $this->context );
	}

	/**
	 *
	 * @return Writer
	 */
	public function getWriter() {
		return new Writer(
			$this->getReader(),
			$this->loadBalancer,
			$this->context
		);
	}

	/**
	 *
	 * @param array $params
	 * @return ReaderParams
	 */
	public function makeReaderParams( array $params = [] ) {
		return new ReaderParams( $params );
	}
}
