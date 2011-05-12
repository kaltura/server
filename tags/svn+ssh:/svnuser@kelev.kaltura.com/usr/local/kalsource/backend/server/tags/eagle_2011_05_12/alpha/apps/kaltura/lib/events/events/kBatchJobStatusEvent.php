<?php
/**
 * Applicative event that raised implicitly by the developer
 */
class kBatchJobStatusEvent extends KalturaEvent implements IKalturaContinualEvent
{
	const EVENT_CONSUMER = 'kBatchJobStatusEventConsumer';
	
	/**
	 * @var BatchJob
	 */
	private $dbBatchJob;
	
	/**
	 * @var BatchJob
	 */
	private $twinJob = null;
	
	/**
	 * @param BatchJob $dbBatchJob
	 * @param BatchJob $twinJob
	 */
	public function __construct(BatchJob $dbBatchJob, BatchJob $twinJob = null)
	{
		$this->dbBatchJob = $dbBatchJob;
		$this->twinJob = $twinJob;
		
		KalturaLog::debug("Event [" . get_class($this) . "] job id [" . $dbBatchJob->getId() . "] type [" . $dbBatchJob->getJobType() . "] sub type [" . $dbBatchJob->getJobSubType() . "]");
	}
	
	public function getConsumerInterface()
	{
		return self::EVENT_CONSUMER;
	}
	
	/**
	 * @param kBatchJobStatusEventConsumer $consumer
	 * @return bool true if should continue to the next consumer
	 */
	protected function doConsume(KalturaEventConsumer $consumer)
	{
		KalturaLog::debug(get_class($this) . " event consumed by " . get_class($consumer) . " job id [" . $this->dbBatchJob->getId() . "] type [" . $this->dbBatchJob->getJobType() . "] sub type [" . $this->dbBatchJob->getJobSubType() . "]");
		return $consumer->updatedJob($this->dbBatchJob, $this->twinJob);
	}

}