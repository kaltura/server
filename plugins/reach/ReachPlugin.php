<?php

/**
 * Enable time based cue point objects management on entry objects
 * @package plugins.reach
 */
class ReachPlugin extends KalturaPlugin implements IKalturaServices, IKalturaPermissions, IKalturaVersion, IKalturaAdminConsolePages, IKalturaPending, IKalturaEventConsumers, IKalturaEnumerator, IKalturaObjectLoader, IKalturaSearchDataContributor
{
	const PLUGIN_NAME = 'reach';
	const PLUGIN_VERSION_MAJOR = 1;
	const PLUGIN_VERSION_MINOR = 0;
	const PLUGIN_VERSION_BUILD = 0;
	const REACH_MANAGER = 'kReachManager';
	const REACH_FLOW_MANAGER = 'kReachFlowManager';
	const SEARCH_FIELD_CATALOG_ITEM_DATA = 'cid';
	const SEARCH_TEXT_SUFFIX = 'ciend';
	const CATALOG_ITEM_INDEX_PREFIX = 'cis_';
	const CATALOG_ITEM_INDEX_SUFFIX = 'cie_';
	const CATALOG_ITEM_INDEX_SERVICE_TYPE = 'cist';
	const CATALOG_ITEM_INDEX_TURN_AROUND_TIME = 'citat';
	const CATALOG_ITEM_INDEX_SERVICE_FEATURE = 'cisf';
	const CATALOG_ITEM_INDEX_LANGUAGE = 'cil';
	
