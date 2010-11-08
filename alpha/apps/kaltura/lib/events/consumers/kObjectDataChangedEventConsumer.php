<?php
interface kObjectDataChangedEventConsumer extends KalturaEventConsumer
{
	/**
	 * @param BaseObject $object
	 * @return bool true if should continue to the next consumer
	 */
	public function objectDataChanged(BaseObject $object);
}