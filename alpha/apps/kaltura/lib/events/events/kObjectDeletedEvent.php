<?php
/**
 * Applicative event that raised implicitly by the developer
 * @package Core
 * @subpackage events
 */
class kObjectDeletedEvent extends kApplicativeEvent
{
	const EVENT_CONSUMER = 'kObjectDeletedEventConsumer';
	
	public function getConsumerInterface()
	{
		return self::EVENT_CONSUMER;
	}
	
	/**
	 * @param kObjectDeletedEventConsumer $consumer
	 * @return bool true if should continue to the next consumer
	 */
	protected function doConsume(KalturaEventConsumer $consumer)
	{
		if(!$consumer->shouldConsumeDeletedEvent($this->object))
			return true;
	
		$additionalLog = '';
		if(method_exists($this->object, 'getId'))
			$additionalLog .= 'id [' . $this->object->getId() . ']';
			
		KalturaLog::debug('consumer [' . get_class($consumer) . '] started handling [' . get_class($this) . '] object type [' . get_class($this->object) . '] ' . $additionalLog);
		$result = $consumer->objectDeleted($this->object, $this->raisedJob);
		KalturaLog::debug('consumer [' . get_class($consumer) . '] finished handling [' . get_class($this) . '] object type [' . get_class($this->object) . '] ' . $additionalLog);
		return $result;
	}

}