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
 * @package Scheduler
 * @subpackage Client
 */
require_once(dirname(__FILE__) . "/../KalturaClientBase.php");
require_once(dirname(__FILE__) . "/../KalturaEnums.php");
require_once(dirname(__FILE__) . "/../KalturaTypes.php");
require_once(dirname(__FILE__) . "/KalturaContentDistributionClientPlugin.php");

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaUverseClickToOrderDistributionField
{
	const BACKGROUND_IMAGE_WIDE = "BACKGROUND_IMAGE_WIDE";
	const BACKGROUND_IMAGE_STANDART = "BACKGROUND_IMAGE_STANDART";
	const SORT_ITEMS_BY_FIELD = "SORT_ITEMS_BY_FIELD";
	const CATEGORY_ENTRY_ID = "CATEGORY_ENTRY_ID";
	const CATEGORY_IMAGE_WIDTH = "CATEGORY_IMAGE_WIDTH";
	const CATEGORY_IMAGE_HEIGHT = "CATEGORY_IMAGE_HEIGHT";
	const ITEM_TITLE = "ITEM_TITLE";
	const ITEM_CONTENT_TYPE = "ITEM_CONTENT_TYPE";
	const ITEM_CCVIDFILE = "ITEM_CCVIDFILE";
	const ITEM_DESTINATION = "ITEM_DESTINATION";
	const ITEM_CONTENT = "ITEM_CONTENT";
	const ITEM_DIRECTIONS = "ITEM_DIRECTIONS";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaUverseClickToOrderDistributionProfileOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaUverseClickToOrderDistributionProviderOrderBy
{
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaUverseClickToOrderDistributionProfile extends KalturaConfigurableDistributionProfile
{
	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $feedUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $backgroundImageWide = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $backgroundImageStandard = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaUverseClickToOrderDistributionProviderBaseFilter extends KalturaDistributionProviderFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaUverseClickToOrderDistributionProviderFilter extends KalturaUverseClickToOrderDistributionProviderBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaUverseClickToOrderDistributionProfileBaseFilter extends KalturaConfigurableDistributionProfileFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaUverseClickToOrderDistributionProfileFilter extends KalturaUverseClickToOrderDistributionProfileBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaUverseClickToOrderDistributionClientPlugin extends KalturaClientPlugin
{
	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	/**
	 * @return KalturaUverseClickToOrderDistributionClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaUverseClickToOrderDistributionClientPlugin($client);
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'uverseClickToOrderDistribution';
	}
}

