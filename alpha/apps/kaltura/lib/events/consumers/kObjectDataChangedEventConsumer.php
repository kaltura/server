<?php
interface kObjectDataChangedEventConsumer extends KalturaEventConsumer
{
	/**
	 * @param BaseObject $object
	 */
	public function objectDataChanged(BaseObject $object);
}