<?php
/**
 * Auto-raised event that raised by the propel generated objects
 * @package Core
 * @subpackage events
 */
interface kObjectCopiedEventConsumer extends KalturaEventConsumer
{
	/**
	 * @param BaseObject $fromObject
	 * @param BaseObject $toObject
	 * @return bool true if should continue to the next consumer
	 */
	public function objectCopied(BaseObject $fromObject, BaseObject $toObject);
	
	/**
	 * @param BaseObject $fromObject
	 * @param BaseObject $toObject
	 * @return bool true if the consumer should handle the event
	 */
	public function shouldConsumeCopiedEvent(BaseObject $fromObject, BaseObject $toObject);
}