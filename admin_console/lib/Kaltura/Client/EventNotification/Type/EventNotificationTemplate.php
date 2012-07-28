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
class Kaltura_Client_EventNotification_Type_EventNotificationTemplate extends Kaltura_Client_ObjectBase
{
	public function getKalturaObjectType()
	{
		return 'KalturaEventNotificationTemplate';
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
		$this->systemName = (string)$xml->systemName;
		$this->description = (string)$xml->description;
		$this->type = (string)$xml->type;
		if(count($xml->status))
			$this->status = (int)$xml->status;
		if(count($xml->createdAt))
			$this->createdAt = (int)$xml->createdAt;
		if(count($xml->updatedAt))
			$this->updatedAt = (int)$xml->updatedAt;
		if(!empty($xml->manualDispatchEnabled))
			$this->manualDispatchEnabled = true;
		if(!empty($xml->automaticDispatchEnabled))
			$this->automaticDispatchEnabled = true;
		$this->eventType = (string)$xml->eventType;
		$this->eventObjectType = (string)$xml->eventObjectType;
		if(empty($xml->eventConditions))
			$this->eventConditions = array();
		else
			$this->eventConditions = Kaltura_Client_Client::unmarshalItem($xml->eventConditions);
	}
	/**
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
	 * 
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_EventNotification_Enum_EventNotificationTemplateType
	 * @insertonly
	 */
	public $type = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_EventNotification_Enum_EventNotificationTemplateStatus
	 * @readonly
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;

	/**
	 * Define that the template could be dispatched manually from the API
	 * 	 
	 *
	 * @var bool
	 */
	public $manualDispatchEnabled = null;

	/**
	 * Define that the template could be dispatched automatically by the system
	 * 	 
	 *
	 * @var bool
	 */
	public $automaticDispatchEnabled = null;

	/**
	 * Define the event that should trigger this notification
	 * 	 
	 *
	 * @var Kaltura_Client_EventNotification_Enum_EventNotificationEventType
	 */
	public $eventType = null;

	/**
	 * Define the object that raied the event that should trigger this notification
	 * 	 
	 *
	 * @var Kaltura_Client_EventNotification_Enum_EventNotificationEventObjectType
	 */
	public $eventObjectType = null;

	/**
	 * Define the conditions that cause this notification to be triggered
	 * 	 
	 *
	 * @var array of KalturaEventCondition
	 */
	public $eventConditions;


}

