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
class KalturaHuluDistributionField
{
	const SERIES_TITLE = "SERIES_TITLE";
	const SERIES_DESCRIPTION = "SERIES_DESCRIPTION";
	const SERIES_PRIMARY_CATEGORY = "SERIES_PRIMARY_CATEGORY";
	const SERIES_ADDITIONAL_CATEGORIES = "SERIES_ADDITIONAL_CATEGORIES";
	const SERIES_CHANNEL = "SERIES_CHANNEL";
	const SEASON_NUMBER = "SEASON_NUMBER";
	const SEASON_SYNOPSIS = "SEASON_SYNOPSIS";
	const SEASON_TUNEIN_INFORMATION = "SEASON_TUNEIN_INFORMATION";
	const VIDEO_MEDIA_TYPE = "VIDEO_MEDIA_TYPE";
	const VIDEO_TITLE = "VIDEO_TITLE";
	const VIDEO_EPISODE_NUMBER = "VIDEO_EPISODE_NUMBER";
	const VIDEO_RATING = "VIDEO_RATING";
	const VIDEO_CONTENT_RATING_REASON = "VIDEO_CONTENT_RATING_REASON";
	const VIDEO_AVAILABLE_DATE = "VIDEO_AVAILABLE_DATE";
	const VIDEO_EXPIRATION_DATE = "VIDEO_EXPIRATION_DATE";
	const VIDEO_DESCRIPTION = "VIDEO_DESCRIPTION";
	const VIDEO_FULL_DESCRIPTION = "VIDEO_FULL_DESCRIPTION";
	const VIDEO_COPYRIGHT = "VIDEO_COPYRIGHT";
	const VIDEO_KEYWORDS = "VIDEO_KEYWORDS";
	const VIDEO_LANGUAGE = "VIDEO_LANGUAGE";
	const VIDEO_PROGRAMMING_TYPE = "VIDEO_PROGRAMMING_TYPE";
	const VIDEO_EXTERNAL_ID = "VIDEO_EXTERNAL_ID";
	const VIDEO_ORIGINAL_PREMIERE_DATE = "VIDEO_ORIGINAL_PREMIERE_DATE";
	const VIDEO_SEGMENTS = "VIDEO_SEGMENTS";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaHuluDistributionProfileOrderBy
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
class KalturaHuluDistributionProviderOrderBy
{
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaHuluDistributionJobProviderData extends KalturaConfigurableDistributionJobProviderData
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
	public $thumbAssetFilePath = null;

	/**
	 * 
	 *
	 * @var array of KalturaCuePoint
	 */
	public $cuePoints;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fileBaseName = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaHuluDistributionProfile extends KalturaConfigurableDistributionProfile
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $sftpHost = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $sftpLogin = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $sftpPass = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $seriesChannel = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $seriesPrimaryCategory = null;

	/**
	 * 
	 *
	 * @var array of KalturaString
	 */
	public $seriesAdditionalCategories;

	/**
	 * 
	 *
	 * @var string
	 */
	public $seasonNumber = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $seasonSynopsis = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $seasonTuneInInformation = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $videoMediaType = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $disableEpisodeNumberCustomValidation = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaHuluDistributionProviderBaseFilter extends KalturaDistributionProviderFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaHuluDistributionProviderFilter extends KalturaHuluDistributionProviderBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaHuluDistributionProfileBaseFilter extends KalturaConfigurableDistributionProfileFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaHuluDistributionProfileFilter extends KalturaHuluDistributionProfileBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaHuluDistributionClientPlugin extends KalturaClientPlugin
{
	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	/**
	 * @return KalturaHuluDistributionClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaHuluDistributionClientPlugin($client);
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
		return 'huluDistribution';
	}
}

