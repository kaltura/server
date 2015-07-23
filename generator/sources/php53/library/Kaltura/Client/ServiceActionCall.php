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
 * @namespace
 */
namespace Kaltura\Client;

/**
 * @package Kaltura
 * @subpackage Client
 */
class ServiceActionCall
{
	/**
	 * @var string
	 */
	public $service;
	
	/**
	 * @var string
	 */
	public $action;
	
	/**
	 * @var array
	 */
	public $params;
	
	/**
	 * @var array
	 */
	public $files;
	
	/**
	 * Contruct new Kaltura service action call, if params array contain sub arrays (for objects), it will be flattened
	 *
	 * @param string $service
	 * @param string $action
	 * @param array $params
	 * @param array $files
	 */
	public function __construct($service, $action, $params = array(), $files = array())
	{
		$this->service = $service;
		$this->action = $action;
		$this->params = $this->parseParams($params);
		$this->files = $files;
	}
	
	/**
	 * Parse params array and sub arrays (for objects)
	 *
	 * @param array $params
	 */
	public function parseParams(array $params)
	{
		$newParams = array();
		foreach($params as $key => $val) 
		{
			if (is_array($val))
			{
				$newParams[$key] = $this->parseParams($val);
			}
			else
			{
				$newParams[$key] = $val;
			}
		}
		return $newParams;
	}
	
	/**
	 * Return the parameters for a multi request
	 *
	 * @param int $multiRequestIndex
	 */
	public function getParamsForMultiRequest($multiRequestIndex)
	{
		$multiRequestParams = array();
		$multiRequestParams[$multiRequestIndex]['service'] = $this->service;
		$multiRequestParams[$multiRequestIndex]['action'] = $this->action;
		foreach($this->params as $key => $val)
		{
			$multiRequestParams[$multiRequestIndex][$key] = $val;
		}
		return $multiRequestParams;
	}

	/**
	 * Return the parameters for a multi request
	 *
	 * @param int $multiRequestIndex
	 */
	public function getFilesForMultiRequest($multiRequestIndex)
	{
		$multiRequestParams = array();
		foreach($this->files as $key => $val)
		{
			$multiRequestParams["$multiRequestIndex:$key"] = $val;
		}
		return $multiRequestParams;
	}
}
