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
class Kaltura_Client_QuickPlayDistribution_Type_QuickPlayDistributionProfile extends Kaltura_Client_ContentDistribution_Type_ConfigurableDistributionProfile
{
	public function getKalturaObjectType()
	{
		return 'KalturaQuickPlayDistributionProfile';
	}
	
	public function __construct(SimpleXMLElement $xml = null)
	{
		parent::__construct($xml);
		
		if(is_null($xml))
			return;
		
		$this->sftpHost = (string)$xml->sftpHost;
		$this->sftpLogin = (string)$xml->sftpLogin;
		$this->sftpPass = (string)$xml->sftpPass;
		$this->sftpBasePath = (string)$xml->sftpBasePath;
		$this->channelTitle = (string)$xml->channelTitle;
		$this->channelLink = (string)$xml->channelLink;
		$this->channelDescription = (string)$xml->channelDescription;
		$this->channelManagingEditor = (string)$xml->channelManagingEditor;
		$this->channelLanguage = (string)$xml->channelLanguage;
		$this->channelImageTitle = (string)$xml->channelImageTitle;
		$this->channelImageWidth = (string)$xml->channelImageWidth;
		$this->channelImageHeight = (string)$xml->channelImageHeight;
		$this->channelImageLink = (string)$xml->channelImageLink;
		$this->channelImageUrl = (string)$xml->channelImageUrl;
		$this->channelCopyright = (string)$xml->channelCopyright;
		$this->channelGenerator = (string)$xml->channelGenerator;
		$this->channelRating = (string)$xml->channelRating;
	}
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
	public $sftpPass = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $sftpBasePath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $channelTitle = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $channelLink = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $channelDescription = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $channelManagingEditor = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $channelLanguage = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $channelImageTitle = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $channelImageWidth = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $channelImageHeight = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $channelImageLink = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $channelImageUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $channelCopyright = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $channelGenerator = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $channelRating = null;


}

