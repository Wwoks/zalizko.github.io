<?php

namespace BlueSpice\InsertLink\ConfigDefinition;

use BlueSpice\ConfigDefinition\BooleanSetting;

class AllowUnMapped extends BooleanSetting {

	public function getPaths() {
		return [
			static::MAIN_PATH_FEATURE . '/' . static::FEATURE_EDITOR . '/BlueSpiceInsertLink',
			static::MAIN_PATH_EXTENSION . '/BlueSpiceInsertLink/' . static::FEATURE_EDITOR,
			static::MAIN_PATH_PACKAGE . '/' . static::PACKAGE_FREE . '/BlueSpiceInsertLink',
		];
	}

	public function getLabelMessageKey() {
		return 'bs-insertlink-pref-allowunmapped';
	}

}
