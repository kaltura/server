<?php

class kBatchJobStatusEvent extends KalturaEvent
{
	const EVENT_CONSUMER = 'kBatchJobStatusEventConsumer';
	
	/**
	 * @var BatchJob
	 */
	private $dbBatchJob;
	
	/**
	 * @var int
	 */
	private $entryStatus;
	
	/**
	 * @var BatchJob
	 */
	private $twinJob = null;
	
	/**
	 * @param BatchJob $dbBatchJob
	 * @param unknown_type $entryStatus
	 * @param BatchJob $twinJob
	 */
	public function __construct(BatchJob $dbBatchJob, $entryStatus, BatchJob $twinJob = null)
	{
		$this->dbBatchJob = $dbBatchJob;
		$this->entryStatus = $entryStatus;
		$this->twinJob = $twinJob;
	}
	
	public function getConsumerInterface()
	{
		return self::EVENT_CONSUMER;
	}
	
	/**
	 * @param kBatchJobStatusEventConsumer $consumer
	 */
	protected function doConsume(KalturaEventConsumer $consumer)
	{
		$consumer->updatedJob($this->dbBatchJob, $this->entryStatus, $this->twinJob);
	}

}