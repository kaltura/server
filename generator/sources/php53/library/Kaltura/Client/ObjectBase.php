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
 * Abstract base class for all client objects
 * 
 * @package Kaltura
 * @subpackage Client
 */
abstract class ObjectBase
{
	abstract public function getKalturaObjectType();
	
	public function __construct(\SimpleXMLElement $xml = null)
	{
		if(is_null($xml))
			return;
	
		if(count($xml->relatedObjects))
		{
			if(empty($xml->relatedObjects))
				$this->relatedObjects = array();
			else
				$this->relatedObjects = \Kaltura\Client\ParseUtils::unmarshalMap($xml->relatedObjects, "KalturaListResponse");
		}
	}
	
	protected function addIfNotNull(&$params, $paramName, $paramValue)
	{
		if ($paramValue !== null)
		{
			if($paramValue instanceof ObjectBase)
			{
				$params[$paramName] = $paramValue->toParams();
			}
			else
			{
				$params[$paramName] = $paramValue;
			}
		}
	}
	
	public function toParams()
	{
		$params = array(
			'objectType' => $this->getKalturaObjectType()
		);
		
	    foreach($this as $prop => $val)
			$this->addIfNotNull($params, $prop, $val);
			
		return $params;
	}
}
