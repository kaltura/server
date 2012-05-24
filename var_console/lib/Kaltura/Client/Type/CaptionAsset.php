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
class Kaltura_Client_Type_CaptionAsset extends Kaltura_Client_Type_Asset
{
	public function getKalturaObjectType()
	{
		return 'KalturaCaptionAsset';
	}
	
	public function __construct(SimpleXMLElement $xml = null)
	{
		parent::__construct($xml);
		
		if(is_null($xml))
			return;
		
		if(count($xml->captionParamsId))
			$this->captionParamsId = (int)$xml->captionParamsId;
		$this->language = (string)$xml->language;
		$this->languageCode = (string)$xml->languageCode;
		if(count($xml->isDefault))
			$this->isDefault = (int)$xml->isDefault;
		$this->label = (string)$xml->label;
		$this->format = (string)$xml->format;
		if(count($xml->status))
			$this->status = (int)$xml->status;
	}
	/**
	 * The Caption Params used to create this Caption Asset
	 * 	 
	 *
	 * @var int
	 * @insertonly
	 */
	public $captionParamsId = null;

	/**
	 * The language of the caption asset content
	 * 	 
	 *
	 * @var Kaltura_Client_Enum_Language
	 */
	public $language = null;

	/**
	 * The language of the caption asset content
	 * 	 
	 *
	 * @var Kaltura_Client_Enum_LanguageCode
	 * @readonly
	 */
	public $languageCode = null;

	/**
	 * Is default caption asset of the entry
	 * 	 
	 *
	 * @var Kaltura_Client_Enum_NullableBoolean
	 */
	public $isDefault = null;

	/**
	 * Friendly label
	 * 	 
	 *
	 * @var string
	 */
	public $label = null;

	/**
	 * The caption format
	 * 	 
	 *
	 * @var Kaltura_Client_Enum_CaptionType
	 * @insertonly
	 */
	public $format = null;

	/**
	 * The status of the asset
	 * 	 
	 *
	 * @var Kaltura_Client_Enum_CaptionAssetStatus
	 * @readonly
	 */
	public $status = null;


}

