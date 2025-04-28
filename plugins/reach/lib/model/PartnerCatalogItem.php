<?php


/**
 * Skeleton subclass for representing a row from the 'partner_catalog_item' table.
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
class PartnerCatalogItem extends BasePartnerCatalogItem
{
	const DEFAULT_REACH_PROFILE_ID = "defaultReachProfileId";

	public function setDefaultReachProfileId($value)
	{
		$this->putInCustomData(self::DEFAULT_REACH_PROFILE_ID, $value);
	}

	public function getDefaultReachProfileId()
	{
		return $this->getFromCustomData(self::DEFAULT_REACH_PROFILE_ID);
	}

} // PartnerCatalogItem
