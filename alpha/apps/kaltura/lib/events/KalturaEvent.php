<?php
/**
 * @package Core
 * @subpackage events
 */
abstract class KalturaEvent
{
	/**
	 * @return string - name of consumer interface
	 */
	public abstract function getConsumerInterface();
	
	/**
	 * Executes the consumer
	 * @param KalturaEventConsumer $consumer
	 * @return bool true if should continue to the next consumer
	 */
	protected abstract function doConsume(KalturaEventConsumer $consumer);

	/**
	 * @param kGenericEventConsumer $consumer
	 * @return bool true if should continue to the next consumer
	 */
	protected function consumeGeneric(kGenericEventConsumer $consumer)
	{
		if(!$consumer->shouldConsumeEvent($this))
			return true;
	
		KalturaLog::debug(get_class($this) . ' event consumed by ' . get_class($consumer));
		return $consumer->consumeEvent($this);
	}
	
	/**
	 * Validate the consumer type and executes it
	 * @param KalturaEventConsumer $consumer
	 * @return bool true if should continue to the next consumer
	 */
	public final function consume(KalturaEventConsumer $consumer)
	{
		$consumerType = $this->getConsumerInterface();	
		if($consumer instanceof $consumerType)
			return $this->doConsume($consumer);	
		elseif($consumer instanceof kGenericEventConsumer)
			return $this->consumeGeneric($consumer);
			
		return true;
	}
	
	public function getKey()
	{
		return null;
	}
	
	public function getPriority()
	{
		return EventPriority::NORMAL;
	}
}