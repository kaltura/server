<?php

class kObjectDeletedEvent extends KalturaEvent
{
	const EVENT_CONSUMER = 'kObjectDeletedEventConsumer';
	
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
	 * @param kObjectDeletedEventConsumer $consumer
	 */
	protected function doConsume(KalturaEventConsumer $consumer)
	{
		$consumer->objectDeleted($this->object);
	}

}