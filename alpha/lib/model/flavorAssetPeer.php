<?php

/**
 * Subclass for performing query and update operations on the 'flavor_asset' table.
 *
 * 
 *
 * @package lib.model
 */ 
class flavorAssetPeer extends assetPeer
{
	/** the related Propel class for this table */
	const OM_CLASS = 'flavorAsset';
	
	public static function setDefaultCriteriaFilter ()
	{
		if ( self::$s_criteria_filter == null )
		{
			self::$s_criteria_filter = new criteriaFilter ();
		}

		$c = new Criteria();
		$c->add ( self::STATUS, flavorAsset::FLAVOR_ASSET_STATUS_DELETED, Criteria::NOT_EQUAL );
		$c->add ( self::TYPE, assetType::FLAVOR );
		self::$s_criteria_filter->setFilter ( $c );
	}
	
	
	/**
	 * 
	 * @return flavorAsset
	 */
	public static function retrieveByEntryIdAndFlavorParams($entryId, $flavorParamsId)
	{
		$c = new Criteria();
		$c->add(self::ENTRY_ID, $entryId);
		$c->add(self::FLAVOR_PARAMS_ID, $flavorParamsId);
		// Gonen 10/05/10 - fixed bug when requesting download of original from KMC1 (pre-Andromeda)
		// migrated entries had all flavors set with flavor_params_ID to 0
		// all normal entries (not migrated) should have only the original with flavor params 0 (and is_original set to 1)
		if($flavorParamsId == 0)
		{
			$c->addAnd(self::IS_ORIGINAL, 1);
		}
		
		return self::doSelectOne($c);
	}
	
	/**
	 * 
	 * @return flavorAsset
	 */
	public static function retrieveOriginalReadyByEntryId($entryId)
	{
		$c = new Criteria();
		$c->add(self::IS_ORIGINAL, true);
		$c->add(self::ENTRY_ID, $entryId);
		$c->add(self::STATUS, flavorAsset::FLAVOR_ASSET_STATUS_READY);
		
		return self::doSelectOne($c);
	}
	
	/**
	 * @param string $entryId
	 * @return flavorAsset|null
	 */
	public static function retrieveBestPlayByEntryId($entryId)
	{
		$flavorAssets = self::retrieveReadyWebByEntryId($entryId);

		if (count($flavorAssets) > 0)
			return $flavorAssets[0];
		else
			return null;
	}
	
	/**
	 * @param string $entryId
	 * @param string $tag tag filter
	 * @return flavorAsset
	 */
	public static function retrieveHighestBitrateByEntryId($entryId, $tag = null)
	{
		$c = new Criteria();
		$c->add(flavorAssetPeer::ENTRY_ID, $entryId);
		$c->add(flavorAssetPeer::STATUS, flavorAsset::FLAVOR_ASSET_STATUS_READY);
		
		$flavorAssets = self::doSelect($c);
		if(!count($flavorAssets))
			return null;
			
		if(!is_null($tag))
			$flavorAssets = self::filterByTag($flavorAssets, $tag);
		
		if(!count($flavorAssets))
			return null;
			
		$ret = null;
		foreach($flavorAssets as $flavorAsset)
			if(!$ret || $ret->getBitrate() < $flavorAsset->getBitrate())
				$ret = $flavorAsset;
				
		return $ret;
	}
}