<?php

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
	 * Validate the consumer type and executes it
	 * @param KalturaEventConsumer $consumer
	 * @return bool true if should continue to the next consumer
	 */
	public final function consume(KalturaEventConsumer $consumer)
	{
		$consumerType = $this->getConsumerInterface();
		if($consumer instanceof $consumerType)
			return $this->doConsume($consumer);
			
		return true;
	}
}