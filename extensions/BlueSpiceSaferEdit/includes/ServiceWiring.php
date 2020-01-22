<?php

use MediaWiki\MediaWikiServices;
use BlueSpice\SaferEdit\SaferEditManager;
use BlueSpice\ExtensionAttributeBasedRegistry;

return [
	'BSSaferEditManager' => function ( MediaWikiServices $services ) {
		$db = $services->getDBLoadBalancer()->getConnection( DB_MASTER );
		$context = RequestContext::getMain();
		$registry = new ExtensionAttributeBasedRegistry( 'BlueSpiceSaferEditEnvironmentCheckers' );
		return new SaferEditManager( $db, $context, $registry );
	},
];
