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
abstract class Kaltura_Client_ShortLink_Type_ShortLinkBaseFilter extends Kaltura_Client_Type_Filter
{
	public function getKalturaObjectType()
	{
		return 'KalturaShortLinkBaseFilter';
	}
	
	public function __construct(SimpleXMLElement $xml = null)
	{
		parent::__construct($xml);
		
		if(is_null($xml))
			return;
		
		if(count($xml->idEqual))
			$this->idEqual = (int)$xml->idEqual;
		$this->idIn = (string)$xml->idIn;
		if(count($xml->createdAtGreaterThanOrEqual))
			$this->createdAtGreaterThanOrEqual = (int)$xml->createdAtGreaterThanOrEqual;
		if(count($xml->createdAtLessThanOrEqual))
			$this->createdAtLessThanOrEqual = (int)$xml->createdAtLessThanOrEqual;
		if(count($xml->updatedAtGreaterThanOrEqual))
			$this->updatedAtGreaterThanOrEqual = (int)$xml->updatedAtGreaterThanOrEqual;
		if(count($xml->updatedAtLessThanOrEqual))
			$this->updatedAtLessThanOrEqual = (int)$xml->updatedAtLessThanOrEqual;
		if(count($xml->expiresAtGreaterThanOrEqual))
			$this->expiresAtGreaterThanOrEqual = (int)$xml->expiresAtGreaterThanOrEqual;
		if(count($xml->expiresAtLessThanOrEqual))
			$this->expiresAtLessThanOrEqual = (int)$xml->expiresAtLessThanOrEqual;
		if(count($xml->partnerIdEqual))
			$this->partnerIdEqual = (int)$xml->partnerIdEqual;
		$this->partnerIdIn = (string)$xml->partnerIdIn;
		$this->userIdEqual = (string)$xml->userIdEqual;
		$this->userIdIn = (string)$xml->userIdIn;
		$this->systemNameEqual = (string)$xml->systemNameEqual;
		$this->systemNameIn = (string)$xml->systemNameIn;
		if(count($xml->statusEqual))
			$this->statusEqual = (int)$xml->statusEqual;
		$this->statusIn = (string)$xml->statusIn;
	}
	/**
	 * 
	 *
	 * @var int
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $expiresAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $expiresAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $partnerIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $userIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $userIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemNameEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemNameIn = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_ShortLink_Enum_ShortLinkStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;


}

