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
class KalturaQuickPlayDistributionField
{
	const TITLE = "TITLE";
	const DESCRIPTION = "DESCRIPTION";
	const GUID = "GUID";
	const CATEGORY = "CATEGORY";
	const PUB_DATE = "PUB_DATE";
	const QPM_KEYWORDS = "QPM_KEYWORDS";
	const QPM_PRICE_ID = "QPM_PRICE_ID";
	const QPM_UPDATE_DATE = "QPM_UPDATE_DATE";
	const QPM_EXPIRY_DATE = "QPM_EXPIRY_DATE";
	const QPM_SORT_ORDER = "QPM_SORT_ORDER";
	const QPM_GENRE = "QPM_GENRE";
	const QPM_COPYRIGHT = "QPM_COPYRIGHT";
	const QPM_ARTIST = "QPM_ARTIST";
	const QPM_DIRECTOR = "QPM_DIRECTOR";
	const QPM_PRODUCER = "QPM_PRODUCER";
	const QPM_EXP_DATE_PADDING = "QPM_EXP_DATE_PADDING";
	const QPM_ON_DEVICE_EXPIRATION_PADDING = "QPM_ON_DEVICE_EXPIRATION_PADDING";
	const QPM_ON_DEVICE_EXPIRATION = "QPM_ON_DEVICE_EXPIRATION";
	const QPM_GROUP_CATEGORY = "QPM_GROUP_CATEGORY";
	const QPM_NOTES = "QPM_NOTES";
	const QPM_RATING = "QPM_RATING";
	const QPM_RATING_SCHEMA = "QPM_RATING_SCHEMA";
	const ENCLOSURE_CONTENT_ENCODING_PROFILE = "ENCLOSURE_CONTENT_ENCODING_PROFILE";
	const ENCLOSURE_THUMBNAIL_ENCODING_PROFILE = "ENCLOSURE_THUMBNAIL_ENCODING_PROFILE";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaQuickPlayDistributionProfileOrderBy
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
class KalturaQuickPlayDistributionProviderOrderBy
{
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaQuickPlayDistributionJobProviderData extends KalturaConfigurableDistributionJobProviderData
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $xml = null;

	/**
	 * 
	 *
	 * @var array of KalturaString
	 */
	public $videoFilePaths;

	/**
	 * 
	 *
	 * @var array of KalturaString
	 */
	public $thumbnailFilePaths;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaQuickPlayDistributionProfile extends KalturaConfigurableDistributionProfile
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
	public $sftpBasePath = null;

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
	public $channelManagingEditor = null;

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
	public $channelImageTitle = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $channelImageWidth = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $channelImageHeight = null;

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
	public $channelImageUrl = null;

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
	public $channelGenerator = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $channelRating = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaQuickPlayDistributionProviderBaseFilter extends KalturaDistributionProviderFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaQuickPlayDistributionProviderFilter extends KalturaQuickPlayDistributionProviderBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaQuickPlayDistributionProfileBaseFilter extends KalturaConfigurableDistributionProfileFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaQuickPlayDistributionProfileFilter extends KalturaQuickPlayDistributionProfileBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaQuickPlayDistributionClientPlugin extends KalturaClientPlugin
{
	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	/**
	 * @return KalturaQuickPlayDistributionClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaQuickPlayDistributionClientPlugin($client);
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
		return 'quickPlayDistribution';
	}
}

