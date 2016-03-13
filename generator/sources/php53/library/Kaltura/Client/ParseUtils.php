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
class ParseUtils 
{
	public static function unmarshalSimpleType(\SimpleXMLElement $xml) 
	{
		return "$xml";
	}
	
	public static function unmarshalObject(\SimpleXMLElement $xml, $fallbackType = null) 
	{
		$objectType = reset($xml->objectType);
		$type = TypeMap::getZendType($objectType);
		if(!class_exists($type)) {
			$type = TypeMap::getZendType($fallbackType);
			if(!class_exists($type))
				throw new ClientException("Invalid object type class [$type] of Kaltura type [$objectType]", ClientException::ERROR_INVALID_OBJECT_TYPE);
		}
			
		return new $type($xml);
	}
	
	public static function unmarshalArray(\SimpleXMLElement $xml, $fallbackType = null)
	{
		$xmls = $xml->children();
		$ret = array();
		foreach($xmls as $xml)
			$ret[] = self::unmarshalObject($xml, $fallbackType);
			
		return $ret;
	}
	
	public static function unmarshalMap(\SimpleXMLElement $xml, $fallbackType = null)
	{
		$xmls = $xml->children();
		$ret = array();
		foreach($xmls as $xml)
			$ret[strval($xml->itemKey)] = self::unmarshalObject($xml, $fallbackType);
			
		return $ret;
	}

}