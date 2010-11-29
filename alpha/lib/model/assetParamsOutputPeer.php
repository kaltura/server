<?php

/**
 * Subclass for performing query and update operations on the 'flavor_params_output' table.
 *
 * 
 *
 * @package lib.model
 */ 
abstract class assetParamsOutputPeer extends BaseassetParamsOutputPeer
{
	// cache classes by their type
	protected static $class_types_cache = array(
		assetType::FLAVOR => flavorParamsOutputPeer::OM_CLASS,
		assetType::THUMBNAIL => thumbParamsOutputPeer::OM_CLASS,
	);
	
	/**
	 * @var assetParamsPeer
	 */
	protected static $instance = null;

	abstract public function setInstanceCriteriaFilter();
	
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
	 * @param string $entryId
	 * @param string $tag
	 * @param $con
	 * @return array<flavorParamsOutput>
	 */
	public static function retrieveByEntryIdAndTag($entryId, $tag, $con = null)
	{
		$criteria = new Criteria();

		$criteria->add(flavorParamsOutputPeer::ENTRY_ID, $entryId);
		$criteria->addDescendingOrderByColumn(flavorParamsOutputPeer::FLAVOR_ASSET_VERSION);

		$flavorParamsOutputs = flavorParamsOutputPeer::doSelect($criteria, $con);
		
		$ret = array();
		
		foreach($flavorParamsOutputs as $flavorParamsOutput)
			if($flavorParamsOutput->hasTag($tag))
				$ret[] = $flavorParamsOutput;
		
		return $ret;
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
}
