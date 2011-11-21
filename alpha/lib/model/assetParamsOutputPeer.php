<?php

/**
 * Subclass for performing query and update operations on the 'flavor_params_output' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class assetParamsOutputPeer extends BaseassetParamsOutputPeer
{
	const FLAVOR_OM_CLASS = 'flavorParamsOutput';
	const THUMBNAIL_OM_CLASS = 'thumbParamsOutput';


	public static function setDefaultCriteriaFilter ()
	{
		if(is_null(self::$s_criteria_filter))
			self::$s_criteria_filter = new criteriaFilter();
		
		$c = new Criteria(); 
		$c->add(self::DELETED_AT, null, Criteria::ISNULL);
		self::$s_criteria_filter->setFilter($c);
	}
	
	// cache classes by their type
	protected static $class_types_cache = array(
		assetType::FLAVOR => assetParamsOutputPeer::FLAVOR_OM_CLASS,
		assetType::THUMBNAIL => assetParamsOutputPeer::THUMBNAIL_OM_CLASS,
	);
	
	/**
	 * @param string $entryId
	 * @param string $tag
	 * @param $con
	 * @return array<flavorParamsOutput>
	 */
	public static function retrieveByEntryIdAndTag($entryId, $tag, $con = null)
	{
		$criteria = new Criteria();

		$criteria->add(assetParamsOutputPeer::ENTRY_ID, $entryId);
		$criteria->addDescendingOrderByColumn(assetParamsOutputPeer::FLAVOR_ASSET_VERSION);

		$flavorParamsOutputs = assetParamsOutputPeer::doSelect($criteria, $con);
		
		$ret = array();
		
		foreach($flavorParamsOutputs as $flavorParamsOutput)
			if($flavorParamsOutput->hasTag($tag))
				$ret[] = $flavorParamsOutput;
		
		return $ret;
	}
	/**
	 * 
	 * @param $assetId
	 * @param $assetVersion
	 * @param $con
	 * 
	 * @return flavorParamsOutput
	 */
	public static function retrieveByAssetId($assetId, $assetVersion = null, $con = null)
	{
		$criteria = new Criteria();

		$criteria->add(assetParamsOutputPeer::FLAVOR_ASSET_ID, $assetId);
		
		if($assetVersion)
		{
			$criteria->add(assetParamsOutputPeer::FLAVOR_ASSET_VERSION, $assetVersion);
		}
		else 
		{
			$criteria->addDescendingOrderByColumn(assetParamsOutputPeer::FLAVOR_ASSET_VERSION);
		}

		return assetParamsOutputPeer::doSelectOne($criteria, $con);
	}
	
	/**
	 * 
	 * @param asset $asset
	 * @param $con
	 * 
	 * @return flavorParamsOutput
	 */
	public static function retrieveByAsset(asset $asset, $con = null)
	{
		return self::retrieveByAssetId($asset->getId(), $asset->getVersion(), $con);
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
			$assetType = $row[$colnum + 37]; // type column
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
	public static function getCacheInvalidationKeys()
	{
		return array(array("flavorParamsOutput:id=%s", self::ID), array("flavorParamsOutput:flavorAssetId=%s", self::FLAVOR_ASSET_ID));		
	}
}
