<?php

/**
 * Subclass for performing query and update operations on the 'flavor_params' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class assetParamsPeer extends BaseassetParamsPeer
{
	// cache classes by their type
	protected static $class_types_cache = array(
		assetType::FLAVOR => flavorParamsPeer::OM_CLASS,
		assetType::THUMBNAIL => thumbParamsPeer::OM_CLASS,
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
			$c->remove(self::DELETED_AT);
			$c->remove(self::TYPE);
		}
		else
		{
			$c = new Criteria();
		}

		$c->add(self::DELETED_AT, null, Criteria::EQUAL);

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
			assetParamsPeer::setDefaultCriteriaFilter();
			
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
			$assetType = $row[$colnum + 33]; // type column
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
	
	public static function alternativeCon($con)
	{
		if($con === null)
			$con = myDbHelper::alternativeCon($con);
			
		if($con === null)
			$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL3);
		
		return $con;
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
	 * @param int $conversionProfileId
	 * @param string $tag
	 * @param $con
	 * @return array<flavorParamsOutput>
	 */
	public static function retrieveByProfileAndTag($conversionProfileId, $tag, $con = null)
	{
		$flavorIds = flavorParamsConversionProfilePeer::getFlavorIdsByProfileId($conversionProfileId);
		
		$criteria = new Criteria();
		$criteria->add(flavorParamsPeer::ID, $flavorIds, Criteria::IN);

		$flavorParams = flavorParamsPeer::doSelect($criteria, $con);
		
		$ret = array();
		
		foreach($flavorParams as $flavorParamsItem)
			if($flavorParamsItem->hasTag($tag))
				$ret[] = $flavorParamsItem;
		
		return $ret;
	}
	
	/**
	 * @param int $conversionProfileId
	 * @param $con
	 * @return array<flavorParamsOutput>
	 */
	public static function retrieveByProfile($conversionProfileId, $con = null)
	{
		$flavorIds = flavorParamsConversionProfilePeer::getFlavorIdsByProfileId($conversionProfileId);
		
		$criteria = new Criteria();
		$criteria->add(flavorParamsPeer::ID, $flavorIds, Criteria::IN);

		return flavorParamsPeer::doSelect($criteria, $con);
	}
}
