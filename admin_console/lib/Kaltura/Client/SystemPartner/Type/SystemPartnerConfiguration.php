<?php
// ===================================================================================================
//                           _  __     _ _
//                          | |/ /__ _| | |_ _  _ _ _ __ _
//                          | ' </ _` | |  _| || | '_/ _` |
//                          |_|\_\__,_|_|\__|\_,_|_| \__,_|
//
// This file is part of the Kaltura Collaborative Media Suite which allows users
// to do with audio, video, and animation what Wiki platfroms allow them to do with
// text.
//
// Copyright (C) 2006-2011  Kaltura Inc.
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Affero General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Affero General Public License for more details.
//
// You should have received a copy of the GNU Affero General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// @ignore
// ===================================================================================================

/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_SystemPartner_Type_SystemPartnerConfiguration extends Kaltura_Client_ObjectBase
{
	public function getKalturaObjectType()
	{
		return 'KalturaSystemPartnerConfiguration';
	}
	
	public function __construct(SimpleXMLElement $xml = null)
	{
		parent::__construct($xml);
		
		if(is_null($xml))
			return;
		
		if(count($xml->id))
			$this->id = (int)$xml->id;
		$this->partnerName = (string)$xml->partnerName;
		$this->description = (string)$xml->description;
		$this->adminName = (string)$xml->adminName;
		$this->adminEmail = (string)$xml->adminEmail;
		$this->host = (string)$xml->host;
		$this->cdnHost = (string)$xml->cdnHost;
		$this->thumbnailHost = (string)$xml->thumbnailHost;
		if(count($xml->partnerPackage))
			$this->partnerPackage = (int)$xml->partnerPackage;
		if(count($xml->monitorUsage))
			$this->monitorUsage = (int)$xml->monitorUsage;
		if(!empty($xml->moderateContent))
			$this->moderateContent = true;
		$this->rtmpUrl = (string)$xml->rtmpUrl;
		if(!empty($xml->storageDeleteFromKaltura))
			$this->storageDeleteFromKaltura = true;
		if(count($xml->storageServePriority))
			$this->storageServePriority = (int)$xml->storageServePriority;
		if(count($xml->kmcVersion))
			$this->kmcVersion = (int)$xml->kmcVersion;
		if(count($xml->restrictThumbnailByKs))
			$this->restrictThumbnailByKs = (int)$xml->restrictThumbnailByKs;
		if(!empty($xml->supportAnimatedThumbnails))
			$this->supportAnimatedThumbnails = true;
		if(count($xml->defThumbOffset))
			$this->defThumbOffset = (int)$xml->defThumbOffset;
		if(count($xml->defThumbDensity))
			$this->defThumbDensity = (int)$xml->defThumbDensity;
		if(count($xml->userSessionRoleId))
			$this->userSessionRoleId = (int)$xml->userSessionRoleId;
		if(count($xml->adminSessionRoleId))
			$this->adminSessionRoleId = (int)$xml->adminSessionRoleId;
		$this->alwaysAllowedPermissionNames = (string)$xml->alwaysAllowedPermissionNames;
		if(!empty($xml->importRemoteSourceForConvert))
			$this->importRemoteSourceForConvert = true;
		if(empty($xml->permissions))
			$this->permissions = array();
		else
			$this->permissions = Kaltura_Client_ParseUtils::unmarshalArray($xml->permissions, "KalturaPermission");
		$this->notificationsConfig = (string)$xml->notificationsConfig;
		if(!empty($xml->allowMultiNotification))
			$this->allowMultiNotification = true;
		if(count($xml->loginBlockPeriod))
			$this->loginBlockPeriod = (int)$xml->loginBlockPeriod;
		if(count($xml->numPrevPassToKeep))
			$this->numPrevPassToKeep = (int)$xml->numPrevPassToKeep;
		if(count($xml->passReplaceFreq))
			$this->passReplaceFreq = (int)$xml->passReplaceFreq;
		if(!empty($xml->isFirstLogin))
			$this->isFirstLogin = true;
		if(count($xml->partnerGroupType))
			$this->partnerGroupType = (int)$xml->partnerGroupType;
		if(count($xml->partnerParentId))
			$this->partnerParentId = (int)$xml->partnerParentId;
		if(empty($xml->limits))
			$this->limits = array();
		else
			$this->limits = Kaltura_Client_ParseUtils::unmarshalArray($xml->limits, "KalturaSystemPartnerLimit");
		$this->streamerType = (string)$xml->streamerType;
		$this->mediaProtocol = (string)$xml->mediaProtocol;
		$this->extendedFreeTrailExpiryReason = (string)$xml->extendedFreeTrailExpiryReason;
		if(count($xml->extendedFreeTrailExpiryDate))
			$this->extendedFreeTrailExpiryDate = (int)$xml->extendedFreeTrailExpiryDate;
		if(count($xml->extendedFreeTrail))
			$this->extendedFreeTrail = (int)$xml->extendedFreeTrail;
		$this->crmId = (string)$xml->crmId;
		$this->crmLink = (string)$xml->crmLink;
		$this->verticalClasiffication = (string)$xml->verticalClasiffication;
		$this->partnerPackageClassOfService = (string)$xml->partnerPackageClassOfService;
		if(!empty($xml->enableBulkUploadNotificationsEmails))
			$this->enableBulkUploadNotificationsEmails = true;
		$this->deliveryRestrictions = (string)$xml->deliveryRestrictions;
		$this->bulkUploadNotificationsEmail = (string)$xml->bulkUploadNotificationsEmail;
		if(!empty($xml->internalUse))
			$this->internalUse = true;
		$this->defaultLiveStreamEntrySourceType = (string)$xml->defaultLiveStreamEntrySourceType;
		$this->liveStreamProvisionParams = (string)$xml->liveStreamProvisionParams;
		if(!empty($xml->autoModerateEntryFilter))
			$this->autoModerateEntryFilter = Kaltura_Client_ParseUtils::unmarshalObject($xml->autoModerateEntryFilter, "KalturaBaseEntryFilter");
		$this->logoutUrl = (string)$xml->logoutUrl;
		if(!empty($xml->defaultEntitlementEnforcement))
			$this->defaultEntitlementEnforcement = true;
		if(count($xml->cacheFlavorVersion))
			$this->cacheFlavorVersion = (int)$xml->cacheFlavorVersion;
		if(count($xml->apiAccessControlId))
			$this->apiAccessControlId = (int)$xml->apiAccessControlId;
		$this->defaultDeliveryType = (string)$xml->defaultDeliveryType;
		$this->defaultEmbedCodeType = (string)$xml->defaultEmbedCodeType;
		if(empty($xml->disabledDeliveryTypes))
			$this->disabledDeliveryTypes = array();
		else
			$this->disabledDeliveryTypes = Kaltura_Client_ParseUtils::unmarshalArray($xml->disabledDeliveryTypes, "KalturaString");
		if(!empty($xml->restrictEntryByMetadata))
			$this->restrictEntryByMetadata = true;
		$this->language = (string)$xml->language;
		$this->audioThumbEntryId = (string)$xml->audioThumbEntryId;
		$this->liveThumbEntryId = (string)$xml->liveThumbEntryId;
	}
	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $partnerName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $adminName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $adminEmail = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $host = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $cdnHost = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $thumbnailHost = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerPackage = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $monitorUsage = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $moderateContent = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $rtmpUrl = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $storageDeleteFromKaltura = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Enum_StorageServePriority
	 */
	public $storageServePriority = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $kmcVersion = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $restrictThumbnailByKs = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $supportAnimatedThumbnails = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $defThumbOffset = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $defThumbDensity = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $userSessionRoleId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $adminSessionRoleId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $alwaysAllowedPermissionNames = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $importRemoteSourceForConvert = null;

	/**
	 * 
	 *
	 * @var array of KalturaPermission
	 */
	public $permissions;

	/**
	 * 
	 *
	 * @var string
	 */
	public $notificationsConfig = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $allowMultiNotification = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $loginBlockPeriod = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $numPrevPassToKeep = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $passReplaceFreq = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $isFirstLogin = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Enum_PartnerGroupType
	 */
	public $partnerGroupType = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerParentId = null;

	/**
	 * 
	 *
	 * @var array of KalturaSystemPartnerLimit
	 */
	public $limits;

	/**
	 * http/rtmp/hdnetwork
	 * 	 
	 *
	 * @var string
	 */
	public $streamerType = null;

	/**
	 * http/https, rtmp/rtmpe
	 * 	 
	 *
	 * @var string
	 */
	public $mediaProtocol = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $extendedFreeTrailExpiryReason = null;

	/**
	 * Unix timestamp (In seconds)
	 * 	 
	 *
	 * @var int
	 */
	public $extendedFreeTrailExpiryDate = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $extendedFreeTrail = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $crmId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $crmLink = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $verticalClasiffication = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $partnerPackageClassOfService = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $enableBulkUploadNotificationsEmails = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $deliveryRestrictions = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $bulkUploadNotificationsEmail = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $internalUse = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Enum_SourceType
	 */
	public $defaultLiveStreamEntrySourceType = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $liveStreamProvisionParams = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Type_BaseEntryFilter
	 */
	public $autoModerateEntryFilter;

	/**
	 * 
	 *
	 * @var string
	 */
	public $logoutUrl = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $defaultEntitlementEnforcement = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $cacheFlavorVersion = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $apiAccessControlId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $defaultDeliveryType = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $defaultEmbedCodeType = null;

	/**
	 * 
	 *
	 * @var array of KalturaString
	 */
	public $disabledDeliveryTypes;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $restrictEntryByMetadata = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Enum_LanguageCode
	 */
	public $language = null;

	/**
	 *
	 *
	 * @var string
	 */
	public $audioThumbEntryId = null;
	
	/**
	 *
	 *
	 * @var string
	 */
	public $liveThumbEntryId = null;
}

