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
class KalturaDistributionProviderType
{
	const GENERIC = "1";
	const SYNDICATION = "2";
	const MSN = "msnDistribution.MSN";
	const HULU = "huluDistribution.HULU";
	const COMCAST = "comcastDistribution.COMCAST";
	const YOUTUBE = "youTubeDistribution.YOUTUBE";
	const VERIZON = "verizonDistribution.VERIZON";
}


/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaContentDistributionBatchService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	function getExclusiveDistributionSubmitJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "maxExecutionTime", $maxExecutionTime);
		$this->client->addParam($kparams, "numberOfJobs", $numberOfJobs);
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "getExclusiveDistributionSubmitJobs", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	function updateExclusiveDistributionSubmitJob($id, KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "job", $job->toParams());
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "updateExclusiveDistributionSubmitJob", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBatchJob");
		return $resultObject;
	}

	function freeExclusiveDistributionSubmitJob($id, KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "resetExecutionAttempts", $resetExecutionAttempts);
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "freeExclusiveDistributionSubmitJob", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFreeJobResponse");
		return $resultObject;
	}

	function getExclusiveAlmostDoneDistributionSubmitJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "maxExecutionTime", $maxExecutionTime);
		$this->client->addParam($kparams, "numberOfJobs", $numberOfJobs);
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "getExclusiveAlmostDoneDistributionSubmitJobs", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	function getExclusiveDistributionUpdateJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "maxExecutionTime", $maxExecutionTime);
		$this->client->addParam($kparams, "numberOfJobs", $numberOfJobs);
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "getExclusiveDistributionUpdateJobs", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	function updateExclusiveDistributionUpdateJob($id, KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "job", $job->toParams());
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "updateExclusiveDistributionUpdateJob", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBatchJob");
		return $resultObject;
	}

	function freeExclusiveDistributionUpdateJob($id, KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "resetExecutionAttempts", $resetExecutionAttempts);
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "freeExclusiveDistributionUpdateJob", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFreeJobResponse");
		return $resultObject;
	}

	function getExclusiveAlmostDoneDistributionUpdateJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "maxExecutionTime", $maxExecutionTime);
		$this->client->addParam($kparams, "numberOfJobs", $numberOfJobs);
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "getExclusiveAlmostDoneDistributionUpdateJobs", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	function getExclusiveDistributionDeleteJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "maxExecutionTime", $maxExecutionTime);
		$this->client->addParam($kparams, "numberOfJobs", $numberOfJobs);
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "getExclusiveDistributionDeleteJobs", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	function updateExclusiveDistributionDeleteJob($id, KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "job", $job->toParams());
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "updateExclusiveDistributionDeleteJob", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBatchJob");
		return $resultObject;
	}

	function freeExclusiveDistributionDeleteJob($id, KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "resetExecutionAttempts", $resetExecutionAttempts);
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "freeExclusiveDistributionDeleteJob", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFreeJobResponse");
		return $resultObject;
	}

	function getExclusiveAlmostDoneDistributionDeleteJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "maxExecutionTime", $maxExecutionTime);
		$this->client->addParam($kparams, "numberOfJobs", $numberOfJobs);
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "getExclusiveAlmostDoneDistributionDeleteJobs", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	function getExclusiveDistributionFetchReportJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "maxExecutionTime", $maxExecutionTime);
		$this->client->addParam($kparams, "numberOfJobs", $numberOfJobs);
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "getExclusiveDistributionFetchReportJobs", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	function updateExclusiveDistributionFetchReportJob($id, KalturaExclusiveLockKey $lockKey, KalturaBatchJob $job)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "job", $job->toParams());
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "updateExclusiveDistributionFetchReportJob", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBatchJob");
		return $resultObject;
	}

	function freeExclusiveDistributionFetchReportJob($id, KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "resetExecutionAttempts", $resetExecutionAttempts);
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "freeExclusiveDistributionFetchReportJob", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFreeJobResponse");
		return $resultObject;
	}

	function getExclusiveAlmostDoneDistributionFetchReportJobs(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "lockKey", $lockKey->toParams());
		$this->client->addParam($kparams, "maxExecutionTime", $maxExecutionTime);
		$this->client->addParam($kparams, "numberOfJobs", $numberOfJobs);
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "getExclusiveAlmostDoneDistributionFetchReportJobs", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	function updateSunStatus()
	{
		$kparams = array();
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "updateSunStatus", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	function createRequiredJobs()
	{
		$kparams = array();
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "createRequiredJobs", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	function getAssetUrl($assetId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "assetId", $assetId);
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "getAssetUrl", $kparams);
		if ($this->client->isMultiRequest())
			return null;
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "string");
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "getExclusiveImportJobs", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "updateExclusiveImportJob", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "freeExclusiveImportJob", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "getExclusiveBulkUploadJobs", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "getExclusiveAlmostDoneBulkUploadJobs", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "updateExclusiveBulkUploadJob", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "freeExclusiveBulkUploadJob", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "addBulkUploadResult", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "getBulkUploadLastResult", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "updateBulkUploadResults", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "getExclusiveAlmostDoneConvertCollectionJobs", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "getExclusiveAlmostDoneConvertProfileJobs", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "updateExclusiveConvertCollectionJob", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "updateExclusiveConvertProfileJob", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "freeExclusiveConvertCollectionJob", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "freeExclusiveConvertProfileJob", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "getExclusiveConvertCollectionJobs", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "getExclusiveConvertJobs", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "getExclusiveAlmostDoneConvertJobs", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "updateExclusiveConvertJob", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "updateExclusiveConvertJobSubType", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "freeExclusiveConvertJob", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "getExclusivePostConvertJobs", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "updateExclusivePostConvertJob", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "freeExclusivePostConvertJob", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "getExclusiveCaptureThumbJobs", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "updateExclusiveCaptureThumbJob", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "freeExclusiveCaptureThumbJob", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "getExclusiveExtractMediaJobs", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "updateExclusiveExtractMediaJob", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "addMediaInfo", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "freeExclusiveExtractMediaJob", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "getExclusiveStorageExportJobs", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "updateExclusiveStorageExportJob", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "freeExclusiveStorageExportJob", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "getExclusiveStorageDeleteJobs", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "updateExclusiveStorageDeleteJob", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "freeExclusiveStorageDeleteJob", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "getExclusiveNotificationJobs", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "updateExclusiveNotificationJob", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "freeExclusiveNotificationJob", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "getExclusiveMailJobs", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "updateExclusiveMailJob", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "freeExclusiveMailJob", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "getExclusiveBulkDownloadJobs", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "getExclusiveAlmostDoneBulkDownloadJobs", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "updateExclusiveBulkDownloadJob", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "freeExclusiveBulkDownloadJob", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "getExclusiveProvisionProvideJobs", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "getExclusiveAlmostDoneProvisionProvideJobs", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "updateExclusiveProvisionProvideJob", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "freeExclusiveProvisionProvideJob", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "getExclusiveProvisionDeleteJobs", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "getExclusiveAlmostDoneProvisionDeleteJobs", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "updateExclusiveProvisionDeleteJob", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "freeExclusiveProvisionDeleteJob", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "resetJobExecutionAttempts", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "freeExclusiveJob", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "getQueueSize", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "getExclusiveJobs", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "getExclusiveAlmostDone", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "updateExclusiveJob", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "cleanExclusiveJobs", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "logConversion", $kparams);
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
		$this->client->queueServiceActionCall("contentdistribution_contentdistributionbatch", "checkFileExists", $kparams);
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
class KalturaContentDistributionClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaContentDistributionClientPlugin
	 */
	protected static $instance;

	/**
	 * @var KalturaContentDistributionBatchService
	 */
	public $contentDistributionBatch = null;

	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
		$this->contentDistributionBatch = new KalturaContentDistributionBatchService($client);
	}

	/**
	 * @return KalturaContentDistributionClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		if(!self::$instance)
			self::$instance = new KalturaContentDistributionClientPlugin($client);
		return self::$instance;
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'contentDistributionBatch' => $this->contentDistributionBatch,
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'contentDistribution';
	}
}

