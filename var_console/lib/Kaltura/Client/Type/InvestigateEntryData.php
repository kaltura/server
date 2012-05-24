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
class Kaltura_Client_Type_InvestigateEntryData extends Kaltura_Client_ObjectBase
{
	public function getKalturaObjectType()
	{
		return 'KalturaInvestigateEntryData';
	}
	
	public function __construct(SimpleXMLElement $xml = null)
	{
		parent::__construct($xml);
		
		if(is_null($xml))
			return;
		
		if(!empty($xml->entry))
			$this->entry = Kaltura_Client_Client::unmarshalItem($xml->entry);
		if(!empty($xml->fileSyncs))
			$this->fileSyncs = Kaltura_Client_Client::unmarshalItem($xml->fileSyncs);
		if(!empty($xml->jobs))
			$this->jobs = Kaltura_Client_Client::unmarshalItem($xml->jobs);
		if(empty($xml->flavorAssets))
			$this->flavorAssets = array();
		else
			$this->flavorAssets = Kaltura_Client_Client::unmarshalItem($xml->flavorAssets);
		if(empty($xml->thumbAssets))
			$this->thumbAssets = array();
		else
			$this->thumbAssets = Kaltura_Client_Client::unmarshalItem($xml->thumbAssets);
		if(empty($xml->tracks))
			$this->tracks = array();
		else
			$this->tracks = Kaltura_Client_Client::unmarshalItem($xml->tracks);
	}
	/**
	 * 
	 *
	 * @var Kaltura_Client_Type_BaseEntry
	 * @readonly
	 */
	public $entry;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Type_FileSyncListResponse
	 * @readonly
	 */
	public $fileSyncs;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Type_BatchJobListResponse
	 * @readonly
	 */
	public $jobs;

	/**
	 * 
	 *
	 * @var array of KalturaInvestigateFlavorAssetData
	 * @readonly
	 */
	public $flavorAssets;

	/**
	 * 
	 *
	 * @var array of KalturaInvestigateThumbAssetData
	 * @readonly
	 */
	public $thumbAssets;

	/**
	 * 
	 *
	 * @var array of KalturaTrackEntry
	 * @readonly
	 */
	public $tracks;


}

