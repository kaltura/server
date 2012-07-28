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
class Kaltura_Client_Type_SchedulerStatus extends Kaltura_Client_ObjectBase
{
	public function getKalturaObjectType()
	{
		return 'KalturaSchedulerStatus';
	}
	
	public function __construct(SimpleXMLElement $xml = null)
	{
		parent::__construct($xml);
		
		if(is_null($xml))
			return;
		
		if(count($xml->id))
			$this->id = (int)$xml->id;
		if(count($xml->schedulerConfiguredId))
			$this->schedulerConfiguredId = (int)$xml->schedulerConfiguredId;
		if(count($xml->workerConfiguredId))
			$this->workerConfiguredId = (int)$xml->workerConfiguredId;
		$this->workerType = (string)$xml->workerType;
		if(count($xml->type))
			$this->type = (int)$xml->type;
		if(count($xml->value))
			$this->value = (int)$xml->value;
		if(count($xml->schedulerId))
			$this->schedulerId = (int)$xml->schedulerId;
		if(count($xml->workerId))
			$this->workerId = (int)$xml->workerId;
	}
	/**
	 * The id of the Category
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * The configured id of the scheduler
	 * 	 
	 *
	 * @var int
	 */
	public $schedulerConfiguredId = null;

	/**
	 * The configured id of the job worker
	 * 	 
	 *
	 * @var int
	 */
	public $workerConfiguredId = null;

	/**
	 * The type of the job worker.
	 * 	 
	 *
	 * @var Kaltura_Client_Enum_BatchJobType
	 */
	public $workerType = null;

	/**
	 * The status type
	 * 	 
	 *
	 * @var Kaltura_Client_Enum_SchedulerStatusType
	 */
	public $type = null;

	/**
	 * The status value
	 * 	 
	 *
	 * @var int
	 */
	public $value = null;

	/**
	 * The id of the scheduler
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $schedulerId = null;

	/**
	 * The id of the worker
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $workerId = null;


}

