<?php

class kObjectCreatedEvent extends KalturaEvent
{
	const EVENT_CONSUMER = 'kObjectCreatedEventConsumer';
	
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
	 * @param kObjectCreatedEventConsumer $consumer
	 */
	protected function doConsume(KalturaEventConsumer $consumer)
	{
		$consumer->objectCreated($this->object);
	}

}