<?php


/**
 * Skeleton subclass for representing a row from the 'vendor_catalog_item' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.reach
 * @subpackage model
 */
class VendorTranslationCatalogItem extends VendorCaptionsCatalogItem 
{
	const CUSTOM_DATA_REQUIRE_SOURCE = 'require_source';
	
	public function applyDefaultValues()
	{
		$this->setServiceFeature(VendorServiceFeature::TRANSLATION);
	}

    /**
     * @param $object
     * @return kTranslationVendorTaskData|null
     */
    public function getTaskJobData($object)
    {
        if($object instanceof CaptionAsset)
        {
            $taskJobData = new kTranslationVendorTaskData();
            $taskJobData->captionAssetId = $object->getId();
            return $taskJobData;
        }

        return null;
    }
	
	public function setRequireSource($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_REQUIRE_SOURCE, $v);
	}
	
	public function getRequireSource()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_REQUIRE_SOURCE, null, true);
	}

} // VendorTranslationCatalogItem
