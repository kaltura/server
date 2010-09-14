<?php

/**
 * Subclass for performing query and update operations on the 'flavor_params' table.
 *
 * 
 *
 * @package lib.model
 */ 
class flavorParamsPeer extends BaseflavorParamsPeer
{
	// cache classes by their type
	private static $class_types_cache = array();
	
	public static function setDefaultCriteriaFilter ()
	{
		if ( self::$s_criteria_filter == null )
		{
			self::$s_criteria_filter = new criteriaFilter ();
		}

		$c = new Criteria();
		$c->add ( self::DELETED_AT, null, Criteria::EQUAL );
		self::$s_criteria_filter->setFilter ( $c );
	}
	
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
	 * Allow access to partner X besides the current session partner
	 */
	public static function allowAccessToSystemDefaultParamsAndPartnerX($partnerXId)
	{
		// remove the partner id from the defualt criteria
		$defaultCriteria = flavorParamsPeer::getCriteriaFilter()->getFilter();
		$defaultCriteria->remove(flavorParamsPeer::PARTNER_ID);
		
		// add partner id or is_default=1
		$crit1 = $defaultCriteria->getNewCriterion( flavorParamsPeer::PARTNER_ID , $partnerXId);
		$crit2 = $defaultCriteria->getNewCriterion ( flavorParamsPeer::IS_DEFAULT , flavorParams::SYSTEM_DEFAULT );
		$crit1->addOr ( $crit2 );
		
		$defaultCriteria->addAnd ( $crit1 );
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
			$flavorParamsFormat = $row[$colnum + 11]; // format
			if(isset(self::$class_types_cache[$flavorParamsFormat]))
				return self::$class_types_cache[$flavorParamsFormat];
			$extendedCls = KalturaPluginManager::getObjectClass(KalturaPluginManager::OBJECT_TYPE_FLAVOR_PARAMS, $flavorParamsFormat);
			if($extendedCls)
			{
				self::$class_types_cache[$flavorParamsFormat] = $extendedCls;
				return $extendedCls;
			}
			self::$class_types_cache[$flavorParamsFormat] = parent::OM_CLASS;
		}
			
		return parent::OM_CLASS;
	}
}
