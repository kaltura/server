<?php
/**
 * @package Core
 * @subpackage events
 */
class kObjectInvalidateCacheEvent extends kApplicativeEvent
{
	const EVENT_CONSUMER = 'kObjectInvalidateCacheEventConsumer';

	public function getConsumerInterface()
	{
		return self::EVENT_CONSUMER;
	}

	/**
	 * @param KalturaEventConsumer $consumer
	 * @return bool true if should continue to the next consumer
	 */
	protected function doConsume(KalturaEventConsumer $consumer)
	{
		if(!$consumer->shouldConsumeInvalidateCache($this->object,$this->raisedJob))
		{
			return true;
		}

		$additionalLog = '';
		if(method_exists($this->object, 'getId'))
			$additionalLog .= 'id [' . $this->object->getId() . ']';

		KalturaLog::debug('consumer [' . get_class($consumer) . '] started handling [' . get_class($this) . '] object type [' . get_class($this->object) . '] ' . $additionalLog);
		$result = $consumer->invalidateCache($this->object, $this->raisedJob);
		KalturaLog::debug('consumer [' . get_class($consumer) . '] finished handling [' . get_class($this) . '] object type [' . get_class($this->object) . '] ' . $additionalLog);
		return $result;
	}
}
