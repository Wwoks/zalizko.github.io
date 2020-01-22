<?php

namespace BlueSpice\WhoIsOnline\Data\Tracer;

use BlueSpice\Services;
use BlueSpice\WhoIsOnline\Data\Writer;

class Store extends \BlueSpice\WhoIsOnline\Data\Store {

	/**
	 *
	 * @return Reader
	 */
	public function getReader() {
		return new Reader(
			Services::getInstance()->getDBLoadBalancer()
		);
	}

	/**
	 *
	 * @return Writer
	 */
	public function getWriter() {
		return new Writer(
			$this->getReader(),
			Services::getInstance()->getDBLoadBalancer()
		);
	}
}
