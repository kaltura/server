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
class Kaltura_Client_MetroPcsDistribution_Type_MetroPcsDistributionProfile extends Kaltura_Client_ContentDistribution_Type_ConfigurableDistributionProfile
{
	public function getKalturaObjectType()
	{
		return 'KalturaMetroPcsDistributionProfile';
	}
	
	public function __construct(SimpleXMLElement $xml = null)
	{
		parent::__construct($xml);
		
		if(is_null($xml))
			return;
		
		$this->ftpHost = (string)$xml->ftpHost;
		$this->ftpLogin = (string)$xml->ftpLogin;
		$this->ftpPass = (string)$xml->ftpPass;
		$this->ftpPath = (string)$xml->ftpPath;
		$this->providerName = (string)$xml->providerName;
		$this->providerId = (string)$xml->providerId;
		$this->copyright = (string)$xml->copyright;
		$this->entitlements = (string)$xml->entitlements;
		$this->rating = (string)$xml->rating;
		$this->itemType = (string)$xml->itemType;
	}
	/**
	 * 
	 *
	 * @var string
	 */
	public $ftpHost = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $ftpLogin = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $ftpPass = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $ftpPath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $providerName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $providerId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $copyright = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entitlements = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $rating = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $itemType = null;


}

