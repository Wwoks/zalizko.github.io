<?php

namespace BlueSpice\Calumma;

class Setup {
	/**
	 *
	 */
	public static function onCallback() {
		$GLOBALS[ 'egChameleonLayoutFile' ] = dirname( __DIR__ ) . '/layouts/default.xml';

		$GLOBALS[ 'wgUseMediaWikiUIEverywhere' ] = true;

		$GLOBALS[ 'wgVisualEditorSupportedSkins' ][] = 'bluespicecalumma';
		$GLOBALS[ 'wgVisualEditorSkinToolbarScrollOffset' ][ 'bluespicecalumma' ] = 64;

		// force overwrite of the custom header registry even when the skin is
		// loaded before the BlueSpiceCustomMenu extension. ERM:16808
		$GLOBALS[ 'bsgExtensionAttributeRegistryOverrides' ]["BlueSpiceCustomMenuRegistry"] = [
			"merge" => [
				"header" => "\\BlueSpice\\Calumma\\CustomMenu\\Header::getInstance"
			]
		];
	}
}
