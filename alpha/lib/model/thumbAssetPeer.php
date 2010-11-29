<?php

/**
 * Subclass for performing query and update operations on the 'flavor_asset' table.
 *
 * 
 *
 * @package lib.model
 */ 
class thumbAssetPeer extends assetPeer
{
	/** the related Propel class for this table */
	const OM_CLASS = 'thumbAsset';
	
	/**
	 * @var thumbAssetPeer
	 */
	private static $myInstance;
		
	public function setInstanceCriteriaFilter ()
	{
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
		$c->add(self::TYPE, assetType::THUMBNAIL);
			
		self::$s_criteria_filter->setFilter ( $c );
	}

	private function __construct()
	{
	}

	public static function getInstance()
	{
		if(!self::$myInstance)	
			self::$myInstance = new thumbAssetPeer();
			
		if(!self::$instance || !(self::$instance instanceof thumbAssetPeer))
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
	 * @return     thumbAsset
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{
		self::getInstance();
		return parent::retrieveByPK($pk, $con);
	}
	
	/**
	 * Retrieve a single object by id.
	 *
	 * @param      int $id the id.
	 * @param      PropelPDO $con the connection to use
	 * @return     thumbAsset
	 */
	public static function retrieveById($id, $con = null)
	{
		self::getInstance();
		return parent::retrieveById($id, $con);
	}
	
	/**
	 * 
	 * @return thumbAsset
	 */
	public static function retrieveByEntryIdAndParams($entryId, $paramsId)
	{
		self::getInstance();
		return parent::retrieveByEntryIdAndParams($entryId, $paramsId);
	}
}