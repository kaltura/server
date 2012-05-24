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
class Kaltura_Client_Type_SchedulerStatusResponse extends Kaltura_Client_ObjectBase
{
	public function getKalturaObjectType()
	{
		return 'KalturaSchedulerStatusResponse';
	}
	
	public function __construct(SimpleXMLElement $xml = null)
	{
		parent::__construct($xml);
		
		if(is_null($xml))
			return;
		
		if(empty($xml->queuesStatus))
			$this->queuesStatus = array();
		else
			$this->queuesStatus = Kaltura_Client_Client::unmarshalItem($xml->queuesStatus);
		if(empty($xml->controlPanelCommands))
			$this->controlPanelCommands = array();
		else
			$this->controlPanelCommands = Kaltura_Client_Client::unmarshalItem($xml->controlPanelCommands);
		if(empty($xml->schedulerConfigs))
			$this->schedulerConfigs = array();
		else
			$this->schedulerConfigs = Kaltura_Client_Client::unmarshalItem($xml->schedulerConfigs);
	}
	/**
	 * The status of all queues on the server
	 * 	 
	 *
	 * @var array of KalturaBatchQueuesStatus
	 */
	public $queuesStatus;

	/**
	 * The commands that sent from the control panel
	 * 	 
	 *
	 * @var array of KalturaControlPanelCommand
	 */
	public $controlPanelCommands;

	/**
	 * The configuration that sent from the control panel
	 * 	 
	 *
	 * @var array of KalturaSchedulerConfig
	 */
	public $schedulerConfigs;


}

