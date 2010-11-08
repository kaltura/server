<?php

class kObjectDataChangedEvent extends KalturaEvent implements IKalturaContinualEvent
{
	const EVENT_CONSUMER = 'kObjectDataChangedEventConsumer';
	
	/**
	 * @var BaseObject
	 */
	private $object;
		
	/**
	 * @param BaseObject $object
	 */
	public function __construct(BaseObject $object)
	{
		$this->object = $object;
	}
	
	public function getConsumerInterface()
	{
		return self::EVENT_CONSUMER;
	}
	
	/**
	 * @param kObjectChangedEventConsumer $consumer
	 * @return bool true if should continue to the next consumer
	 */
	protected function doConsume(KalturaEventConsumer $consumer)
	{
		return $consumer->objectDataChanged($this->object);
	}

}