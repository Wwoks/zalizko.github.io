<?php
/**
 * Maintenance script to recover pages and revisions deleted by namespacemanager
 *
 * @file
 * @ingroup Maintenance
 * @author Patric Wirth
 * @license GPL-3.0-only
 */

// TODO: check if namespace (newns) exists
// TODO: write log
// TODO: add some logic
$options = [ 'help', 'execute', 'oldns', 'newns' ];
require_once 'BSMaintenance.php';
print_r( $options );

$bDry = true;
if ( isset( $options['execute'] ) ) {
	$bDry = false;
}

if ( isset( $options['help'] ) ) {
	showHelp();
} else {
	if ( isset( $options['oldns'] ) && $options['oldns'] > 100 ) {
		NSRecoveryController( $bDry, $options );
	} else {
		showHelp();
	}
}

function showHelp() {
	echo( "Recover Pages/Revisions/Texts form backuptables\n" );
	echo( "Usage: php ModifyUserProperties.php [<option>=<>]\n" );
	echo( " --help : displays description\n\n" );
	echo( " --oldns : get revs and pages with this ns from bakuptable\n" );
	echo( " --newns : rename ns (if changed)\n" );
	echo( " --execute : Actually executes the script\n" );
	echo( "\n" );
}

/**
 *
 * @param bool $bDry
 * @param array $options
 */
function NSRecoveryController( $bDry, $options ) {
	$aPages = getDataFromNSBackup( 'page', [ 'page_namespace' => $options['oldns'] ] );

		if ( empty( $aPages ) ) {
			die( "backup for namespace " . $options['oldns'] . " not found" );
		}
		$aRevisions = [];
		$aTexts     = [];
		$numPages = count( $aPages );
		for ( $i = 0; $i < $numPages; $i++ ) {
			$aRevisions[$i] = getDataFromNSBackup( 'revision', [ 'rev_page' => $aPages[$i]['page_id'] ] );

			$numRevisions = count( $aRevisions[$i] );
			for ( $ir = 0; $ir < $numRevisions; $ir++ ) {
				$aTexts[$i][$ir] = getDataFromNSBackup(
					'text',
					[ 'old_id' => $aRevisions[$i][$ir]['rev_text_id'] ]
				);
			}
		}
		// var_dump($aRevisions);
		setDataFromNSBackup( $aPages, $aRevisions, $aTexts, $bDry, $options );
}

/**
 *
 * @param string $sTable
 * @param array $aConditions
 * @param array $aReturn
 * @return array
 */
function getDataFromNSBackup( $sTable, $aConditions = [], $aReturn = [] ) {
	$oDbr = wfGetDB( DB_REPLICA );

	$sTable = 'bs_namespacemanager_backup_' . $sTable;

	$rRes = $oDbr->select( $sTable, '*', $aConditions );
	var_dump( $oDbr->lastQuery() );
	if ( empty( $rRes ) ) {
		return [];
	}

	foreach ( $rRes as $row ) {
		$aReturn[] = (array)$row;
	}

	return $aReturn;
}

/**
 *
 * @param array $aPages
 * @param array $aRevisions
 * @param array $aTexts
 * @param bool $bDry
 * @param array $options
 */
function setDataFromNSBackup( $aPages, $aRevisions, $aTexts, $bDry, $options ) {
	$oDbr = wfGetDB( DB_MASTER );
	$numPages = count( $aPages );
	for ( $iP = 0; $iP < $numPages; $iP++ ) {

		/*if( empty( $aRevisions[$iP] ) ) {
			echo "error: ".$aPages[$iP]['page_title']."  no revision found\n";
			continue;
		}
		$rRes = $oDbr->select('page', 'page_id' , array('page_id' => $aPages[$iP]['page_id']) );
		if( $rRes->fetchRow() ) {
			echo "error: ".$aPages[$iP]['page_title']."  already exists\n";
			continue;
		}
		*/
		$numRevisions = count( $aRevisions[$iP] );
		for ( $iR = 0; $iR < $numRevisions; $iR++ ) {
			echo 'Revision';
			if ( !$bDry && $options['execute'] ) {
				$oDbr->insert( 'text', $aTexts[$iP][$iR][0] );
				$oDbr->insert( 'revision', $aRevisions[$iP][$iR] );
				var_dump( $aRevisions[$iP][$iR] );
			}

		}
		if ( isset( $options['newns'] ) ) {
			$aPages[$iP]['page_namespace'] = $options['newns'];
		}
		if ( !$bDry && $options['execute'] ) {
			// $rRes = $oDbr->insert( 'page', $aPages[$iP]);
		}
		// if($rRes ){
			echo $aPages[$iP]['page_title'] . " recovered\n";
		// }
		// echo $aPages[$iP]['page_title']." recovered\n";
	}
}
