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
	 */
	protected abstract function doConsume(KalturaEventConsumer $consumer);
	
	/**
	 * Validate the consumer type and executes it
	 * @param KalturaEventConsumer $consumer
	 */
	public final function consume(KalturaEventConsumer $consumer)
	{
		$consumerType = $this->getConsumerInterface();
		if($consumer instanceof $consumerType)
			$this->doConsume($consumer);
	}
}