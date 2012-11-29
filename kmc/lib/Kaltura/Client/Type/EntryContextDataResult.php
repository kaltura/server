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

class Kaltura_Client_Type_EntryContextDataResult extends Kaltura_Client_ObjectBase
{
	public function getKalturaObjectType()
	{
		return 'KalturaEntryContextDataResult';
	}
	
	public function __construct(SimpleXMLElement $xml = null)
	{
		parent::__construct($xml);
		
		if(is_null($xml))
			return;
		
		if(!empty($xml->isSiteRestricted))
			$this->isSiteRestricted = true;
		if(!empty($xml->isCountryRestricted))
			$this->isCountryRestricted = true;
		if(!empty($xml->isSessionRestricted))
			$this->isSessionRestricted = true;
		if(!empty($xml->isIpAddressRestricted))
			$this->isIpAddressRestricted = true;
		if(!empty($xml->isUserAgentRestricted))
			$this->isUserAgentRestricted = true;
		if(count($xml->previewLength))
			$this->previewLength = (int)$xml->previewLength;
		if(!empty($xml->isScheduledNow))
			$this->isScheduledNow = true;
		if(!empty($xml->isAdmin))
			$this->isAdmin = true;
		$this->streamerType = (string)$xml->streamerType;
		$this->mediaProtocol = (string)$xml->mediaProtocol;
		$this->storageProfilesXML = (string)$xml->storageProfilesXML;
		if(empty($xml->accessControlMessages))
			$this->accessControlMessages = array();
		else
			$this->accessControlMessages = Kaltura_Client_Client::unmarshalItem($xml->accessControlMessages);
		if(empty($xml->accessControlActions))
			$this->accessControlActions = array();
		else
			$this->accessControlActions = Kaltura_Client_Client::unmarshalItem($xml->accessControlActions);
	}
	/**
	 * 
	 *
	 * @var bool
	 */
	public $isSiteRestricted = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $isCountryRestricted = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $isSessionRestricted = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $isIpAddressRestricted = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $isUserAgentRestricted = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $previewLength = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $isScheduledNow = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $isAdmin = null;

	/**
	 * http/rtmp/hdnetwork
	 * 	 
	 *
	 * @var string
	 */
	public $streamerType = null;

	/**
	 * http/https, rtmp/rtmpe
	 * 	 
	 *
	 * @var string
	 */
	public $mediaProtocol = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $storageProfilesXML = null;

	/**
	 * Array of messages as received from the access control rules that invalidated
	 * 	 
	 *
	 * @var array of KalturaString
	 */
	public $accessControlMessages;

	/**
	 * Array of actions as received from the access control rules that invalidated
	 * 	 
	 *
	 * @var array of KalturaAccessControlAction
	 */
	public $accessControlActions;


}

