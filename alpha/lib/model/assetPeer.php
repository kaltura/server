<?php

/**
 * Subclass for performing query and update operations on the 'flavor_asset' table.
 *
 * 
 *
 * @package lib.model
 */ 
class assetPeer extends BaseassetPeer
{
	// cache classes by their type
	protected static $class_types_cache = array(
		assetType::FLAVOR => flavorAssetPeer::OM_CLASS,
		assetType::THUMBNAIL => thumbAssetPeer::OM_CLASS,
	);
	
	/**
	 * @var assetParamsPeer
	 */
	protected static $instance = null;

	public static function resetInstanceCriteriaFilter()
	{
		self::$instance = null;
		
		if ( self::$s_criteria_filter == null )
			self::$s_criteria_filter = new criteriaFilter ();

		$c = self::$s_criteria_filter->getFilter();
		if($c)
		{
			$c->remove(self::STATUS);
			$c->remove(self::TYPE);
		}
		else
		{
			$c = new Criteria();
		}

		$c->add(self::STATUS, asset::FLAVOR_ASSET_STATUS_DELETED, Criteria::NOT_EQUAL);

		self::$s_criteria_filter->setFilter ( $c );
	}
	
	public function setInstanceCriteriaFilter()
	{
	}
	
	/**
	 * Returns the default criteria filter
	 *
	 * @return     criteriaFilter The default criteria filter.
	 */
	public static function &getCriteriaFilter()
	{
		if(self::$s_criteria_filter == null)
			self::setDefaultCriteriaFilter();
			
		if(self::$instance)
			self::$instance->setInstanceCriteriaFilter();
			
		return self::$s_criteria_filter;
	}

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
		$c->add(assetPeer::ID, $id); 
		return assetPeer::doSelectOne($c, $con);
	}
	
	public static function doSelectOneJoinFlavorParams(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = assetPeer::doSelectJoinflavorParams($critcopy, $con);
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
	
	public static function retreiveReadyByEntryIdAndFlavorParams($entryId, array $flavorParamsIds)
	{
		$c = new Criteria();
		$c->add(assetPeer::ENTRY_ID, $entryId);
		$c->add(assetPeer::STATUS, flavorAsset::FLAVOR_ASSET_STATUS_READY);
		$c->add(assetPeer::FLAVOR_PARAMS_ID, $flavorParamsIds, Criteria::IN);
		
		// The client will most probably expect the list to be ordered by bitrate
		$c->addAscendingOrderByColumn ( assetPeer::BITRATE ); /// TODO - should be server side ?
		
		return assetPeer::doSelect($c);
	}
	
	/**
	 * @param string $entryId
	 * @return flavorAsset
	 */
	public static function retreiveOriginalByEntryId($entryId)
	{
		$c = new Criteria();
		$c->add(assetPeer::ENTRY_ID, $entryId);
		$c->add(assetPeer::IS_ORIGINAL, true);
		
		return assetPeer::doSelectOne($c);
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

	/**
	 * @param string $entryId
	 * @param array $paramsIds
	 * @param $con
	 * 
	 * @return array
	 */
	public static function getReadyIdsByParamsIds($entryId, array $paramsIds, $con = null)
	{
		$criteria = new Criteria();
		$criteria->addSelectColumn(assetPeer::ID);
		$criteria->add(assetPeer::ENTRY_ID, $entryId);
		$criteria->add(assetPeer::STATUS, asset::FLAVOR_ASSET_STATUS_READY);
		$criteria->add(assetPeer::FLAVOR_PARAMS_ID, $paramsIds, Criteria::IN);

		$stmt = assetPeer::doSelectStmt($criteria, $con);
		return $stmt->fetchAll(PDO::FETCH_COLUMN);
	}
}