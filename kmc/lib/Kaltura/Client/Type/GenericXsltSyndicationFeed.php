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

class Kaltura_Client_Type_GenericXsltSyndicationFeed extends Kaltura_Client_Type_GenericSyndicationFeed
{
	public function getKalturaObjectType()
	{
		return 'KalturaGenericXsltSyndicationFeed';
	}
	
	public function __construct(SimpleXMLElement $xml = null)
	{
		parent::__construct($xml);
		
		if(is_null($xml))
			return;
		
		$this->xslt = (string)$xml->xslt;
		if(empty($xml->itemXpathsToExtend))
			$this->itemXpathsToExtend = array();
		else
			$this->itemXpathsToExtend = Kaltura_Client_Client::unmarshalItem($xml->itemXpathsToExtend);
	}
	/**
	 * 
	 *
	 * @var string
	 */
	public $xslt = null;

	/**
	 * This parameter determines which custom metadata fields of type related-entry should be
	 * 	 expanded to contain the kaltura MRSS feed of the related entry. Related-entry fields not
	 * 	 included in this list will contain only the related entry id.
	 * 	 This property contains a list xPaths in the Kaltura MRSS.
	 * 	 
	 *
	 * @var array of KalturaString
	 */
	public $itemXpathsToExtend;


}

