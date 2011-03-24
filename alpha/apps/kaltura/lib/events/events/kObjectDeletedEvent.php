<?php
/**
 * Applicative event that raised implicitly by the developer
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
		return $consumer->objectDeleted($this->object, $this->raisedJob);
	}

}