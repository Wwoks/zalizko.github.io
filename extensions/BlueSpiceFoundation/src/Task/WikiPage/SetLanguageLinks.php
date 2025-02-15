<?php

namespace BlueSpice\Task\WikiPage;

use Title;
use BlueSpice\ParamProcessor\ParamDefinition;
use BlueSpice\ParamProcessor\ParamType;
use BlueSpice\Utility\WikiTextLinksHelper\InterlanguageLinksHelper;

class SetLanguageLinks extends \BlueSpice\Task\WikiPage {
	const PARAM_LANGUAGE_LINKS = 'languagelinks';

	/**
	 * @return Status
	 */
	protected function doExecute() {
		$langLinks = $this->getParam( static::PARAM_LANGUAGE_LINKS );
		$wikiText = $this->fetchCurrentRevisionWikiText();
		$helper = $this->getLanguageLinksHelper( $wikiText );

		$remove = array_values( $this->filterDiffTargets(
			$helper->getTargets(),
			$langLinks
		) );

		$add = array_values( $this->filterDiffTargets(
			$langLinks,
			$helper->getTargets()
		) );

		$helper->removeTargets( $remove );
		$helper->addTargets( $add );

		return $this->saveWikiPage( $helper->getWikitext() );
	}

	/**
	 *
	 * @param Title[] $array1
	 * @param Title[] $array2
	 * @return type
	 */
	protected function filterDiffTargets( array $array1, array $array2 ) {
		return array_filter( $array1, function ( Title $e ) use( $array2 ) {
			foreach ( $array2 as $target ) {
				if ( $e->equals( $target ) ) {
					return false;
				}
			}
			return true;
		} );
	}

	/**
	 *
	 * @param string $wikiText
	 * @return InterlanguageLinksHelper
	 */
	protected function getLanguageLinksHelper( $wikiText ) {
		return $this->getServices()->getBSUtilityFactory()
			->getWikiTextLinksHelper( $wikiText )->getLanguageLinksHelper();
	}

	/**
	 *
	 * @return ParamDefinition[]
	 */
	public function getArgsDefinitions() {
		$langLinks = new \BSTitleListParam(
			ParamType::TITLE_LIST,
			static::PARAM_LANGUAGE_LINKS,
			[],
			null,
			true
		);

		$validator = new \BSTitleValidator();
		$validator->setOptions( [ 'hastoexist' => false ] );
		$langLinks->setValueValidator( $validator );

		return [
			$langLinks
		];
	}

	/**
	 *
	 * @return string[]
	 */
	public function getTaskPermissions() {
		return [ 'edit' ];
	}

}
