<?php
require_once(dirname(__FILE__) . "/../KalturaClientBase.php");
require_once(dirname(__FILE__) . "/../KalturaEnums.php");
require_once(dirname(__FILE__) . "/../KalturaTypes.php");


class KalturaDropFolderService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	function add(KalturaDropFolder $dropFolder)
	{
		$kparams = array();
		$this->client->addParam($kparams, "dropFolder", $dropFolder->toParams());
		$this->client->queueServiceActionCall("dropfolder_dropfolder", "add", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDropFolder");
		return $resultObject;
	}

	function get($dropFolderId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "dropFolderId", $dropFolderId);
		$this->client->queueServiceActionCall("dropfolder_dropfolder", "get", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDropFolder");
		return $resultObject;
	}

	function update($dropFolderId, KalturaDropFolder $dropFolder)
	{
		$kparams = array();
		$this->client->addParam($kparams, "dropFolderId", $dropFolderId);
		$this->client->addParam($kparams, "dropFolder", $dropFolder->toParams());
		$this->client->queueServiceActionCall("dropfolder_dropfolder", "update", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDropFolder");
		return $resultObject;
	}

	function delete($dropFolderId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "dropFolderId", $dropFolderId);
		$this->client->queueServiceActionCall("dropfolder_dropfolder", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDropFolder");
		return $resultObject;
	}

	function listAction(KalturaDropFolderFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("dropfolder_dropfolder", "list", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDropFolderListResponse");
		return $resultObject;
	}
}

class KalturaDropFolderFileService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	function add(KalturaDropFolderFile $dropFolderFile)
	{
		$kparams = array();
		$this->client->addParam($kparams, "dropFolderFile", $dropFolderFile->toParams());
		$this->client->queueServiceActionCall("dropfolder_dropfolderfile", "add", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDropFolderFile");
		return $resultObject;
	}

	function get($dropFolderFileId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "dropFolderFileId", $dropFolderFileId);
		$this->client->queueServiceActionCall("dropfolder_dropfolderfile", "get", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDropFolderFile");
		return $resultObject;
	}

	function update($dropFolderFileId, KalturaDropFolderFile $dropFolderFile)
	{
		$kparams = array();
		$this->client->addParam($kparams, "dropFolderFileId", $dropFolderFileId);
		$this->client->addParam($kparams, "dropFolderFile", $dropFolderFile->toParams());
		$this->client->queueServiceActionCall("dropfolder_dropfolderfile", "update", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDropFolderFile");
		return $resultObject;
	}

	function delete($dropFolderFileId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "dropFolderFileId", $dropFolderFileId);
		$this->client->queueServiceActionCall("dropfolder_dropfolderfile", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDropFolderFile");
		return $resultObject;
	}

	function listAction(KalturaDropFolderFileFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("dropfolder_dropfolderfile", "list", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDropFolderFileListResponse");
		return $resultObject;
	}

	function ignore($dropFolderFileId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "dropFolderFileId", $dropFolderFileId);
		$this->client->queueServiceActionCall("dropfolder_dropfolderfile", "ignore", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDropFolderFile");
		return $resultObject;
	}
}
class KalturaDropFolderClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaDropFolderClientPlugin
	 */
	protected static $instance;

	/**
	 * @var KalturaDropFolderService
	 */
	public $dropFolder = null;

	/**
	 * @var KalturaDropFolderFileService
	 */
	public $dropFolderFile = null;

	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
		$this->dropFolder = new KalturaDropFolderService($client);
		$this->dropFolderFile = new KalturaDropFolderFileService($client);
	}

	/**
	 * @return KalturaDropFolderClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		if(!self::$instance)
			self::$instance = new KalturaDropFolderClientPlugin($client);
		return self::$instance;
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'dropFolder' => $this->dropFolder,
			'dropFolderFile' => $this->dropFolderFile,
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'dropFolder';
	}
}

