<?php
/**
 * Applicative event that raised implicitly by the developer
 */
class kObjectReadyForIndexEvent extends kApplicativeEvent
{
	const EVENT_CONSUMER = 'kObjectReadyForIndexEventConsumer';
	
	public function getConsumerInterface()
	{
		return self::EVENT_CONSUMER;
	}
	
	/**
	 * @param kObjectAddedEventConsumer $consumer
	 * @return bool true if should continue to the next consumer
	 */
	protected function doConsume(KalturaEventConsumer $consumer)
	{
		if(!$consumer->shouldConsumeReadyForIndexEvent($this->object))
			return true;
			
		$additionalLog = '';
		if(method_exists($this->object, 'getId'))
			$additionalLog .= 'id [' . $this->object->getId() . ']';
			
		KalturaLog::debug(get_class($this) . ' event consumed by ' . get_class($consumer) . ' object type [' . get_class($this->object) . '] ' . $additionalLog);
		return $consumer->objectReadyForIndex($this->object, $this->raisedJob);
	}

}