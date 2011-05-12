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
		$additionalLog = '';
		if(method_exists($this->object, 'getId'))
			$additionalLog .= 'id [' . $this->object->getId() . ']';
			
		KalturaLog::debug(get_class($this) . ' event consumed by ' . get_class($consumer) . ' object type [' . get_class($this->object) . '] ' . $additionalLog);
		return $consumer->objectDataChanged($this->object, $this->previousVersion, $this->raisedJob);
	}

}