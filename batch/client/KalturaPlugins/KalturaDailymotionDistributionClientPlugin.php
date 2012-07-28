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
class KalturaDailymotionGeoBlockingMapping
{
	const DISABLED = 0;
	const ACCESS_CONTROL = 1;
	const METADATA = 2;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDailymotionDistributionField
{
	const VIDEO_TITLE = "VIDEO_TITLE";
	const VIDEO_DESCRIPTION = "VIDEO_DESCRIPTION";
	const VIDEO_TAGS = "VIDEO_TAGS";
	const VIDEO_CHANNEL = "VIDEO_CHANNEL";
	const VIDEO_LANGUAGE = "VIDEO_LANGUAGE";
	const VIDEO_TYPE = "VIDEO_TYPE";
	const VIDEO_GEO_BLOCKING_OPERATION = "VIDEO_GEO_BLOCKING_OPERATION";
	const VIDEO_GEO_BLOCKING_COUNTRY_LIST = "VIDEO_GEO_BLOCKING_COUNTRY_LIST";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDailymotionDistributionProfileOrderBy
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
class KalturaDailymotionDistributionProviderOrderBy
{
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDailymotionDistributionJobProviderData extends KalturaConfigurableDistributionJobProviderData
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $videoAssetFilePath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $accessControlGeoBlockingOperation = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $accessControlGeoBlockingCountryList = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDailymotionDistributionProfile extends KalturaConfigurableDistributionProfile
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $user = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $password = null;

	/**
	 * 
	 *
	 * @var KalturaDailymotionGeoBlockingMapping
	 */
	public $geoBlockingMapping = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaDailymotionDistributionProviderBaseFilter extends KalturaDistributionProviderFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDailymotionDistributionProviderFilter extends KalturaDailymotionDistributionProviderBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaDailymotionDistributionProfileBaseFilter extends KalturaConfigurableDistributionProfileFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDailymotionDistributionProfileFilter extends KalturaDailymotionDistributionProfileBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDailymotionDistributionClientPlugin extends KalturaClientPlugin
{
	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	/**
	 * @return KalturaDailymotionDistributionClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaDailymotionDistributionClientPlugin($client);
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
		return 'dailymotionDistribution';
	}
}

