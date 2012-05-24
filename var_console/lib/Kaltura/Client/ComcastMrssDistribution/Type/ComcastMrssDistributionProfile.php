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
class Kaltura_Client_ComcastMrssDistribution_Type_ComcastMrssDistributionProfile extends Kaltura_Client_ContentDistribution_Type_ConfigurableDistributionProfile
{
	public function getKalturaObjectType()
	{
		return 'KalturaComcastMrssDistributionProfile';
	}
	
	public function __construct(SimpleXMLElement $xml = null)
	{
		parent::__construct($xml);
		
		if(is_null($xml))
			return;
		
		if(count($xml->metadataProfileId))
			$this->metadataProfileId = (int)$xml->metadataProfileId;
		$this->feedUrl = (string)$xml->feedUrl;
		$this->feedTitle = (string)$xml->feedTitle;
		$this->feedLink = (string)$xml->feedLink;
		$this->feedDescription = (string)$xml->feedDescription;
		$this->feedLastBuildDate = (string)$xml->feedLastBuildDate;
		$this->itemLink = (string)$xml->itemLink;
		if(empty($xml->cPlatformTvSeries))
			$this->cPlatformTvSeries = array();
		else
			$this->cPlatformTvSeries = Kaltura_Client_Client::unmarshalItem($xml->cPlatformTvSeries);
		$this->cPlatformTvSeriesField = (string)$xml->cPlatformTvSeriesField;
	}
	/**
	 * 
	 *
	 * @var int
	 */
	public $metadataProfileId = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $feedUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $feedTitle = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $feedLink = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $feedDescription = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $feedLastBuildDate = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $itemLink = null;

	/**
	 * 
	 *
	 * @var array of KalturaKeyValue
	 */
	public $cPlatformTvSeries;

	/**
	 * 
	 *
	 * @var string
	 */
	public $cPlatformTvSeriesField = null;


}

