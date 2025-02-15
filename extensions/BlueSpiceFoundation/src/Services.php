<?php

namespace BlueSpice;

class Services extends ServicesDecorator {

	/**
	 *
	 * @return ExtensionRegistry
	 */
	public function getBSExtensionRegistry() {
		return $this->getService( 'BSExtensionRegistry' );
	}

	/**
	 *
	 * @return ExtensionFactory
	 */
	public function getBSExtensionFactory() {
		return $this->getService( 'BSExtensionFactory' );
	}

	/**
	 *
	 * @return ConfigDefinitionFactory
	 */
	public function getBSConfigDefinitionFactory() {
		return $this->getService( 'BSConfigDefinitionFactory' );
	}

	/**
	 *
	 * @return DynamicFileDispatcher\Factory
	 */
	public function getBSDynamicFileDispatcherFactory() {
		return $this->getService( 'BSDynamicFileDispatcherFactory' );
	}

	/**
	 *
	 * @return DynamicFileDispatcher\UrlBuilder
	 */
	public function getBSDynamicFileDispatcherUrlBuilder() {
		return $this->getService( 'BSDynamicFileDispatcherUrlBuilder' );
	}

	/**
	 *
	 * @return EntityConfigFactory
	 */
	public function getBSEntityConfigFactory() {
		return $this->getService( 'BSEntityConfigFactory' );
	}

	/**
	 *
	 * @return EntityFactory
	 */
	public function getBSEntityFactory() {
		return $this->getService( 'BSEntityFactory' );
	}

	/**
	 *
	 * @return AdminToolFactory
	 */
	public function getBSAdminToolFactory() {
		return $this->getService( 'BSAdminToolFactory' );
	}

	/**
	 *
	 * @return TagFactory
	 */
	public function getBSTagFactory() {
		return $this->getService( 'BSTagFactory' );
	}

	/**
	 * @return RendererFactory
	 */
	public function getBSRendererFactory() {
		return $this->getService( 'BSRendererFactory' );
	}

	/**
	 *
	 * @return SkinDataRendererFactory
	 */
	public function getBSSkinDataRendererFactory() {
		return $this->getService( 'BSSkinDataRendererFactory' );
	}

	/**
	 *
	 * @return SettingPathFactory
	 */
	public function getBSSettingPathFactory() {
		return $this->getService( 'BSSettingPathFactory' );
	}

	/**
	 *
	 * @return TaskFactory
	 */
	public function getBSTaskFactory() {
		return $this->getService( 'BSTaskFactory' );
	}

	/**
	 *
	 * @return UtilityFactory
	 */
	public function getBSUtilityFactory() {
		return $this->getService( 'BSUtilityFactory' );
	}

	/**
	 *
	 * @return NotificationManager
	 */
	public function getBSNotificationManager() {
		return $this->getService( 'BSNotificationManager' );
	}

	/**
	 *
	 * @return TargetCacheFactory
	 */
	public function getBSTargetCacheFactory() {
		return $this->getService( 'BSTargetCacheFactory' );
	}

	/**
	 *
	 * @return TargetCache\Title
	 */
	public function getBSTargetCacheTitle() {
		return $this->getService( 'BSTargetCacheTitle' );
	}

	/**
	 *
	 * @return PermissionLockdownFactory
	 */
	public function getBSPermissionLockdownFactory() {
		return $this->getService( 'BSPermissionLockdownFactory' );
	}

	/**
	 *
	 * @return \BlueSpice\Permission\Role\Manager
	 */
	public function getBSRoleManager() {
		return $this->getService( 'BSRoleManager' );
	}

	/**
	 *
	 * @return TemplateFactory
	 */
	public function getBSTemplateFactory() {
		return $this->getService( 'BSTemplateFactory' );
	}

	/**
	 *
	 * @return PageInfoElementFactory
	 */
	public function getBSPageInfoElementFactory() {
		return $this->getService( 'BSPageInfoElementFactory' );
	}

	/**
	 *
	 * @return DeferredNotificationStack
	 */
	public function getBSDeferredNotificationStack() {
		return $this->getService( 'BSDeferredNotificationStack' );
	}

}
