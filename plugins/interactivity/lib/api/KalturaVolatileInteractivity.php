<?php

/**
 * @package plugins.interactivity
 * @subpackage api.objects
 */
class KalturaVolatileInteractivity extends KalturaBaseInteractivity
{
	protected function getFileSyncSubType()
	{
		return kEntryFileSyncSubType::VOLATILE_INTERACTIVITY_DATA;
	}

	public function toObject($object_to_fill = null, $propsToSkip = array())
	{
		if (!$object_to_fill)
		{
			$object_to_fill = new  kVolatileInteractivity();
		}

		return parent::toObject($object_to_fill, $propsToSkip);
	}

	protected function getNoDataErrorMsg()
	{
		return KalturaInteractivityErrors::NO_VOLATILE_INTERACTIVITY_DATA;
	}
}