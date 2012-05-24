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
class Kaltura_Client_CodeCuePoint_Type_CodeCuePointBaseFilter extends Kaltura_Client_CuePoint_Type_CuePointFilter
{
	public function getKalturaObjectType()
	{
		return 'KalturaCodeCuePointBaseFilter';
	}
	
	public function __construct(SimpleXMLElement $xml = null)
	{
		parent::__construct($xml);
		
		if(is_null($xml))
			return;
		
		$this->codeLike = (string)$xml->codeLike;
		$this->codeMultiLikeOr = (string)$xml->codeMultiLikeOr;
		$this->codeMultiLikeAnd = (string)$xml->codeMultiLikeAnd;
		$this->codeEqual = (string)$xml->codeEqual;
		$this->codeIn = (string)$xml->codeIn;
		$this->descriptionLike = (string)$xml->descriptionLike;
		$this->descriptionMultiLikeOr = (string)$xml->descriptionMultiLikeOr;
		$this->descriptionMultiLikeAnd = (string)$xml->descriptionMultiLikeAnd;
		if(count($xml->endTimeGreaterThanOrEqual))
			$this->endTimeGreaterThanOrEqual = (int)$xml->endTimeGreaterThanOrEqual;
		if(count($xml->endTimeLessThanOrEqual))
			$this->endTimeLessThanOrEqual = (int)$xml->endTimeLessThanOrEqual;
		if(count($xml->durationGreaterThanOrEqual))
			$this->durationGreaterThanOrEqual = (int)$xml->durationGreaterThanOrEqual;
		if(count($xml->durationLessThanOrEqual))
			$this->durationLessThanOrEqual = (int)$xml->durationLessThanOrEqual;
	}
	/**
	 * 
	 *
	 * @var string
	 */
	public $codeLike = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $codeMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $codeMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $codeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $codeIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $descriptionLike = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $descriptionMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $descriptionMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $endTimeGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $endTimeLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $durationGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $durationLessThanOrEqual = null;


}

