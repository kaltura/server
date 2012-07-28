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
class KalturaAttachmentAssetStatus
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
class KalturaAttachmentAssetOrderBy
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
class KalturaAttachmentType
{
	const TEXT = "1";
	const MEDIA = "2";
	const DOCUMENT = "3";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAttachmentAssetListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaAttachmentAsset
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
class KalturaAttachmentAsset extends KalturaAsset
{
	/**
	 * The filename of the attachment asset content
	 * 	 
	 *
	 * @var string
	 */
	public $filename = null;

	/**
	 * Attachment asset title
	 * 	 
	 *
	 * @var string
	 */
	public $title = null;

	/**
	 * The attachment format
	 * 	 
	 *
	 * @var KalturaAttachmentType
	 */
	public $format = null;

	/**
	 * The status of the asset
	 * 	 
	 *
	 * @var KalturaAttachmentAssetStatus
	 * @readonly
	 */
	public $status = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaAttachmentAssetBaseFilter extends KalturaAssetFilter
{
	/**
	 * 
	 *
	 * @var KalturaAttachmentType
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
	 * @var KalturaAttachmentAssetStatus
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
class KalturaAttachmentAssetFilter extends KalturaAttachmentAssetBaseFilter
{

}


/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAttachmentAssetService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	function add($entryId, KalturaAttachmentAsset $attachmentAsset)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "attachmentAsset", $attachmentAsset->toParams());
		$this->client->queueServiceActionCall("attachment_attachmentasset", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaAttachmentAsset");
		return $resultObject;
	}

	function setContent($id, KalturaContentResource $contentResource)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "contentResource", $contentResource->toParams());
		$this->client->queueServiceActionCall("attachment_attachmentasset", "setContent", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaAttachmentAsset");
		return $resultObject;
	}

	function update($id, KalturaAttachmentAsset $attachmentAsset)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "attachmentAsset", $attachmentAsset->toParams());
		$this->client->queueServiceActionCall("attachment_attachmentasset", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaAttachmentAsset");
		return $resultObject;
	}

	function getUrl($id, $storageId = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "storageId", $storageId);
		$this->client->queueServiceActionCall("attachment_attachmentasset", "getUrl", $kparams);
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
		$this->client->queueServiceActionCall("attachment_attachmentasset", "getRemotePaths", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaRemotePathListResponse");
		return $resultObject;
	}

	function serve($attachmentAssetId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "attachmentAssetId", $attachmentAssetId);
		$this->client->queueServiceActionCall('attachment_attachmentasset', 'serve', $kparams);
		$resultObject = $this->client->getServeUrl();
		return $resultObject;
	}

	function get($attachmentAssetId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "attachmentAssetId", $attachmentAssetId);
		$this->client->queueServiceActionCall("attachment_attachmentasset", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaAttachmentAsset");
		return $resultObject;
	}

	function listAction(KalturaAssetFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("attachment_attachmentasset", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaAttachmentAssetListResponse");
		return $resultObject;
	}

	function delete($attachmentAssetId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "attachmentAssetId", $attachmentAssetId);
		$this->client->queueServiceActionCall("attachment_attachmentasset", "delete", $kparams);
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
class KalturaAttachmentClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaAttachmentAssetService
	 */
	public $attachmentAsset = null;

	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
		$this->attachmentAsset = new KalturaAttachmentAssetService($client);
	}

	/**
	 * @return KalturaAttachmentClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaAttachmentClientPlugin($client);
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'attachmentAsset' => $this->attachmentAsset,
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'attachment';
	}
}

