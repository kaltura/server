<?php
interface kObjectChangedEventConsumer extends KalturaEventConsumer
{
	/**
	 * @param BaseObject $object
	 * @param array $modifiedColumns
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns);
}