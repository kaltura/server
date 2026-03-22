<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */
class KalturaVendorImmersiveAgentChatCatalogItem extends KalturaVendorCatalogItem
{
	protected function getServiceFeature()
	{
		return VendorServiceFeature::IMMERSIVE_AGENT_CHAT;
	}

	public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
	{
		if (is_null($object_to_fill)) {
			$object_to_fill = new VendorImmersiveAgentChatCatalogItem();
		}

		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if(is_null($object_to_fill)) {
			$object_to_fill = new VendorImmersiveAgentChatCatalogItem();
		}

		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
