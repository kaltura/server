<?php

class kObjectCopiedEvent extends KalturaEvent
{
	const EVENT_CONSUMER = 'kObjectCopiedEventConsumer';
	
	/**
	 * @var BaseObject
	 */
	private $fromObject;
	
	/**
	 * @var BaseObject
	 */
	private $toObject;
	
	/**
	 * @param BaseObject $fromObject
	 * @param BaseObject $toObject
	 */
	public function __construct(BaseObject $fromObject, BaseObject $toObject)
	{
		$this->fromObject = $fromObject;
		$this->toObject = $toObject;
	}
	
	public function getConsumerInterface()
	{
		return self::EVENT_CONSUMER;
	}
	
	/**
	 * @param kObjectCopiedEventConsumer $consumer
	 */
	protected function doConsume(KalturaEventConsumer $consumer)
	{
		$consumer->objectCopied($this->fromObject, $this->toObject);
	}

}