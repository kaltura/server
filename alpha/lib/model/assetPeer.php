<?php

/**
 * Subclass for performing query and update operations on the 'flavor_asset' table.
 *
 * 
 *
 * @package lib.model
 */ 
abstract class assetPeer extends BaseflavorAssetPeer
{
	// cache classes by their type
	protected static $class_types_cache = array(
		assetType::FLAVOR => flavorAssetPeer::OM_CLASS,
		assetType::THUMBNAIL => thumbAssetPeer::OM_CLASS,
	);

	/**
	 * The returned Class will contain objects of the default type or
	 * objects that inherit from the default.
	 *
	 * @param      array $row PropelPDO result row.
	 * @param      int $colnum Column to examine for OM class information (first is 0).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getOMClass($row, $colnum)
	{
		if($row)
		{
			$assetType = $row[$colnum + 21]; // type column
			if(isset(self::$class_types_cache[$assetType]))
				return self::$class_types_cache[$assetType];
				
			$extendedCls = KalturaPluginManager::getObjectClass(parent::OM_CLASS, $assetType);
			if($extendedCls)
			{
				self::$class_types_cache[$assetType] = $extendedCls;
				return $extendedCls;
			}
			self::$class_types_cache[$assetType] = parent::OM_CLASS;
		}
			
		return parent::OM_CLASS;
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
	public static function retrieveByEntryIdAndParams($entryId, $paramsId)
	{
		$c = new Criteria();
		$c->add(self::ENTRY_ID, $entryId);
		$c->add(self::FLAVOR_PARAMS_ID, $paramsId);
		// Gonen 10/05/10 - fixed bug when requesting download of original from KMC1 (pre-Andromeda)
		// migrated entries had all flavors set with flavor_params_ID to 0
		// all normal entries (not migrated) should have only the original with flavor params 0 (and is_original set to 1)
		if($paramsId == 0)
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
	 * Leaves only the specified tag in the flavor assets array
	 * 
	 * @param array $assets
	 * @param string $tag
	 * @return array<assets>
	 */
	public static function filterByTag(array &$assets, $tag)
	{
		$newAssets = array();
		foreach($assets as &$asset)
		{
			if ($asset->hasTag($tag))
				$newAssets[] = &$asset;
		}
		
		$assets = $newAssets;
		return $newAssets;
	}
}