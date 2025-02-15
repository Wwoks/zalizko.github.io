<?php

namespace BS\ExtendedSearch\MediaWiki\Specials;

use BlueSpice\Services;
use Title;
use SpecialPage;
use FormatJson;
use BS\ExtendedSearch\Lookup;
use BS\ExtendedSearch\Backend as SearchBackend;

class SearchCenter extends SpecialPage {

	public function __construct() {
		// SearchCenter should only be reached via searchBar
		parent::__construct( 'BSSearchCenter', '', false );
	}

	public function execute( $subPage ) {
		$this->setHeaders();

		$config = Services::getInstance()->getConfigFactory()->makeConfig( 'bsg' );

		$returnTo = $this->getRequest()->getText( 'returnto' );
		$title = Title::newFromText( $returnTo );
		if ( $title instanceof Title ) {
			$this->getOutput()->addReturnTo( $title );
		}

		// Query string param that can contain search term or entire lookup object
		$query = $this->getRequest()->getText( 'q' );
		$lookup = $this->lookupFromQuery( $query );

		$queryString = $lookup->getQueryString();
		$rawTerm = $this->getRequest()->getText( 'raw_term' );
		// If user has submitted the form too fast, before
		// Lookup object had time to init/update on client side,
		// we must use raw_term to set the lookup
		if ( $rawTerm != '' ) {
			$queryStringBits = explode( '/', $queryString[ 'query' ] );
			$rawTermIsSubpage = false;
			if ( !empty( $queryStringBits ) &&
				$queryStringBits[ count( $queryStringBits ) - 1 ] == $rawTerm ) {
				$rawTermIsSubpage = true;
			}

			if ( $queryString['query'] == '' || ( $queryString['query'] != $rawTerm && !$rawTermIsSubpage ) ) {
				$queryString['query'] = $rawTerm;
				$lookup->setQueryString( $queryString );
			}
		}

		$out = $this->getOutput();
		$out->addModules( "ext.blueSpiceExtendedSearch.SearchCenter" );

		$localBackend = SearchBackend::instance();
		$defaultResultStructure = $localBackend->getDefaultResultStructure();

		// Add _score manually, as its not a real field
		$sortableFields = [ '_score' ];
		$allowedSortableFieldTypes = [ 'date', 'time', 'integer' ];

		$availableTypes = [];
		$resultStructures = [];

		foreach ( $localBackend->getSources() as $sourceKey => $source ) {
			foreach ( $source->getMappingProvider()->getPropertyConfig() as $fieldName => $fieldConfig ) {
				if ( in_array( $fieldName, $sortableFields ) ) {
					continue;
				}

				if ( in_array( $fieldConfig['type'], $allowedSortableFieldTypes ) ) {
					$sortableFields[] = $fieldName;
					continue;
				}

				if ( $fieldConfig['type'] == 'text' ) {
					if ( isset( $fieldConfig['fielddate'] ) && $fieldConfig['fielddata'] == true ) {
						$sortableFields[] = $fieldName;
					}
				}
			}

			$resultStructure = $source->getFormatter()->getResultStructure( $defaultResultStructure );
			$resultStructures[$source->getTypeKey()] = $resultStructure;

			$searchPermission = $source->getSearchPermission();
			if ( !$searchPermission || $this->getUser()->isAllowed( $searchPermission ) ) {
				$availableTypes[] = $source->getTypeKey();
			}
		}

		$out->enableOOUI();
		$out->addHTML( \Html::element( 'div', [ 'id' => 'bs-es-tools' ] ) );
		$out->addHTML( \Html::element( 'div', [ 'id' => 'bs-es-alt-search' ] ) );
		$out->addHTML( \Html::element( 'div', [ 'id' => 'bs-es-results' ] ) );

		if ( $lookup ) {
			$out->addJsConfigVars( 'bsgLookupConfig', FormatJson::encode( $lookup ) );
		}

		// Structure of the result displayed in UI, decorated by each source
		$out->addJsConfigVars( 'bsgESResultStructures', $resultStructures );
		// Array of fields available for sorting
		$out->addJsConfigVars( 'bsgESSortableFields', $sortableFields );
		// Array of each source's types.
		$out->addJsConfigVars( 'bsgESAvailbleTypes', $availableTypes );
		$out->addJsConfigVars( 'bsgESResultsPerPage', 25 );
		$out->addJsConfigVars( 'ESSearchCenterDefaultFilters', $config->get( 'ESSearchCenterDefaultFilters' ) );
		$out->addJsConfigVars( 'bsgESUserCanExport', $this->userCanExport() );
	}

	/**
	 * Makes lookup from given string, if possible,
	 * otherwise returns empty Lookup
	 *
	 * @param string $query
	 * @return Lookup
	 */
	protected function lookupFromQuery( $query ) {
		$lookup = new Lookup();
		if ( !$query ) {
			return $lookup;
		}

		$parseStatus = FormatJson::parse( $query, FormatJson::FORCE_ASSOC );
		if ( $parseStatus->isOK() ) {
			return new Lookup( $parseStatus->getValue() );
		}

		if ( is_string( $query ) == false ) {
			return $lookup;
		}

		$lookup->setQueryString( $query );
		return $lookup;
	}

	protected function getGroupName() {
		return 'bluespice';
	}

	private function userCanExport() {
		$pageToTest = Title::makeTitle( NS_MEDIAWIKI, 'Dummy' );
		if ( $pageToTest->userCan( 'edit', $this->getUser() ) ) {
			return true;
		}
		return false;
	}
}
