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
class KalturaFreewheelGenericDistributionField
{
	const VIDEO_ID = "VIDEO_ID";
	const FWTITLES_EPISODE_TITLE1 = "FWTITLES_EPISODE_TITLE1";
	const FWTITLES_EPISODE_TITLE2 = "FWTITLES_EPISODE_TITLE2";
	const FWTITLES_SERIES = "FWTITLES_SERIES";
	const FWTITLES_SEASON = "FWTITLES_SEASON";
	const FWTITLES_GROUP1 = "FWTITLES_GROUP1";
	const FWTITLES_GROUP2 = "FWTITLES_GROUP2";
	const FWTITLES_GROUP3 = "FWTITLES_GROUP3";
	const FWTITLES_GROUP4 = "FWTITLES_GROUP4";
	const FWTITLES_GROUP5 = "FWTITLES_GROUP5";
	const FWTITLES_GROUP6 = "FWTITLES_GROUP6";
	const FWTITLES_GROUP7 = "FWTITLES_GROUP7";
	const FWTITLES_GROUP8 = "FWTITLES_GROUP8";
	const FWTITLES_GROUP9 = "FWTITLES_GROUP9";
	const FWTITLES_GROUP10 = "FWTITLES_GROUP10";
	const FWDESCRIPTIONS_SERIES = "FWDESCRIPTIONS_SERIES";
	const FWDESCRIPTIONS_EPISODE = "FWDESCRIPTIONS_EPISODE";
	const GENRE = "GENRE";
	const RATING = "RATING";
	const DATE_AVAILABLE_START = "DATE_AVAILABLE_START";
	const DATE_AVAILABLE_END = "DATE_AVAILABLE_END";
	const DATE_ISSUED = "DATE_ISSUED";
	const DATE_LAST_AIRED = "DATE_LAST_AIRED";
	const DURATION = "DURATION";
	const FWMETADATA = "FWMETADATA";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaFreewheelGenericDistributionProfileOrderBy
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
class KalturaFreewheelGenericDistributionProviderOrderBy
{
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaFreewheelGenericDistributionJobProviderData extends KalturaConfigurableDistributionJobProviderData
{
	/**
	 * Demonstrate passing array of paths to the job
	 * 	 
	 *
	 * @var array of KalturaString
	 */
	public $videoAssetFilePaths;

	/**
	 * Demonstrate passing single path to the job
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


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaFreewheelGenericDistributionProfile extends KalturaConfigurableDistributionProfile
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $apikey = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $email = null;

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
	public $sftpLogin = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $contentOwner = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $upstreamVideoId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $upstreamNetworkName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $upstreamNetworkId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $categoryId = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $replaceGroup = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $replaceAirDates = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaFreewheelGenericDistributionProviderBaseFilter extends KalturaDistributionProviderFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaFreewheelGenericDistributionProviderFilter extends KalturaFreewheelGenericDistributionProviderBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaFreewheelGenericDistributionProfileBaseFilter extends KalturaConfigurableDistributionProfileFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaFreewheelGenericDistributionProfileFilter extends KalturaFreewheelGenericDistributionProfileBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaFreewheelGenericDistributionClientPlugin extends KalturaClientPlugin
{
	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	/**
	 * @return KalturaFreewheelGenericDistributionClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaFreewheelGenericDistributionClientPlugin($client);
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
		return 'freewheelGenericDistribution';
	}
}

