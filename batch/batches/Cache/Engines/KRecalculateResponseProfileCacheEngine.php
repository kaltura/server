<?php
/**
 * @package Scheduler
 * @subpackage RecalculateCache
 */
class KRecalculateResponseProfileCacheEngine extends KRecalculateCacheEngine
{
	/* (non-PHPdoc)
	 * @see KRecalculateCacheEngine::recalculate()
	 */
	public function recalculate(KalturaRecalculateCacheJobData $data)
	{
		return $this->doRecalculate($data);
	}
	
	public function doRecalculate(KalturaRecalculateResponseProfileCacheJobData $data)
	{
// 		KBatchBase::$kClient->responseProfile->recalculate();
		
		$options = new KalturaResponseProfileCacheRecalculateOptions();
		$options->limit;
		$options->cachedObjectType;
		$options->objectId;
		$options->startDocId;
		$options->endDocId;
	}
}
