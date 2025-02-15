<?php
namespace BlueSpice\PageAssignments\Assignment;

class Everyone extends \BlueSpice\PageAssignments\Assignment {

	/**
	 *
	 * @var int[]
	 */
	protected static $userIdCache = null;

	/**
	 *
	 * @return string
	 */
	protected function makeAnchor() {
		return \Html::element(
			'span',
			[ 'class' => 'bs-pa-special-everyone' ],
			$this->getText()
		);
	}

	/**
	 *
	 * @return string
	 */
	public function getText() {
		return \Message::newFromKey(
			'bs-pageassignments-assignee-special-everyone-label'
		)->plain();
	}

	/**
	 *
	 * @return int[]
	 */
	public function getUserIds() {
		if ( isset( static::$userIdCache ) ) {
			return static::$userIdCache;
		}
		static::$userIdCache = [];

		$loadBalancer = Services::getInstance()->getDBLoadBalancer();
		$res = $loadBalancer->getConnection( DB_REPLICA )->select(
			'user',
			'user_id'
		);
		foreach ( $res as $row ) {
			$allowed = $this->getTitle()->userCan(
				'pageassignable',
				\User::newFromId( (int)$row->user_id )
			);
			if ( !$allowed ) {
				continue;
			}
			static::$userIdCache[] = (int)$row->user_id;
		}

		return static::$userIdCache;
	}

}
