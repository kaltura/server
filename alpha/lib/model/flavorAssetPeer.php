<?php

/**
 * Subclass for performing query and update operations on the 'flavor_asset' table.
 *
 * 
 *
 * @package lib.model
 */ 
class flavorAssetPeer extends BaseflavorAssetPeer
{

	public static function setDefaultCriteriaFilter ()
	{
		if ( self::$s_criteria_filter == null )
		{
			self::$s_criteria_filter = new criteriaFilter ();
		}

		$c = new Criteria();
		$c->add ( self::STATUS, flavorAsset::FLAVOR_ASSET_STATUS_DELETED, Criteria::NOT_EQUAL );
		self::$s_criteria_filter->setFilter ( $c );
	}

	/**
	 * 
	 * @return flavorAsset
	 */
	public static function retrieveByPKNoFilter ($pk, $con = null)
	{
		self::setUseCriteriaFilter ( false );
		$res = parent::retrieveByPK( $pk , $con );
		self::setUseCriteriaFilter ( true );
		return $res;
	}

	public static function retrieveByPKsNoFilter ($pks, $con = null)
	{
		self::setUseCriteriaFilter ( false );
		$res = parent::retrieveByPKs( $pks , $con );
		self::setUseCriteriaFilter ( true );
		return $res;
	}
	
	/**
	 * Retrieve by ID instead of INT_ID
	 * @param $id
	 * @param $con
	 * @return flavorAsset
	 */
	public static function retrieveById($id, $con = null)
	{
		$c = new Criteria(); 
		$c->add(flavorAssetPeer::ID, $id); 
		return flavorAssetPeer::doSelectOne($c, $con);
	}
	
	public static function doSelectOneJoinFlavorParams(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = flavorAssetPeer::doSelectJoinflavorParams($critcopy, $con);
		if ($objects) {
			return $objects[0];
		}
		return null;
	}

	/**
	 * 
	 * @return flavorAsset
	 */
	public static function retrieveByEntryIdAndExtension($entryId, $extension)
	{
		$c = new Criteria();
		$c->add(self::ENTRY_ID, $entryId);
		$c->add(self::FILE_EXT, $extension);
		
		return self::doSelectOne($c);
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
	 * @param string $entryId
	 * @return array<flavorAsset>
	 */
	public static function retrieveByEntryId($entryId)
	{
		$c = new Criteria();
		$c->add(self::ENTRY_ID, $entryId);
		
		return self::doSelect($c);
	}
	
	/**
	 * 
	 * @return flavorAsset
	 */
	public static function retrieveOriginalByEntryId($entryId)
	{
		$c = new Criteria();
		$c->add(self::ENTRY_ID, $entryId);
		$c->add(self::IS_ORIGINAL, true);
		
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
	
	public static function retreiveReadyByEntryIdAndTag($entryId, $tag)
	{
		$flavorAssets = self::retreiveReadyByEntryId($entryId);
		self::filterByTag($flavorAssets, $tag);
		return $flavorAssets;
	}
	
	public static function retreiveReadyByEntryIdAndFlavorParams($entryId, array $flavorParamsIds)
	{
		$c = new Criteria();
		$c->add(flavorAssetPeer::ENTRY_ID, $entryId);
		$c->add(flavorAssetPeer::STATUS, flavorAsset::FLAVOR_ASSET_STATUS_READY);
		$c->add(flavorAssetPeer::FLAVOR_PARAMS_ID, $flavorParamsIds, Criteria::IN);
		
		// The client will most probably expect the list to be ordered by bitrate
		$c->addAscendingOrderByColumn ( flavorAssetPeer::BITRATE ); /// TODO - should be server side ?
		
		return flavorAssetPeer::doSelect($c);
	}
	
	/**
	 * @param string $entryId
	 * @return flavorAsset
	 */
	public static function retreiveOriginalByEntryId($entryId)
	{
		$c = new Criteria();
		$c->add(flavorAssetPeer::ENTRY_ID, $entryId);
		$c->add(flavorAssetPeer::IS_ORIGINAL, true);
		
		return flavorAssetPeer::doSelectOne($c);
	}
	
	public static function retreiveReadyByEntryId($entryId)
	{
		$c = new Criteria();
		$c->add(flavorAssetPeer::ENTRY_ID, $entryId);
		$c->add(flavorAssetPeer::STATUS, flavorAsset::FLAVOR_ASSET_STATUS_READY);
		
		// The client will most probably expect the list to be ordered by bitrate
		$c->addAscendingOrderByColumn ( flavorAssetPeer::BITRATE ); /// TODO - should be server side ?
		
		return flavorAssetPeer::doSelect($c);
	}
	
	public static function retrieveReadyWebByEntryId($entryId)
	{
		$flavorAssets = self::retreiveReadyByEntryIdAndTag($entryId, flavorParams::TAG_MBR);
		// TODO - until now production was searching by tag 'mbr',
		// until we test this deeper, we keep MBR.
//		$flavorAssets = self::retreiveReadyByEntryIdAndTag($entryId, flavorParams::TAG_WEB);
		return $flavorAssets;
	}
	
	public static function retrieveBestEditByEntryId($entryId)
	{
		$flavorAssets = self::retreiveReadyByEntryIdAndTag($entryId, flavorParams::TAG_EDIT);
		
		if (count($flavorAssets) > 0)
			return $flavorAssets[0];
		else
			return self::retrieveBestPlayByEntryId($entryId);
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
	 * @return flavorAsset|null
	 */
	public static function retrieveHighestBitrateByEntryId($entryId)
	{
		$c = new Criteria();
		$c->add(flavorAssetPeer::ENTRY_ID, $entryId);
		$c->add(flavorAssetPeer::STATUS, flavorAsset::FLAVOR_ASSET_STATUS_READY);
		$c->addDescendingOrderByColumn(flavorAssetPeer::BITRATE);
		
		return flavorAssetPeer::doSelectOne($c);
	}
	
	/**
	 * Leaves only the specified tag in the flavor assets array
	 * 
	 * @param array $flavorAssets
	 * @param string $tag
	 */
	public static function filterByTag(array &$flavorAssets, $tag)
	{
		$newFlavors = array();
		foreach($flavorAssets as &$flavorAsset)
		{
			if ($flavorAsset->hasTag($tag))
				$newFlavors[] = &$flavorAsset;
		}
		
		$flavorAssets = $newFlavors;
		return $newFlavors;
	}
}