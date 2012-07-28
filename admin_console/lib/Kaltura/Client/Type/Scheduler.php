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
class Kaltura_Client_Type_Scheduler extends Kaltura_Client_ObjectBase
{
	public function getKalturaObjectType()
	{
		return 'KalturaScheduler';
	}
	
	public function __construct(SimpleXMLElement $xml = null)
	{
		parent::__construct($xml);
		
		if(is_null($xml))
			return;
		
		if(count($xml->id))
			$this->id = (int)$xml->id;
		if(count($xml->configuredId))
			$this->configuredId = (int)$xml->configuredId;
		$this->name = (string)$xml->name;
		$this->host = (string)$xml->host;
		if(empty($xml->statuses))
			$this->statuses = array();
		else
			$this->statuses = Kaltura_Client_Client::unmarshalItem($xml->statuses);
		if(empty($xml->configs))
			$this->configs = array();
		else
			$this->configs = Kaltura_Client_Client::unmarshalItem($xml->configs);
		if(empty($xml->workers))
			$this->workers = array();
		else
			$this->workers = Kaltura_Client_Client::unmarshalItem($xml->workers);
		if(count($xml->createdAt))
			$this->createdAt = (int)$xml->createdAt;
		if(count($xml->lastStatus))
			$this->lastStatus = (int)$xml->lastStatus;
		$this->lastStatusStr = (string)$xml->lastStatusStr;
	}
	/**
	 * The id of the Scheduler
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * The id as configured in the batch config
	 * 	 
	 *
	 * @var int
	 */
	public $configuredId = null;

	/**
	 * The scheduler name
	 * 	 
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * The host name
	 * 	 
	 *
	 * @var string
	 */
	public $host = null;

	/**
	 * Array of the last statuses
	 * 	 
	 *
	 * @var array of KalturaSchedulerStatus
	 * @readonly
	 */
	public $statuses;

	/**
	 * Array of the last configs
	 * 	 
	 *
	 * @var array of KalturaSchedulerConfig
	 * @readonly
	 */
	public $configs;

	/**
	 * Array of the workers
	 * 	 
	 *
	 * @var array of KalturaSchedulerWorker
	 * @readonly
	 */
	public $workers;

	/**
	 * creation time
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * last status time
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $lastStatus = null;

	/**
	 * last status formated
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $lastStatusStr = null;


}

