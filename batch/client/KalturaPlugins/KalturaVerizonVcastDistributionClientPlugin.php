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
class KalturaVerizonVcastDistributionField
{
	const TITLE = "TITLE";
	const EXTERNAL_ID = "EXTERNAL_ID";
	const ISRC = "ISRC";
	const UPC = "UPC";
	const SHORT_DESCRIPTION = "SHORT_DESCRIPTION";
	const DESCRIPTION = "DESCRIPTION";
	const KEYWORDS = "KEYWORDS";
	const PROGRAM_NAME = "PROGRAM_NAME";
	const PROGRAM_CODE = "PROGRAM_CODE";
	const PROGRAM_DESCRIPTION = "PROGRAM_DESCRIPTION";
	const VIDEO_TYPE = "VIDEO_TYPE";
	const VIDEO_DESCRIPTORS = "VIDEO_DESCRIPTORS";
	const SEASON = "SEASON";
	const EPISODE = "EPISODE";
	const STUDIO = "STUDIO";
	const ORIGINAL_COUNTRY = "ORIGINAL_COUNTRY";
	const ORIGINAL_RELEASE = "ORIGINAL_RELEASE";
	const PUB_DATE = "PUB_DATE";
	const CATEGORY = "CATEGORY";
	const CATEGORY_DESCRIPTOR = "CATEGORY_DESCRIPTOR";
	const SUB_CATEGORY_DESCRIPTOR = "SUB_CATEGORY_DESCRIPTOR";
	const GENRE = "GENRE";
	const TOP_STORY = "TOP_STORY";
	const SORT_NAME = "SORT_NAME";
	const RATING = "RATING";
	const RATING_DESCRIPTOR = "RATING_DESCRIPTOR";
	const COPYRIGHT = "COPYRIGHT";
	const ENTITLEMENT = "ENTITLEMENT";
	const LIVE_DATE = "LIVE_DATE";
	const END_DATE = "END_DATE";
	const PURCHASE_END_DATE = "PURCHASE_END_DATE";
	const PRIORITY = "PRIORITY";
	const ALLOW_STREAMING = "ALLOW_STREAMING";
	const STREAMING_PRICE_CODE = "STREAMING_PRICE_CODE";
	const ALLOW_DOWNLOAD = "ALLOW_DOWNLOAD";
	const DOWNLOAD_PRICE_CODE = "DOWNLOAD_PRICE_CODE";
	const PROVIDER = "PROVIDER";
	const PROVIDER_ID = "PROVIDER_ID";
	const ALERT_CODE = "ALERT_CODE";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaVerizonVcastDistributionProfileOrderBy
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
class KalturaVerizonVcastDistributionProviderOrderBy
{
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaVerizonVcastDistributionJobProviderData extends KalturaConfigurableDistributionJobProviderData
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $xml = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaVerizonVcastDistributionProfile extends KalturaConfigurableDistributionProfile
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
	public $entitlement = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $priority = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $allowStreaming = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $streamingPriceCode = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $allowDownload = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $downloadPriceCode = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaVerizonVcastDistributionProviderBaseFilter extends KalturaDistributionProviderFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaVerizonVcastDistributionProviderFilter extends KalturaVerizonVcastDistributionProviderBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaVerizonVcastDistributionProfileBaseFilter extends KalturaConfigurableDistributionProfileFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaVerizonVcastDistributionProfileFilter extends KalturaVerizonVcastDistributionProfileBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaVerizonVcastDistributionClientPlugin extends KalturaClientPlugin
{
	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	/**
	 * @return KalturaVerizonVcastDistributionClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaVerizonVcastDistributionClientPlugin($client);
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
		return 'verizonVcastDistribution';
	}
}

