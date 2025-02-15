<?php

// @todo: Hier wird nicht die Eingabe validiert, sondern bereits der angepaßte
// Name (z.B. _ ersetzt durch " ").
// Es ist also zusätzlich ein Mechanismus zur Validierung der Benutzereingabe erforderlich.

/**
 * DEPRECATED!
 * @deprecated since version 3.1 - Use ParamProcessor instead
 */
class BsValidatorMwUsernamePlugin implements BsValidatorPlugin {

	/**
	 * DEPRECATED!
	 * @deprecated since version 3.1 - Use ParamProcessor instead
	 * @param mixed $mwUsername the value to be checked against
	 * @param array $options
	 * @return BsValidatorResponse If type is not BsValidatorResponse an error is thrown
	 */
	public static function isValid( $mwUsername, $options ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		if ( is_null( $mwUsername ) || $mwUsername == '' ) {
			return new BsValidatorResponse( 1, 'UserManager', 'enter_user' );
		}

		if ( strpos( $mwUsername, '\\' ) !== false || !is_string( $mwUsername )
			|| strlen( $mwUsername ) < 2 ) {
			return new BsValidatorResponse( 2, 'UserManager', 'invalid_uname' );
		}

		// $mwUsername = ucfirst($mwUsername);

		if ( !User::isCreatableName( $mwUsername ) ) {
			if ( !User::isValidUserName( $mwUsername ) ) {
				return new BsValidatorResponse( 3, 'UserManager', 'invalid_uname' );
			}

			if ( !User::isUsableName( $mwUsername ) ) {
				return new BsValidatorResponse( 4, 'UserManager', 'reserved_uname' );
			}

			return new BsValidatorResponse( 5, 'UserManager', 'invalid_uname' );
		}
		// return new BsValidatorResponse(0, 'UserManager', 'uname_validation_approved);
		return new BsValidatorResponse( 0 );
	}
}
