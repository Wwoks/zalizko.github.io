<?php

namespace BlueSpice\Utility;

use Title;

/**
 * A lot of MediaWiki (Web)APIs accept a title context information (e.g.
 * ApiParse)
 * - "pageid=233"
 * - "title=Some page"
 * - "page=Some page"
 * - "target=Some page"
 *
 * Some even lists of titles (e.g. ApiPurge)
 * - "pageids=233|453|2843"
 * - "titles=First|Second|Third"
 *
 * Then there are some that accept one or more revision ids (e.g. ApiReview)
 * - "revid=3424"
 * - "revids=34234|67556|435"
 * - "oldid=3647"
 * - "previd=3898"
 * - "baserevid=7487"
 *
 * Last but not least there are BlueSpice TaskAPIs that provide things like
 * - "page_id:324"
 * - "pid:324"
 * - "page_prefixedtitle:'Help:Some_page'"
 * - "page_title:'Some_page',page_namespace:12"
 */
class TitleParamsResolver {

	/**
	 *
	 * @var array
	 */
	protected $params = [];

	/**
	 *
	 * @var Title[]
	 */
	protected $titles = null;

	/**
	 *
	 * @var Title
	 */
	protected $default = null;

	/**
	 *
	 * @param array $params
	 * @param Title[] $default
	 */
	public function __construct( $params, $default = [] ) {
		$this->params = $params;
		$this->default = $default;
	}

	/**
	 *
	 * @return Title[] all titles that could be found in the provided params
	 */
	public function resolve() {
		foreach ( $this->params as $paramName => $paramValue ) {
			$lcParamName = strtolower( $paramName );
			$this->resolvePageIds( $lcParamName, $paramValue );
			$this->resolvePageNames( $lcParamName, $paramValue );
			$this->resolveRevisionIds( $lcParamName, $paramValue );
		}

		return $this->getResultOrDefault();
	}

	/**
	 *
	 * @return Title[]
	 */
	protected function getResultOrDefault() {
		if ( empty( $this->titles ) ) {
			return $this->default;
		}
		return array_values( $this->titles );
	}

	/**
	 *
	 * @param string $paramName
	 * @param mixed $paramValue
	 */
	protected function resolvePageIds( $paramName, $paramValue ) {
		$pageIds = [];
		if ( $this->isPageIdParam( $paramName ) ) {
			$pageIds = [ $paramValue ];
		} elseif ( $this->isPageIdsParam( $paramName ) ) {
			$pageIds = explode( '|', $paramValue );
		}

		foreach ( $pageIds as $pageId ) {
			$title = Title::newFromID( $pageId );
			if ( $title instanceof Title ) {
				$this->titles[$title->getPrefixedDBkey()] = $title;
			}
		}
	}

	/**
	 *
	 * @param string $paramName
	 * @return bool
	 */
	protected function isPageIdParam( $paramName ) {
		return in_array( $paramName, [ 'pageid', 'page_id', 'pid', 'iarticleid' ] );
	}

	/**
	 *
	 * @param string $paramName
	 * @return bool
	 */
	protected function isPageIdsParam( $paramName ) {
		return in_array( $paramName, [ 'pageids', 'page_ids', 'pids' ] );
	}

	/**
	 *
	 * @param string $paramName
	 * @param mixed $paramValue
	 */
	protected function resolvePageNames( $paramName, $paramValue ) {
		$pageNames = [];
		if ( $this->isPageNameParam( $paramName ) ) {
			$pageNames = [ $paramValue ];
		} elseif ( $this->isPageNamesParam( $paramName ) ) {
			$pageNames = explode( '|', $paramValue );
		}

		foreach ( $pageNames as $pageName ) {
			$title = Title::newFromText( $pageName );
			if ( $title instanceof  Title ) {
				$this->titles[$title->getPrefixedDBkey()] = $title;
			}
		}
	}

	/**
	 *
	 * @param string $paramName
	 * @return bool
	 */
	protected function isPageNameParam( $paramName ) {
		return in_array( $paramName, [ 'title', 'page', 'target', 'pagename' ] );
	}

	/**
	 *
	 * @param string $paramName
	 * @return bool
	 */
	protected function isPageNamesParam( $paramName ) {
		return in_array( $paramName, [ 'titles', 'pages', 'targets', 'pagename' ] );
	}

	/**
	 *
	 * @param string $paramName
	 * @param mixed $paramValue
	 */
	protected function resolveRevisionIds( $paramName, $paramValue ) {
		$revisionIds = [];
		if ( $this->isRevisionIdParam( $paramName ) ) {
			$revisionIds = [ $paramValue ];
		} elseif ( $this->isRevisionIdsParam( $paramName ) ) {
			$revisionIds = explode( '|', $paramValue );
		}

		foreach ( $revisionIds as $revId ) {
			$revision = \Revision::newFromId( $revId );
			if ( $revision instanceof \Revision ) {
				$title = $revision->getTitle();
				if ( $title instanceof  Title ) {
					$this->titles[$title->getPrefixedDBkey()] = $title;
				}
			}
		}
	}

	/**
	 *
	 * @param string $paramName
	 * @return bool
	 */
	protected function isRevisionIdParam( $paramName ) {
		return in_array( $paramName, [ 'revid', 'oldid', 'previd', 'baserevid' ] );
	}

	/**
	 *
	 * @param string $paramName
	 * @return bool
	 */
	protected function isRevisionIdsParam( $paramName ) {
		return in_array( $paramName, [ 'revids', 'oldids', 'previds', 'baserevids' ] );
	}

}
