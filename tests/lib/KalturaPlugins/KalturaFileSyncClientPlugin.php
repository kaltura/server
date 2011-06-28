<?php
require_once(dirname(__FILE__) . "/../KalturaClientBase.php");
require_once(dirname(__FILE__) . "/../KalturaEnums.php");
require_once(dirname(__FILE__) . "/../KalturaTypes.php");


class KalturaFileSyncService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	function listAction(KalturaFileSyncFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("filesync_filesync", "list", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFileSyncListResponse");
		return $resultObject;
	}

	function sync($fileSyncId, $fileData)
	{
		$kparams = array();
		$this->client->addParam($kparams, "fileSyncId", $fileSyncId);
		$kfiles = array();
		$this->client->addParam($kfiles, "fileData", $fileData);
		$this->client->queueServiceActionCall("filesync_filesync", "sync", $kparams, $kfiles);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFileSync");
		return $resultObject;
	}
}
class KalturaFileSyncClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaFileSyncClientPlugin
	 */
	protected static $instance;

	/**
	 * @var KalturaFileSyncService
	 */
	public $fileSync = null;

	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
		$this->fileSync = new KalturaFileSyncService($client);
	}

	/**
	 * @return KalturaFileSyncClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		if(!self::$instance)
			self::$instance = new KalturaFileSyncClientPlugin($client);
		return self::$instance;
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'fileSync' => $this->fileSync,
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'fileSync';
	}
}

