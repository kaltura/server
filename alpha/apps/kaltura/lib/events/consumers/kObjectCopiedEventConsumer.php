<?php
/**
 * Auto-raised event that raised by the propel generated objects
 */
interface kObjectCopiedEventConsumer extends KalturaEventConsumer
{
	/**
	 * @param BaseObject $fromObject
	 * @param BaseObject $toObject
	 * @return bool true if should continue to the next consumer
	 */
	public function objectCopied(BaseObject $fromObject, BaseObject $toObject);
}