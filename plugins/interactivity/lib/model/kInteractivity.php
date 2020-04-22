<?php
/**
 * @package plugins.interactivity
 * @subpackage model.items
 */

class kInteractivity extends kBaseInteractivity
{
	protected function getFileSyncSubType()
	{
		return kEntryFileSyncSubType::INTERACTIVITY_DATA;
	}

	protected function setEntryInteractivityVersionAndCapability($newVersion)
	{
		$this->entry->addCapability(InteractivityPlugin::getCapabilityCoreValue());
		$this->setEntryInteractivityVersion($newVersion);
	}

	protected function setEntryInteractivityVersion($newVersion)
	{
		$this->entry->setInteractivityVersion($newVersion);
		$this->entry->save();
	}
}