	/**
	 * return field name as appears in index schema
	 * @param string $fieldName
	 */
	public static function getSearchFieldName($fieldName)
	{
		if ($fieldName == self::SEARCH_FIELD_CATALOG_ITEM_DATA)
			return 'catalog_item_data';
		
		return CuePointPlugin::getPluginName() . '_' . $fieldName;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if ($baseClass == 'BaseVendorCatalogItem' && $enumValue == KalturaVendorCatalogItemType::CAPTIONS)
			return 'KalturaVendorCaptionsCatalogItem';
		
		if ($baseClass == 'BaseVendorCatalogItem' && $enumValue == KalturaVendorCatalogItemType::TRANSLATION)
			return 'KalturaVendorTranslationCatalogItem';
		
		if ($baseClass == 'KalturaCondition' && $enumValue == ReachPlugin::getConditionTypeCoreValue(ReachConditionType::EVENT_CATEGORY_ENTRY))
			return 'KalturaCategoryEntryCondition';
		
		if ($baseClass == 'kRuleAction' && $enumValue == ReachPlugin::getRuleActionTypeCoreValue(ReachRuleActionType::ADD_ENTRY_VENDOR_TASK))
			return 'kAddEntryVendorTaskAction';
		
		if ($baseClass == 'KalturaRuleAction' && ReachPlugin::getRuleActionTypeCoreValue(ReachRuleActionType::ADD_ENTRY_VENDOR_TASK))
			return 'KalturaAddEntryVendorTaskAction';
		
		if ($baseClass == 'kJobData')
		{
			if ($enumValue == self::getBatchJobTypeCoreValue(ReachEntryVendorTasksCsvBatchType::ENTRY_VENDOR_TASK_CSV))
			{
				return 'kEntryVendorTaskCsvJobData';
			}
		}
		
		if ($baseClass == 'KalturaJobData')
		{
			if ($enumValue == self::getApiValue(ReachEntryVendorTasksCsvBatchType::ENTRY_VENDOR_TASK_CSV))
			{
				return 'KalturaEntryVendorTaskCsvJobData';
			}
		}
		
		return null;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{
		if (is_null($baseEnumName))
			return array('SyncReachCreditTaskBatchType', 'ReachConditionType', 'ReachEntryVendorTasksCsvBatchType', 'ReachRuleActionType');
		
		if ($baseEnumName == 'BatchJobType')
			return array('SyncReachCreditTaskBatchType', 'ReachEntryVendorTasksCsvBatchType');
		
		if ($baseEnumName == 'ConditionType')
			return array('ReachConditionType');
		
		if ($baseEnumName == 'RuleActionType')
			return array('ReachRuleActionType');
		
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
		if (in_array($partnerId, array(Partner::ADMIN_CONSOLE_PARTNER_ID, Partner::BATCH_PARTNER_ID, PartnerPeer::GLOBAL_PARTNER)))
			return true;
		
		if (PermissionPeer::isValidForPartner(PermissionName::REACH_VENDOR_PARTNER_PERMISSION, $partnerId))
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
			'reachProfile' => 'ReachProfileService',
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
		$pages[] = new ReachProfileListAction();
		$pages[] = new ReachProfileConfigureAction();
		$pages[] = new ReachProfileSetStatusAction();
		$pages[] = new ReachProfileCreditConfigureAction();
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
	
	/**
	 * @return array
	 */
	public static function getEventConsumers()
	{
		return array(
			self::REACH_FLOW_MANAGER,
			self::REACH_MANAGER
		);
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
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getRuleActionTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('RuleActionType', $value);
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
		if ($baseClass == 'KalturaCondition' && $enumValue == ReachPlugin::getConditionTypeCoreValue(ReachConditionType::EVENT_CATEGORY_ENTRY))
			return new KalturaCategoryEntryCondition();
		
		if ($baseClass == 'kRuleAction' && $enumValue == ReachPlugin::getRuleActionTypeCoreValue(ReachRuleActionType::ADD_ENTRY_VENDOR_TASK))
			return new kAddEntryVendroTaskAction();
		
		if ($baseClass == 'KalturaRuleAction' && $enumValue ==  ReachPlugin::getRuleActionTypeCoreValue(ReachRuleActionType::ADD_ENTRY_VENDOR_TASK))
			return new KalturaAddEntryVendorTaskAction();
		
		if ($baseClass == 'kJobData')
		{
			if ($enumValue == self::getBatchJobTypeCoreValue(ReachEntryVendorTasksCsvBatchType::ENTRY_VENDOR_TASK_CSV))
			{
				return new kEntryVendorTaskCsvJobData();
			}
		}
		
		if ($baseClass == 'KalturaJobData')
		{
			if ($enumValue == self::getApiValue(ReachEntryVendorTasksCsvBatchType::ENTRY_VENDOR_TASK_CSV) ||
				$enumValue == self::getBatchJobTypeCoreValue(ReachEntryVendorTasksCsvBatchType::ENTRY_VENDOR_TASK_CSV)
			)
			{
				return new KalturaEntryVendorTaskCsvJobData();
			}
		}
		return null;
	}
	
	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getBatchJobTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('BatchJobType', $value);
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaSearchDataContributor::getSearchData()
	 */
	public static function getSearchData(BaseObject $object)
	{
		if ($object instanceof EntryVendorTask && self::isAllowedPartner($object->getPartnerId()))
			return self::getEntryVendorTaskSearchData($object);
		
		return null;
	}
	
	public static function getEntryVendorTaskSearchData(EntryVendorTask $entryVendorTask)
	{
		$catalogItem = $entryVendorTask->getCatalogItem();
		$catalogItemSearchField = ReachPlugin::getSearchFieldName(ReachPlugin::SEARCH_FIELD_CATALOG_ITEM_DATA);
		
		$contributedData = self::buildDataOnTask($catalogItem, $entryVendorTask->getPartnerId());
		
		$searchValues = array(
			$catalogItemSearchField => ReachPlugin::PLUGIN_NAME . "_" . $entryVendorTask->getPartnerId() . ' ' . $contributedData . ' ' . ReachPlugin::SEARCH_TEXT_SUFFIX
		);
		
		return $searchValues;
	}
	
	public static function buildDataOnTask(VendorCatalogItem $catalogItem, $partnerId)
	{
		$data = self::CATALOG_ITEM_INDEX_PREFIX . $partnerId;
		
		$data .= " " . self::CATALOG_ITEM_INDEX_SERVICE_TYPE . $catalogItem->getServiceType();
		$data .= " " . self::CATALOG_ITEM_INDEX_SERVICE_FEATURE . $catalogItem->getServiceFeature();
		$data .= " " . self::CATALOG_ITEM_INDEX_TURN_AROUND_TIME . $catalogItem->getTurnAroundTime();
		$data .= " " . self::CATALOG_ITEM_INDEX_LANGUAGE . $catalogItem->getSourceLanguage();
		
		$data .= " " . self::CATALOG_ITEM_INDEX_SUFFIX . $partnerId;
		
		return $data;
	}
	
}
