<?php

namespace BlueSpice\Permission\Lockdown;

use Title;
use User;
use Message;

interface IModule {

	/**
	 * Returns true, whenever the rule sets of this module applies to given user
	 * and title
	 * @param Title $title
	 * @param User $user
	 * @return bool
	 */
	public function applies( Title $title, User $user );

	/**
	 * Returns if this is locked down or not
	 * @param Title $title
	 * @param User $user
	 * @param string $action
	 * @return bool
	 */
	public function mustLockdown( Title $title, User $user, $action );

	/**
	 * Returns the Message with the reason, this module was locked down.
	 * Should not be called befote mustLockdown() returns false
	 * @param Title $title
	 * @param User $user
	 * @param string $action
	 * @return Message
	 */
	public function getLockdownReason( Title $title, User $user, $action );

}
