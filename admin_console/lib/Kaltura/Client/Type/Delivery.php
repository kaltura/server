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
class Kaltura_Client_Type_Delivery extends Kaltura_Client_ObjectBase
{
	public function getKalturaObjectType()
	{
		return 'KalturaDelivery';
	}
	
	public function __construct(SimpleXMLElement $xml = null)
	{
		parent::__construct($xml);
		
		if(is_null($xml))
			return;
		
		if(count($xml->id))
			$this->id = (int)$xml->id;
		if(count($xml->partnerId))
			$this->partnerId = (int)$xml->partnerId;
		$this->name = (string)$xml->name;
		$this->type = (string)$xml->type;
		$this->systemName = (string)$xml->systemName;
		$this->description = (string)$xml->description;
		if(count($xml->createdAt))
			$this->createdAt = (int)$xml->createdAt;
		if(count($xml->updatedAt))
			$this->updatedAt = (int)$xml->updatedAt;
		$this->protocol = (string)$xml->protocol;
		$this->url = (string)$xml->url;
		$this->host = (string)$xml->host;
		if(count($xml->status))
			$this->status = (int)$xml->status;
		if(!empty($xml->recognizer))
			$this->recognizer = Kaltura_Client_ParseUtils::unmarshalObject($xml->recognizer, "KalturaUrlRecognizer");
		if(!empty($xml->tokenizer))
			$this->tokenizer = Kaltura_Client_ParseUtils::unmarshalObject($xml->tokenizer, "KalturaUrlTokenizer");
		if(count($xml->isDefault))
			$this->isDefault = (int)$xml->isDefault;
		if(count($xml->isSecure))
			$this->isSecure = (int)$xml->isSecure;
		if(count($xml->parentId))
			$this->parentId = (int)$xml->parentId;
	}
	/**
	 * The id of the Delivery
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * The name of the Delivery
	 * 	 
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * Delivery type
	 * 	 
	 *
	 * @var Kaltura_Client_Enum_DeliveryType
	 */
	public $type = null;

	/**
	 * System name of the delivery
	 * 	 
	 *
	 * @var string
	 */
	public $systemName = null;

	/**
	 * The description of the Delivery
	 * 	 
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * Creation time as Unix timestamp (In seconds)
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * Update time as Unix timestamp (In seconds)
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Enum_PlaybackProtocol
	 */
	public $protocol = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $url = null;

	/**
	 * the host part of the url
	 * 	 
	 *
	 * @var string
	 */
	public $host = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Enum_DeliveryStatus
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Type_UrlRecognizer
	 */
	public $recognizer;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Type_UrlTokenizer
	 */
	public $tokenizer;

	/**
	 * True if this is the systemwide default for the protocol
	 * 	 
	 *
	 * @var Kaltura_Client_Enum_NullableBoolean
	 */
	public $isDefault = null;

	/**
	 * True if this delivery is appropriate for protected entries (access control/entitlement)
	 * 	 
	 *
	 * @var Kaltura_Client_Enum_NullableBoolean
	 */
	public $isSecure = null;

	/**
	 * the object from which this object was cloned (or 0)
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $parentId = null;


}

