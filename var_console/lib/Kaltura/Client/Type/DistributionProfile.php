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
abstract class Kaltura_Client_Type_DistributionProfile extends Kaltura_Client_ObjectBase
{
	public function getKalturaObjectType()
	{
		return 'KalturaDistributionProfile';
	}
	
	public function __construct(SimpleXMLElement $xml = null)
	{
		parent::__construct($xml);
		
		if(is_null($xml))
			return;
		
		if(count($xml->id))
			$this->id = (int)$xml->id;
		if(count($xml->createdAt))
			$this->createdAt = (int)$xml->createdAt;
		if(count($xml->updatedAt))
			$this->updatedAt = (int)$xml->updatedAt;
		if(count($xml->partnerId))
			$this->partnerId = (int)$xml->partnerId;
		$this->providerType = (string)$xml->providerType;
		$this->name = (string)$xml->name;
		if(count($xml->status))
			$this->status = (int)$xml->status;
		if(count($xml->submitEnabled))
			$this->submitEnabled = (int)$xml->submitEnabled;
		if(count($xml->updateEnabled))
			$this->updateEnabled = (int)$xml->updateEnabled;
		if(count($xml->deleteEnabled))
			$this->deleteEnabled = (int)$xml->deleteEnabled;
		if(count($xml->reportEnabled))
			$this->reportEnabled = (int)$xml->reportEnabled;
		$this->autoCreateFlavors = (string)$xml->autoCreateFlavors;
		$this->autoCreateThumb = (string)$xml->autoCreateThumb;
		$this->optionalFlavorParamsIds = (string)$xml->optionalFlavorParamsIds;
		$this->requiredFlavorParamsIds = (string)$xml->requiredFlavorParamsIds;
		if(empty($xml->optionalThumbDimensions))
			$this->optionalThumbDimensions = array();
		else
			$this->optionalThumbDimensions = Kaltura_Client_Client::unmarshalItem($xml->optionalThumbDimensions);
		if(empty($xml->requiredThumbDimensions))
			$this->requiredThumbDimensions = array();
		else
			$this->requiredThumbDimensions = Kaltura_Client_Client::unmarshalItem($xml->requiredThumbDimensions);
		if(count($xml->sunriseDefaultOffset))
			$this->sunriseDefaultOffset = (int)$xml->sunriseDefaultOffset;
		if(count($xml->sunsetDefaultOffset))
			$this->sunsetDefaultOffset = (int)$xml->sunsetDefaultOffset;
		if(count($xml->recommendedStorageProfileForDownload))
			$this->recommendedStorageProfileForDownload = (int)$xml->recommendedStorageProfileForDownload;
		if(count($xml->recommendedDcForDownload))
			$this->recommendedDcForDownload = (int)$xml->recommendedDcForDownload;
	}
	/**
	 * Auto generated unique id
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * Profile creation date as Unix timestamp (In seconds)
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * Profile last update date as Unix timestamp (In seconds)
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Enum_DistributionProviderType
	 * @insertonly
	 */
	public $providerType = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Enum_DistributionProfileStatus
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Enum_DistributionProfileActionStatus
	 */
	public $submitEnabled = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Enum_DistributionProfileActionStatus
	 */
	public $updateEnabled = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Enum_DistributionProfileActionStatus
	 */
	public $deleteEnabled = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Enum_DistributionProfileActionStatus
	 */
	public $reportEnabled = null;

	/**
	 * Comma separated flavor params ids that should be auto converted
	 * 	 
	 *
	 * @var string
	 */
	public $autoCreateFlavors = null;

	/**
	 * Comma separated thumbnail params ids that should be auto generated
	 * 	 
	 *
	 * @var string
	 */
	public $autoCreateThumb = null;

	/**
	 * Comma separated flavor params ids that should be submitted if ready
	 * 	 
	 *
	 * @var string
	 */
	public $optionalFlavorParamsIds = null;

	/**
	 * Comma separated flavor params ids that required to be readt before submission
	 * 	 
	 *
	 * @var string
	 */
	public $requiredFlavorParamsIds = null;

	/**
	 * Thumbnail dimensions that should be submitted if ready
	 * 	 
	 *
	 * @var array of KalturaDistributionThumbDimensions
	 */
	public $optionalThumbDimensions;

	/**
	 * Thumbnail dimensions that required to be readt before submission
	 * 	 
	 *
	 * @var array of KalturaDistributionThumbDimensions
	 */
	public $requiredThumbDimensions;

	/**
	 * If entry distribution sunrise not specified that will be the default since entry creation time, in seconds
	 * 	 
	 *
	 * @var int
	 */
	public $sunriseDefaultOffset = null;

	/**
	 * If entry distribution sunset not specified that will be the default since entry creation time, in seconds
	 * 	 
	 *
	 * @var int
	 */
	public $sunsetDefaultOffset = null;

	/**
	 * The best external storage to be used to download the asset files from
	 * 	 
	 *
	 * @var int
	 */
	public $recommendedStorageProfileForDownload = null;

	/**
	 * The best Kaltura data center to be used to download the asset files to
	 * 	 
	 *
	 * @var int
	 */
	public $recommendedDcForDownload = null;


}

