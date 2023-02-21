<?php
/**
 * Skeleton subclass for representing a row from the 'vendor_catalog_item' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *ac
 * @package plugins.reach
 * @subpackage model
 */
class VendorExtendedAudioDescriptionCatalogItem extends VendorAudioCatalogItem
{
	public function applyDefaultValues()
	{
		$this->setServiceFeature(VendorServiceFeature::EXTENDED_AUDIO_DESCRIPTION);
	}
} // VendorExtendedAudioDescriptionCatalogItem