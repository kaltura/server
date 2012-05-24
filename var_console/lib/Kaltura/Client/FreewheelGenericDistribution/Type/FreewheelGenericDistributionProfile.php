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
class Kaltura_Client_FreewheelGenericDistribution_Type_FreewheelGenericDistributionProfile extends Kaltura_Client_ContentDistribution_Type_ConfigurableDistributionProfile
{
	public function getKalturaObjectType()
	{
		return 'KalturaFreewheelGenericDistributionProfile';
	}
	
	public function __construct(SimpleXMLElement $xml = null)
	{
		parent::__construct($xml);
		
		if(is_null($xml))
			return;
		
		$this->apikey = (string)$xml->apikey;
		$this->email = (string)$xml->email;
		$this->sftpPass = (string)$xml->sftpPass;
		$this->sftpLogin = (string)$xml->sftpLogin;
		$this->contentOwner = (string)$xml->contentOwner;
		$this->upstreamVideoId = (string)$xml->upstreamVideoId;
		$this->upstreamNetworkName = (string)$xml->upstreamNetworkName;
		$this->upstreamNetworkId = (string)$xml->upstreamNetworkId;
		$this->categoryId = (string)$xml->categoryId;
		if(!empty($xml->replaceGroup))
			$this->replaceGroup = true;
		if(!empty($xml->replaceAirDates))
			$this->replaceAirDates = true;
	}
	/**
	 * 
	 *
	 * @var string
	 */
	public $apikey = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $email = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $sftpPass = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $sftpLogin = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $contentOwner = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $upstreamVideoId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $upstreamNetworkName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $upstreamNetworkId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $categoryId = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $replaceGroup = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $replaceAirDates = null;


}

