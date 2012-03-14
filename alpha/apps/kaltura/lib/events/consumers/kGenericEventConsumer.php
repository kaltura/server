<?php
/**
 * Consume any type of event
 */
interface kGenericEventConsumer extends KalturaEventConsumer
{
	/**
	 * @param KalturaEvent $event
	 * @return bool true if should continue to the next consumer
	 */
	public function consumeEvent(KalturaEvent $event);
	
	/**
	 * @param KalturaEvent $event
	 * @return bool true if the consumer should handle the event
	 */
	public function shouldConsumeEvent(KalturaEvent $event);
}