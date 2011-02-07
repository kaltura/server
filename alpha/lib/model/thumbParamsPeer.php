<?php

/**
 * Subclass for performing query and update operations on the 'flavor_params' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class thumbParamsPeer extends assetParamsPeer
{
	/** the related Propel class for this table */
	const OM_CLASS = 'thumbParams';
	
	/**
	 * @var thumbParamsPeer
	 */
	private static $myInstance;
		
	public function setInstanceCriteriaFilter ()
	{
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
		$c->add(self::TYPE, assetType::THUMBNAIL);
			
		self::$s_criteria_filter->setFilter ( $c );
	}

	private function __construct()
	{
	}

	public static function getInstance()
	{
		if(!self::$myInstance)
			self::$myInstance = new thumbParamsPeer();
			
		if(!self::$instance || !(self::$instance instanceof thumbParamsPeer))
			self::$instance = self::$myInstance;
			
		return self::$myInstance;
	}

	public static function doCount(Criteria $criteria, $distinct = false, PropelPDO $con = null)
	{
		self::getInstance();
		return parent::doCount($criteria, $distinct, $con);
	}
	
	public static function doSelect(Criteria $criteria, PropelPDO $con = null)
	{
		self::getInstance();
		return parent::doSelect($criteria, $con);	
	}
	
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		self::getInstance();
		return parent::doSelectOne($criteria, $con);	
	}
	
	public static function doSelectStmt(Criteria $criteria, PropelPDO $con = null)
	{
		self::getInstance();
		return parent::doSelectStmt($criteria, $con);	
	}
	
	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     thumbParams
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{
		self::getInstance();
		return parent::retrieveByPK($pk, $con);
	}

	/**
	 * Retrieve multiple objects by pkey.
	 *
	 * @param      array $pks List of primary keys
	 * @param      PropelPDO $con the connection to use
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function retrieveByPKs($pks, PropelPDO $con = null)
	{
		self::getInstance();
		return parent::retrieveByPKs($pks, $con);
	}
}
