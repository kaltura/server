<?php

/**
 * @package plugins.interactivity
 * @subpackage api.objects
 */

class KalturaInteractivity extends KalturaBaseInteractivity
{
	protected function getFileSyncSubType()
	{
		return kEntryFileSyncSubType::INTERACTIVITY_DATA;
	}

	public function toObject($object_to_fill = null, $propsToSkip = array())
	{
		if (!$object_to_fill)
		{
			$object_to_fill = new  kInteractivity();
		}

		return parent::toObject($object_to_fill, $propsToSkip);
	}

	protected function getNoDataErrorMsg()
	{
		return KalturaInteractivityErrors::NO_INTERACTIVITY_DATA;
	}
}