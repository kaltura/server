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

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaCaptionAssetStatus
{
	const ERROR = -1;
	const QUEUED = 0;
	const READY = 2;
	const DELETED = 3;
	const IMPORTING = 7;
	const EXPORTING = 9;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaCaptionAssetOrderBy
{
	const SIZE_ASC = "+size";
	const SIZE_DESC = "-size";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
	const DELETED_AT_ASC = "+deletedAt";
	const DELETED_AT_DESC = "-deletedAt";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaCaptionParamsOrderBy
{
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaCaptionType
{
	const SRT = "1";
	const DFXP = "2";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaCaptionAssetListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaCaptionAsset
	 * @readonly
	 */
	public $objects;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $totalCount = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaCaptionAsset extends KalturaAsset
{
	/**
	 * The Caption Params used to create this Caption Asset
	 * 	 
	 *
	 * @var int
	 * @insertonly
	 */
	public $captionParamsId = null;

	/**
	 * The language of the caption asset content
	 * 	 
	 *
	 * @var KalturaLanguage
	 */
	public $language = null;

	/**
	 * The language of the caption asset content
	 * 	 
	 *
	 * @var KalturaLanguageCode
	 * @readonly
	 */
	public $languageCode = null;

	/**
	 * Is default caption asset of the entry
	 * 	 
	 *
	 * @var KalturaNullableBoolean
	 */
	public $isDefault = null;

	/**
	 * Friendly label
	 * 	 
	 *
	 * @var string
	 */
	public $label = null;

	/**
	 * The caption format
	 * 	 
	 *
	 * @var KalturaCaptionType
	 * @insertonly
	 */
	public $format = null;

	/**
	 * The status of the asset
	 * 	 
	 *
	 * @var KalturaCaptionAssetStatus
	 * @readonly
	 */
	public $status = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaCaptionParams extends KalturaAssetParams
{
	/**
	 * The language of the caption content
	 * 	 
	 *
	 * @var KalturaLanguage
	 * @insertonly
	 */
	public $language = null;

	/**
	 * Is default caption asset of the entry
	 * 	 
	 *
	 * @var KalturaNullableBoolean
	 */
	public $isDefault = null;

	/**
	 * Friendly label
	 * 	 
	 *
	 * @var string
	 */
	public $label = null;

	/**
	 * The caption format
	 * 	 
	 *
	 * @var KalturaCaptionType
	 * @insertonly
	 */
	public $format = null;

	/**
	 * Id of the caption params or the flavor params to be used as source for the caption creation
	 * 	 
	 *
	 * @var int
	 */
	public $sourceParamsId = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaCaptionAssetBaseFilter extends KalturaAssetFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $captionParamsIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $captionParamsIdIn = null;

	/**
	 * 
	 *
	 * @var KalturaCaptionType
	 */
	public $formatEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $formatIn = null;

	/**
	 * 
	 *
	 * @var KalturaCaptionAssetStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusNotIn = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaCaptionParamsBaseFilter extends KalturaAssetParamsFilter
{
	/**
	 * 
	 *
	 * @var KalturaCaptionType
	 */
	public $formatEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $formatIn = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaCaptionAssetFilter extends KalturaCaptionAssetBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaCaptionParamsFilter extends KalturaCaptionParamsBaseFilter
{

}


/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaCaptionAssetService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	function add($entryId, KalturaCaptionAsset $captionAsset)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "captionAsset", $captionAsset->toParams());
		$this->client->queueServiceActionCall("caption_captionasset", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaCaptionAsset");
		return $resultObject;
	}

	function setContent($id, KalturaContentResource $contentResource)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "contentResource", $contentResource->toParams());
		$this->client->queueServiceActionCall("caption_captionasset", "setContent", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaCaptionAsset");
		return $resultObject;
	}

	function update($id, KalturaCaptionAsset $captionAsset)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "captionAsset", $captionAsset->toParams());
		$this->client->queueServiceActionCall("caption_captionasset", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaCaptionAsset");
		return $resultObject;
	}

	function serveByEntryId($entryId, $captionParamId = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "captionParamId", $captionParamId);
		$this->client->queueServiceActionCall('caption_captionasset', 'serveByEntryId', $kparams);
		$resultObject = $this->client->getServeUrl();
		return $resultObject;
	}

	function getUrl($id, $storageId = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "storageId", $storageId);
		$this->client->queueServiceActionCall("caption_captionasset", "getUrl", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "string");
		return $resultObject;
	}

	function getRemotePaths($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("caption_captionasset", "getRemotePaths", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaRemotePathListResponse");
		return $resultObject;
	}

	function serve($captionAssetId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "captionAssetId", $captionAssetId);
		$this->client->queueServiceActionCall('caption_captionasset', 'serve', $kparams);
		$resultObject = $this->client->getServeUrl();
		return $resultObject;
	}

	function setAsDefault($captionAssetId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "captionAssetId", $captionAssetId);
		$this->client->queueServiceActionCall("caption_captionasset", "setAsDefault", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	function get($captionAssetId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "captionAssetId", $captionAssetId);
		$this->client->queueServiceActionCall("caption_captionasset", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaCaptionAsset");
		return $resultObject;
	}

	function listAction(KalturaAssetFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("caption_captionasset", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaCaptionAssetListResponse");
		return $resultObject;
	}

	function delete($captionAssetId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "captionAssetId", $captionAssetId);
		$this->client->queueServiceActionCall("caption_captionasset", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}
}
/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaCaptionClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaCaptionAssetService
	 */
	public $captionAsset = null;

	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
		$this->captionAsset = new KalturaCaptionAssetService($client);
	}

	/**
	 * @return KalturaCaptionClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaCaptionClientPlugin($client);
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'captionAsset' => $this->captionAsset,
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'caption';
	}
}

