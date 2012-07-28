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
class KalturaMsnDistributionField
{
	const PROVIDER_ID = "PROVIDER_ID";
	const CSID = "CSID";
	const SOURCE = "SOURCE";
	const SOURCE_FRIENDLY_NAME = "SOURCE_FRIENDLY_NAME";
	const PAGE_GROUP = "PAGE_GROUP";
	const TITLE = "TITLE";
	const DESCRIPTION = "DESCRIPTION";
	const START_DATE = "START_DATE";
	const ACTIVATE_END_DATE = "ACTIVATE_END_DATE";
	const SEARCHABLE_END_DATE = "SEARCHABLE_END_DATE";
	const ARCHIVE_END_DATE = "ARCHIVE_END_DATE";
	const TAGS_PUBLIC = "TAGS_PUBLIC";
	const TAGS_MSNVIDEO_CAT = "TAGS_MSNVIDEO_CAT";
	const TAGS_MSNVIDEO_TOP = "TAGS_MSNVIDEO_TOP";
	const TAGS_MSNVIDEO_TOP_CAT = "TAGS_MSNVIDEO_TOP_CAT";
	const RELATED_LINK_N_TITLE = "RELATED_LINK_N_TITLE";
	const RELATED_LINK_N_URL = "RELATED_LINK_N_URL";
	const TAGS_PREMIUM_N_MARKET = "TAGS_PREMIUM_N_MARKET";
	const TAGS_PREMIUM_N_NAMESPACE = "TAGS_PREMIUM_N_NAMESPACE";
	const TAGS_PREMIUM_N_VALUE = "TAGS_PREMIUM_N_VALUE";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaMsnDistributionProfileOrderBy
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
class KalturaMsnDistributionProviderOrderBy
{
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaMsnDistributionJobProviderData extends KalturaConfigurableDistributionJobProviderData
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
class KalturaMsnDistributionProfile extends KalturaConfigurableDistributionProfile
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
	public $password = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $domain = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $csId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $source = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $sourceFriendlyName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $pageGroup = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $sourceFlavorParamsId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $wmvFlavorParamsId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $flvFlavorParamsId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $slFlavorParamsId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $slHdFlavorParamsId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $msnvideoCat = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $msnvideoTop = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $msnvideoTopCat = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaMsnDistributionProviderBaseFilter extends KalturaDistributionProviderFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaMsnDistributionProviderFilter extends KalturaMsnDistributionProviderBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaMsnDistributionProfileBaseFilter extends KalturaConfigurableDistributionProfileFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaMsnDistributionProfileFilter extends KalturaMsnDistributionProfileBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaMsnDistributionClientPlugin extends KalturaClientPlugin
{
	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	/**
	 * @return KalturaMsnDistributionClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaMsnDistributionClientPlugin($client);
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
		return 'msnDistribution';
	}
}

