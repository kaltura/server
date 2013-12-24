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
	 * @var string 
	 */
	public $rtmpUrl;
	
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
	public $deliveryRestrictions;
	
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
	 * @var KalturaStringArray
	 */
	public $disabledDeliveryTypes;
	
	/**
	 * @var bool
	 */
	public $restrictEntryByMetadata;
	
	/**
	 * @var KalturaLanguageCode
	 */
	public $language;
	
	private static $map_between_objects = array
	(
		"id",
		"partnerName",
		"description",
		"adminName",
		"adminEmail",
		"host",
		"cdnHost",
	    "thumbnailHost",
		//"maxBulkSize",
		"partnerPackage",
		"monitorUsage",
		"moderateContent",
		"rtmpUrl",
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
		"verticalClasiffication",
		"partnerPackageClassOfService",
		"enableBulkUploadNotificationsEmails",
		"deliveryRestrictions",
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
		"disabledDeliveryTypes",
		"language",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function fromObject ( $source_object  )
	{
		parent::fromObject($source_object);
		
		$permissions = PermissionPeer::retrievePartnerLevelPermissions($source_object->getId());
		$this->permissions = KalturaPermissionArray::fromDbArray($permissions);
		$this->limits = KalturaSystemPartnerLimitArray::fromPartner($source_object);
		
		$this->restrictEntryByMetadata = $source_object->getShouldApplyAccessControlOnEntryMetadata();
		
		$dbAutoModerationEntryFilter = $source_object->getAutoModerateEntryFilter();
		if ($dbAutoModerationEntryFilter)
		{
			$this->autoModerateEntryFilter = new KalturaBaseEntryFilter();
			$this->autoModerateEntryFilter->fromObject($dbAutoModerationEntryFilter);
		}

		$this->partnerName = kString::stripUtf8InvalidChars($this->partnerName);
		$this->description = kString::stripUtf8InvalidChars($this->description);
		$this->adminName = kString::stripUtf8InvalidChars($this->adminName);
	}
	
	private function createLiveConversionProfile(Partner $partner)
	{
		$c = new Criteria();
		$c->add(conversionProfile2Peer::PARTNER_ID, $partner->getId());
		$c->add(conversionProfile2Peer::TYPE, ConversionProfileType::LIVE_STREAM);
		$c->add(conversionProfile2Peer::STATUS, ConversionProfileStatus::ENABLED);
		
		if(conversionProfile2Peer::doCount($c))
			return;
			
		$templatePartner = PartnerPeer::retrieveByPK(kConf::get('template_partner_id'));
		if($templatePartner)
			myPartnerUtils::copyConversionProfiles($templatePartner, $partner, ConversionProfileType::LIVE_STREAM);
	}
	
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		$object_to_fill = parent::toObject($object_to_fill, $props_to_skip);
		if (!$object_to_fill) {
			KalturaLog::err('Cannot find object to fill');
			return null;
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
				KalturaLog::debug("partner: " . $object_to_fill->getId() . " add permissions: " . print_r($permission,true));
				
				$dbPermission = PermissionPeer::getByNameAndPartner($permission->name, array($object_to_fill->getId()));
				if($dbPermission)
				{
					KalturaLog::debug("add permissions: exists; set status; " . $permission->status);
					KalturaLog::debug("db permissions:  " . print_r($dbPermission,true));
					$dbPermission->setStatus($permission->status);
				}
				else
				{
					KalturaLog::debug("add permissions: didn't exists");
					$dbPermission = new Permission();
					$dbPermission->setType($permission->type);
					$dbPermission->setPartnerId($object_to_fill->getId());
					//$dbPermission->setStatus($permission->status);
					$permission->type = null;
					$dbPermission = $permission->toInsertableObject($dbPermission);
				}
				
				KalturaLog::debug("add permissions: save" . print_r($dbPermission,true));
				$dbPermission->save();
				
				if($dbPermission->getName() == PermissionName::FEATURE_LIVE_STREAM && $dbPermission->getStatus() == PermissionStatus::ACTIVE)
				{
					$this->createLiveConversionProfile($object_to_fill);
				}
			}
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
}