<?php

/**
 * Subclass for performing query and update operations on the 'access_control' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class accessControlPeer extends BaseaccessControlPeer
{
	public static function alternativeCon($con, $queryDB = kQueryCache::QUERY_DB_UNDEFINED)
	{
		if($con === null)
			$con = myDbHelper::alternativeCon($con);
			
		if($con === null)
			$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL3);
		
		return $con;
	}
	
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

	/**
	 * @param int $pk
	 * @param PropelPDO $con
	 * @return accessControl
	 */
	public static function retrieveByPKNoFilter($pk, PropelPDO $con = null)
	{
		self::setUseCriteriaFilter ( false );
		$res = parent::retrieveByPK( $pk , $con );
		self::setUseCriteriaFilter ( true );
		return $res;
	}

	/**
	 * @param array $pk
	 * @param PropelPDO $con
	 * @return array<accessControl>
	 */
	public static function retrieveByPKsNoFilter($pks, PropelPDO $con = null)
	{
		self::setUseCriteriaFilter ( false );
		$res = parent::retrieveByPKs( $pks , $con );
		self::setUseCriteriaFilter ( true );
		return $res;
	}
	
	public static function getCacheInvalidationKeys()
	{
		return array(array("accessControl:id=%s", self::ID));		
	}
}