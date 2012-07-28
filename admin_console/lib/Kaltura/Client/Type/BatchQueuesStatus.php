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
class Kaltura_Client_Type_BatchQueuesStatus extends Kaltura_Client_ObjectBase
{
	public function getKalturaObjectType()
	{
		return 'KalturaBatchQueuesStatus';
	}
	
	public function __construct(SimpleXMLElement $xml = null)
	{
		parent::__construct($xml);
		
		if(is_null($xml))
			return;
		
		$this->jobType = (string)$xml->jobType;
		if(count($xml->workerId))
			$this->workerId = (int)$xml->workerId;
		$this->typeName = (string)$xml->typeName;
		if(count($xml->size))
			$this->size = (int)$xml->size;
		if(count($xml->waitTime))
			$this->waitTime = (int)$xml->waitTime;
	}
	/**
	 * 
	 *
	 * @var Kaltura_Client_Enum_BatchJobType
	 */
	public $jobType = null;

	/**
	 * The worker configured id
	 * 	 
	 *
	 * @var int
	 */
	public $workerId = null;

	/**
	 * The friendly name of the type
	 * 	 
	 *
	 * @var string
	 */
	public $typeName = null;

	/**
	 * The size of the queue
	 * 	 
	 *
	 * @var int
	 */
	public $size = null;

	/**
	 * The avarage wait time
	 * 	 
	 *
	 * @var int
	 */
	public $waitTime = null;


}

