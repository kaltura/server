<?php
/**
 * Applicative event that raised implicitly by the developer
 */
class kObjectUpdatedEvent extends KalturaEvent implements IKalturaContinualEvent
{
	const EVENT_CONSUMER = 'kObjectUpdatedEventConsumer';
	
	/**
	 * @var BaseObject
	 */
	private $object;
	
	/**
	 * @param BaseObject $object
	 */
	public function __construct(BaseObject $object)
	{
		$this->object = $object;
	}
	
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
		return $consumer->objectUpdated($this->object);
	}

}