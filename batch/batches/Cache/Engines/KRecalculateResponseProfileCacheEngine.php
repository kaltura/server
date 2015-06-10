<?php
/**
 * @package Scheduler
 * @subpackage RecalculateCache
 */
class KRecalculateResponseProfileCacheEngine extends KRecalculateCacheEngine
{
	protected $maxCacheObjectsPerRequest = 10;
	
	public function __construct()
	{
		if(KBatchBase::$taskConfig->params->maxCacheObjectsPerRequest)
			$this->maxCacheObjectsPerRequest = intval(KBatchBase::$taskConfig->params->maxCacheObjectsPerRequest);
	}
	
	/* (non-PHPdoc)
	 * @see KRecalculateCacheEngine::recalculate()
	 */
	public function recalculate(KalturaRecalculateCacheJobData $data)
	{
		return $this->doRecalculate($data);
	}
	
	public function doRecalculate(KalturaRecalculateResponseProfileCacheJobData $data)
	{
		$job = KJobHandlerWorker::getCurrentJob();
		KBatchBase::impersonate($job->partnerId);
		$partner = KBatchBase::$kClient->partner->get($job->partnerId);
		KBatchBase::unimpersonate();
		
		$role = reset($data->userRoles);
		/* @var $role KalturaIntegerValue */
		$privileges = array(
			'setrole:' . $role->value,
			'disableentitlement',
		);
		$privileges = implode(',', $privileges);
		
		$client = new KalturaClient(KBatchBase::$kClientConfig);
		$ks = $client->generateSession($partner->adminSecret, 'batchUser', $data->ksType, $job->partnerId, 86400, $privileges);
		$client->setKs($ks);
		
		$options = new KalturaResponseProfileCacheRecalculateOptions();
		$options->limit = $this->maxCacheObjectsPerRequest;
		$options->cachedObjectType = $data->cachedObjectType;
		$options->objectId = $data->objectId;
		$options->startDocId = $data->startDocId;
		$options->endDocId = $data->endDocId;
		
		do
		{
			$results = $client->responseProfile->recalculate($options);
			$options->startDocId = $results->lastKeyId;
		} while($results->recalculated == $options->limit);
	}
}
