<?php
/**
 * @package plugins.systemPartner
 * @subpackage api.objects
 */
class KalturaSystemPartnerConfiguration extends KalturaObject
{
	/**
	 * @var int
	 * @readonly
	 */
	public $id;
	
	/**
	 * @var string
	 */
	public $partnerName;
	
	/**
	 * @var string
	 */
	public $description;
	
	/**
	 * @var string
	 */
	public $adminName;
	
	/**
	 * @var string
	 */
	public $adminEmail;
	
	/**
	 * @var string
	 */
	public $host;
	
	/**
	 * @var string
	 */
	public $cdnHost;

	/**
	 * @var string
	 */
	public $cdnHostWhiteList;

	/**
	 * @var string
	 */
	public $thumbnailHost;
	
	/**
	 * @var int
	 */
	public $partnerPackage;
	
	/**
	 * @var int
	 */
	public $monitorUsage;
	
	/**
	 * @var bool
	 */
	public $moderateContent;
	
	/**
	 * @var bool
	 */
	public $storageDeleteFromKaltura;
	
	/**
	 * @var KalturaStorageServePriority
	 */
	public $storageServePriority;
	
	/**
	 * 
	 * @var int
	 */
	public $kmcVersion;
	
	/**
	 * @var int
	 * @deprecated
	 */
	public $restrictThumbnailByKs;

	/**
	 * @var bool
	 */
	public $supportAnimatedThumbnails;
		
	/**
	 * @var int
	 */
	public $defThumbOffset;
	
	/**
	 * @var int
	 */
	public $defThumbDensity;
	
	/**
	 * @var int
	 */
	public $userSessionRoleId;
	
	/**
	 * @var int
	 */
	public $adminSessionRoleId;
	
	/**
	 * @var string
	 */
	public $alwaysAllowedPermissionNames;
	
	/**
	 * @var bool
	 */
	public $importRemoteSourceForConvert;
	
	/**
	 * @var KalturaPermissionArray
	 */
	public $permissions;
	
	/**
	 * @var string
	 */
	public $notificationsConfig;
	
	/**
	 * @var bool
	 */
	public $allowMultiNotification;
		
	/**
	 * @var int
	 */
	public $loginBlockPeriod; 
	
	/**
	 * @var int
	 */
	public $numPrevPassToKeep;
	
	/**
	 * @var int
	 */
	public $passReplaceFreq;
	
	/**
	 * @var bool
	 */
	public $isFirstLogin;
	
	/**
	 * @var KalturaPartnerGroupType
	 */
	public $partnerGroupType;
	
	/**
	 * @var int
	 */
	public $partnerParentId;
	
	/**
	 * @var KalturaSystemPartnerLimitArray
	 */
	public $limits;
	
	/**
	 * http/rtmp/hdnetwork
	 * @var string
	 */
	public $streamerType;
	
	/**
	 * http/https, rtmp/rtmpe
	 * @var string
	 */
	public $mediaProtocol;
	
	/**
	 * @var string 
	 */
	public $extendedFreeTrailExpiryReason;
	
	/**
	 *  Unix timestamp (In seconds)
	 * 
	 * @var int
	 * 
	 */
	public $extendedFreeTrailExpiryDate;
	
	/**
	 * @var int
	 */
	public $extendedFreeTrail;
	
	/**
	 * @var string
	 */
	public $crmId;

	/**
	 * @var string
	 */
	public $referenceId;
	
	/**
	 * @var string
	 */
	public $crmLink;
	
	/**
	 * @var string
	 */
	public $verticalClasiffication;
	
	/**
	 * @var string
	 */
	public $partnerPackageClassOfService;
	
	/**
	 * 
	 * @var bool
	 */
	public $enableBulkUploadNotificationsEmails;
	
	/**
	 * @var string 
	 */
	public $deliveryProfileIds;
	
	/**
	 * @var string
	 */
	public $liveDeliveryProfileIds;
	
	/**
	 * @var bool 
	 */
	public $enforceDelivery;
	
	/**
	 * 
	 * @var string
	 */
	public $bulkUploadNotificationsEmail;
	
	/**
	 * 
	 * @var bool
	 */
	public $internalUse;
	

	/**
	 * @var KalturaSourceType
	 */
	public $defaultLiveStreamEntrySourceType;

	
	/**
	 * @var string
	 */
	public $liveStreamProvisionParams;
	

	/**
	 * 
	 * @var KalturaBaseEntryFilter
	 */
	public $autoModerateEntryFilter;
	
	/**
	 * @var string
	 */
	public $logoutUrl;
	
	/**
	 * @var bool
	 */
	public $defaultEntitlementEnforcement;
	
	/**
	 * @var int
	 */
	public $cacheFlavorVersion;

	/**
	 * @var int
	 */
	public $apiAccessControlId;
	
	/**
	 * @var string
	 */
	public $defaultDeliveryType;
	
	/**
	 * @var string
	 */
	public $defaultEmbedCodeType;
	
	/**
	 * @var KalturaKeyBooleanValueArray
	 */
	public $customDeliveryTypes;
	
	/**
	 * @var bool
	 */
	public $restrictEntryByMetadata;
	
