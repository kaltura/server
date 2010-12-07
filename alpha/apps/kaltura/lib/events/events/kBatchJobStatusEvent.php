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
		return $consumer->updatedJob($this->dbBatchJob, $this->twinJob);
	}

}