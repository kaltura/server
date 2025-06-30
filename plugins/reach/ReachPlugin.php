<?php

/**
 * Enable Reach feature
 * @package plugins.reach
 */
class ReachPlugin extends KalturaPlugin implements IKalturaServices, IKalturaPermissions, IKalturaVersion, IKalturaAdminConsolePages, IKalturaPending, IKalturaEventConsumers, IKalturaEnumerator, IKalturaObjectLoader, IKalturaSearchDataContributor, IKalturaApplicationTranslations, IKalturaAccessControlContributor
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
	const CATALOG_ITEM_INDEX_TARGET_LANGUAGE = 'citl';
	
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
			return array('SyncReachCreditTaskBatchType', 'ReachConditionType', 'ReachEntryVendorTasksCsvBatchType', 'ReachRuleActionType', 'EntryVendorTaskExportObjectType');
		
		if ($baseEnumName == 'BatchJobType')
			return array('SyncReachCreditTaskBatchType', 'ReachEntryVendorTasksCsvBatchType');
		
		if ($baseEnumName == 'ConditionType')
			return array('ReachConditionType');
		
		if ($baseEnumName == 'RuleActionType')
			return array('ReachRuleActionType');
		
		if ($baseEnumName == 'ExportObjectType')
			return array('EntryVendorTaskExportObjectType');
		
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
		if ($partner)
		{
			return $partner->getPluginEnabled(self::PLUGIN_NAME);
		}
		return false;
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
		$pages[] = new CatalogItemListAction();
		$pages[] = new CatalogItemConfigureAction();
		$pages[] = new CatalogItemSetStatusAction();
		$pages[] = new CatalogItemExportAction();
		$pages[] = new CatalogItemImportAction();
		$pages[] = new CatalogItemImportResultAction();
		$pages[] = new PartnerCatalogItemListAction();
		$pages[] = new PartnerCatalogItemConfigureAction();
		$pages[] = new PartnerCatalogItemSetStatusAction();
		$pages[] = new PartnerCatalogItemsCloneAction();
		$pages[] = new ReachProfileListAction();
		$pages[] = new ReachProfileConfigureAction();
		$pages[] = new ReachProfileSetStatusAction();
		$pages[] = new ReachProfileCreditConfigureAction();
		$pages[] = new ReachProfileCloneAction();
		$pages[] = new ReachRequestsListAction();
		$pages[] = new ReachRequestsExportAction();
		$pages[] = new ReachRequestsAbortAction();

		return $pages;
	}
	
	/* (non-PHPdoc)
 	 * @see IKalturaPending::dependsOn()
 	*/
	public static function dependsOn()
	{
		$eventNotificationDependency = new KalturaDependency(EventNotificationPlugin::getPluginName());
		$bulkUploadDependency = new KalturaDependency(BulkUploadPlugin::getPluginName());
		$captionPluginDependency = new KalturaDependency(CaptionPlugin::getPluginName());
		$scheduledEventDependency = new KalturaDependency(SchedulePlugin::getPluginName());
		$scheduledEventNotificationDependency = new KalturaDependency(ScheduleEventNotificationsPlugin::getPluginName());
		$transcriptPluginDependency = new KalturaDependency(TranscriptPlugin::getPluginName());

		return array($eventNotificationDependency, $bulkUploadDependency, $captionPluginDependency, $scheduledEventDependency, $scheduledEventNotificationDependency, $transcriptPluginDependency);
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
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getExportTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('ExportObjectType', $value);
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
			return new kAddEntryVendorTaskAction();
		
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
		
		if($baseClass == 'KalturaJobData' && $enumValue == BatchJobType::EXPORT_CSV && (isset($constructorArgs['coreJobSubType']) &&  $constructorArgs['coreJobSubType']== self::getExportTypeCoreValue(EntryVendorTaskExportObjectType::ENTRY_VENDOR_TASK)))
		{
			return new KalturaEntryVendorTaskCsvJobData();
		}
		
		if ($baseClass == 'KObjectExportEngine' && $enumValue == KalturaExportObjectType::ENTRY_VENDOR_TASK)
		{
			return new KExportEntryVendorTaskEngine($constructorArgs);
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
		if($catalogItem->getTargetLanguage())
			$data .= " " . self::CATALOG_ITEM_INDEX_TARGET_LANGUAGE . $catalogItem->getTargetLanguage();
		
		$data .= " " . self::CATALOG_ITEM_INDEX_SUFFIX . $partnerId;
		
		return $data;
	}

    /**
     * @inheritDoc
     */
    public static function getTranslations($locale)
    {
        $array = array();

        $langFilePath = __DIR__ . "/config/lang/$locale.php";
        if(!file_exists($langFilePath))
        {
            $default = 'en';
            $langFilePath = __DIR__ . "/config/lang/$default.php";
        }

        KalturaLog::info("Loading file [$langFilePath]");
        $array = include($langFilePath);

        return array($locale => $array);
    }

	public static function shouldSkipRulesValidation($entryId, $ks)
	{
		if(	($ks->getRole() === UserRoleId::REACH_VENDOR_ROLE)			&&
			($ks->getPrivilegeValue(kSessionBase::PRIVILEGE_VIEW) === $entryId)	&&
			(ReachProfilePeer::retrieveByPartnerId($ks->getPartnerId())) )
		{
			KalturaLog::debug("Reach vendor KS, skip Access Control rules validation for entry ($entryId)");
			return true;
		}

		return false;
	}

	public static function isEntryTypeSupportedForReach($entryType)
	{
		$supportedEntryTypes = kConf::get('reach_supported_entry_types', kConfMapNames::RUNTIME_CONFIG, array(entryType::MEDIA_CLIP, entryType::LIVE_STREAM, entryType::DOCUMENT));

		return in_array($entryType, $supportedEntryTypes);
	}

	public static function getServiceFeatureName($serviceFeature)
	{
		$constantNames = ReachPlugin::getServiceFeatureNames();
		return isset($constantNames[$serviceFeature]) ? $constantNames[$serviceFeature] : "";
	}

	public static function getServiceFeatureNames()
	{
		$reflector = new ReflectionClass('VendorServiceFeature');
		return array_flip($reflector->getConstants());
	}

	public static function getTranslatedServiceFeature($serviceFeature)
	{
		$serviceFeatureName = ReachPlugin::getServiceFeatureName($serviceFeature);
		return str_replace("_", " ", strtolower($serviceFeatureName));
	}

	public static function getServiceFeatureClassName($serviceFeature)
	{
		$serviceFeatureName = ReachPlugin::getServiceFeatureName($serviceFeature);
		$finalName = '';
		if($serviceFeatureName)
		{
			$serviceFeatureWords = explode("_", strtolower($serviceFeatureName));
			foreach ($serviceFeatureWords as $serviceFeatureWord)
			{
				if(strlen($serviceFeatureWord) > 0)
				{
					$capitalFirstChar = strtoupper(substr($serviceFeatureWord, 0, 1));
					$finalName .= $capitalFirstChar . substr($serviceFeatureWord, 1);
				}
			}
		}
		return $finalName;
	}

	public static function getCatalogItemCoreName($serviceFeature)
	{
		switch ($serviceFeature)
		{
			default:
				$serviceFeatureNameInClass = ReachPlugin::getServiceFeatureClassName($serviceFeature);
				return "Vendor" . $serviceFeatureNameInClass . "CatalogItem";
		}
	}

	public static function getCatalogItemName($serviceFeature)
	{
		switch ($serviceFeature)
		{
			default:
				return "Kaltura" . ReachPlugin::getCatalogItemCoreName($serviceFeature);
		}
	}

	public static function getCatalogItemCoreFilterName($serviceFeature)
	{
		switch ($serviceFeature)
		{
			default:
				return ReachPlugin::getCatalogItemCoreName($serviceFeature) . "Filter";
		}
	}
}