	/**
	 * @var KalturaLanguageCode
	 */
	public $language;
	
	/**
	 * @var string
	 */
	public $audioThumbEntryId;

	/**
	 * @var string
	 */
	public $liveThumbEntryId;
	
	/**
	 * @var bool
	 */
	public $timeAlignedRenditions;

	/**
	 * @var int
	 */
	public $htmlPurifierBehaviour;

	/**
	 * @var bool
	 */
	public $htmlPurifierBaseListUsage;
	
	/**
 * @var int
 */
	public $defaultLiveStreamSegmentDuration;
	
	
	private static $map_between_objects = array
	(
		"id",
		"partnerName",
		"description",
		"adminName",
		"adminEmail",
		"host",
		"cdnHost",
		"cdnHostWhiteList",
	    "thumbnailHost",
		//"maxBulkSize",
		"partnerPackage",
		"monitorUsage",
		"moderateContent",
		"storageDeleteFromKaltura",
		"storageServePriority",
		"kmcVersion",
		"defThumbOffset",
		"defThumbDensity",
	//"adminLoginUsersQuota",
		"userSessionRoleId",
		"adminSessionRoleId",
		"alwaysAllowedPermissionNames",
		"importRemoteSourceForConvert",
		"notificationsConfig",
		"allowMultiNotification",
		//"maxLoginAttempts",
		"loginBlockPeriod",
		"numPrevPassToKeep",
		"passReplaceFreq",
		"isFirstLogin",
		"enforceDelivery",
		"partnerGroupType",
		"partnerParentId",
		"streamerType",
		"mediaProtocol",
		"extendedFreeTrailExpiryDate",
		"extendedFreeTrailExpiryReason",
		"extendedFreeTrail",
		"restrictThumbnailByKs",
		"supportAnimatedThumbnails",
		"crmLink",
		"crmId",
		"referenceId",
		"verticalClasiffication",
		"partnerPackageClassOfService",
		"enableBulkUploadNotificationsEmails",
		"bulkUploadNotificationsEmail",
		"internalUse",
		"defaultLiveStreamEntrySourceType",
		"liveStreamProvisionParams",
		"logoutUrl",
	    "defaultEntitlementEnforcement",
		"cacheFlavorVersion",
		"apiAccessControlId",
		"defaultDeliveryType",
		"defaultEmbedCodeType",
		"customDeliveryTypes",
		"language",
		"audioThumbEntryId",
		"liveThumbEntryId",		
		"deliveryProfileIds",
		"liveDeliveryProfileIds",
	    "timeAlignedRenditions",
		"htmlPurifierBehaviour",
		"htmlPurifierBaseListUsage",
		"defaultLiveStreamSegmentDuration"
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function doFromObject($source_object, KalturaDetachedResponseProfile $responseProfile = null)
	{
		parent::doFromObject($source_object, $responseProfile);
		
		$permissions = PermissionPeer::retrievePartnerLevelPermissions($source_object->getId());
		$this->permissions = KalturaPermissionArray::fromDbArray($permissions);
		$this->limits = KalturaSystemPartnerLimitArray::fromPartner($source_object);
		
		$this->restrictEntryByMetadata = $source_object->getShouldApplyAccessControlOnEntryMetadata();
		$this->htmlPurifierBaseListUsage = $source_object->getHtmlPurifierBaseListUsage();
		
		$dbAutoModerationEntryFilter = $source_object->getAutoModerateEntryFilter();
		if ($dbAutoModerationEntryFilter)
		{
			$this->autoModerateEntryFilter = new KalturaBaseEntryFilter();
			$this->autoModerateEntryFilter->fromObject($dbAutoModerationEntryFilter);
		}

		$this->partnerName = kString::stripUtf8InvalidChars($this->partnerName);
		$this->description = kString::stripUtf8InvalidChars($this->description);
		$this->adminName = kString::stripUtf8InvalidChars($this->adminName);
		if($this->deliveryProfileIds) {
			$this->deliveryProfileIds = json_encode($this->deliveryProfileIds);
		}
		
		if($this->liveDeliveryProfileIds) {
			$this->liveDeliveryProfileIds = json_encode($this->liveDeliveryProfileIds);
		}
	}
	
	private function copyMissingConversionProfiles(Partner $partner)
	{
		$templatePartner = PartnerPeer::retrieveByPK($partner->getI18nTemplatePartnerId() ? $partner->getI18nTemplatePartnerId() : kConf::get('template_partner_id'));
		if($templatePartner)
			myPartnerUtils::copyConversionProfiles($templatePartner, $partner, true);
	}
	
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		$audioThumbEntryId = $this->audioThumbEntryId;
		if ($audioThumbEntryId)
		{
			$audioThumbEntry = entryPeer::retrieveByPK($audioThumbEntryId);
			if (!$audioThumbEntry || $audioThumbEntry->getMediaType() != entry::ENTRY_MEDIA_TYPE_IMAGE)
				throw new KalturaAPIException(SystemPartnerErrors::PARTNER_AUDIO_THUMB_ENTRY_ID_ERROR, $audioThumbEntryId);
		}

		$liveThumbEntryId = $this->liveThumbEntryId;
		if ($liveThumbEntryId)
		{
			$liveThumbEntry = entryPeer::retrieveByPK($liveThumbEntryId);
			if (!$liveThumbEntry || $liveThumbEntry->getMediaType() != entry::ENTRY_MEDIA_TYPE_IMAGE)
				throw new KalturaAPIException(SystemPartnerErrors::PARTNER_LIVE_THUMB_ENTRY_ID_ERROR, $liveThumbEntryId);
		}
		
		if (!$this->isNull('defaultLiveStreamSegmentDuration'))
		{
			if (!PermissionPeer::isValidForPartner(PermissionName::FEATURE_DYNAMIC_SEGMENT_DURATION, $this->id)) {
				throw new KalturaAPIException(KalturaErrors::DYNAMIC_SEGMENT_DURATION_DISABLED, $this->getFormattedPropertyNameWithClassName('defaultLiveStreamSegmentDuration'));
			}
			
			$this->validatePropertyNumeric('defaultLiveStreamSegmentDuration');
			$this->validatePropertyMinMaxValue('defaultLiveStreamSegmentDuration', KalturaLiveEntry::MIN_ALLOWED_SEGMENT_DURATION_MILLISECONDS, KalturaLiveEntry::MAX_ALLOWED_SEGMENT_DURATION_MILLISECONDS);
		}
	
		return parent::validateForUpdate($sourceObject,$propertiesToSkip);
	}
	
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		$object_to_fill = parent::toObject($object_to_fill, $props_to_skip);
		if (!$object_to_fill) {
			KalturaLog::err('Cannot find object to fill');
			return null;
		}
		
