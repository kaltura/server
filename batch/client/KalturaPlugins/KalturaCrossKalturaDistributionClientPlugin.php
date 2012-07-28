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
class KalturaCrossKalturaDistributionField
{
	const BASE_ENTRY_NAME = "BASE_ENTRY_NAME";
	const BASE_ENTRY_DESCRIPTION = "BASE_ENTRY_DESCRIPTION";
	const BASE_ENTRY_USER_ID = "BASE_ENTRY_USER_ID";
	const BASE_ENTRY_TAGS = "BASE_ENTRY_TAGS";
	const BASE_ENTRY_CATEGORIES = "BASE_ENTRY_CATEGORIES";
	const BASE_ENTRY_PARTNER_DATA = "BASE_ENTRY_PARTNER_DATA";
	const BASE_ENTRY_START_DATE = "BASE_ENTRY_START_DATE";
	const BASE_ENTRY_END_DATE = "BASE_ENTRY_END_DATE";
	const BASE_ENTRY_REFERENCE_ID = "BASE_ENTRY_REFERENCE_ID";
	const BASE_ENTRY_LICENSE_TYPE = "BASE_ENTRY_LICENSE_TYPE";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaCrossKalturaDistributionProfileOrderBy
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
class KalturaCrossKalturaDistributionProviderOrderBy
{
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaCrossKalturaDistributionJobProviderData extends KalturaConfigurableDistributionJobProviderData
{
	/**
	 * Key-value array where the keys are IDs of distributed flavor assets in the source account and the values are the matching IDs in the target account
	 *      
	 *
	 * @var string
	 */
	public $distributedFlavorAssets = null;

	/**
	 * Key-value array where the keys are IDs of distributed thumb assets in the source account and the values are the matching IDs in the target account
	 *      
	 *
	 * @var string
	 */
	public $distributedThumbAssets = null;

	/**
	 * Key-value array where the keys are IDs of distributed metadata objects in the source account and the values are the matching IDs in the target account
	 *      
	 *
	 * @var string
	 */
	public $distributedMetadata = null;

	/**
	 * Key-value array where the keys are IDs of distributed caption assets in the source account and the values are the matching IDs in the target account
	 *      
	 *
	 * @var string
	 */
	public $distributedCaptionAssets = null;

	/**
	 * Key-value array where the keys are IDs of distributed cue points in the source account and the values are the matching IDs in the target account
	 *      
	 *
	 * @var string
	 */
	public $distributedCuePoints = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaCrossKalturaDistributionProfile extends KalturaConfigurableDistributionProfile
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $targetServiceUrl = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $targetAccountId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $targetLoginId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $targetLoginPassword = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $metadataXslt = null;

	/**
	 * 
	 *
	 * @var array of KalturaStringValue
	 */
	public $metadataXpathsTriggerUpdate;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $distributeCaptions = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $distributeCuePoints = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $distributeRemoteFlavorAssetContent = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $distributeRemoteThumbAssetContent = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $distributeRemoteCaptionAssetContent = null;

	/**
	 * 
	 *
	 * @var array of KalturaKeyValue
	 */
	public $mapAccessControlProfileIds;

	/**
	 * 
	 *
	 * @var array of KalturaKeyValue
	 */
	public $mapConversionProfileIds;

	/**
	 * 
	 *
	 * @var array of KalturaKeyValue
	 */
	public $mapMetadataProfileIds;

	/**
	 * 
	 *
	 * @var array of KalturaKeyValue
	 */
	public $mapStorageProfileIds;

	/**
	 * 
	 *
	 * @var array of KalturaKeyValue
	 */
	public $mapFlavorParamsIds;

	/**
	 * 
	 *
	 * @var array of KalturaKeyValue
	 */
	public $mapThumbParamsIds;

	/**
	 * 
	 *
	 * @var array of KalturaKeyValue
	 */
	public $mapCaptionParamsIds;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaCrossKalturaDistributionProviderBaseFilter extends KalturaDistributionProviderFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaCrossKalturaDistributionProviderFilter extends KalturaCrossKalturaDistributionProviderBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaCrossKalturaDistributionProfileBaseFilter extends KalturaConfigurableDistributionProfileFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaCrossKalturaDistributionProfileFilter extends KalturaCrossKalturaDistributionProfileBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaCrossKalturaDistributionClientPlugin extends KalturaClientPlugin
{
	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	/**
	 * @return KalturaCrossKalturaDistributionClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaCrossKalturaDistributionClientPlugin($client);
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
		return 'crossKalturaDistribution';
	}
}

