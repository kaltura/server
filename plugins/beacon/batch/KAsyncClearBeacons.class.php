<?php

/**
 * @package plugins.beacons
 * @subpackage Scheduler
 */

class KAsyncClearBeacons extends KJobHandlerWorker
{
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::CLEAR_BEACONS;
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		KalturaLog::debug("Testing:: job = " . print_r($job, true));
		
		try
		{
			$this->clearBeacons($job, $job->data);
		}
		catch(Exception $ex)
		{
			self::unimpersonate();
			$job = $this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $ex->getCode(), "Error: " . $ex->getMessage(), KalturaBatchJobStatus::FAILED);
		}
		
		return $job;
	}
	
	private function clearBeacons(KalturaBatchJob $job, KalturaClearBeaconsJobData $data)
	{
		$this->updateJob($job, "Start handling beacons clear for object id [{$data->objectId} related object type [{$data->relatedObjectType}]", KalturaBatchJobStatus::PROCESSING);
		
		$beaconsFilter = new KalturaBeaconFilter();
		$beaconsFilter->objectIdIn = $data->objectId;
		$beaconsFilter->relatedObjectTypeIn = $data->relatedObjectType;
		$beaconsFilter->orderBy = KalturaBeaconOrderBy::CREATED_AT_ASC;
		
		$pager = new KalturaFilterPager();
		$pager->pageSize = 100;
		$pager->pageIndex = 1;
		
		$beaconPlugin = KalturaBeaconClientPlugin::get(self::$kClient);
		$this->impersonate($job->partnerId);
		$beaconsListResponse = $beaconPlugin->beacon->listAction($beaconsFilter, $pager);
		
		while(count($beaconsListResponse->objects))
		{
			foreach($beaconsListResponse->objects as $beacon)
			{
				$beaconPlugin->beacon->delete($beacon->id, $beacon->indexType);
			}
			
			$pager->pageIndex++;
			$beaconsListResponse = $beaconPlugin->beacon->listAction($beaconsFilter, $pager);
		}
		$this->unimpersonate();
		
		return $this->closeJob($job, null, null, "Beacons Cleared for object id [{$data->objectId}] related object type [{$data->relatedObjectType}]", KalturaBatchJobStatus::FINISHED, $data);
	}
}
