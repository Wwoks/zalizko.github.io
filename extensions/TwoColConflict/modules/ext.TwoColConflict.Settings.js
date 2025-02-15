( function ( mw, $ ) {
	/**
	 * @constructor
	 */
	var Settings = function () {
		this.hideHelpDialogue = this.loadBoolean( 'hide-help-dialogue' );
	};

	$.extend( Settings.prototype, {
		/**
		 * @type {boolean}
		 */
		hideHelpDialogue: null,

		/**
		 * @return {boolean}
		 */
		shouldHideHelpDialogue: function () {
			return this.hideHelpDialogue;
		},

		/**
		 * @param {boolean} newSetting
		 */
		setHideHelpDialogue: function ( newSetting ) {
			if ( newSetting !== this.hideHelpDialogue ) {
				this.saveBoolean( 'hide-help-dialogue', newSetting );
				this.hideHelpDialogue = newSetting;
			}
		},

		/**
		 * @param {string} name
		 * @param {string} defaultValue
		 * @return {string|boolean}
		 */
		loadSetting: function ( name, defaultValue ) {
			var setting;
			if ( !mw.user.isAnon() ) {
				setting = mw.user.options.get( 'userjs-twocolconflict-' + name );
			} else {
				setting = mw.storage.get( 'mw-twocolconflict-' + name );
				if ( !setting ) {
					setting = mw.cookie.get( '-twocolconflict-' + name );
				}
			}

			return setting !== null && setting !== false ? setting : defaultValue;
		},

		/**
		 * @param {string} name
		 * @param {boolean} defaultValue
		 * @return {boolean}
		 */
		loadBoolean: function ( name, defaultValue ) {
			return this.loadSetting( name, defaultValue ? '1' : '0' ) === '1';
		},

		/**
		 * @param {string} name
		 * @param {string} value
		 */
		saveSetting: function ( name, value ) {
			if ( !mw.user.isAnon() ) {
				( new mw.Api() ).saveOption( 'userjs-twocolconflict-' + name, value );
			} else {
				if ( !mw.storage.set( 'mw-twocolconflict-' + name, value ) ) {
					mw.cookie.set( '-twocolconflict-' + name, value ); // use cookie when localStorage is not available
				}
			}
		},

		/**
		 * @param {string} name
		 * @param {boolean} value
		 */
		saveBoolean: function ( name, value ) {
			this.saveSetting( name, value ? '1' : '0' );
		}
	} );

	mw.libs.twoColConflict = mw.libs.twoColConflict || {};
	mw.libs.twoColConflict.Settings = Settings;
}( mediaWiki, jQuery ) );
