<?php
/**
 * @package plugins.interactivity
 * @subpackage model.items
 */

class kVolatileInteractivity extends kBaseInteractivity
{

	protected function getFileSyncSubType()
	{
		return kEntryFileSyncSubType::VOLATILE_INTERACTIVITY_DATA;
	}

	protected function setEntryInteractivityVersion($newVersion)
	{
		$this->entry->setVolatileInteractivityVersion($newVersion);
		$this->entry->save();
	}
}