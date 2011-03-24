<?php
/**
 * Applicative event that raised implicitly by the developer
 */
class kObjectDataChangedEvent extends kApplicativeEvent
{
	const EVENT_CONSUMER = 'kObjectDataChangedEventConsumer';
	
	/**
	 * @var string
	 */
	private $previousVersion;
		
	/**
	 * @param BaseObject $object
	 * @param string $previousVersion
	 */
	public function __construct(BaseObject $object, $previousVersion = null, BatchJob $raisedJob = null)
	{
		parent::__construct($object, $raisedJob);
		
		$this->previousVersion = $previousVersion;
	}
	
	public function getConsumerInterface()
	{
		return self::EVENT_CONSUMER;
	}
	
	/**
	 * @param kObjectDataChangedEventConsumer $consumer
	 * @return bool true if should continue to the next consumer
	 */
	protected function doConsume(KalturaEventConsumer $consumer)
	{
		return $consumer->objectDataChanged($this->object, $this->previousVersion, $this->raisedJob);
	}

}