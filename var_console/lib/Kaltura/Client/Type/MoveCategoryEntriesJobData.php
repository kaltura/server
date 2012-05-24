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
class Kaltura_Client_Type_MoveCategoryEntriesJobData extends Kaltura_Client_Type_JobData
{
	public function getKalturaObjectType()
	{
		return 'KalturaMoveCategoryEntriesJobData';
	}
	
	public function __construct(SimpleXMLElement $xml = null)
	{
		parent::__construct($xml);
		
		if(is_null($xml))
			return;
		
		if(count($xml->srcCategoryId))
			$this->srcCategoryId = (int)$xml->srcCategoryId;
		if(count($xml->destCategoryId))
			$this->destCategoryId = (int)$xml->destCategoryId;
		if(count($xml->lastMovedCategoryId))
			$this->lastMovedCategoryId = (int)$xml->lastMovedCategoryId;
		if(count($xml->lastMovedCategoryPageIndex))
			$this->lastMovedCategoryPageIndex = (int)$xml->lastMovedCategoryPageIndex;
		if(count($xml->lastMovedCategoryEntryPageIndex))
			$this->lastMovedCategoryEntryPageIndex = (int)$xml->lastMovedCategoryEntryPageIndex;
		if(!empty($xml->moveFromChildren))
			$this->moveFromChildren = true;
		if(!empty($xml->copyOnly))
			$this->copyOnly = true;
	}
	/**
	 * Source category id
	 * 	 
	 *
	 * @var int
	 */
	public $srcCategoryId = null;

	/**
	 * Destination category id
	 *      
	 *
	 * @var int
	 */
	public $destCategoryId = null;

	/**
	 * Saves the last category id that its entries moved completely
	 *      In case of crash the batch will restart from that point
	 *      
	 *
	 * @var int
	 */
	public $lastMovedCategoryId = null;

	/**
	 * Saves the last page index of the child categories filter pager
	 *      In case of crash the batch will restart from that point
	 *      
	 *
	 * @var int
	 */
	public $lastMovedCategoryPageIndex = null;

	/**
	 * Saves the last page index of the category entries filter pager
	 *      In case of crash the batch will restart from that point
	 *      
	 *
	 * @var int
	 */
	public $lastMovedCategoryEntryPageIndex = null;

	/**
	 * All entries from all child categories will be moved as well
	 *      
	 *
	 * @var bool
	 */
	public $moveFromChildren = null;

	/**
	 * Entries won't be deleted from the source entry
	 *      
	 *
	 * @var bool
	 */
	public $copyOnly = null;


}

