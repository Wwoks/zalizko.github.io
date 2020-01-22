<?php

use MediaWiki\MediaWikiServices;
use BlueSpice\WhoIsOnline\Tracer;

return [

	'BSWhoIsOnlineTracer' => function ( MediaWikiServices $services ) {
		return new Tracer(
			$services->getDBLoadBalancer(),
			$services->getConfigFactory()->makeConfig( 'bsg' ),
			$services->getReadOnlyMode(),
			$services->getService( 'BSUtilityFactory' )
		);
	},

];
