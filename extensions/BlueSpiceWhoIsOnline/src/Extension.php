<?php

/**
 * WhoIsOnline extension for BlueSpice
 *
 * Displays a list of users who are currently online.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3.
 *
 * This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 * This file is part of BlueSpice MediaWiki
 * For further information visit http://www.bluespice.com
 *
 * @author     Markus Glaser <glaser@hallowelt.com>

 * @package    BlueSpice_Extensions
 * @subpackage WhoIsOnline
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */

namespace BlueSpice\WhoIsOnline;

/**
 * Base class for WhoIsOnline extension
 * @package BlueSpice_Extensions
 * @subpackage WhoIsOnline
 */
class Extension extends \BlueSpice\Extension {

	/**
	 * Register tag with UsageTracker extension
	 * @param array &$config
	 * @return Always true to keep hook running
	 */
	public static function onBSUsageTrackerRegisterCollectors( &$config ) {
		$config['bs:whoisonline:count'] = [
			'class' => 'Property',
			'config' => [
				'identifier' => 'bs-tag-userscount'
			]
		];
		$config['bs:whoisonline:popup'] = [
			'class' => 'Property',
			'config' => [
				'identifier' => 'bs-tag-userslink'
			]
		];
		return true;
	}
}
