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
class Kaltura_Client_FtpDistribution_Type_FtpDistributionProfile extends Kaltura_Client_ContentDistribution_Type_ConfigurableDistributionProfile
{
	public function getKalturaObjectType()
	{
		return 'KalturaFtpDistributionProfile';
	}
	
	public function __construct(SimpleXMLElement $xml = null)
	{
		parent::__construct($xml);
		
		if(is_null($xml))
			return;
		
		if(count($xml->protocol))
			$this->protocol = (int)$xml->protocol;
		$this->host = (string)$xml->host;
		if(count($xml->port))
			$this->port = (int)$xml->port;
		$this->basePath = (string)$xml->basePath;
		$this->username = (string)$xml->username;
		$this->password = (string)$xml->password;
		$this->passphrase = (string)$xml->passphrase;
		$this->sftpPublicKey = (string)$xml->sftpPublicKey;
		$this->sftpPrivateKey = (string)$xml->sftpPrivateKey;
		if(!empty($xml->disableMetadata))
			$this->disableMetadata = true;
		$this->metadataXslt = (string)$xml->metadataXslt;
		$this->metadataFilenameXslt = (string)$xml->metadataFilenameXslt;
		$this->flavorAssetFilenameXslt = (string)$xml->flavorAssetFilenameXslt;
		$this->thumbnailAssetFilenameXslt = (string)$xml->thumbnailAssetFilenameXslt;
	}
	/**
	 * 
	 *
	 * @var Kaltura_Client_ContentDistribution_Enum_DistributionProtocol
	 */
	public $protocol = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $host = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $port = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $basePath = null;

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
	public $password = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $passphrase = null;

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
	 * @var bool
	 */
	public $disableMetadata = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $metadataXslt = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $metadataFilenameXslt = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $flavorAssetFilenameXslt = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $thumbnailAssetFilenameXslt = null;


}

