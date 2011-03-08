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
class KalturaStorageProfileProtocol
{
	const KALTURA_DC = 0;
	const FTP = 1;
	const SCP = 2;
	const SFTP = 3;
}

/**
 * @package Admin
 * @subpackage Client
 */
class KalturaStorageProfileStatus
{
	const DISABLED = 1;
	const AUTOMATIC = 2;
	const MANUAL = 3;
}

/**
 * @package Admin
 * @subpackage Client
 */
class KalturaStorageServePriority
{
	const KALTURA_ONLY = 1;
	const KALTURA_FIRST = 2;
	const EXTERNAL_FIRST = 3;
	const EXTERNAL_ONLY = 4;
}

/**
 * @package Admin
 * @subpackage Client
 */
class KalturaStorageProfile extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $desciption = null;

	/**
	 * 
	 *
	 * @var KalturaStorageProfileStatus
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var KalturaStorageProfileProtocol
	 */
	public $protocol = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $storageUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $storageBaseDir = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $storageUsername = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $storagePassword = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $storageFtpPassiveMode = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $deliveryHttpBaseUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $deliveryRmpBaseUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $deliveryIisBaseUrl = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $minFileSize = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $maxFileSize = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $flavorParamsIds = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $maxConcurrentConnections = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $pathManagerClass = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $urlManagerClass = null;

	/**
	 * TODO - remove after events manager is implemented
	 * No need to create enum for temp field
	 * 
	 *
	 * @var int
	 */
	public $trigger = null;


}

/**
 * @package Admin
 * @subpackage Client
 */
class KalturaStorageProfileListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaStorageProfile
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
class KalturaStorageProfileService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	function listByPartner(KalturaPartnerFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("storageprofile_storageprofile", "listByPartner", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaStorageProfileListResponse");
		return $resultObject;
	}

	function updateStatus($storageId, $status)
	{
		$kparams = array();
		$this->client->addParam($kparams, "storageId", $storageId);
		$this->client->addParam($kparams, "status", $status);
		$this->client->queueServiceActionCall("storageprofile_storageprofile", "updateStatus", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	function get($storageProfileId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "storageProfileId", $storageProfileId);
		$this->client->queueServiceActionCall("storageprofile_storageprofile", "get", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaStorageProfile");
		return $resultObject;
	}

	function update($storageProfileId, KalturaStorageProfile $storageProfile)
	{
		$kparams = array();
		$this->client->addParam($kparams, "storageProfileId", $storageProfileId);
		$this->client->addParam($kparams, "storageProfile", $storageProfile->toParams());
		$this->client->queueServiceActionCall("storageprofile_storageprofile", "update", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaStorageProfile");
		return $resultObject;
	}

	function add(KalturaStorageProfile $storageProfile)
	{
		$kparams = array();
		$this->client->addParam($kparams, "storageProfile", $storageProfile->toParams());
		$this->client->queueServiceActionCall("storageprofile_storageprofile", "add", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaStorageProfile");
		return $resultObject;
	}
}
/**
 * @package Admin
 * @subpackage Client
 */
class KalturaStorageProfileClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaStorageProfileClientPlugin
	 */
	protected static $instance;

	/**
	 * @var KalturaStorageProfileService
	 */
	public $storageProfile = null;

	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
		$this->storageProfile = new KalturaStorageProfileService($client);
	}

	/**
	 * @return KalturaStorageProfileClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		if(!self::$instance)
			self::$instance = new KalturaStorageProfileClientPlugin($client);
		return self::$instance;
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'storageProfile' => $this->storageProfile,
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'storageProfile';
	}
}

