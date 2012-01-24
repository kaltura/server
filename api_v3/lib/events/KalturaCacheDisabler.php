<?php

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