<?php
/**
 * @package plugins.interactivity
 * @subpackage model
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

	/**
	 * @param string $entryId
	 * @throws kCoreException
	 * @throws kFileSyncException
	 */
	public function insert($entryId)
	{
		$this->setEntry($entryId);
		$syncKey = $this->getSyncKey();
		kFileSyncUtils::file_put_contents($syncKey, $this->data, true);
		$this->setEntryInteractivityVersionAndCapability($syncKey->getVersion());
	}
}