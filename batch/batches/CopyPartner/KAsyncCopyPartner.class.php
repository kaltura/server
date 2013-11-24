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
		$retVal = $this;

		try
		{
			$retVal = $this->doCopyPartner($job, $job->data);
		}
		catch ( Exception $e )
		{
			self::unimpersonate(); // Make sure we're not impresonating anymore
			throw $e;
		}
		
		$this->log("KAsyncCopyPartner done.");
		
		return $retVal;
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 * @return KalturaBatchJob
	 */
	protected function doCopyPartner(KalturaBatchJob $job, KalturaCopyPartnerJobData $jobData)
	{
		$this->fromPartnerId = $jobData->fromPartnerId;
		$this->toPartnerId = $jobData->toPartnerId;
		$this->log( "CopyPartner job id [$job->id], From PID: $jobData->fromPartnerId, To PID: $jobData->toPartnerId" );

		try
		{
			// copy permssions before trying to copy additional objects such as distribution profiles which are not enabled yet for the partner
	 		$this->copyAllEntries();
		}
		catch ( Exception $e )
		{
			self::unimpersonate(); // Make sure we're not impresonating anymore
			throw $e;
		}
		
 		$res = $this->closeJob($job, null, null, "CopyPartner finished", KalturaBatchJobStatus::FINISHED);
		
 		return $res;
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
		$pageFilter->pageIndex = 0;
		
		$totalReceivedObjectsCount = 0;		
		
		/* @var $this->getClient() KalturaClient */
		do
		{
			// Get the source partner's entries list
			self::impersonate( $this->fromPartnerId );
			$entriesList = $this->getClient()->baseEntry->listAction( $entryFilter, $pageFilter );

			$totalCount = $entriesList->totalCount;
			$receivedObjectsCount = count($entriesList->objects);
			$totalReceivedObjectsCount += $receivedObjectsCount; 
			$pageFilter->pageIndex++;
			
			$this->log( "Got $receivedObjectsCount entry object(s) [= $totalReceivedObjectsCount/$totalCount]" );
			
			if ( $receivedObjectsCount > 0 )
			{
				// Write the source partner's entries to the destination partner 
				self::impersonate( $this->toPartnerId );
				foreach ( $entriesList->objects as $entry )
				{
					$newEntry = $this->getClient()->baseEntry->cloneAction( $entry->id /* anything else? */ );
				}
			}			
		} while ( $totalReceivedObjectsCount < $totalCount );
	
		self::unimpersonate();
	}	
}
