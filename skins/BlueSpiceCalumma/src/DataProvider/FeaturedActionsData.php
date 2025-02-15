<?php
namespace BlueSpice\Calumma\DataProvider;

use BlueSpice\SkinData;
use BlueSpice\Calumma\SkinDataFieldDefinition as SDFD;

class FeaturedActionsData {

	/**
	 * @param \Skin $skin
	 * @param \SkinTemplate &$skintemplate
	 * @param array &$data
	 * @throws \MWException
	 */
	public static function populate( $skin, &$skintemplate, &$data ) {
		self::initFeaturedActions( $skin, $skintemplate, $data );
		self::populateActionsEdit( $skin, $skintemplate, $data );
		self::populateActionsNew( $skin, $skintemplate, $data );
	}

	/**
	 *
	 * @param \Skin $skin
	 * @param \SkinTemplate &$skintemplate
	 * @param array &$data
	 */
	public static function initFeaturedActions( $skin, &$skintemplate, &$data ) {
		if ( !array_key_exists( 'edit', $data[SkinData::FEATURED_ACTIONS] ) ) {
			$data[SkinData::FEATURED_ACTIONS] += [
				'edit' => []
			];
		};
		if ( !array_key_exists( 'new', $data[SkinData::FEATURED_ACTIONS] ) ) {
			$data[SkinData::FEATURED_ACTIONS] += [
				'new' => []
			];
		};
	}

	/**
	 *
	 * @param \Skin $skin
	 * @param \SkinTemplate &$skintemplate
	 * @param array &$data
	 */
	public static function populateActionsEdit( $skin, &$skintemplate, &$data ) {
		$curTitle = $skin->getTitle();

		$newSection = [
			'position' => '10',
			'id' => 'new-section',
		];

		if ( $curTitle->userCan( 'edit' ) && !$skin->getUser()->isLoggedIn() ) {
			$newSection += [
				'title' => wfMessage( 'bs-action-edit-please-login-title' )->plain(),
				'href' => self::getLoginUrl( $skin ),
				'classes' => [ 'disabled' ]
			];

			$data[SkinData::FEATURED_ACTIONS]['edit']['new-section'] = $newSection;
			return;
		}

		if ( !$curTitle->userCan( 'edit' ) ) {
			$newSection += [
				'title' => wfMessage( 'bs-action-edit-disabled-title' )->text(),
				'href' => '#',
				'classes' => [ 'disabled' ]
			];

			$data[SkinData::FEATURED_ACTIONS]['edit']['new-section'] = $newSection;
			return;
		}

		$content_navigation_data = $data[SDFD::CONTENT_NAVIGATION_DATA];
		$defaultEditActions = [];

		foreach ( $content_navigation_data as $key => $value ) {
			if ( $value['bs-group'] !== 'featuredActionsEdit' ) {
				continue;
			}

			if ( $key === 'edit' ) {
				$defaultEditActions['ca-edit'] = $value;
			} else {
				$defaultEditActions[$key] = $value;
			}
		}

		if ( !$curTitle->isSpecialPage() ) {
			$defaultEditActions['new-section'] = [
				'position' => '10',
				'id' => 'new-section',
				'text' => wfMessage( 'bs-action-new-section-text' )->plain(),
				'title' => wfMessage( 'bs-action-new-section-title' )->plain(),
				'href' => $curTitle->getLocalURL( [
					'action' => 'edit',
					'section' => 'new'
				] )
			];
		}

		$data[SkinData::FEATURED_ACTIONS]['edit'] += $defaultEditActions;
	}

	/**
	 * @param \Skin $skin
	 * @param \SkinTemplate &$skintemplate
	 * @param array &$data
	 * @throws \MWException
	 */
	public static function populateActionsNew( $skin, &$skintemplate, &$data ) {
		$curTitle = $skin->getTitle();
		$curUser = $skin->getUser();

		$newSection = [
			'position' => '10',
			'id' => 'new-section'
		];

		if ( $curTitle->userCan( 'edit' ) && !$skin->getUser()->isLoggedIn() ) {
			$newSection += [
				'title' => wfMessage( 'bs-action-new-please-login-title' )->plain(),
				'href' => self::getLoginUrl( $skin )
			];

			$data[SkinData::FEATURED_ACTIONS]['new']['new-section'] = $newSection;
			return;
		}

		if ( !$curUser->isAllowed( 'createpage' ) ) {
			$newSection += [
				'title' => wfMessage( 'bs-action-new-disabled-title' )->text(),
				'href' => ''
			];

			$data[SkinData::FEATURED_ACTIONS]['new']['new-section'] = $newSection;
			return;
		}

		$content_navigation_data = $data[SDFD::CONTENT_NAVIGATION_DATA];
		$defaultNewActions = [];

		foreach ( $content_navigation_data as $key => $value ) {
			if ( $value['bs-group'] !== 'featuredActionsNew' ) {
				continue;
			}
			$defaultNewActions[$key] = $value;
		}

		$defaultNewActions['new-page'] = [
			'classes' => [ 'selected', 'bs-fa-new-page' ],
			'primary' => true,
			'id' => 'new-page',
			'position' => '01',
			'text' => wfMessage( 'bs-action-new-page-text' )->plain(),
			'title' => wfMessage( 'bs-action-new-page-text' )->plain(),
			'href' => '#'
		];

		if ( $curTitle->getArticleID() > 0 && !$curTitle->isRedirect() ) {
			$defaultNewActions['new-subpage'] = [
				'position' => '10',
				'id' => 'new-subpage',
				'classes' => [ 'bs-fa-new-subpage' ],
				'text' => wfMessage( 'bs-action-new-subpage-text' )->plain(),
				'title' => wfMessage( 'bs-action-new-subpage-text' )->plain(),
				'href' => '#'
			];
		}
		$defaultNewActions['new-file'] = [
			'position' => '20',
			'id' => 'new-file',
			'classes' => [ 'bs-fa-new-file' ],
			'text' => wfMessage( 'bs-action-new-file-text' )->plain(),
			'title' => wfMessage( 'bs-action-new-file-title' )->plain(),
			'href' => \SpecialPage::getTitleFor( 'Upload' )->getLocalURL()
		];

		$data[SkinData::FEATURED_ACTIONS]['new'] += $defaultNewActions;
	}

	/**
	 * @param \Skin $skin
	 * @return string
	 * @throws \MWException
	 */
	private static function getLoginUrl( $skin ) {
		$title = \SpecialPage::getTitleFor( 'Login' );
		$login = $title->newFromText( 'Login', NS_SPECIAL );

		$returnToPage = $skin->getTitle();
		$query = [ 'returnTo' => $returnToPage->getPrefixedDBkey() ];

		return $login->getFullURL( $query );
	}
}
