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
class Kaltura_Client_Type_SchedulerWorker extends Kaltura_Client_ObjectBase
{
	public function getKalturaObjectType()
	{
		return 'KalturaSchedulerWorker';
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
		if(count($xml->schedulerId))
			$this->schedulerId = (int)$xml->schedulerId;
		if(count($xml->schedulerConfiguredId))
			$this->schedulerConfiguredId = (int)$xml->schedulerConfiguredId;
		$this->type = (string)$xml->type;
		$this->typeName = (string)$xml->typeName;
		$this->name = (string)$xml->name;
		if(empty($xml->statuses))
			$this->statuses = array();
		else
			$this->statuses = Kaltura_Client_Client::unmarshalItem($xml->statuses);
		if(empty($xml->configs))
			$this->configs = array();
		else
			$this->configs = Kaltura_Client_Client::unmarshalItem($xml->configs);
		if(empty($xml->lockedJobs))
			$this->lockedJobs = array();
		else
			$this->lockedJobs = Kaltura_Client_Client::unmarshalItem($xml->lockedJobs);
		if(count($xml->avgWait))
			$this->avgWait = (int)$xml->avgWait;
		if(count($xml->avgWork))
			$this->avgWork = (int)$xml->avgWork;
		if(count($xml->lastStatus))
			$this->lastStatus = (int)$xml->lastStatus;
		$this->lastStatusStr = (string)$xml->lastStatusStr;
	}
	/**
	 * The id of the Worker
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
	 * The id of the Scheduler
	 * 	 
	 *
	 * @var int
	 */
	public $schedulerId = null;

	/**
	 * The id of the scheduler as configured in the batch config
	 * 	 
	 *
	 * @var int
	 */
	public $schedulerConfiguredId = null;

	/**
	 * The worker type
	 * 	 
	 *
	 * @var Kaltura_Client_Enum_BatchJobType
	 */
	public $type = null;

	/**
	 * The friendly name of the type
	 * 	 
	 *
	 * @var string
	 */
	public $typeName = null;

	/**
	 * The scheduler name
	 * 	 
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * Array of the last statuses
	 * 	 
	 *
	 * @var array of KalturaSchedulerStatus
	 */
	public $statuses;

	/**
	 * Array of the last configs
	 * 	 
	 *
	 * @var array of KalturaSchedulerConfig
	 */
	public $configs;

	/**
	 * Array of jobs that locked to this worker
	 * 	 
	 *
	 * @var array of KalturaBatchJob
	 */
	public $lockedJobs;

	/**
	 * Avarage time between creation and queue time
	 * 	 
	 *
	 * @var int
	 */
	public $avgWait = null;

	/**
	 * Avarage time between queue time end finish time
	 * 	 
	 *
	 * @var int
	 */
	public $avgWork = null;

	/**
	 * last status time
	 * 	 
	 *
	 * @var int
	 */
	public $lastStatus = null;

	/**
	 * last status formated
	 * 	 
	 *
	 * @var string
	 */
	public $lastStatusStr = null;


}

