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
class Kaltura_Client_Type_DistributionJobData extends Kaltura_Client_Type_JobData
{
	public function getKalturaObjectType()
	{
		return 'KalturaDistributionJobData';
	}
	
	public function __construct(SimpleXMLElement $xml = null)
	{
		parent::__construct($xml);
		
		if(is_null($xml))
			return;
		
		if(count($xml->distributionProfileId))
			$this->distributionProfileId = (int)$xml->distributionProfileId;
		if(!empty($xml->distributionProfile))
			$this->distributionProfile = Kaltura_Client_Client::unmarshalItem($xml->distributionProfile);
		if(count($xml->entryDistributionId))
			$this->entryDistributionId = (int)$xml->entryDistributionId;
		if(!empty($xml->entryDistribution))
			$this->entryDistribution = Kaltura_Client_Client::unmarshalItem($xml->entryDistribution);
		$this->remoteId = (string)$xml->remoteId;
		$this->providerType = (string)$xml->providerType;
		if(!empty($xml->providerData))
			$this->providerData = Kaltura_Client_Client::unmarshalItem($xml->providerData);
		$this->results = (string)$xml->results;
		$this->sentData = (string)$xml->sentData;
		if(empty($xml->mediaFiles))
			$this->mediaFiles = array();
		else
			$this->mediaFiles = Kaltura_Client_Client::unmarshalItem($xml->mediaFiles);
	}
	/**
	 * 
	 *
	 * @var int
	 */
	public $distributionProfileId = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Type_DistributionProfile
	 */
	public $distributionProfile;

	/**
	 * 
	 *
	 * @var int
	 */
	public $entryDistributionId = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_ContentDistribution_Type_EntryDistribution
	 */
	public $entryDistribution;

	/**
	 * Id of the media in the remote system
	 * 	 
	 *
	 * @var string
	 */
	public $remoteId = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Enum_DistributionProviderType
	 */
	public $providerType = null;

	/**
	 * Additional data that relevant for the provider only
	 * 	 
	 *
	 * @var Kaltura_Client_ContentDistribution_Type_DistributionJobProviderData
	 */
	public $providerData;

	/**
	 * The results as returned from the remote destination
	 * 	 
	 *
	 * @var string
	 */
	public $results = null;

	/**
	 * The data as sent to the remote destination
	 * 	 
	 *
	 * @var string
	 */
	public $sentData = null;

	/**
	 * Stores array of media files that submitted to the destination site
	 * 	 Could be used later for media update 
	 * 	 
	 *
	 * @var array of KalturaDistributionRemoteMediaFile
	 */
	public $mediaFiles;


}

