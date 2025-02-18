<?php
/**
 * @package plugins.reach
 * @subpackage model
 */
class VendorModerationCatalogItem extends VendorCatalogItem
{
    public function applyDefaultValues(): void
    {
        $this->setServiceFeature(VendorServiceFeature::MODERATION);
    }
}
