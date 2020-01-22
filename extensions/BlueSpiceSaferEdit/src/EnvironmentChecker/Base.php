<?php

namespace BlueSpice\SaferEdit\EnvironmentChecker;

use BlueSpice\SaferEdit\IEnvironmentChecker;
use IContextSource;

abstract class Base implements IEnvironmentChecker {
	/**
	 * @var IContextSource
	 */
	protected $context;

	/**
	 * @param IContextSource $context
	 * @return IEnvironmentChecker
	 */
	public static function factory( IContextSource $context ) {
		return new static( $context );
	}

	/**
	 * @param IContextSource $context
	 */
	protected function __construct( $context ) {
		$this->context = $context;
	}

	protected function userCanEdit() {
		$title = $this->context->getTitle();
		$user = $this->context->getUser();
		if ( $title->userCan( 'edit', $user ) ) {
			return true;
		}
		return false;
	}

	/**
	 * @inheritDoc
	 */
	public function getEditedTitle( &$title ) {
		return true;
	}

	abstract public function isEditMode( &$result );

	abstract public function shouldShowWarning( &$result );
}
