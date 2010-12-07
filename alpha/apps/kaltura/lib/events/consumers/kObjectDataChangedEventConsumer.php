<?php
/**
 * Applicative event that raised implicitly by the developer
 */
interface kObjectDataChangedEventConsumer extends KalturaEventConsumer
{
	/**
	 * @param BaseObject $object
	 * @param string $previousVersion
	 * @return bool true if should continue to the next consumer
	 */
	public function objectDataChanged(BaseObject $object, $previousVersion = null);
}