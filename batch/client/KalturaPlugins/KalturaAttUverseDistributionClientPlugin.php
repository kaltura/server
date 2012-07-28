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
class KalturaAttUverseDistributionField
{
	const CHANNEL_TITLE = "CHANNEL_TITLE";
	const ITEM_ENTRY_ID = "ITEM_ENTRY_ID";
	const ITEM_CREATED_AT = "ITEM_CREATED_AT";
	const ITEM_UPDATED_AT = "ITEM_UPDATED_AT";
	const ITEM_START_DATE = "ITEM_START_DATE";
	const ITEM_END_DATE = "ITEM_END_DATE";
	const ITEM_TITLE = "ITEM_TITLE";
	const ITEM_DESCRIPTION = "ITEM_DESCRIPTION";
	const ITEM_TAGS = "ITEM_TAGS";
	const ITEM_CATEGORIES = "ITEM_CATEGORIES";
	const ITEM_METADATA_SHORT_TITLE = "ITEM_METADATA_SHORT_TITLE";
	const ITEM_METADATA_TUNEIN = "ITEM_METADATA_TUNEIN";
	const ITEM_METADATA_CONTENT_RATING = "ITEM_METADATA_CONTENT_RATING";
	const ITEM_METADATA_LEGAL_DISCLAIMER = "ITEM_METADATA_LEGAL_DISCLAIMER";
	const ITEM_METADATA_GENRE = "ITEM_METADATA_GENRE";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAttUverseDistributionProfileOrderBy
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
class KalturaAttUverseDistributionProviderOrderBy
{
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAttUverseDistributionJobProviderData extends KalturaConfigurableDistributionJobProviderData
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
	public $thumbLocalPaths = null;

	/**
	 * The remote URL of the video asset that was distributed
	 * 	 
	 *
	 * @var string
	 */
	public $remoteAssetFileUrls = null;

	/**
	 * The remote URL of the video asset that was distributed
	 * 	 
	 *
	 * @var string
	 */
	public $remoteThumbnailFileUrls = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAttUverseDistributionProfile extends KalturaConfigurableDistributionProfile
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
	public $ftpHost = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $ftpUsername = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $ftpPassword = null;

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
	public $channelTitle = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaAttUverseDistributionProviderBaseFilter extends KalturaDistributionProviderFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAttUverseDistributionProviderFilter extends KalturaAttUverseDistributionProviderBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaAttUverseDistributionProfileBaseFilter extends KalturaConfigurableDistributionProfileFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAttUverseDistributionProfileFilter extends KalturaAttUverseDistributionProfileBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAttUverseDistributionClientPlugin extends KalturaClientPlugin
{
	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	/**
	 * @return KalturaAttUverseDistributionClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaAttUverseDistributionClientPlugin($client);
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
		return 'attUverseDistribution';
	}
}

