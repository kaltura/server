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
class KalturaYouTubeDistributionField
{
	const NOTIFICATION_EMAIL = "NOTIFICATION_EMAIL";
	const OWNER_NAME = "OWNER_NAME";
	const TARGET = "TARGET";
	const DATE_RECORDED = "DATE_RECORDED";
	const LANGUAGE = "LANGUAGE";
	const START_TIME = "START_TIME";
	const END_TIME = "END_TIME";
	const URGENT_REFERENCE_FILE = "URGENT_REFERENCE_FILE";
	const KEEP_FINGERPRINT = "KEEP_FINGERPRINT";
	const ACCOUNT_USERNAME = "ACCOUNT_USERNAME";
	const ACCOUNT_PASSWORD = "ACCOUNT_PASSWORD";
	const MEDIA_TITLE = "MEDIA_TITLE";
	const MEDIA_DESCRIPTION = "MEDIA_DESCRIPTION";
	const MEDIA_KEYWORDS = "MEDIA_KEYWORDS";
	const MEDIA_CATEGORY = "MEDIA_CATEGORY";
	const MEDIA_RATING = "MEDIA_RATING";
	const ALLOW_COMMENTS = "ALLOW_COMMENTS";
	const ALLOW_RESPONSES = "ALLOW_RESPONSES";
	const ALLOW_RATINGS = "ALLOW_RATINGS";
	const ALLOW_EMBEDDING = "ALLOW_EMBEDDING";
	const POLICY_COMMERCIAL = "POLICY_COMMERCIAL";
	const POLICY_UGC = "POLICY_UGC";
	const WEB_METADATA_CUSTOM_ID = "WEB_METADATA_CUSTOM_ID";
	const WEB_METADATA_NOTES = "WEB_METADATA_NOTES";
	const MOVIE_METADATA_CUSTOM_ID = "MOVIE_METADATA_CUSTOM_ID";
	const MOVIE_METADATA_DIRECTOR = "MOVIE_METADATA_DIRECTOR";
	const MOVIE_METADATA_NOTES = "MOVIE_METADATA_NOTES";
	const MOVIE_METADATA_TITLE = "MOVIE_METADATA_TITLE";
	const MOVIE_METADATA_TMS_ID = "MOVIE_METADATA_TMS_ID";
	const TV_METADATA_CUSTOM_ID = "TV_METADATA_CUSTOM_ID";
	const TV_METADATA_SHOW_TITLE = "TV_METADATA_SHOW_TITLE";
	const TV_METADATA_EPISODE = "TV_METADATA_EPISODE";
	const TV_METADATA_EPISODE_TITLE = "TV_METADATA_EPISODE_TITLE";
	const TV_METADATA_NOTES = "TV_METADATA_NOTES";
	const TV_METADATA_SEASON = "TV_METADATA_SEASON";
	const TV_METADATA_TMS_ID = "TV_METADATA_TMS_ID";
	const PLAYLISTS = "PLAYLISTS";
	const ADVERTISING_ADSENSE_FOR_VIDEO = "ADVERTISING_ADSENSE_FOR_VIDEO";
	const ADVERTISING_INVIDEO = "ADVERTISING_INVIDEO";
	const ADVERTISING_ALLOW_PRE_ROLL_ADS = "ADVERTISING_ALLOW_PRE_ROLL_ADS";
	const ADVERTISING_ALLOW_POST_ROLL_ADS = "ADVERTISING_ALLOW_POST_ROLL_ADS";
	const THIRD_PARTY_AD_SERVER_AD_TYPE = "THIRD_PARTY_AD_SERVER_AD_TYPE";
	const THIRD_PARTY_AD_SERVER_PARTNER_ID = "THIRD_PARTY_AD_SERVER_PARTNER_ID";
	const THIRD_PARTY_AD_SERVER_VIDEO_ID = "THIRD_PARTY_AD_SERVER_VIDEO_ID";
	const LOCATION_COUNTRY = "LOCATION_COUNTRY";
	const LOCATION_LOCATION_TEXT = "LOCATION_LOCATION_TEXT";
	const LOCATION_ZIP_CODE = "LOCATION_ZIP_CODE";
	const DISTRIBUTION_RESTRICTION_DISTRIBUTION_RULE = "DISTRIBUTION_RESTRICTION_DISTRIBUTION_RULE";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaYouTubeDistributionProfileOrderBy
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
class KalturaYouTubeDistributionProviderOrderBy
{
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaYouTubeDistributionJobProviderData extends KalturaConfigurableDistributionJobProviderData
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
	 * @var string
	 */
	public $sftpDirectory = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $sftpMetadataFilename = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $currentPlaylists = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaYouTubeDistributionProfile extends KalturaConfigurableDistributionProfile
{
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
	public $notificationEmail = null;

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
	public $sftpPublicKey = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $sftpPrivateKey = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $sftpBaseDir = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $ownerName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $defaultCategory = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $allowComments = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $allowEmbedding = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $allowRatings = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $allowResponses = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $commercialPolicy = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $ugcPolicy = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $target = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $adServerPartnerId = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $enableAdServer = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $allowPreRollAds = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $allowPostRollAds = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaYouTubeDistributionProviderBaseFilter extends KalturaDistributionProviderFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaYouTubeDistributionProviderFilter extends KalturaYouTubeDistributionProviderBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaYouTubeDistributionProfileBaseFilter extends KalturaConfigurableDistributionProfileFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaYouTubeDistributionProfileFilter extends KalturaYouTubeDistributionProfileBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaYouTubeDistributionClientPlugin extends KalturaClientPlugin
{
	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	/**
	 * @return KalturaYouTubeDistributionClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaYouTubeDistributionClientPlugin($client);
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
		return 'youTubeDistribution';
	}
}