		if(empty($this->deliveryProfileIds)) {
			$object_to_fill->setDeliveryProfileIds(array());
		} else {
			$object_to_fill->setDeliveryProfileIds(json_decode($this->deliveryProfileIds, true));
		}
		
		if(empty($this->liveDeliveryProfileIds)) {
			$object_to_fill->setLiveDeliveryProfileIds(array());
		} else {
			$object_to_fill->setLiveDeliveryProfileIds(json_decode($this->liveDeliveryProfileIds, true));
		}
		
		if (!$this->isNull('partnerParentId') && $this->partnerParentId > 0)
		{
		    $parentPartnerDb = PartnerPeer::retrieveByPK($this->partnerParentId);
		    
		    if ($parentPartnerDb->getPartnerGroupType() != KalturaPartnerGroupType::GROUP 
		        && $parentPartnerDb->getPartnerGroupType() != KalturaPartnerGroupType::VAR_GROUP)
		    {
		        throw new KalturaAPIException(SystemPartnerErrors::UNABLE_TO_FORM_GROUP_ASSOCIATION, $this->partnerParentId, $parentPartnerDb->getPartnerGroupType());
		    }
		}
		
		if(!is_null($this->permissions))
		{
			foreach($this->permissions as $permission)
			{
				$dbPermission = PermissionPeer::getByNameAndPartner($permission->name, array($object_to_fill->getId()));
				if($dbPermission)
				{
					$dbPermission->setStatus($permission->status);
				}
				else
				{
					$dbPermission = new Permission();
					$dbPermission->setType($permission->type);
					$dbPermission->setPartnerId($object_to_fill->getId());
					//$dbPermission->setStatus($permission->status);
					$permission->type = null;
					$dbPermission = $permission->toInsertableObject($dbPermission);
				}
				
				$dbPermission->save();
				
				
				if($dbPermission->getStatus() == PermissionStatus::ACTIVE) {
					$this->enablePermissionForPlugins($object_to_fill->getId(), $dbPermission->getName());
				}
				else if ($dbPermission->getName == PermissionName::FEATURE_DYNAMIC_SEGMENT_DURATION) {
					$this->defaultLiveStreamSegmentDuration = LiveEntry::DEFAULT_SEGMENT_DURATION_MILLISECONDS;
				}
			}
			
			//Raise template partner's conversion profiles (so far) and check whether the partner now has permissions for them.
			$this->copyMissingConversionProfiles($object_to_fill);
		
		}
		
		if (!is_null($this->limits))
		{
			foreach ($this->limits as $limit)
			{
				$limit->apply($object_to_fill);
			}
		}
		
		if (!is_null($this->autoModerateEntryFilter))
		{
			$dbAutoModerationEntryFilter = new entryFilter();
			$this->autoModerateEntryFilter->toObject($dbAutoModerationEntryFilter);
			$object_to_fill->setAutoModerateEntryFilter($dbAutoModerationEntryFilter);
		}
		
		$object_to_fill->setShouldApplyAccessControlOnEntryMetadata($this->restrictEntryByMetadata);
		
		return $object_to_fill;
	}
	
	private function enablePermissionForPlugins($partnerId, $permissionName)
	{
		if(strstr($permissionName, '_PLUGIN_PERMISSION'))
		{
			$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaPermissionsEnabler');
			foreach($pluginInstances as $pluginInstance)
			{
				$pluginInstance->permissionEnabled($partnerId, $permissionName);
			}					
		}
	}
}