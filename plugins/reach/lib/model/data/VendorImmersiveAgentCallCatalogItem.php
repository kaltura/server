<?php
/**
 * @package plugins.reach
 * @subpackage model
 */
class VendorImmersiveAgentCallCatalogItem extends VendorCatalogItem
{
	public function applyDefaultValues(): void
	{
		$this->setServiceFeature(VendorServiceFeature::IMMERSIVE_AGENT_CALL);
	}

	public function isDuplicateTask($entryId, $entryObjectType, $partnerId): bool
	{
		return false;
	}

	public function isEntryTypeSupported($type, $mediaType = null): bool
	{
		return true;
	}
}
