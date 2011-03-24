<?php
/**
 * Applicative event that raised implicitly by the developer
 */
class kObjectUpdatedEvent extends kApplicativeEvent
{
	const EVENT_CONSUMER = 'kObjectUpdatedEventConsumer';
	
	public function getConsumerInterface()
	{
		return self::EVENT_CONSUMER;
	}
	
	/**
	 * @param kObjectUpdatedEventConsumer $consumer
	 * @return bool true if should continue to the next consumer
	 */
	protected function doConsume(KalturaEventConsumer $consumer)
	{
		return $consumer->objectUpdated($this->object, $this->raisedJob);
	}

}