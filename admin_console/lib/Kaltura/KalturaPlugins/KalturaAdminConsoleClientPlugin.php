<?php
/**
 * @package Admin
 * @subpackage Client
 */
require_once(dirname(__FILE__) . "/../KalturaClientBase.php");
require_once(dirname(__FILE__) . "/../KalturaEnums.php");
require_once(dirname(__FILE__) . "/../KalturaTypes.php");

/**
 * @package Admin
 * @subpackage Client
 */
class KalturaTrackEntryEventType
{
	const UPLOADED_FILE = 1;
	const WEBCAM_COMPLETED = 2;
	const IMPORT_STARTED = 3;
	const ADD_ENTRY = 4;
	const UPDATE_ENTRY = 5;
	const DELETED_ENTRY = 6;
}

/**
 * @package Admin
 * @subpackage Client
 */
class KalturaFlavorParamsOutputListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaFlavorParamsOutput
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
 * @package Admin
 * @subpackage Client
 */
class KalturaThumbParamsOutputListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaThumbParamsOutput
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
 * @package Admin
 * @subpackage Client
 */
class KalturaMediaInfoListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaMediaInfo
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
 * @package Admin
 * @subpackage Client
 */
class KalturaTrackEntry extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var KalturaTrackEntryEventType
	 */
	public $trackEventType = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $psVersion = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $context = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entryId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $hostName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $userId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $changedProperties = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $paramStr1 = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $paramStr2 = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $paramStr3 = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $ks = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAt = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAt = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $userIp = null;


}

/**
 * @package Admin
 * @subpackage Client
 */
class KalturaTrackEntryListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaTrackEntry
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
 * @package Admin
 * @subpackage Client
 */
class KalturaFlavorParamsOutputService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	function listAction(KalturaFlavorParamsOutputFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("adminconsole_flavorparamsoutput", "list", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFlavorParamsOutputListResponse");
		return $resultObject;
	}
}

/**
 * @package Admin
 * @subpackage Client
 */
class KalturaThumbParamsOutputService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	function listAction(KalturaThumbParamsOutputFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("adminconsole_thumbparamsoutput", "list", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaThumbParamsOutputListResponse");
		return $resultObject;
	}
}

/**
 * @package Admin
 * @subpackage Client
 */
class KalturaMediaInfoService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	function listAction(KalturaMediaInfoFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("adminconsole_mediainfo", "list", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMediaInfoListResponse");
		return $resultObject;
	}
}

/**
 * @package Admin
 * @subpackage Client
 */
class KalturaEntryAdminService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	function get($entryId, $version = -1)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "version", $version);
		$this->client->queueServiceActionCall("adminconsole_entryadmin", "get", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBaseEntry");
		return $resultObject;
	}

	function getByFlavorId($flavorId, $version = -1)
	{
		$kparams = array();
		$this->client->addParam($kparams, "flavorId", $flavorId);
		$this->client->addParam($kparams, "version", $version);
		$this->client->queueServiceActionCall("adminconsole_entryadmin", "getByFlavorId", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBaseEntry");
		return $resultObject;
	}

	function getTracks($entryId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->queueServiceActionCall("adminconsole_entryadmin", "getTracks", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaTrackEntryListResponse");
		return $resultObject;
	}
}

/**
 * @package Admin
 * @subpackage Client
 */
class KalturaUiConfAdminService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	function add(KalturaUiConf $uiConf)
	{
		$kparams = array();
		$this->client->addParam($kparams, "uiConf", $uiConf->toParams());
		$this->client->queueServiceActionCall("adminconsole_uiconfadmin", "add", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaUiConf");
		return $resultObject;
	}

	function update($id, KalturaUiConf $uiConf)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "uiConf", $uiConf->toParams());
		$this->client->queueServiceActionCall("adminconsole_uiconfadmin", "update", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaUiConf");
		return $resultObject;
	}

	function get($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("adminconsole_uiconfadmin", "get", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaUiConf");
		return $resultObject;
	}

	function delete($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("adminconsole_uiconfadmin", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}
}
/**
 * @package Admin
 * @subpackage Client
 */
class KalturaAdminConsoleClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaAdminConsoleClientPlugin
	 */
	protected static $instance;

	/**
	 * @var KalturaFlavorParamsOutputService
	 */
	public $flavorParamsOutput = null;

	/**
	 * @var KalturaThumbParamsOutputService
	 */
	public $thumbParamsOutput = null;

	/**
	 * @var KalturaMediaInfoService
	 */
	public $mediaInfo = null;

	/**
	 * @var KalturaEntryAdminService
	 */
	public $entryAdmin = null;

	/**
	 * @var KalturaUiConfAdminService
	 */
	public $uiConfAdmin = null;

	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
		$this->flavorParamsOutput = new KalturaFlavorParamsOutputService($client);
		$this->thumbParamsOutput = new KalturaThumbParamsOutputService($client);
		$this->mediaInfo = new KalturaMediaInfoService($client);
		$this->entryAdmin = new KalturaEntryAdminService($client);
		$this->uiConfAdmin = new KalturaUiConfAdminService($client);
	}

	/**
	 * @return KalturaAdminConsoleClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		if(!self::$instance)
			self::$instance = new KalturaAdminConsoleClientPlugin($client);
		return self::$instance;
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'flavorParamsOutput' => $this->flavorParamsOutput,
			'thumbParamsOutput' => $this->thumbParamsOutput,
			'mediaInfo' => $this->mediaInfo,
			'entryAdmin' => $this->entryAdmin,
			'uiConfAdmin' => $this->uiConfAdmin,
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'adminConsole';
	}
}

