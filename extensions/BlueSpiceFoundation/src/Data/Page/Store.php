<?php

namespace BlueSpice\Data\Page;

use IContextSource;
use Wikimedia\Rdbms\LoadBalancer;
use BlueSpice\Data\NoWriterException;
use BlueSpice\Data\IStore;

class Store implements IStore {

	/**
	 *
	 * @var IContextSource
	 */
	protected $context = null;

	/**
	 * @var LoadBalancer
	 */
	protected $loadBalancer;

	/**
	 *
	 * @param IContextSource $context
	 * @param LoadBalancer $loadBalancer
	 */
	public function __construct( $context, $loadBalancer ) {
		$this->context = $context;
		$this->loadBalancer = $loadBalancer;
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
	 * @throws NoWriterException
	 */
	public function getWriter() {
		throw new NoWriterException();
	}
}
