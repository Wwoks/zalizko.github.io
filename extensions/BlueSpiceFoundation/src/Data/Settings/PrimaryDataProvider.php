<?php

namespace BlueSpice\Data\Settings;

use \BlueSpice\Data\IPrimaryDataProvider;

class PrimaryDataProvider implements IPrimaryDataProvider {

	/**
	 *
	 * @var \BlueSpice\Data\Record[]
	 */
	protected $data = [];

	/**
	 *
	 * @var \Wikimedia\Rdbms\IDatabase
	 */
	protected $db = null;

	/**
	 *
	 * @param \Wikimedia\Rdbms\IDatabase $db
	 */
	public function __construct( $db ) {
		$this->db = $db;
	}

	/**
	 *
	 * @param \BlueSpice\Data\ReaderParams $params
	 * @return Record[]
	 */
	public function makeData( $params ) {
		$this->data = [];
		// workaround for the upgrade process. The new settings cannot be
		// accessed before they are migrated
		if ( !$this->db->tableExists( 'bs_settings3', __METHOD__ ) ) {
			return $this->data;
		}

		$res = $this->db->select( 'bs_settings3', '*', '', __METHOD__ );
		foreach ( $res as $row ) {
			$this->appendRowToData( $row );
		}

		return $this->data;
	}

	/**
	 *
	 * @param \stdClass $row
	 */
	protected function appendRowToData( $row ) {
		$this->data[] = new Record( (object)[
			Record::NAME => $row->s_name,
			Record::VALUE => \FormatJson::decode( $row->s_value )
		] );
	}
}
