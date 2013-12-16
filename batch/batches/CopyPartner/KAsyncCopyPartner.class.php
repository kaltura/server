<?php
/**
 * Copy an entire partner to and new one
 *
 * @package Scheduler
 * @subpackage CopyPartner
 */
class KAsyncCopyPartner extends KJobHandlerWorker
{
	protected $fromPartnerId;
	protected $toPartnerId;
	
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::COPY_PARTNER;
	}
	
	/* (non-PHPdoc)
	 * @see KBatchBase::getJobType()
	 */
	public function getJobType()
	{
		return self::getType();
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 * @return KalturaBatchJob
	 */
	protected function exec(KalturaBatchJob $job)
	{
		return $this->doCopyPartner($job, $job->data);
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 * @return KalturaBatchJob
	 */
	protected function doCopyPartner(KalturaBatchJob $job, KalturaCopyPartnerJobData $jobData)
	{
		$this->log( "doCopyPartner job id [$job->id], From PID: $jobData->fromPartnerId, To PID: $jobData->toPartnerId" );

		$this->fromPartnerId = $jobData->fromPartnerId;
		$this->toPartnerId = $jobData->toPartnerId;
		
		// copy permssions before trying to copy additional objects such as distribution profiles which are not enabled yet for the partner
 		$this->copyAllEntries();
		
 		return $this->closeJob($job, null, null, "doCopyPartner finished", KalturaBatchJobStatus::FINISHED);
	}
	
	/**
	 * copyAllEntries()
	 */
	protected function copyAllEntries()
	{
		$entryFilter = new KalturaBaseEntryFilter();
 		$entryFilter->order = KalturaBaseEntryOrderBy::CREATED_AT_ASC;
		
		$pageFilter = new KalturaFilterPager();
		$pageFilter->pageSize = 50;
		$pageFilter->pageIndex = 1;
		
		/* @var $this->getClient() KalturaClient */
		do
		{
			// Get the source partner's entries list
			self::impersonate( $this->fromPartnerId );
			$entriesList = $this->getClient()->baseEntry->listAction( $entryFilter, $pageFilter );

			$receivedObjectsCount = count($entriesList->objects);
			$pageFilter->pageIndex++;
			
			if ( $receivedObjectsCount > 0 )
			{
				// Write the source partner's entries to the destination partner 
				self::impersonate( $this->toPartnerId );
				foreach ( $entriesList->objects as $entry )
				{
					$newEntry = $this->getClient()->baseEntry->cloneAction( $entry->id );
				}
			}			
		} while ( $receivedObjectsCount );
	
		self::unimpersonate();
	}	
}
