<?php
/**
 * @package plugins.integration
 * @subpackage lib.events
 */
class kIntegrationJobClosedEvent extends KalturaEvent implements IKalturaObjectRelatedEvent, IKalturaContinualEvent
{
	const EVENT_CONSUMER = 'kIntegrationJobClosedEventConsumer';

	/**
	 * @var BatchJob
	 */
	private $batchJob;
	
	/**
	 * @param BaseObject $object
	 */
	public function __construct(BatchJob $batchJob)
	{
		$this->batchJob = $batchJob;
		
		KalturaLog::debug("Event [" . get_class($this) . "] batch-job id [" . $batchJob->getId() . "] status [" . $batchJob->getStatus() . "]");
	}
	
	/* (non-PHPdoc)
	 * @see KalturaEvent::getConsumerInterface()
	 */
	public function getConsumerInterface()
	{
		return self::EVENT_CONSUMER;
	}

	/* (non-PHPdoc)
	 * @see KalturaEvent::doConsume()
	 */
	protected function doConsume(KalturaEventConsumer $consumer)
	{
		if(!$consumer->shouldConsumeIntegrationCloseEvent($this->object, $this->modifiedColumns))
			return true;
			
		KalturaLog::debug('consumer [' . get_class($consumer) . '] started handling [' . get_class($this) . '] batch-job id [' . $this->batchJob->getId() . '] status [' . $this->batchJob->getStatus() . ']');
		$result = $consumer->integrationJobClosed($this->batchJob);
		KalturaLog::debug('consumer [' . get_class($consumer) . '] finished handling [' . get_class($this) . '] batch-job id [' . $this->batchJob->getId() . '] status [' . $this->batchJob->getStatus() . ']');
		return $result;
	}

	/* (non-PHPdoc)
	 * @see IKalturaObjectRelatedEvent::getObject()
	 */
	public function getObject()
	{
		$this->batchJob->getObject();
	}
	
	/* (non-PHPdoc)
	 * @see KalturaEvent::getScope()
	 */
	public function getScope()
	{
		$scope = parent::getScope();
		$scope->setPartnerId($this->batchJob->getPartnerId());
		
		return $scope;
	}
}
