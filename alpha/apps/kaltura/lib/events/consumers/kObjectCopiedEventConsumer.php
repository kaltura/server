<?php
interface kObjectCopiedEventConsumer extends KalturaEventConsumer
{
	/**
	 * @param BaseObject $fromObject
	 * @param BaseObject $toObject
	 * @return bool true if should continue to the next consumer
	 */
	public function objectCopied(BaseObject $fromObject, BaseObject $toObject);
}