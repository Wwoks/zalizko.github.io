<?php
/**
 * Provides the base api for BlueSpice.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 * This file is part of BlueSpice MediaWiki
 * For further information visit https://bluespice.com
 *
 * @author     Robert Vogel <vogel@hallowelt.com>
 * @author     Patric Wirth <wirth@hallowelt.com>
 * @package    Bluespice_Foundation
 * @copyright  Copyright (C) 2018 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */

namespace BlueSpice;

use RequestContext;
use Language;
use ApiBase;
use User;
use Title;
use Status;
use ApiMessage;
use BlueSpice\Api\Format\Json;
use BlueSpice\Api\ErrorFormatter;
use WikiPage;
use BSExtendedApiContext;

/**
 * Api base class in BlueSpice
 * @package BlueSpice_Foundation
 */
abstract class Api extends ApiBase {
	/**
	 * Checks access permissions based on a list of titles and permissions. If
	 * one of it fails the API processing is ended with an appropriate message
	 * @param Title[] $titles Array of Title objects to check the requires permissions against
	 * @param User|null $user the User object of the requesting user. Does a fallback to
	 * $this->getUser();
	 */
	protected function checkPermissions( $titles = [], $user = null ) {
		if ( empty( $this->getRequiredPermissions() ) ) {
			return;
		}

		if ( $user instanceof User === false ) {
			$user = $this->getUser();
		}

		$status = Status::newGood();
		foreach ( $this->getRequiredPermissions() as $permission ) {
			if ( empty( $titles ) ) {
				$this->checkPermission( $status, $user, $permission );
				continue;
			}
			foreach ( $titles as $title ) {
				if ( !$title instanceof Title ) {
					continue;
				}
				$this->checkPermission( $status, $user, $permission, $title );
			}
		}

		if ( !$status->isOK() ) {
			$this->dieStatus( $status );
		}
	}

	/**
	 *
	 * @return string[]
	 */
	protected function getRequiredPermissions() {
		return [ 'read' ];
	}

	/**
	 *
	 * @return string[]
	 */
	protected function getExamples() {
		return [
			'api.php?action=' . $this->getModuleName(),
		];
	}

	/**
	 * Custom output printer for JSON. See class Json for details
	 * @return Json
	 */
	public function getCustomPrinter() {
		return new Json( $this->getMain(), $this->getParameter( 'format' ) );
	}

	/**
	 *
	 * @return ErrorFormatter
	 */
	public function getErrorFormatter() {
		$request = $this->getContext()->getRequest();
		$errorFormat = $request->getVal( 'errorformat', 'html' );
		$errorLangCode = $request->getVal( 'errorlang',	'uselang' );
		$errorsUseDB = $request->getCheck( 'errorsuselocal' );

		if ( $errorLangCode === 'uselang' ) {
			$errorLang = $this->getLanguage();
		} elseif ( $errorLangCode === 'content' ) {
			global $wgContLang;
			$errorLang = $wgContLang;
		} else {
			$errorLangCode = RequestContext::sanitizeLangCode( $errorLangCode );
			$errorLang = Language::factory( $errorLangCode );
		}

		return new ErrorFormatter(
			$this->getResult(),
			$errorLang,
			$errorFormat,
			$errorsUseDB
		);
	}

	/**
	 * Get the Config object
	 *
	 * @since 1.23
	 * @return Config
	 */
	public function getConfig() {
		return $this->getServices()->getConfigFactory()->makeConfig( 'bsg' );
	}

	/**
	 *
	 * @return Services
	 */
	protected function getServices() {
		return Services::getInstance();
	}

	/**
	 *
	 * @return Context
	 */
	public function getContext() {
		$context = parent::getContext();
		// whenever no action was sent this module was constructed internally by
		// the API help or API Sandbox i.e.
		// we can not overwrite the context, as this would result in an exception,
		// cause this modules need their own context to work with
		if ( $context->getRequest()->getVal( 'action', '' ) !== $this->getModuleName() ) {
			return $context;
		}
		// TODO: Replace this class with something more modular which extends the
		// context only if is aditional data and this module is actially called
		// by WebRequest
		$extendedContext = BSExtendedApiContext::newFromRequest(
			$context->getRequest()
		);

		$context->setTitle( $extendedContext->getTitle() );
		if ( $extendedContext->getTitle()->getNamespace() > -1 ) {
			// Page does not have to exist, but it must be in a "real" NS
			$context->setWikiPage(
				WikiPage::factory( $context->getTitle() )
			);
		}

		return new Context( $context, $this->getConfig() );
	}

	/**
	 * DEPRECATED
	 * Initializes the context of the API call
	 * @deprecated since version 3.1 - not in use anymore
	 */
	public function initContext() {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
	}

	/**
	 *
	 * @param Status $status
	 * @param User $user
	 * @param type $permission
	 * @param Title|null $title
	 * @return null
	 */
	protected function checkPermission( Status $status, User $user, $permission,
		Title $title = null ) {
		if ( !$status->isOK() ) {
			return;
		}
		if ( !$title ) {
			if ( !$user->isAllowed( $permission ) ) {
				$status->fatal(
					[ 'apierror-permissiondenied', $this->msg( "action-$permission" ) ]
				);
			}
			return;
		}
		foreach ( $title->getUserPermissionsErrors( $permission, $user ) as $error ) {
			$status->fatal(
				ApiMessage::create(
					$error,
					null,
					[ 'title' => $title->getPrefixedText() ]
				)
			);
		}
	}
}
