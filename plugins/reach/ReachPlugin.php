<?php
/**
 * Enable time based cue point objects management on entry objects
 * @package plugins.reach
 */
class ReachPlugin extends KalturaPlugin implements IKalturaServices, IKalturaPermissions, IKalturaVersion,IKalturaAdminConsolePages, IKalturaPending, IKalturaEventConsumers, IKalturaEnumerator, IKalturaObjectLoader
{
	const PLUGIN_NAME = 'reach';
	const PLUGIN_VERSION_MAJOR = 1;
	const PLUGIN_VERSION_MINOR = 0;
	const PLUGIN_VERSION_BUILD = 0;
	const REACH_MANAGER = 'kReachManager';
	const REACH_FLOW_MANAGER = 'kReachFlowManager';

	/*
	 * (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if($baseClass == 'BaseVendorCatalogItem' && $enumValue == KalturaVendorCatalogItemType::CAPTIONS)
			return 'KalturaVendorCaptionsCatalogItem';

		if($baseClass == 'BaseVendorCatalogItem' && $enumValue == KalturaVendorCatalogItemType::TRANSLATION)
			return 'KalturaVendorTranslationCatalogItem';
		
		if($baseClass == 'KalturaCondition' && $enumValue == ReachPlugin::getConditionTypeCoreValue(ReachConditionType::EVENT_CATEGORY_ENTRY))
			return 'KalturaCategoryEntryCondition';
		
		if($baseClass == 'kRuleAction' && $enumValue == ReachRuleActionType::ADD_ENTRY_VENDOR_TASK)
			return 'kAddEntryVendorTaskAction';
		
		if($baseClass == 'KalturaRuleAction' && ReachRuleActionType::ADD_ENTRY_VENDOR_TASK)
			return 'KalturaAddEntryVendorTaskAction';

		return null;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('SyncReachCreditTaskBatchType', 'ReachConditionType');

		if($baseEnumName == 'BatchJobType')
			return array('SyncReachCreditTaskBatchType');
		
		if($baseEnumName == 'ConditionType')
			return array('ReachConditionType');

		return array();
	}

	/* (non-PHPdoc)
	 * @see IKalturaPlugin::getPluginName()
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaVersion::getVersion()
	 */
	public static function getVersion()
	{
		return new KalturaVersion(
			self::PLUGIN_VERSION_MAJOR,
			self::PLUGIN_VERSION_MINOR,
			self::PLUGIN_VERSION_BUILD
		);
	}

	/* (non-PHPdoc)
	 * @see IKalturaPermissions::isAllowedPartner()
	 */
	public static function isAllowedPartner($partnerId)
	{
		if(in_array($partnerId, array(Partner::ADMIN_CONSOLE_PARTNER_ID, Partner::BATCH_PARTNER_ID, PartnerPeer::GLOBAL_PARTNER)))
			return true;
		
		if(PermissionPeer::isValidForPartner(PermissionName::REACH_VENDOR_PARTNER_PERMISSION, $partnerId))
			return true;
		
		$partner = PartnerPeer::retrieveByPK($partnerId);
		return $partner->getPluginEnabled(self::PLUGIN_NAME);
	}


	public static function isAllowAdminApi($actionApi = null)
	{
		$currentPermissions = Infra_AclHelper::getCurrentPermissions();
		return ($currentPermissions && in_array(Kaltura_Client_Enum_PermissionName::SYSTEM_ADMIN_CATALOG_ITEM_MODIFY, $currentPermissions));
	}

	/* (non-PHPdoc)
	 * @see IKalturaServices::getServicesMap()
	 */
	public static function getServicesMap()
	{
		$map = array(
			'vendorCatalogItem' => 'VendorCatalogItemService',
			'vendorProfile' => 'VendorProfileService',
			'entryVendorTask' => 'EntryVendorTaskService',
			'partnerCatalogItem' => 'PartnerCatalogItemService',
		);
		return $map;
	}

 	/*
 	 * @see IKalturaAdminConsolePages::getApplicationPages()
 	 */
	public static function getApplicationPages()
	{
 		$pages = array();
		$pages[] = new PartnerCatalogItemListAction();
		$pages[] = new PartnerCatalogItemConfigureAction();
		$pages[] = new PartnerCatalogItemSetStatusAction();
		$pages[] = new CatalogItemListAction();
		$pages[] = new CatalogItemConfigureAction();
		$pages[] = new CatalogItemSetStatusAction();
		$pages[] = new VendorProfileListAction();
		$pages[] = new VendorProfileConfigureAction();
		$pages[] = new VendorProfileSetStatusAction();
		$pages[] = new VendorProfileCreditConfigureAction();
		return $pages;
	}
	
	/* (non-PHPdoc)
 	 * @see IKalturaPending::dependsOn()
 	*/
	public static function dependsOn()
	{
		$eventNotificationDependency = new KalturaDependency(EventNotificationPlugin::getPluginName());
		return array($eventNotificationDependency);
	}
	
	/* (non-PHPdoc)
 	 * @see IKalturaEventConsumers::getEventConsumers()
 	 */
	public static function getEventConsumers()
	{
		return array(self::REACH_MANAGER, self::REACH_FLOW_MANAGER);
	}
	
	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getConditionTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('ConditionType', $value);
	}
	
	/**
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		if($baseClass == 'KalturaCondition' && $enumValue == ReachPlugin::getConditionTypeCoreValue(ReachConditionType::EVENT_CATEGORY_ENTRY))
			return new KalturaCategoryEntryCondition();
		
		if($baseClass == 'kRuleAction' && $enumValue == ReachRuleActionType::ADD_ENTRY_VENDOR_TASK)
			return new kAddEntryVendroTaskAction();
		
		if($baseClass == 'KalturaRuleAction' && $enumValue == ReachRuleActionType::ADD_ENTRY_VENDOR_TASK)
			return new KalturaAddEntryVendorTaskAction();
		
		return null;
	}
	
	//TODO add reach plugin permission
}
