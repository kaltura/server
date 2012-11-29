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

class Kaltura_Client_Type_Partner extends Kaltura_Client_ObjectBase
{
	public function getKalturaObjectType()
	{
		return 'KalturaPartner';
	}
	
	public function __construct(SimpleXMLElement $xml = null)
	{
		parent::__construct($xml);
		
		if(is_null($xml))
			return;
		
		if(count($xml->id))
			$this->id = (int)$xml->id;
		$this->name = (string)$xml->name;
		$this->website = (string)$xml->website;
		$this->notificationUrl = (string)$xml->notificationUrl;
		if(count($xml->appearInSearch))
			$this->appearInSearch = (int)$xml->appearInSearch;
		if(count($xml->createdAt))
			$this->createdAt = (int)$xml->createdAt;
		$this->adminName = (string)$xml->adminName;
		$this->adminEmail = (string)$xml->adminEmail;
		$this->description = (string)$xml->description;
		if(count($xml->commercialUse))
			$this->commercialUse = (int)$xml->commercialUse;
		$this->landingPage = (string)$xml->landingPage;
		$this->userLandingPage = (string)$xml->userLandingPage;
		$this->contentCategories = (string)$xml->contentCategories;
		if(count($xml->type))
			$this->type = (int)$xml->type;
		$this->phone = (string)$xml->phone;
		$this->describeYourself = (string)$xml->describeYourself;
		if(!empty($xml->adultContent))
			$this->adultContent = true;
		$this->defConversionProfileType = (string)$xml->defConversionProfileType;
		if(count($xml->notify))
			$this->notify = (int)$xml->notify;
		if(count($xml->status))
			$this->status = (int)$xml->status;
		if(count($xml->allowQuickEdit))
			$this->allowQuickEdit = (int)$xml->allowQuickEdit;
		if(count($xml->mergeEntryLists))
			$this->mergeEntryLists = (int)$xml->mergeEntryLists;
		$this->notificationsConfig = (string)$xml->notificationsConfig;
		if(count($xml->maxUploadSize))
			$this->maxUploadSize = (int)$xml->maxUploadSize;
		if(count($xml->partnerPackage))
			$this->partnerPackage = (int)$xml->partnerPackage;
		$this->secret = (string)$xml->secret;
		$this->adminSecret = (string)$xml->adminSecret;
		$this->cmsPassword = (string)$xml->cmsPassword;
		if(count($xml->allowMultiNotification))
			$this->allowMultiNotification = (int)$xml->allowMultiNotification;
		if(count($xml->adminLoginUsersQuota))
			$this->adminLoginUsersQuota = (int)$xml->adminLoginUsersQuota;
		$this->adminUserId = (string)$xml->adminUserId;
		$this->firstName = (string)$xml->firstName;
		$this->lastName = (string)$xml->lastName;
		$this->country = (string)$xml->country;
		$this->state = (string)$xml->state;
		if(empty($xml->additionalParams))
			$this->additionalParams = array();
		else
			$this->additionalParams = Kaltura_Client_Client::unmarshalItem($xml->additionalParams);
		if(count($xml->publishersQuota))
			$this->publishersQuota = (int)$xml->publishersQuota;
		if(count($xml->partnerGroupType))
			$this->partnerGroupType = (int)$xml->partnerGroupType;
		if(!empty($xml->defaultEntitlementEnforcement))
			$this->defaultEntitlementEnforcement = true;
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
	public $name = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $website = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $notificationUrl = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $appearInSearch = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * deprecated - lastName and firstName replaces this field
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
	public $description = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Enum_CommercialUseType
	 */
	public $commercialUse = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $landingPage = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $userLandingPage = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $contentCategories = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Enum_PartnerType
	 */
	public $type = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $phone = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $describeYourself = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $adultContent = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $defConversionProfileType = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $notify = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Enum_PartnerStatus
	 * @readonly
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $allowQuickEdit = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $mergeEntryLists = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $notificationsConfig = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $maxUploadSize = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerPackage = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $secret = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $adminSecret = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $cmsPassword = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $allowMultiNotification = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $adminLoginUsersQuota = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $adminUserId = null;

	/**
	 * firstName and lastName replace the old (deprecated) adminName
	 * 	 
	 *
	 * @var string
	 */
	public $firstName = null;

	/**
	 * lastName and firstName replace the old (deprecated) adminName
	 * 	 
	 *
	 * @var string
	 */
	public $lastName = null;

	/**
	 * country code (2char) - this field is optional
	 * 	 
	 *
	 * @var string
	 */
	public $country = null;

	/**
	 * state code (2char) - this field is optional
	 * 	 
	 *
	 * @var string
	 */
	public $state = null;

	/**
	 * 
	 *
	 * @var array of KalturaKeyValue
	 * @insertonly
	 */
	public $additionalParams;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $publishersQuota = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Enum_PartnerGroupType
	 * @readonly
	 */
	public $partnerGroupType = null;

	/**
	 * 
	 *
	 * @var bool
	 * @readonly
	 */
	public $defaultEntitlementEnforcement = null;


}

