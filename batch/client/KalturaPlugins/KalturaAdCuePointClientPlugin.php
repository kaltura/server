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
require_once(dirname(__FILE__) . "/KalturaCuePointClientPlugin.php");

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAdCuePointOrderBy
{
	const END_TIME_ASC = "+endTime";
	const END_TIME_DESC = "-endTime";
	const DURATION_ASC = "+duration";
	const DURATION_DESC = "-duration";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
	const START_TIME_ASC = "+startTime";
	const START_TIME_DESC = "-startTime";
	const PARTNER_SORT_VALUE_ASC = "+partnerSortValue";
	const PARTNER_SORT_VALUE_DESC = "-partnerSortValue";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAdProtocolType
{
	const CUSTOM = "0";
	const VAST = "1";
	const VAST_2_0 = "2";
	const VPAID = "3";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAdType
{
	const VIDEO = "1";
	const OVERLAY = "2";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAdCuePoint extends KalturaCuePoint
{
	/**
	 * 
	 *
	 * @var KalturaAdProtocolType
	 * @insertonly
	 */
	public $protocolType = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $sourceUrl = null;

	/**
	 * 
	 *
	 * @var KalturaAdType
	 */
	public $adType = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $title = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $endTime = null;

	/**
	 * Duration in milliseconds
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $duration = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaAdCuePointBaseFilter extends KalturaCuePointFilter
{
	/**
	 * 
	 *
	 * @var KalturaAdProtocolType
	 */
	public $protocolTypeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $protocolTypeIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $titleLike = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $titleMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $titleMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $endTimeGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $endTimeLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $durationGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $durationLessThanOrEqual = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAdCuePointFilter extends KalturaAdCuePointBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAdCuePointClientPlugin extends KalturaClientPlugin
{
	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	/**
	 * @return KalturaAdCuePointClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaAdCuePointClientPlugin($client);
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
		return 'adCuePoint';
	}
}

