<?php
/**
 * Auto-raised event that raised by the propel generated objects
 */
class kObjectCopiedEvent extends KalturaEvent implements IKalturaDatabaseEvent
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
	 * @return bool true if should continue to the next consumer
	 */
	protected function doConsume(KalturaEventConsumer $consumer)
	{
		return $consumer->objectCopied($this->fromObject, $this->toObject);
	}

}