<?php
/**
 * Consumer to disable caching after an object is saved.
 *
 * @package api
 * @subpackage cache
 */
class KalturaCacheDisabler implements kObjectSavedEventConsumer
{
	public function objectSaved(BaseObject $object)
	{
		KalturaResponseCacher::disableCache();
	}
	
	public function shouldConsumeSavedEvent(BaseObject $object)
	{
		return KalturaResponseCacher::isCacheEnabled();
	}
}