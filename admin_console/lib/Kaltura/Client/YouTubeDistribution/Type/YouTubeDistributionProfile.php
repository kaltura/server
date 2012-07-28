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
class Kaltura_Client_YouTubeDistribution_Type_YouTubeDistributionProfile extends Kaltura_Client_ContentDistribution_Type_ConfigurableDistributionProfile
{
	public function getKalturaObjectType()
	{
		return 'KalturaYouTubeDistributionProfile';
	}
	
	public function __construct(SimpleXMLElement $xml = null)
	{
		parent::__construct($xml);
		
		if(is_null($xml))
			return;
		
		$this->username = (string)$xml->username;
		$this->notificationEmail = (string)$xml->notificationEmail;
		$this->sftpHost = (string)$xml->sftpHost;
		$this->sftpLogin = (string)$xml->sftpLogin;
		$this->sftpPublicKey = (string)$xml->sftpPublicKey;
		$this->sftpPrivateKey = (string)$xml->sftpPrivateKey;
		$this->sftpBaseDir = (string)$xml->sftpBaseDir;
		$this->ownerName = (string)$xml->ownerName;
		$this->defaultCategory = (string)$xml->defaultCategory;
		$this->allowComments = (string)$xml->allowComments;
		$this->allowEmbedding = (string)$xml->allowEmbedding;
		$this->allowRatings = (string)$xml->allowRatings;
		$this->allowResponses = (string)$xml->allowResponses;
		$this->commercialPolicy = (string)$xml->commercialPolicy;
		$this->ugcPolicy = (string)$xml->ugcPolicy;
		$this->target = (string)$xml->target;
		$this->adServerPartnerId = (string)$xml->adServerPartnerId;
		if(!empty($xml->enableAdServer))
			$this->enableAdServer = true;
		if(!empty($xml->allowPreRollAds))
			$this->allowPreRollAds = true;
		if(!empty($xml->allowPostRollAds))
			$this->allowPostRollAds = true;
	}
	/**
	 * 
	 *
	 * @var string
	 */
	public $username = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $notificationEmail = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $sftpHost = null;

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
	public $sftpPublicKey = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $sftpPrivateKey = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $sftpBaseDir = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $ownerName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $defaultCategory = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $allowComments = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $allowEmbedding = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $allowRatings = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $allowResponses = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $commercialPolicy = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $ugcPolicy = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $target = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $adServerPartnerId = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $enableAdServer = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $allowPreRollAds = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $allowPostRollAds = null;


}

