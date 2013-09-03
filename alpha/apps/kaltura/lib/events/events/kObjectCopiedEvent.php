<?php
/**
 * Auto-raised event that raised by the propel generated objects
 * @package Core
 * @subpackage events
 */
class kObjectCopiedEvent extends KalturaEvent implements IKalturaDatabaseEvent, IKalturaObjectRelatedEvent
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
		if(!$consumer->shouldConsumeCopiedEvent($this->fromObject, $this->toObject))
			return true;
			
		$additionalLog1 = '';
		$additionalLog2 = '';
		if(method_exists($this->fromObject, 'getId'))
			$additionalLog1 .= 'id [' . $this->fromObject->getId() . ']';
		if(method_exists($this->toObject, 'getId'))
			$additionalLog2 .= 'id [' . $this->toObject->getId() . ']';
			
		KalturaLog::debug('consumer [' . get_class($consumer) . '] started handling [' . get_class($this) . '] from object type [' . get_class($this->fromObject) . "] $additionalLog1 to object type [" . get_class($this->toObject) . "] $additionalLog2");
		$result = $consumer->objectCopied($this->fromObject, $this->toObject);
		KalturaLog::debug('consumer [' . get_class($consumer) . '] finished handling [' . get_class($this) . '] from object type [' . get_class($this->fromObject) . "] $additionalLog1 to object type [" . get_class($this->toObject) . "] $additionalLog2");
		return $result;
	}
	
	public function getKey()
	{
		if(method_exists($this->toObject, 'getId'))
			return get_class($this->object).$this->toObject->getId();
		
		return null;
	}
	
	/**
	 * @return BaseObject $fromObject
	 */
	public function getFromObject() 
	{
		return $this->fromObject;
	}
	
	/**
	 * @return BaseObject $toObject
	 */
	public function getToObject() 
	{
		return $this->toObject;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaEvent::getScope()
	 */
	public function getScope()
	{
		$scope = parent::getScope();
		if(method_exists($this->toObject, 'getPartnerId'))
			$scope->setPartnerId($this->toObject->getPartnerId());
			
		return $scope;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectRelatedEvent::getObject()
	 */
	public function getObject() 
	{
		return $this->getToObject();
	}

}