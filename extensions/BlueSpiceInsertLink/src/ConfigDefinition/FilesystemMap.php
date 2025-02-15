<?php

namespace BlueSpice\InsertLink\ConfigDefinition;

use BlueSpice\ConfigDefinition\FileExtensions;
use BlueSpice\Html\FormField\KeyValueField;

class FilesystemMap extends FileExtensions {

	public function getPaths() {
		return [
			static::MAIN_PATH_FEATURE . '/' . static::FEATURE_EDITOR . '/BlueSpiceInsertLink',
			static::MAIN_PATH_EXTENSION . '/BlueSpiceInsertLink' . '/' . static::FEATURE_EDITOR,
			static::MAIN_PATH_PACKAGE . '/' . static::PACKAGE_FREE . '/BlueSpiceInsertLink',
		];
	}

	public function getLabelMessageKey() {
		return 'bs-insertlink-pref-filesystemmap';
	}

	public function getHtmlFormField() {
		return new KeyValueField( $this->makeFormFieldParams() );
	}

	protected function makeFormFieldParams() {
		return array_merge( parent::makeFormFieldParams(), [
			'allowAdditions' => true,
			'valueRequired' => false,
			'labelsOnlyOnFirst' => true,
			'keyLabel' => wfMessage( 'bs-insertlink-pref-filesystemmap-fs-prefix' )->plain(),
			'valueLabel' => wfMessage( 'bs-insertlink-pref-filesystemmap-web-prefix' )->plain()
		] );
	}

}
