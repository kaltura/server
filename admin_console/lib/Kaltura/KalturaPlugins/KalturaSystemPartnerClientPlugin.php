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
class KalturaSystemPartnerUsageFilter extends KalturaFilter
{
	/**
	 * Date range from
	 * 
	 *
	 * @var int
	 */
	public $fromDate = null;

	/**
	 * Date range to
	 * 
	 *
	 * @var int
	 */
	public $toDate = null;


}

/**
 * @package Admin
 * @subpackage Client
 */
class KalturaSystemPartnerUsageItem extends KalturaObjectBase
{
	/**
	 * Partner ID
	 * 
	 *
	 * @var int
	 */
	public $partnerId = null;

	/**
	 * Partner name
	 * 
	 *
	 * @var string
	 */
	public $partnerName = null;

	/**
	 * Partner status
	 * 
	 *
	 * @var KalturaPartnerStatus
	 */
	public $partnerStatus = null;

	/**
	 * Partner package
	 * 
	 *
	 * @var int
	 */
	public $partnerPackage = null;

	/**
	 * Partner creation date (Unix timestamp)
	 * 
	 *
	 * @var int
	 */
	public $partnerCreatedAt = null;

	/**
	 * Number of player loads in the specific date range
	 * 
	 *
	 * @var int
	 */
	public $views = null;

	/**
	 * Number of plays in the specific date range
	 * 
	 *
	 * @var int
	 */
	public $plays = null;

	/**
	 * Number of new entries created during specific date range
	 * 
	 *
	 * @var int
	 */
	public $entriesCount = null;

	/**
	 * Total number of entries
	 * 
	 *
	 * @var int
	 */
	public $totalEntriesCount = null;

	/**
	 * Number of new video entries created during specific date range
	 * 
	 *
	 * @var int
	 */
	public $videoEntriesCount = null;

	/**
	 * Number of new image entries created during specific date range
	 * 
	 *
	 * @var int
	 */
	public $imageEntriesCount = null;

	/**
	 * Number of new audio entries created during specific date range
	 * 
	 *
	 * @var int
	 */
	public $audioEntriesCount = null;

	/**
	 * Number of new mix entries created during specific date range
	 * 
	 *
	 * @var int
	 */
	public $mixEntriesCount = null;

	/**
	 * The total bandwidth usage during the given date range (in MB)
	 * 
	 *
	 * @var float
	 */
	public $bandwidth = null;

	/**
	 * The total storage consumption (in MB)
	 * 
	 *
	 * @var float
	 */
	public $totalStorage = null;

	/**
	 * The change in storage consumption (new uploads) during the given date range (in MB)
	 * 
	 *
	 * @var float
	 */
	public $storage = null;


}

/**
 * @package Admin
 * @subpackage Client
 */
class KalturaSystemPartnerUsageListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaSystemPartnerUsageItem
	 */
	public $objects;

	/**
	 * 
	 *
	 * @var int
	 */
	public $totalCount = null;


}

/**
 * @package Admin
 * @subpackage Client
 */
class KalturaSystemPartnerConfiguration extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $partnerName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $adminName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $adminEmail = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $host = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $cdnHost = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $maxBulkSize = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerPackage = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $monitorUsage = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $liveStreamEnabled = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $moderateContent = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $rtmpUrl = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $storageDeleteFromKaltura = null;

	/**
	 * 
	 *
	 * @var KalturaStorageServePriority
	 */
	public $storageServePriority = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $kmcVersion = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $enableAnalyticsTab = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $enableSilverLight = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $enableVast = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $enable508Players = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $enableMetadata = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $enableContentDistribution = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $enableAuditTrail = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $enableAnnotation = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $enablePs2PermissionValidation = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $defThumbOffset = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $adminLoginUsersQuota = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $userSessionRoleId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $adminSessionRoleId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $alwaysAllowedPermissionNames = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $importRemoteSourceForConvert = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $enableEntryReplacement = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $enableEntryReplacementApproval = null;


}

/**
 * @package Admin
 * @subpackage Client
 */
class KalturaSystemPartnerPackage extends KalturaObjectBase
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
	 * @var string
	 */
	public $name = null;


}


/**
 * @package Admin
 * @subpackage Client
 */
class KalturaSystemPartnerService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	function get($partnerId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "partnerId", $partnerId);
		$this->client->queueServiceActionCall("systempartner_systempartner", "get", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaPartner");
		return $resultObject;
	}

	function getUsage(KalturaPartnerFilter $partnerFilter = null, KalturaSystemPartnerUsageFilter $usageFilter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($partnerFilter !== null)
			$this->client->addParam($kparams, "partnerFilter", $partnerFilter->toParams());
		if ($usageFilter !== null)
			$this->client->addParam($kparams, "usageFilter", $usageFilter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("systempartner_systempartner", "getUsage", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaSystemPartnerUsageListResponse");
		return $resultObject;
	}

	function listAction(KalturaPartnerFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("systempartner_systempartner", "list", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaPartnerListResponse");
		return $resultObject;
	}

	function updateStatus($partnerId, $status)
	{
		$kparams = array();
		$this->client->addParam($kparams, "partnerId", $partnerId);
		$this->client->addParam($kparams, "status", $status);
		$this->client->queueServiceActionCall("systempartner_systempartner", "updateStatus", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	function getAdminSession($partnerId, $userId = "")
	{
		$kparams = array();
		$this->client->addParam($kparams, "partnerId", $partnerId);
		$this->client->addParam($kparams, "userId", $userId);
		$this->client->queueServiceActionCall("systempartner_systempartner", "getAdminSession", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "string");
		return $resultObject;
	}

	function updateConfiguration($partnerId, KalturaSystemPartnerConfiguration $configuration)
	{
		$kparams = array();
		$this->client->addParam($kparams, "partnerId", $partnerId);
		$this->client->addParam($kparams, "configuration", $configuration->toParams());
		$this->client->queueServiceActionCall("systempartner_systempartner", "updateConfiguration", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	function getConfiguration($partnerId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "partnerId", $partnerId);
		$this->client->queueServiceActionCall("systempartner_systempartner", "getConfiguration", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaSystemPartnerConfiguration");
		return $resultObject;
	}

	function getPackages()
	{
		$kparams = array();
		$this->client->queueServiceActionCall("systempartner_systempartner", "getPackages", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}
}
/**
 * @package Admin
 * @subpackage Client
 */
class KalturaSystemPartnerClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaSystemPartnerClientPlugin
	 */
	protected static $instance;

	/**
	 * @var KalturaSystemPartnerService
	 */
	public $systemPartner = null;

	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
		$this->systemPartner = new KalturaSystemPartnerService($client);
	}

	/**
	 * @return KalturaSystemPartnerClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		if(!self::$instance)
			self::$instance = new KalturaSystemPartnerClientPlugin($client);
		return self::$instance;
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'systemPartner' => $this->systemPartner,
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'systemPartner';
	}
}

