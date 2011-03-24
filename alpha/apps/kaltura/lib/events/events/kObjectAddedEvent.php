<?php
/**
 * Applicative event that raised implicitly by the developer
 */
class kObjectAddedEvent extends kApplicativeEvent
{
	const EVENT_CONSUMER = 'kObjectAddedEventConsumer';
	
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
		return $consumer->objectAdded($this->object, $this->raisedJob);
	}

}