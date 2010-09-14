<?php
interface kObjectCopiedEventConsumer extends KalturaEventConsumer
{
	/**
	 * @param BaseObject $fromObject
	 * @param BaseObject $toObject
	 */
	public function objectCopied(BaseObject $fromObject, BaseObject $toObject);
}