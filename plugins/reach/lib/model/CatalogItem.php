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
class CatalogItem extends BaseVendorCatalogItem 
{
	const CUSTOM_DATA_PRICING = "pricing";
	
	public function setPricing($pricing)
	{
		$this->putInCustomData(self::CUSTOM_DATA_PRICING, serialize($pricing));
	}
	
	/**
	 * @return kCatalogItemPricing
	 */
	public function getPricing()
	{
		$pricing = $this->getFromCustomData(self::CUSTOM_DATA_PRICING);
		
		if($pricing)
			$pricing = unserialize($pricing);
		
		return $pricing;
	}
	
} // CatalogItem
