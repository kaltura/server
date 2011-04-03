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
		
		$additionalLog1 = '';
		$additionalLog2 = '';
		if(method_exists($fromObject, 'getId'))
			$additionalLog1 .= 'id [' . $fromObject->getId() . ']';
		if(method_exists($toObject, 'getId'))
			$additionalLog2 .= 'id [' . $toObject->getId() . ']';
			
		KalturaLog::debug("Event [" . get_class($this) . "] from object type [" . get_class($fromObject) . "] $additionalLog1 to object type [" . get_class($toObject) . "] $additionalLog2");
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
		$additionalLog1 = '';
		$additionalLog2 = '';
		if(method_exists($this->fromObject, 'getId'))
			$additionalLog1 .= 'id [' . $this->fromObject->getId() . ']';
		if(method_exists($this->toObject, 'getId'))
			$additionalLog2 .= 'id [' . $this->toObject->getId() . ']';
			
		KalturaLog::debug(get_class($this) . " event consumed by " . get_class($consumer) . " from object type [" . get_class($this->fromObject) . "] $additionalLog1 to object type [" . get_class($this->toObject) . "] $additionalLog2");
		return $consumer->objectCopied($this->fromObject, $this->toObject);
	}

}