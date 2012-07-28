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
class KalturaMetroPcsDistributionField
{
	const TITLE = "TITLE";
	const LINK = "LINK";
	const EXTERNAL_ID = "EXTERNAL_ID";
	const PROVIDER_ID = "PROVIDER_ID";
	const SHORT_DESCRIPTION = "SHORT_DESCRIPTION";
	const DESCRIPTION = "DESCRIPTION";
	const LANGUAGE = "LANGUAGE";
	const COPYRIGHT = "COPYRIGHT";
	const MANAGING_EDITOR = "MANAGING_EDITOR";
	const PUB_DATE = "PUB_DATE";
	const CATEGORY = "CATEGORY";
	const UPC = "UPC";
	const ISRC = "ISRC";
	const PROGRAM = "PROGRAM";
	const SEASON_ID = "SEASON_ID";
	const EPISODIC_ID = "EPISODIC_ID";
	const CHAPTER_ID = "CHAPTER_ID";
	const ARTIST = "ARTIST";
	const PERFORMER = "PERFORMER";
	const DIRECTOR = "DIRECTOR";
	const STUDIO = "STUDIO";
	const ORIGINAL_RELEASE = "ORIGINAL_RELEASE";
	const TOP_STORY = "TOP_STORY";
	const SORT_ORDER = "SORT_ORDER";
	const SORT_NAME = "SORT_NAME";
	const GENRE = "GENRE";
	const KEYWORDS = "KEYWORDS";
	const LOCAL_CODE = "LOCAL_CODE";
	const ENTITLEMENTS = "ENTITLEMENTS";
	const START_DATE = "START_DATE";
	const END_DATE = "END_DATE";
	const RATING = "RATING";
	const ITEM_TITLE = "ITEM_TITLE";
	const ITEM_DESCRIPTION = "ITEM_DESCRIPTION";
	const ITEM_TYPE = "ITEM_TYPE";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaMetroPcsDistributionProfileOrderBy
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
class KalturaMetroPcsDistributionProviderOrderBy
{
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaMetroPcsDistributionJobProviderData extends KalturaConfigurableDistributionJobProviderData
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $assetLocalPaths = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $thumbUrls = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaMetroPcsDistributionProfile extends KalturaConfigurableDistributionProfile
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $ftpHost = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $ftpLogin = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $ftpPass = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $ftpPath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $providerName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $providerId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $copyright = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entitlements = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $rating = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $itemType = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaMetroPcsDistributionProviderBaseFilter extends KalturaDistributionProviderFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaMetroPcsDistributionProviderFilter extends KalturaMetroPcsDistributionProviderBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaMetroPcsDistributionProfileBaseFilter extends KalturaConfigurableDistributionProfileFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaMetroPcsDistributionProfileFilter extends KalturaMetroPcsDistributionProfileBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaMetroPcsDistributionClientPlugin extends KalturaClientPlugin
{
	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	/**
	 * @return KalturaMetroPcsDistributionClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaMetroPcsDistributionClientPlugin($client);
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
		return 'metroPcsDistribution';
	}
}

