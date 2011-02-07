<?php
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
class KalturaVirusFoundAction
{
	const NONE = 0;
	const DELETE = 1;
	const CLEAN_NONE = 2;
	const CLEAN_DELETE = 3;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaVirusScanEngineType
{
	const SYMANTEC_SCAN_ENGINE = "symantecScanEngine.SymantecScanEngine";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaVirusScanJobResult
{
	const SCAN_ERROR = 1;
	const FILE_IS_CLEAN = 2;
	const FILE_WAS_CLEANED = 3;
	const FILE_INFECTED = 4;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaVirusScanJobData extends KalturaJobData
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $srcFilePath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $flavorAssetId = null;

	/**
	 * 
	 *
	 * @var KalturaVirusScanJobResult
	 */
	public $scanResult = null;

	/**
	 * 
	 *
	 * @var KalturaVirusFoundAction
	 */
	public $virusFoundAction = null;


}


/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaVirusScanBatchService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	function getExclusiveVirusScanJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "maxExecutionTime", $maxExecutionTime);
		$this->client->addParam($kparams, "numberOfJobs", $numberOfJobs);
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "getExclusiveVirusScanJobs", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	function updateExclusiveVirusScanJob($id, KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "job", $job->toParams());
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "updateExclusiveVirusScanJob", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBatchJob");
		return $resultObject;
	}

	function freeExclusiveVirusScanJob($id, KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "resetExecutionAttempts", $resetExecutionAttempts);
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "freeExclusiveVirusScanJob", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFreeJobResponse");
		return $resultObject;
	}

	function getExclusiveImportJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "maxExecutionTime", $maxExecutionTime);
		$this->client->addParam($kparams, "numberOfJobs", $numberOfJobs);
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "getExclusiveImportJobs", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	function updateExclusiveImportJob($id, KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "job", $job->toParams());
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "updateExclusiveImportJob", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBatchJob");
		return $resultObject;
	}

	function freeExclusiveImportJob($id, KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "resetExecutionAttempts", $resetExecutionAttempts);
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "freeExclusiveImportJob", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFreeJobResponse");
		return $resultObject;
	}

	function getExclusiveBulkUploadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "maxExecutionTime", $maxExecutionTime);
		$this->client->addParam($kparams, "numberOfJobs", $numberOfJobs);
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "getExclusiveBulkUploadJobs", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	function getExclusiveAlmostDoneBulkUploadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "maxExecutionTime", $maxExecutionTime);
		$this->client->addParam($kparams, "numberOfJobs", $numberOfJobs);
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "getExclusiveAlmostDoneBulkUploadJobs", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	function updateExclusiveBulkUploadJob($id, KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "job", $job->toParams());
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "updateExclusiveBulkUploadJob", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBatchJob");
		return $resultObject;
	}

	function freeExclusiveBulkUploadJob($id, KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "resetExecutionAttempts", $resetExecutionAttempts);
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "freeExclusiveBulkUploadJob", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFreeJobResponse");
		return $resultObject;
	}

	function addBulkUploadResult(KalturaBulkUploadResult $bulkUploadResult, array $pluginDataArray = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "bulkUploadResult", $bulkUploadResult->toParams());
		if ($pluginDataArray !== null)
			foreach($pluginDataArray as $index => $obj)
			{
				$this->client->addParam($kparams, "pluginDataArray:$index", $obj->toParams());
			}
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "addBulkUploadResult", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBulkUploadResult");
		return $resultObject;
	}

	function getBulkUploadLastResult($bulkUploadJobId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "bulkUploadJobId", $bulkUploadJobId);
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "getBulkUploadLastResult", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBulkUploadResult");
		return $resultObject;
	}

	function updateBulkUploadResults($bulkUploadJobId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "bulkUploadJobId", $bulkUploadJobId);
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "updateBulkUploadResults", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "integer");
		return $resultObject;
	}

	function getExclusiveAlmostDoneConvertCollectionJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "maxExecutionTime", $maxExecutionTime);
		$this->client->addParam($kparams, "numberOfJobs", $numberOfJobs);
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "getExclusiveAlmostDoneConvertCollectionJobs", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	function getExclusiveAlmostDoneConvertProfileJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "maxExecutionTime", $maxExecutionTime);
		$this->client->addParam($kparams, "numberOfJobs", $numberOfJobs);
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "getExclusiveAlmostDoneConvertProfileJobs", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	function updateExclusiveConvertCollectionJob($id, KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job, array $flavorsData = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "job", $job->toParams());
		if ($flavorsData !== null)
			foreach($flavorsData as $index => $obj)
			{
				$this->client->addParam($kparams, "flavorsData:$index", $obj->toParams());
			}
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "updateExclusiveConvertCollectionJob", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBatchJob");
		return $resultObject;
	}

	function updateExclusiveConvertProfileJob($id, KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "job", $job->toParams());
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "updateExclusiveConvertProfileJob", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBatchJob");
		return $resultObject;
	}

	function freeExclusiveConvertCollectionJob($id, KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "resetExecutionAttempts", $resetExecutionAttempts);
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "freeExclusiveConvertCollectionJob", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFreeJobResponse");
		return $resultObject;
	}

	function freeExclusiveConvertProfileJob($id, KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "resetExecutionAttempts", $resetExecutionAttempts);
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "freeExclusiveConvertProfileJob", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFreeJobResponse");
		return $resultObject;
	}

	function getExclusiveConvertCollectionJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "maxExecutionTime", $maxExecutionTime);
		$this->client->addParam($kparams, "numberOfJobs", $numberOfJobs);
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "getExclusiveConvertCollectionJobs", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	function getExclusiveConvertJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "maxExecutionTime", $maxExecutionTime);
		$this->client->addParam($kparams, "numberOfJobs", $numberOfJobs);
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "getExclusiveConvertJobs", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	function getExclusiveAlmostDoneConvertJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "maxExecutionTime", $maxExecutionTime);
		$this->client->addParam($kparams, "numberOfJobs", $numberOfJobs);
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "getExclusiveAlmostDoneConvertJobs", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	function updateExclusiveConvertJob($id, KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "job", $job->toParams());
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "updateExclusiveConvertJob", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBatchJob");
		return $resultObject;
	}

	function updateExclusiveConvertJobSubType($id, KalturaExclusiveLockKey $lockKey, $subType)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "subType", $subType);
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "updateExclusiveConvertJobSubType", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBatchJob");
		return $resultObject;
	}

	function freeExclusiveConvertJob($id, KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "resetExecutionAttempts", $resetExecutionAttempts);
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "freeExclusiveConvertJob", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFreeJobResponse");
		return $resultObject;
	}

	function getExclusivePostConvertJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "maxExecutionTime", $maxExecutionTime);
		$this->client->addParam($kparams, "numberOfJobs", $numberOfJobs);
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "getExclusivePostConvertJobs", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	function updateExclusivePostConvertJob($id, KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "job", $job->toParams());
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "updateExclusivePostConvertJob", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBatchJob");
		return $resultObject;
	}

	function freeExclusivePostConvertJob($id, KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "resetExecutionAttempts", $resetExecutionAttempts);
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "freeExclusivePostConvertJob", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFreeJobResponse");
		return $resultObject;
	}

	function getExclusiveCaptureThumbJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "maxExecutionTime", $maxExecutionTime);
		$this->client->addParam($kparams, "numberOfJobs", $numberOfJobs);
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "getExclusiveCaptureThumbJobs", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	function updateExclusiveCaptureThumbJob($id, KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "job", $job->toParams());
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "updateExclusiveCaptureThumbJob", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBatchJob");
		return $resultObject;
	}

	function freeExclusiveCaptureThumbJob($id, KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "resetExecutionAttempts", $resetExecutionAttempts);
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "freeExclusiveCaptureThumbJob", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFreeJobResponse");
		return $resultObject;
	}

	function getExclusiveExtractMediaJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "maxExecutionTime", $maxExecutionTime);
		$this->client->addParam($kparams, "numberOfJobs", $numberOfJobs);
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "getExclusiveExtractMediaJobs", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	function updateExclusiveExtractMediaJob($id, KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "job", $job->toParams());
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "updateExclusiveExtractMediaJob", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBatchJob");
		return $resultObject;
	}

	function addMediaInfo(KalturaMediaInfo $mediaInfo)
	{
		$kparams = array();
		$this->client->addParam($kparams, "mediaInfo", $mediaInfo->toParams());
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "addMediaInfo", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMediaInfo");
		return $resultObject;
	}

	function freeExclusiveExtractMediaJob($id, KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "resetExecutionAttempts", $resetExecutionAttempts);
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "freeExclusiveExtractMediaJob", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFreeJobResponse");
		return $resultObject;
	}

	function getExclusiveStorageExportJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "maxExecutionTime", $maxExecutionTime);
		$this->client->addParam($kparams, "numberOfJobs", $numberOfJobs);
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "getExclusiveStorageExportJobs", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	function updateExclusiveStorageExportJob($id, KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "job", $job->toParams());
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "updateExclusiveStorageExportJob", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBatchJob");
		return $resultObject;
	}

	function freeExclusiveStorageExportJob($id, KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "resetExecutionAttempts", $resetExecutionAttempts);
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "freeExclusiveStorageExportJob", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFreeJobResponse");
		return $resultObject;
	}

	function getExclusiveStorageDeleteJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "maxExecutionTime", $maxExecutionTime);
		$this->client->addParam($kparams, "numberOfJobs", $numberOfJobs);
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "getExclusiveStorageDeleteJobs", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	function updateExclusiveStorageDeleteJob($id, KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "job", $job->toParams());
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "updateExclusiveStorageDeleteJob", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBatchJob");
		return $resultObject;
	}

	function freeExclusiveStorageDeleteJob($id, KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "resetExecutionAttempts", $resetExecutionAttempts);
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "freeExclusiveStorageDeleteJob", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFreeJobResponse");
		return $resultObject;
	}

	function getExclusiveNotificationJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "maxExecutionTime", $maxExecutionTime);
		$this->client->addParam($kparams, "numberOfJobs", $numberOfJobs);
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "getExclusiveNotificationJobs", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBatchGetExclusiveNotificationJobsResponse");
		return $resultObject;
	}

	function updateExclusiveNotificationJob($id, KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "job", $job->toParams());
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "updateExclusiveNotificationJob", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBatchJob");
		return $resultObject;
	}

	function freeExclusiveNotificationJob($id, KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "resetExecutionAttempts", $resetExecutionAttempts);
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "freeExclusiveNotificationJob", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFreeJobResponse");
		return $resultObject;
	}

	function getExclusiveMailJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "maxExecutionTime", $maxExecutionTime);
		$this->client->addParam($kparams, "numberOfJobs", $numberOfJobs);
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "getExclusiveMailJobs", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	function updateExclusiveMailJob($id, KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "job", $job->toParams());
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "updateExclusiveMailJob", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBatchJob");
		return $resultObject;
	}

	function freeExclusiveMailJob($id, KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "resetExecutionAttempts", $resetExecutionAttempts);
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "freeExclusiveMailJob", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFreeJobResponse");
		return $resultObject;
	}

	function getExclusiveBulkDownloadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "maxExecutionTime", $maxExecutionTime);
		$this->client->addParam($kparams, "numberOfJobs", $numberOfJobs);
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "getExclusiveBulkDownloadJobs", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	function getExclusiveAlmostDoneBulkDownloadJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "maxExecutionTime", $maxExecutionTime);
		$this->client->addParam($kparams, "numberOfJobs", $numberOfJobs);
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "getExclusiveAlmostDoneBulkDownloadJobs", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	function updateExclusiveBulkDownloadJob($id, KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "job", $job->toParams());
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "updateExclusiveBulkDownloadJob", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBatchJob");
		return $resultObject;
	}

	function freeExclusiveBulkDownloadJob($id, KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "resetExecutionAttempts", $resetExecutionAttempts);
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "freeExclusiveBulkDownloadJob", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFreeJobResponse");
		return $resultObject;
	}

	function getExclusiveProvisionProvideJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "maxExecutionTime", $maxExecutionTime);
		$this->client->addParam($kparams, "numberOfJobs", $numberOfJobs);
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "getExclusiveProvisionProvideJobs", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	function getExclusiveAlmostDoneProvisionProvideJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "maxExecutionTime", $maxExecutionTime);
		$this->client->addParam($kparams, "numberOfJobs", $numberOfJobs);
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "getExclusiveAlmostDoneProvisionProvideJobs", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	function updateExclusiveProvisionProvideJob($id, KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "job", $job->toParams());
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "updateExclusiveProvisionProvideJob", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBatchJob");
		return $resultObject;
	}

	function freeExclusiveProvisionProvideJob($id, KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "resetExecutionAttempts", $resetExecutionAttempts);
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "freeExclusiveProvisionProvideJob", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFreeJobResponse");
		return $resultObject;
	}

	function getExclusiveProvisionDeleteJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "maxExecutionTime", $maxExecutionTime);
		$this->client->addParam($kparams, "numberOfJobs", $numberOfJobs);
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "getExclusiveProvisionDeleteJobs", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	function getExclusiveAlmostDoneProvisionDeleteJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "maxExecutionTime", $maxExecutionTime);
		$this->client->addParam($kparams, "numberOfJobs", $numberOfJobs);
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "getExclusiveAlmostDoneProvisionDeleteJobs", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	function updateExclusiveProvisionDeleteJob($id, KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "job", $job->toParams());
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "updateExclusiveProvisionDeleteJob", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBatchJob");
		return $resultObject;
	}

	function freeExclusiveProvisionDeleteJob($id, KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "resetExecutionAttempts", $resetExecutionAttempts);
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "freeExclusiveProvisionDeleteJob", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFreeJobResponse");
		return $resultObject;
	}

	function resetJobExecutionAttempts($id, KalturaExclusiveLockKey $lockKey, $jobType)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "jobType", $jobType);
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "resetJobExecutionAttempts", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	function freeExclusiveJob($id, KalturaExclusiveLockKey $lockKey, $jobType, $resetExecutionAttempts = false)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "jobType", $jobType);
		$this->client->addParam($kparams, "resetExecutionAttempts", $resetExecutionAttempts);
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "freeExclusiveJob", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFreeJobResponse");
		return $resultObject;
	}

	function getQueueSize(KalturaWorkerQueueFilter $workerQueueFilter)
	{
		$kparams = array();
		$this->client->addParam($kparams, "workerQueueFilter", $workerQueueFilter->toParams());
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "getQueueSize", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "integer");
		return $resultObject;
	}

	function getExclusiveJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, $jobType = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "maxExecutionTime", $maxExecutionTime);
		$this->client->addParam($kparams, "numberOfJobs", $numberOfJobs);
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		$this->client->addParam($kparams, "jobType", $jobType);
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "getExclusiveJobs", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	function getExclusiveAlmostDone(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, $jobType = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "maxExecutionTime", $maxExecutionTime);
		$this->client->addParam($kparams, "numberOfJobs", $numberOfJobs);
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		$this->client->addParam($kparams, "jobType", $jobType);
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "getExclusiveAlmostDone", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	function updateExclusiveJob($id, KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "job", $job->toParams());
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "updateExclusiveJob", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBatchJob");
		return $resultObject;
	}

	function cleanExclusiveJobs()
	{
		$kparams = array();
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "cleanExclusiveJobs", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "integer");
		return $resultObject;
	}

	function logConversion($flavorAssetId, $data)
	{
		$kparams = array();
		$this->client->addParam($kparams, "flavorAssetId", $flavorAssetId);
		$this->client->addParam($kparams, "data", $data);
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "logConversion", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	function checkFileExists($localPath, $size)
	{
		$kparams = array();
		$this->client->addParam($kparams, "localPath", $localPath);
		$this->client->addParam($kparams, "size", $size);
		$this->client->queueServiceActionCall("virusscan_virusscanbatch", "checkFileExists", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFileExistsResponse");
		return $resultObject;
	}
}
/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaVirusScanClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaClientPlugin
	 */
	protected static $instance;

	/**
	 * @var KalturaVirusScanBatchService
	 */
	public $virusScanBatch = null;

	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
		$this->virusScanBatch = new KalturaVirusScanBatchService($client);
	}

	/**
	 * @return KalturaClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		if(!self::$instance)
			self::$instance = new KalturaVirusScanClientPlugin($client);
		return self::$instance;
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'virusScanBatch' => $this->virusScanBatch,
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'virusScan';
	}
}

