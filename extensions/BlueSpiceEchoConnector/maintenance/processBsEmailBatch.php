<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once dirname( dirname( dirname( __DIR__ ) ) ) . '/maintenance/Maintenance.php';

/**
 * A maintenance script that processes email digest
 */
class ProcessBsEchoEmailBatch extends Maintenance {

	/**
	 * Max number of records to process at a time
	 * @var int
	 */
	protected $batchSize = 300;

	public function __construct() {
		parent::__construct();
		$this->addDescription( "Process email digest" );

		$this->addOption( "ignoreConfiguredSchedule", "Send all pending notifications immediately even if configured to be weekly or daily.", false, false, "i" );

		$this->requireExtension( 'Echo' );
	}

	public function execute() {
		global $wgEchoCluster;

		$ignoreConfiguredSchedule = $this->getOption( "ignoreConfiguredSchedule", 0 );

		$this->output( "Started processing... \n" );

		$startUserId = 0;
		$count = $this->batchSize;

		while ( $count === $this->batchSize ) {
			$count = 0;

			$res = \BlueSpice\EchoConnector\EchoEmailBatch::getUsersToNotify( $startUserId, $this->batchSize );

			$updated = false;
			foreach ( $res as $row ) {
				$userId = intval( $row->eeb_user_id );
				if ( $userId && $userId > $startUserId ) {
					$emailBatch = \BlueSpice\EchoConnector\EchoEmailBatch::newFromUserId( $userId, !$ignoreConfiguredSchedule );
					if ( $emailBatch ) {
						$this->output( "processing user_Id " . $userId . " \n" );
						$emailBatch->process();
					}
					$startUserId = $userId;
					$updated = true;
				}
				$count++;
			}
			wfWaitForSlaves( false, false, $wgEchoCluster );
			// This is required since we are updating user properties in main wikidb
			wfWaitForSlaves();

			// double check to make sure that the id is updated
			if ( !$updated ) {
				break;
			}
		}

		$this->output( "Completed \n" );
	}

}

$maintClass = "ProcessBsEchoEmailBatch";
require_once RUN_MAINTENANCE_IF_MAIN;
