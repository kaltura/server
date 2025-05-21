<?php
/**
 * @package plugins.reach
 * @subpackage model
 */
class VendorSentimentAnalysisCatalogItem extends VendorCatalogItem
{
    public function applyDefaultValues(): void
    {
        $this->setServiceFeature(VendorServiceFeature::SENTIMENT_ANALYSIS);
    }
}
