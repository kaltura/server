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
class Kaltura_Client_CrossKalturaDistribution_Type_CrossKalturaDistributionProfile extends Kaltura_Client_ContentDistribution_Type_ConfigurableDistributionProfile
{
	public function getKalturaObjectType()
	{
		return 'KalturaCrossKalturaDistributionProfile';
	}
	
	public function __construct(SimpleXMLElement $xml = null)
	{
		parent::__construct($xml);
		
		if(is_null($xml))
			return;
		
		$this->targetServiceUrl = (string)$xml->targetServiceUrl;
		if(count($xml->targetAccountId))
			$this->targetAccountId = (int)$xml->targetAccountId;
		$this->targetLoginId = (string)$xml->targetLoginId;
		$this->targetLoginPassword = (string)$xml->targetLoginPassword;
		$this->metadataXslt = (string)$xml->metadataXslt;
		if(empty($xml->metadataXpathsTriggerUpdate))
			$this->metadataXpathsTriggerUpdate = array();
		else
			$this->metadataXpathsTriggerUpdate = Kaltura_Client_Client::unmarshalItem($xml->metadataXpathsTriggerUpdate);
		if(!empty($xml->distributeCaptions))
			$this->distributeCaptions = true;
		if(!empty($xml->distributeCuePoints))
			$this->distributeCuePoints = true;
		if(!empty($xml->distributeRemoteFlavorAssetContent))
			$this->distributeRemoteFlavorAssetContent = true;
		if(!empty($xml->distributeRemoteThumbAssetContent))
			$this->distributeRemoteThumbAssetContent = true;
		if(!empty($xml->distributeRemoteCaptionAssetContent))
			$this->distributeRemoteCaptionAssetContent = true;
		if(empty($xml->mapAccessControlProfileIds))
			$this->mapAccessControlProfileIds = array();
		else
			$this->mapAccessControlProfileIds = Kaltura_Client_Client::unmarshalItem($xml->mapAccessControlProfileIds);
		if(empty($xml->mapConversionProfileIds))
			$this->mapConversionProfileIds = array();
		else
			$this->mapConversionProfileIds = Kaltura_Client_Client::unmarshalItem($xml->mapConversionProfileIds);
		if(empty($xml->mapMetadataProfileIds))
			$this->mapMetadataProfileIds = array();
		else
			$this->mapMetadataProfileIds = Kaltura_Client_Client::unmarshalItem($xml->mapMetadataProfileIds);
		if(empty($xml->mapStorageProfileIds))
			$this->mapStorageProfileIds = array();
		else
			$this->mapStorageProfileIds = Kaltura_Client_Client::unmarshalItem($xml->mapStorageProfileIds);
		if(empty($xml->mapFlavorParamsIds))
			$this->mapFlavorParamsIds = array();
		else
			$this->mapFlavorParamsIds = Kaltura_Client_Client::unmarshalItem($xml->mapFlavorParamsIds);
		if(empty($xml->mapThumbParamsIds))
			$this->mapThumbParamsIds = array();
		else
			$this->mapThumbParamsIds = Kaltura_Client_Client::unmarshalItem($xml->mapThumbParamsIds);
		if(empty($xml->mapCaptionParamsIds))
			$this->mapCaptionParamsIds = array();
		else
			$this->mapCaptionParamsIds = Kaltura_Client_Client::unmarshalItem($xml->mapCaptionParamsIds);
	}
	/**
	 * 
	 *
	 * @var string
	 */
	public $targetServiceUrl = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $targetAccountId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $targetLoginId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $targetLoginPassword = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $metadataXslt = null;

	/**
	 * 
	 *
	 * @var array of KalturaStringValue
	 */
	public $metadataXpathsTriggerUpdate;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $distributeCaptions = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $distributeCuePoints = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $distributeRemoteFlavorAssetContent = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $distributeRemoteThumbAssetContent = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $distributeRemoteCaptionAssetContent = null;

	/**
	 * 
	 *
	 * @var array of KalturaKeyValue
	 */
	public $mapAccessControlProfileIds;

	/**
	 * 
	 *
	 * @var array of KalturaKeyValue
	 */
	public $mapConversionProfileIds;

	/**
	 * 
	 *
	 * @var array of KalturaKeyValue
	 */
	public $mapMetadataProfileIds;

	/**
	 * 
	 *
	 * @var array of KalturaKeyValue
	 */
	public $mapStorageProfileIds;

	/**
	 * 
	 *
	 * @var array of KalturaKeyValue
	 */
	public $mapFlavorParamsIds;

	/**
	 * 
	 *
	 * @var array of KalturaKeyValue
	 */
	public $mapThumbParamsIds;

	/**
	 * 
	 *
	 * @var array of KalturaKeyValue
	 */
	public $mapCaptionParamsIds;


}

