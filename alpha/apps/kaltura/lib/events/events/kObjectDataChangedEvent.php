<?php

class kObjectDataChangedEvent extends KalturaEvent
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
	 */
	protected function doConsume(KalturaEventConsumer $consumer)
	{
		$consumer->objectDataChanged($this->object);
	}

}