<?php
/**
 * @package Core
 * @subpackage events
 */
class kObjectErasedEvent extends KalturaEvent implements IKalturaDatabaseEvent
{
    const EVENT_CONSUMER = 'kObjectErasedEventConsumer';
	/* (non-PHPdoc)
     * @see KalturaEvent::doConsume()
     */
    protected function doConsume (KalturaEventConsumer $consumer)
    {
        if(!$consumer->shouldConsumeErasedEvent($this->object))
			return true;
			
		$additionalLog = '';
		if(method_exists($this->object, 'getId'))
			$additionalLog .= 'id [' . $this->object->getId() . ']';
			
		KalturaLog::debug(get_class($this) . ' event consumed by ' . get_class($consumer) . ' object type [' . get_class($this->object) . '] ' . $additionalLog);
		return $consumer->objectErased($this->object);
        
    }

	/* (non-PHPdoc)
     * @see KalturaEvent::getConsumerInterface()
     */
    public function getConsumerInterface ()
    {
        return self::EVENT_CONSUMER;
        
    }
    
    public function __construct(BaseObject $object)
	{
		$this->object = $object;
		
		$additionalLog = '';
		if(method_exists($object, 'getId'))
			$additionalLog .= ' id [' . $object->getId() . ']';
			
		KalturaLog::debug("Event [" . get_class($this) . "] object type [" . get_class($object) . "]" . $additionalLog);
	}


}