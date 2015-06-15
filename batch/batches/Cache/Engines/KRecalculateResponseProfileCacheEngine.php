<?php
/**
 * @package Scheduler
 * @subpackage RecalculateCache
 */
class KRecalculateResponseProfileCacheEngine extends KRecalculateCacheEngine
{
	const RESPONSE_PROFILE_CACHE_ALREADY_RECALCULATED = 'RESPONSE_PROFILE_CACHE_ALREADY_RECALCULATED';
	const RESPONSE_PROFILE_CACHE_RECALCULATE_RESTARTED = 'RESPONSE_PROFILE_CACHE_RECALCULATE_RESTARTED';
	
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
		$options->jobCreatedAt = $job->createdAt;
		
		$recalculated = 0;
		try 
		{
			do
			{
				$results = $client->responseProfile->recalculate($options);
				$recalculated += $results->recalculated;
				$options->startDocId = $results->lastKeyId;
			} while($results->recalculated == $options->limit);
		}
		catch(KalturaException $e)
		{
			if($e->getCode() != self::RESPONSE_PROFILE_CACHE_ALREADY_RECALCULATED && $e->getCode() != self::RESPONSE_PROFILE_CACHE_RECALCULATE_RESTARTED)
				throw $e;
			
			KalturaLog::err($e);
		}
		
		return $recalculated;
	}
}
