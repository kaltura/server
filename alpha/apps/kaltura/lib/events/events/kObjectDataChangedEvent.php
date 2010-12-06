<?php

class kObjectDataChangedEvent extends KalturaEvent implements IKalturaContinualEvent
{
	const EVENT_CONSUMER = 'kObjectDataChangedEventConsumer';
	
	/**
	 * @var BaseObject
	 */
	private $object;
	
	/**
	 * @var string
	 */
	private $previousVersion;
		
	/**
	 * @param BaseObject $object
	 * @param string $previousVersion
	 */
	public function __construct(BaseObject $object, $previousVersion = null)
	{
		$this->object = $object;
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
		return $consumer->objectDataChanged($this->object, $this->previousVersion);
	}

}