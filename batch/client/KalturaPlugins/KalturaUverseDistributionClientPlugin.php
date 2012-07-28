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
class KalturaUverseDistributionField
{
	const ITEM_GUID = "ITEM_GUID";
	const ITEM_TITLE = "ITEM_TITLE";
	const ITEM_LINK = "ITEM_LINK";
	const ITEM_DESCRIPTION = "ITEM_DESCRIPTION";
	const ITEM_MEDIA_RATING = "ITEM_MEDIA_RATING";
	const ITEM_MEDIA_CATEGORY = "ITEM_MEDIA_CATEGORY";
	const ITEM_PUB_DATE = "ITEM_PUB_DATE";
	const ITEM_EXPIRATION_DATE = "ITEM_EXPIRATION_DATE";
	const ITEM_MEDIA_KEYWORDS = "ITEM_MEDIA_KEYWORDS";
	const ITEM_LIVE_ORIGINAL_RELEASE_DATE = "ITEM_LIVE_ORIGINAL_RELEASE_DATE";
	const ITEM_MEDIA_TITLE = "ITEM_MEDIA_TITLE";
	const ITEM_MEDIA_DESCRIPTION = "ITEM_MEDIA_DESCRIPTION";
	const ITEM_MEDIA_COPYRIGHT = "ITEM_MEDIA_COPYRIGHT";
	const ITEM_MEDIA_COPYRIGHT_URL = "ITEM_MEDIA_COPYRIGHT_URL";
	const ITEM_THUMBNAIL_CREDIT = "ITEM_THUMBNAIL_CREDIT";
	const ITEM_CONTENT_LANG = "ITEM_CONTENT_LANG";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaUverseDistributionProfileOrderBy
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
class KalturaUverseDistributionProviderOrderBy
{
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaUverseDistributionJobProviderData extends KalturaConfigurableDistributionJobProviderData
{
	/**
	 * The local file path of the video asset that needs to be distributed
	 * 	 
	 *
	 * @var string
	 */
	public $localAssetFilePath = null;

	/**
	 * The remote URL of the video asset that was distributed
	 * 	 
	 *
	 * @var string
	 */
	public $remoteAssetUrl = null;

	/**
	 * The file name of the remote video asset that was distributed
	 * 	 
	 *
	 * @var string
	 */
	public $remoteAssetFileName = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaUverseDistributionProfile extends KalturaConfigurableDistributionProfile
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
	public $channelTitle = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $channelLink = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $channelDescription = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $channelLanguage = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $channelCopyright = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $channelImageTitle = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $channelImageUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $channelImageLink = null;

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
	public $ftpPassword = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaUverseDistributionProviderBaseFilter extends KalturaDistributionProviderFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaUverseDistributionProviderFilter extends KalturaUverseDistributionProviderBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaUverseDistributionProfileBaseFilter extends KalturaConfigurableDistributionProfileFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaUverseDistributionProfileFilter extends KalturaUverseDistributionProfileBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaUverseDistributionClientPlugin extends KalturaClientPlugin
{
	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	/**
	 * @return KalturaUverseDistributionClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaUverseDistributionClientPlugin($client);
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
		return 'uverseDistribution';
	}
}

