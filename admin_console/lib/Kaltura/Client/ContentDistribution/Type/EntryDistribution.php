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
class Kaltura_Client_ContentDistribution_Type_EntryDistribution extends Kaltura_Client_ObjectBase
{
	public function getKalturaObjectType()
	{
		return 'KalturaEntryDistribution';
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
		if(count($xml->submittedAt))
			$this->submittedAt = (int)$xml->submittedAt;
		$this->entryId = (string)$xml->entryId;
		if(count($xml->partnerId))
			$this->partnerId = (int)$xml->partnerId;
		if(count($xml->distributionProfileId))
			$this->distributionProfileId = (int)$xml->distributionProfileId;
		if(count($xml->status))
			$this->status = (int)$xml->status;
		if(count($xml->sunStatus))
			$this->sunStatus = (int)$xml->sunStatus;
		if(count($xml->dirtyStatus))
			$this->dirtyStatus = (int)$xml->dirtyStatus;
		$this->thumbAssetIds = (string)$xml->thumbAssetIds;
		$this->flavorAssetIds = (string)$xml->flavorAssetIds;
		if(count($xml->sunrise))
			$this->sunrise = (int)$xml->sunrise;
		if(count($xml->sunset))
			$this->sunset = (int)$xml->sunset;
		$this->remoteId = (string)$xml->remoteId;
		if(count($xml->plays))
			$this->plays = (int)$xml->plays;
		if(count($xml->views))
			$this->views = (int)$xml->views;
		if(empty($xml->validationErrors))
			$this->validationErrors = array();
		else
			$this->validationErrors = Kaltura_Client_Client::unmarshalItem($xml->validationErrors);
		if(count($xml->errorType))
			$this->errorType = (int)$xml->errorType;
		if(count($xml->errorNumber))
			$this->errorNumber = (int)$xml->errorNumber;
		$this->errorDescription = (string)$xml->errorDescription;
		if(count($xml->hasSubmitResultsLog))
			$this->hasSubmitResultsLog = (int)$xml->hasSubmitResultsLog;
		if(count($xml->hasSubmitSentDataLog))
			$this->hasSubmitSentDataLog = (int)$xml->hasSubmitSentDataLog;
		if(count($xml->hasUpdateResultsLog))
			$this->hasUpdateResultsLog = (int)$xml->hasUpdateResultsLog;
		if(count($xml->hasUpdateSentDataLog))
			$this->hasUpdateSentDataLog = (int)$xml->hasUpdateSentDataLog;
		if(count($xml->hasDeleteResultsLog))
			$this->hasDeleteResultsLog = (int)$xml->hasDeleteResultsLog;
		if(count($xml->hasDeleteSentDataLog))
			$this->hasDeleteSentDataLog = (int)$xml->hasDeleteSentDataLog;
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
	 * Entry distribution creation date as Unix timestamp (In seconds)
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * Entry distribution last update date as Unix timestamp (In seconds)
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;

	/**
	 * Entry distribution submission date as Unix timestamp (In seconds)
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $submittedAt = null;

	/**
	 * 
	 *
	 * @var string
	 * @insertonly
	 */
	public $entryId = null;

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
	 * @var int
	 * @insertonly
	 */
	public $distributionProfileId = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_ContentDistribution_Enum_EntryDistributionStatus
	 * @readonly
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_ContentDistribution_Enum_EntryDistributionSunStatus
	 * @readonly
	 */
	public $sunStatus = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_ContentDistribution_Enum_EntryDistributionFlag
	 * @readonly
	 */
	public $dirtyStatus = null;

	/**
	 * Comma separated thumbnail asset ids
	 * 	 
	 *
	 * @var string
	 */
	public $thumbAssetIds = null;

	/**
	 * Comma separated flavor asset ids
	 * 	 
	 *
	 * @var string
	 */
	public $flavorAssetIds = null;

	/**
	 * Entry distribution publish time as Unix timestamp (In seconds)
	 * 	 
	 *
	 * @var int
	 */
	public $sunrise = null;

	/**
	 * Entry distribution un-publish time as Unix timestamp (In seconds)
	 * 	 
	 *
	 * @var int
	 */
	public $sunset = null;

	/**
	 * The id as returned from the distributed destination
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $remoteId = null;

	/**
	 * The plays as retrieved from the remote destination reports
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $plays = null;

	/**
	 * The views as retrieved from the remote destination reports
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $views = null;

	/**
	 * 
	 *
	 * @var array of KalturaDistributionValidationError
	 */
	public $validationErrors;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Enum_BatchJobErrorTypes
	 * @readonly
	 */
	public $errorType = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $errorNumber = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $errorDescription = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Enum_NullableBoolean
	 * @readonly
	 */
	public $hasSubmitResultsLog = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Enum_NullableBoolean
	 * @readonly
	 */
	public $hasSubmitSentDataLog = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Enum_NullableBoolean
	 * @readonly
	 */
	public $hasUpdateResultsLog = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Enum_NullableBoolean
	 * @readonly
	 */
	public $hasUpdateSentDataLog = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Enum_NullableBoolean
	 * @readonly
	 */
	public $hasDeleteResultsLog = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Enum_NullableBoolean
	 * @readonly
	 */
	public $hasDeleteSentDataLog = null;


}

