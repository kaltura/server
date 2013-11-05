<?php

/**
 * Subclass for performing query and update operations on the 'flavor_asset' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class assetPeer extends BaseassetPeer
{
	const FLAVOR_OM_CLASS = 'flavorAsset';
	const THUMBNAIL_OM_CLASS = 'thumbAsset';
	const LIVE_OM_CLASS = 'liveAsset';
	
	/**
	 * Map that holds the assets according to their ids
	 * @var array<id, asset>
	 */
	public static $assetInstancesById = array();
	
	// cache classes by their type
	protected static $class_types_cache = array(
		assetType::FLAVOR => self::FLAVOR_OM_CLASS,
		assetType::THUMBNAIL => self::THUMBNAIL_OM_CLASS,
		assetType::LIVE => self::LIVE_OM_CLASS,
	);

	public static function addInstanceToPool(asset $obj, $key = null)
	{
		parent::addInstanceToPool($obj, $key);
		
		if (Propel::isInstancePoolingEnabled())
			self::$assetInstancesById[$obj->getId()] = $obj;
	}
	
	public static function removeInstanceFromPool($value)
	{
		parent::removeInstanceFromPool($value);
		
		if (is_object($value) && $value instanceof asset)
			unset(self::$assetInstancesById[$value->getId()]);
	}

	public static function getInstanceFromIdPool($key)
	{
		if (Propel::isInstancePoolingEnabled() && isset(self::$assetInstancesById[$key]))
			return self::$assetInstancesById[$key];
			
		return null;
	}

	public static function clearInstancePool()
	{
		parent::clearInstancePool();			
		self::$assetInstancesById = array();
	}
	
	public static function setDefaultCriteriaFilter ()
	{
		if(is_null(self::$s_criteria_filter))
			self::$s_criteria_filter = new criteriaFilter();
		
		$c = new Criteria(); 
		$c->add(self::STATUS, asset::FLAVOR_ASSET_STATUS_DELETED, Criteria::NOT_EQUAL);
		self::$s_criteria_filter->setFilter($c);
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
			$typeField = self::translateFieldName(assetPeer::TYPE, BasePeer::TYPE_COLNAME, BasePeer::TYPE_NUM);
			$assetType = $row[$typeField];
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
	 * @param int $assetType enum from assetType
	 * @return asset
	 */
	public static function getNewAsset($assetType)
	{
		$class = null;
		
		if(isset(self::$class_types_cache[$assetType]))
			$class = self::$class_types_cache[$assetType];
			
		if(!$class)
		{
			$extendedCls = KalturaPluginManager::getObjectClass(parent::OM_CLASS, $assetType);
			if($extendedCls)
			{
				self::$class_types_cache[$assetType] = $extendedCls;
				$class = $extendedCls;
			}
		}
		
		if(!$class)
			throw new kCoreException("Unable to instatiate asset of type [$assetType]", kCoreException::OBJECT_TYPE_NOT_FOUND);
				
		return new $class();
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
	 * @param int $id
	 * @param $con
	 * @return asset
	 */
	public static function retrieveById($id, $con = null)
	{
		if (null !== ($obj = assetPeer::getInstanceFromIdPool($id)))
			return $obj;
		
		$c = new Criteria(); 
		$c->add(assetPeer::ID, $id); 
		return assetPeer::doSelectOne($c, $con);
	}
	
	/**
	 * Retrieve by ID instead of INT_ID
	 * @param int $id
	 * @param $con
	 * @return asset
	 */
	public static function retrieveByIdNoFilter($id, $con = null)
	{
		self::setUseCriteriaFilter(false);
		$asset = self::retrieveById($id, $con);
		self::setUseCriteriaFilter(true);
		
		return $asset;
	}
	
	/**
	 * Retrieve by IDs instead of INT_ID
	 * @param $ids
	 * @param $con
	 * @return array<asset>
	 */
	public static function retrieveByIds($ids, $con = null)
	{
		$c = new Criteria(); 
		$c->add(assetPeer::ID, $ids, Criteria::IN); 
		return assetPeer::doSelect($c, $con);
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
	 * @param string $entryId
	 * @param array $types
	 * @return array<flavorAsset>
	 */
	public static function retrieveByEntryId($entryId, array $types = null)
	{
		$c = new Criteria();
		$c->add(self::ENTRY_ID, $entryId);
		if(count($types))
			$c->add(self::TYPE, $types, Criteria::IN);
		
		return self::doSelect($c);
	}
	
	public static function retrieveAllFlavorsTypes()
	{
		$flavorTypes = KalturaPluginManager::getExtendedTypes(self::OM_CLASS, assetType::FLAVOR);
		$flavorTypes[] = assetType::LIVE;
		return $flavorTypes;
	}
	
	
	/**
	 * 
	 * @param string $entryId
	 * @return array<flavorAsset>
	 */
	public static function retrieveFlavorsByEntryId($entryId)
	{
		$c = new Criteria();
		$c->add(self::ENTRY_ID, $entryId);
		
		$flavorTypes = self::retrieveAllFlavorsTypes();
		$c->add(assetPeer::TYPE, $flavorTypes, Criteria::IN);
		
		return self::doSelect($c);
	}
	
	/**
	 * 
	 * @param string $entryId
	 * @return array<thumbAsset>
	 */
	public static function retrieveThumbnailsByEntryId($entryId)
	{
		$thumbTypes = KalturaPluginManager::getExtendedTypes(self::OM_CLASS, assetType::THUMBNAIL);
		return self::retrieveByEntryId($entryId, $thumbTypes);
	}
	
	/**
	 * @param string $entryId
	 * @param array $types
	 * @return array<flavorAsset>
	 */
	public static function countByEntryId($entryId, array $types = null)
	{
		$c = new Criteria();
		$c->add(self::ENTRY_ID, $entryId);
		if(count($types))
			$c->add(self::TYPE, $types, Criteria::IN);
		
		return self::doCount($c);
	}
	
	/**
	 * 
	 * @param string $entryId
	 * @return array<flavorAsset>
	 */
	public static function countThumbnailsByEntryId($entryId)
	{
		$types = KalturaPluginManager::getExtendedTypes(self::OM_CLASS, assetType::THUMBNAIL);
		return self::countByEntryId($entryId, $types);
	}

	public static function retrieveReadyByEntryId($entryId, $ids = null, array $statuses = null)
	{
		$c = new Criteria();
		$c->add(assetPeer::ENTRY_ID, $entryId);
		if(count($statuses))
		    $c->add(assetPeer::STATUS, $statuses, Criteria::IN);
		else 
			$c->add(assetPeer::STATUS, flavorAsset::FLAVOR_ASSET_STATUS_READY);
		if (!is_null($ids))
			$c->add(assetPeer::ID, $ids, Criteria::IN);	
		return self::doSelectAscendingBitrate($c);
	}

	public static function retrieveReadyFlavorsByEntryId($entryId, array $paramsIds = null)
	{
		$c = new Criteria();
		$c->add(assetPeer::ENTRY_ID, $entryId);
		$c->add(assetPeer::STATUS, flavorAsset::FLAVOR_ASSET_STATUS_READY);
		
		if(count($paramsIds))
			$c->add(assetPeer::FLAVOR_PARAMS_ID, $paramsIds, Criteria::IN);
		
		$flavorTypes = self::retrieveAllFlavorsTypes();
		$c->add(assetPeer::TYPE, $flavorTypes, Criteria::IN);
		
		return self::doSelectAscendingBitrate($c);
	}
	
	public static function retrieveFlavorsByEntryIdAndStatus($entryId, array $paramsIds = null, array $statuses = null)
	{
		$c = new Criteria();
		$c->add(assetPeer::ENTRY_ID, $entryId);
		
		if(count($statuses)) {
		    $c->add(assetPeer::STATUS, $statuses, Criteria::IN);
		}
		
		if(count($paramsIds)) {
			$c->add(assetPeer::FLAVOR_PARAMS_ID, $paramsIds, Criteria::IN);
		}
		
		$flavorTypes = self::retrieveAllFlavorsTypes();
		$c->add(assetPeer::TYPE, $flavorTypes, Criteria::IN);
		
		return self::doSelectAscendingBitrate($c);
	}
	
	public static function retrieveReadyFlavorsIdsByEntryId($entryId, array $paramsIds = null)
	{
		$c = new Criteria();
		$c->addSelectColumn(assetPeer::ID);
		$c->add(assetPeer::ENTRY_ID, $entryId);
		$c->add(assetPeer::STATUS, flavorAsset::FLAVOR_ASSET_STATUS_READY);
		
		if(count($paramsIds))
			$c->add(assetPeer::FLAVOR_PARAMS_ID, $paramsIds, Criteria::IN);
		
		$flavorTypes = self::retrieveAllFlavorsTypes();
		$c->add(assetPeer::TYPE, $flavorTypes, Criteria::IN);

		$stmt = assetPeer::doSelectStmt($c, null);
		return $stmt->fetchAll(PDO::FETCH_COLUMN);
	}

	public static function retrieveReadyThumbnailsByEntryId($entryId, array $paramsIds = null)
	{
		$c = new Criteria();
		$c->add(assetPeer::ENTRY_ID, $entryId);
		$c->add(assetPeer::STATUS, flavorAsset::FLAVOR_ASSET_STATUS_READY);
		
		if(count($paramsIds))
			$c->add(assetPeer::FLAVOR_PARAMS_ID, $paramsIds, Criteria::IN);
			
		$flavorTypes = KalturaPluginManager::getExtendedTypes(self::OM_CLASS, assetType::THUMBNAIL);
		$c->add(assetPeer::TYPE, $flavorTypes, Criteria::IN);
		
		return self::doSelect($c);
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
	
	public static function retrieveReadyByEntryIdAndFlavorParams($entryId, array $flavorParamsIds, $notIn = false)
	{
		$c = new Criteria();
		$c->add(assetPeer::ENTRY_ID, $entryId);
		$c->add(assetPeer::STATUS, flavorAsset::FLAVOR_ASSET_STATUS_READY);
		if($notIn)
			$c->add(assetPeer::FLAVOR_PARAMS_ID, $flavorParamsIds, Criteria::NOT_IN);
		else
			$c->add(assetPeer::FLAVOR_PARAMS_ID, $flavorParamsIds, Criteria::IN);
		
		return assetPeer::doSelectAscendingBitrate($c);
	}
	
	public static function doSelectAscendingBitrate(Criteria $criteria, PropelPDO $con = null)
	{
		$assets = assetPeer::doSelect($criteria);
		usort($assets, array('assetPeer', 'compareBitrate'));
		return $assets;
	}
	
	public static function compareBitrate(asset $a, asset $b)
	{
		$bitrate1 = 0;
		if ($a instanceof flavorAsset)
			$bitrate1 = $a->getBitrate();

		$bitrate2 = 0;
		if ($b instanceof flavorAsset)
			$bitrate2 = $b->getBitrate();
			
		return ($bitrate1 - $bitrate2); 
	}
	
	public static function retrieveReadyByEntryIdAndTag($entryId, $tag)
	{
		$flavorAssets = self::retrieveReadyByEntryId($entryId);
		self::filterByTag($flavorAssets, $tag);
		return $flavorAssets;
	}
	
	public static function retrieveReadyFlavorsByEntryIdAndTag($entryId, $tag)
	{
		$flavorAssets = self::retrieveReadyFlavorsByEntryId($entryId);
		self::filterByTag($flavorAssets, $tag);
		return $flavorAssets;
	}
	
	public static function retrieveBestEditByEntryId($entryId)
	{
		$flavorAssets = self::retrieveReadyByEntryIdAndTag($entryId, flavorParams::TAG_EDIT);
		
		if (count($flavorAssets) > 0)
			return $flavorAssets[0];
		else
			return self::retrieveBestPlayByEntryId($entryId);
	}
	
	public static function retrieveReadyWebByEntryId($entryId)
	{
		$flavorAssets = self::retrieveReadyByEntryIdAndTag($entryId, flavorParams::TAG_MBR);
		
		//Requirement for mantis 13058: if there are no flavors tagged as MBR, fallback to flavors tagged as WEB
		if ( !count($flavorAssets) )
		{	
			$flavorAssets = self::retrieveReadyByEntryIdAndTag($entryId, flavorParams::TAG_WEB);
		}
		return $flavorAssets;
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
	 * @return flavorAsset
	 */
	public static function retrieveOriginalByEntryId($entryId)
	{
		$c = new Criteria();
		$c->add(assetPeer::ENTRY_ID, $entryId);
		$c->add(assetPeer::IS_ORIGINAL, true);
		
		return assetPeer::doSelectOne($c);
	}
	
	/**
	 * @param string $entryId
	 * @param string $tag tag filter
	 * @return flavorAsset
	 */
	public static function retrieveHighestBitrateByEntryId($entryId, $tag = null)
	{
		$c = new Criteria();
		$c->add(assetPeer::ENTRY_ID, $entryId);
		$c->add(assetPeer::STATUS, flavorAsset::FLAVOR_ASSET_STATUS_READY);
		$flavorTypes = self::retrieveAllFlavorsTypes();
		$c->add(assetPeer::TYPE, $flavorTypes, Criteria::IN);
		
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

	public static function getCacheInvalidationKeys()
	{
		return array(array("flavorAsset:id=%s", self::ID), array("flavorAsset:entryId=%s", self::ENTRY_ID));		
	}
	
	public static function retrieveByFileSync(FileSync $fileSync)
	{
		if ($fileSync->getObjectType() != FileSyncObjectType::ASSET) {
	        return null;
	    }
	    if ($fileSync->getObjectSubType() != asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET) {
	        return null;
	    }
	    $asset = assetPeer::retrieveById($fileSync->getObjectId());
	    return $asset;
	}
	
	public static function retrieveByEntryIdAndStatus($entryId, $status)
	{
		$c = new Criteria();
		$c->add(assetPeer::ENTRY_ID, $entryId);
		$c->add(assetPeer::STATUS, $status);
		return self::doSelect($c);
	}
	
	public static function getAtomicColumns()
	{
		return array(assetPeer::STATUS);
	}
}