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
	 * @var string
	 */
	public $allowedFromEmailWhiteList;
	
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

    /**
     * @var int
     */
    public $defaultRecordingConversionProfile;

	/**
	 * @var string
	 */
	public $eSearchLanguages;

	/**
	 * @var int
	 */
	public $publisherEnvironmentType;

	/**
	 * @var string
	 */
	public $ovpEnvironmentUrl;

	/**
	 * @var string
	 */
	public $ottEnvironmentUrl;

	/**
	 * @var bool
	 */
	public $enableSelfServe;

	/**
	 * @var bool
	 */
	public $useTwoFactorAuthentication;

	/**
	 * @var bool
	 */
	public $useSso;

	/**
	 * @var bool
	 */
	public $blockDirectLogin;
	
	/**
	 * @var bool
	 */
	public $ignoreSynonymEsearch;

	/**
	 * @var bool
	 */
	public $avoidIndexingSearchHistory;

	/**
	 * @var int
	 */
	public $usagePercent;

	/**
	 * @var int
	 */
	public $eightyPercentWarning;

	/**
	 * @var int
	 */
	public $usageLimitWarning;

	/**
	 * @var int
	 */
	public $lastFreeTrialNotificationDay;

	/**
	 * @var bool
	 */
	public $extendedFreeTrailEndsWarning;

	/**
	 * @var bool
	 */
	public $enforceHttpsApi;
	
	/**
	 * @var string
	 */
	public $passwordStructureValidations;
	
	/**
	 * @var string
	 */
	public $passwordStructureValidationsDescription;
	
	
	/**
	 * @var int
	 */
	public $secondarySecretRoleId;
	
	/**
	 * @var string
	 */
	public $excludedAdminRoleName;
	
	/**
	 * @var string
	 */
	public $allowedDomains;
	
	/**
	 * @var int
	 */
	public $trigramPercentage;
	
	/**
	 * @var int
	 */
	public $maxWordForNgram;

	/**
	 * @var int
	 */
	public $searchMaxMetadataIndexLength;
	 
	/**
	 * @var KalturaTwoFactorAuthenticationMode
	 */
	public $twoFactorAuthenticationMode;
	
	/**
	 * @var bool
	 */
	public $purifyImageContent;

	/**
	 * @var bool
	 */
	public $isSelfServe;
	
	/**
	 * @var string
	 */
	public $eventPlatformAllowedTemplates;
	
	/**
	 * @var int
	 */
	public $recycleBinRetentionPeriod;
	
	/**
	 * @var string
	 */
	public $customAnalyticsDomain;

	/**
	 * @var string
	 */
	public $allowedEmailDomainsForAdmins;

	
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
		"allowedFromEmailWhiteList",
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
		"defaultLiveStreamSegmentDuration",
		"defaultRecordingConversionProfile",
		"eSearchLanguages",
		"publisherEnvironmentType",
		"ovpEnvironmentUrl",
		"ottEnvironmentUrl",
		"enableSelfServe",
		"useTwoFactorAuthentication",
		"useSso",
		"blockDirectLogin",
		"ignoreSynonymEsearch",
		"avoidIndexingSearchHistory",
		"usagePercent",
		"eightyPercentWarning",
		"usageLimitWarning",
		"lastFreeTrialNotificationDay",
		"extendedFreeTrailEndsWarning",
		'enforceHttpsApi',
		'secondarySecretRoleId',
		'excludedAdminRoleName',
		'allowedDomains',
		'trigramPercentage',
		'maxWordForNgram',
		'searchMaxMetadataIndexLength',
		'twoFactorAuthenticationMode',
		'purifyImageContent',
		'isSelfServe',
		'eventPlatformAllowedTemplates',
		'recycleBinRetentionPeriod',
		'customAnalyticsDomain',
		'allowedEmailDomainsForAdmins',
	);

	const PRIVACY_CONTEX_THRESHOLD_FOR_CATEGORY_LIMIT = 1;

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
		$this->purifyImageContent = $source_object->getPurifyImageContent();
		$this->ovpEnvironmentUrl = $source_object->getOvpEnvironmentUrl();
		$this->ottEnvironmentUrl = $source_object->getOttEnvironmentUrl();
		
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
		if($this->eSearchLanguages) {
			$this->eSearchLanguages = json_encode($this->eSearchLanguages);
		}
		
		$passwordValidation = $source_object->getPasswordStructureValidations();
		if (!isset($passwordValidation[0]))
		{
			$this->passwordStructureValidations = array();
			$this->passwordStructureValidationsDescription = null;
		}

	}
	
	private function copyMissingConversionProfiles(Partner $partner)
	{
		$templatePartner = PartnerPeer::retrieveByPK($partner->getI18nTemplatePartnerId() ? $partner->getI18nTemplatePartnerId() : kConf::get('template_partner_id'));
		if($templatePartner)
			myPartnerUtils::copyConversionProfiles($templatePartner, $partner, true);
	}

	protected function isPermissionStatusAsRquired($permissionName, $status)
	{
		$dbPermission = PermissionPeer::getByNameAndPartner($permissionName, $this->id);
		foreach ($this->permissions as $permission)
		{
			if ($permission->name == $permissionName && $permission->status == $status && $dbPermission->getStatus() != $status)
			{
				return true;
			}
		}
		return false;
	}

	protected function buildExcedingPrivecyContextForCategoryQuery($partnerId, $threshold)
	{
		$data =
			[
				"bool" =>
				[
					"filter" =>
					[
						[
							"term" =>
							[
								"partner_status" =>
								[
									"value" => "p" . $partnerId . "s2"
								]
							]
						],
						[
							"script" =>
							[
								"script" =>
								[
									"source" => "
										def contexts = doc['privacy_contexts'];
										def count = 0;
										if (contexts instanceof List) 
										{
											for (context in contexts) 
											{
												if (!context.contains('DEFAULTPC') && !context.contains('NOTDEFAULTPC')) 
												{
													count++;
												}
											}
										} 
										else 
										{
											if (!contexts.contains('DEFAULTPC') && !contexts.contains('NOTDEFAULTPC')) 
											{
												count++;
											}
										}
										return count > params.threshold;
									",
									"params" =>
									[
										"threshold" => $threshold
									]
								]
							]
						]
					]
				]
			];
		return $data;
	}

	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		if ($this->isPermissionStatusAsRquired(PermissionName::FEATURE_DISABLE_CATEGORY_LIMIT, PermissionStatus::ACTIVE))
		{
			$indexName = kBaseESearch::getElasticIndexNamePerPartner( ElasticIndexMap::ELASTIC_CATEGORY_INDEX, kCurrentContext::getCurrentPartnerId());
			$params = array(
				'index' => $indexName,
				'type' => ElasticIndexMap::ELASTIC_CATEGORY_TYPE
			);
			$body = array();
			$body['_source'] = false;
			$body['query'] = $this->buildExcedingPrivecyContextForCategoryQuery($this->id, self::PRIVACY_CONTEX_THRESHOLD_FOR_CATEGORY_LIMIT);
			$params['body'] = $body;

			$elasticClient = new elasticClient();
			$results = $elasticClient->search($params, true);
			$categoriesCount = $results['hits']['total'];
			KalturaLog::notice('Categories with over [' . self::PRIVACY_CONTEX_THRESHOLD_FOR_CATEGORY_LIMIT . '] Privacy Context = ' . $categoriesCount);
			if ($categoriesCount > 0)
			{
				throw new KalturaAPIException(SystemPartnerErrors::PARTNER_CATEGORY_TOO_MANY_PRIVACY_CONTEXTS, $categoriesCount);
			}
		}
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

		if (!$this->isNull('defaultRecordingConversionProfile'))
        {
            $this->validatePropertyNumeric('defaultRecordingConversionProfile');
            $conversionProfile = conversionProfile2Peer::retrieveByPKAndPartnerId($this->defaultRecordingConversionProfile, $sourceObject->getId());
            if ($this->defaultRecordingConversionProfile != 0 && !$conversionProfile) {  // 0 is for disable the feature - not need to check if conversion profile exist
                throw new KalturaAPIException(SystemPartnerErrors::PARTNER_RECORDING_CONVERSION_PROFILE_ID_ERROR, $this->defaultRecordingConversionProfile);
            }
        }
		
		if ($sourceObject->getAllowedFromEmailWhiteList() != $this->allowedFromEmailWhiteList)
		{
			$this->validateAllowedFromEmailWhiteList();
		}
		return parent::validateForUpdate($sourceObject,$propertiesToSkip);
	}
	protected function validateAllowedFromEmailWhiteList()
	{
		if ($this->allowedFromEmailWhiteList)
		{
			$fromEmailList =  array_map('trim',explode(',',$this->allowedFromEmailWhiteList));
			$domains = self::getDomains($fromEmailList);
			$domainsNotAllowed = kSpfMailerValidator::getDomainsNotAllowed($domains);
			if ($domainsNotAllowed)
			{
				throw new KalturaAPIException(SystemPartnerErrors::DOMAINS_NOT_ALLOWED, implode(',',$domainsNotAllowed));
			}
			KalturaLog::debug('All domains are allowing Kaltura');
		}
	}

	protected static function getDomains($fromEmailList)
	{
		$domains = array();
		foreach ($fromEmailList as $email)
		{
			if ($email)	//don't handel empty emails
			{
				$domainPos = strpos($email,'@');
				if ($domainPos === false || $domainPos === strlen($email) - 1)
				{
					$domains[$email] = $email;
				}
				else if ($domainPos < strlen($email) - 1)	//get the domain from the email
				{
					$emailDomain = substr($email, $domainPos + 1);
					$domains[$emailDomain] = $emailDomain;
				}
			}
		}
		return $domains;
	}
	
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		$object_to_fill = parent::toObject($object_to_fill, $props_to_skip);
		if (!$object_to_fill)
		{
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

		if(empty($this->eSearchLanguages)) {
			$object_to_fill->setESearchLanguages(array());
		} else {
			$object_to_fill->setESearchLanguages(json_decode($this->eSearchLanguages, true));
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
				
				
				if($dbPermission->getStatus() == PermissionStatus::ACTIVE)
					$this->enablePermissionForPlugins($object_to_fill->getId(), $dbPermission->getName());
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
		$passwordToFill = array();
		if ($this->passwordStructureValidations)
		{
			$passwordValidationJson = json_decode($this->passwordStructureValidations);
			foreach ($passwordValidationJson as $regex => $description)
			{
				if ($regex)
				{
					$passwordToFill[] = array(trim($regex),$description);
				}
			}
			$object_to_fill->setPasswordStructureValidations($passwordToFill);
		}

		if(!is_null($this->secondarySecretRoleId))
		{
			$object_to_fill->setSecondarySecretRoleId($this->secondarySecretRoleId);
		}
		else
		{
			$object_to_fill->setSecondarySecretRoleId(null);
		}
		
		if(!is_null($this->excludedAdminRoleName))
		{
			$object_to_fill->setExcludedAdminRoleName($this->excludedAdminRoleName);
		}
		else
		{
			$object_to_fill->setExcludedAdminRoleName('');
		}
		
		if(!is_null($this->allowedDomains))
		{
			$object_to_fill->setAllowedDomains($this->allowedDomains);
		}
		else
		{
			$object_to_fill->setAllowedDomains('');
		}
		
		if (is_null($this->customAnalyticsDomain))
		{
			$object_to_fill->setCustomAnalyticsDomain('');
		}

		if (!is_null($this->allowedEmailDomainsForAdmins))
		{
			$object_to_fill->setAllowedEmailDomainsForAdmins($this->allowedEmailDomainsForAdmins);
		}
		else
		{
			$object_to_fill->setAllowedEmailDomainsForAdmins('');
		}
		
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
