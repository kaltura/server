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
class Kaltura_Client_MsnDistribution_Type_MsnDistributionProfile extends Kaltura_Client_ContentDistribution_Type_ConfigurableDistributionProfile
{
	public function getKalturaObjectType()
	{
		return 'KalturaMsnDistributionProfile';
	}
	
	public function __construct(SimpleXMLElement $xml = null)
	{
		parent::__construct($xml);
		
		if(is_null($xml))
			return;
		
		$this->username = (string)$xml->username;
		$this->password = (string)$xml->password;
		$this->domain = (string)$xml->domain;
		$this->csId = (string)$xml->csId;
		$this->source = (string)$xml->source;
		$this->sourceFriendlyName = (string)$xml->sourceFriendlyName;
		$this->pageGroup = (string)$xml->pageGroup;
		if(count($xml->sourceFlavorParamsId))
			$this->sourceFlavorParamsId = (int)$xml->sourceFlavorParamsId;
		if(count($xml->wmvFlavorParamsId))
			$this->wmvFlavorParamsId = (int)$xml->wmvFlavorParamsId;
		if(count($xml->flvFlavorParamsId))
			$this->flvFlavorParamsId = (int)$xml->flvFlavorParamsId;
		if(count($xml->slFlavorParamsId))
			$this->slFlavorParamsId = (int)$xml->slFlavorParamsId;
		if(count($xml->slHdFlavorParamsId))
			$this->slHdFlavorParamsId = (int)$xml->slHdFlavorParamsId;
		$this->msnvideoCat = (string)$xml->msnvideoCat;
		$this->msnvideoTop = (string)$xml->msnvideoTop;
		$this->msnvideoTopCat = (string)$xml->msnvideoTopCat;
	}
	/**
	 * 
	 *
	 * @var string
	 */
	public $username = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $password = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $domain = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $csId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $source = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $sourceFriendlyName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $pageGroup = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $sourceFlavorParamsId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $wmvFlavorParamsId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $flvFlavorParamsId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $slFlavorParamsId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $slHdFlavorParamsId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $msnvideoCat = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $msnvideoTop = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $msnvideoTopCat = null;


}

