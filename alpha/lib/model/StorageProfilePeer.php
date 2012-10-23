<?php

/**
 * Subclass for performing query and update operations on the 'storage_profile' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class StorageProfilePeer extends BaseStorageProfilePeer
{
	protected static $class_types_cache = array(
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
			$protocolField = self::translateFieldName(StorageProfilePeer::PROTOCOL, BasePeer::TYPE_COLNAME, BasePeer::TYPE_NUM);
			$storageProfileProtocol = $row[$protocolField];
			if(isset(self::$class_types_cache[$storageProfileProtocol]))
				return self::$class_types_cache[$storageProfileProtocol];
				
			$extendedCls = KalturaPluginManager::getObjectClass(parent::OM_CLASS, $storageProfileProtocol);
			if($extendedCls)
			{
				self::$class_types_cache[$storageProfileProtocol] = $extendedCls;
				return $extendedCls;
			}
			self::$class_types_cache[$storageProfileProtocol] = parent::OM_CLASS;
		}
			
		return parent::OM_CLASS;
	}
	
	public static function alternativeCon($con, $queryDB = kQueryCache::QUERY_DB_UNDEFINED)
	{
		if($con === null)
			$con = myDbHelper::alternativeCon($con);
			
		if($con === null)
			$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL3);
		
		return $con;
	}
	
	public static function retrieveAutomaticByPartnerId($partnerId, $con = null)
	{
		$criteria = new Criteria(StorageProfilePeer::DATABASE_NAME);
		$criteria->add(StorageProfilePeer::PARTNER_ID, array(0, $partnerId), Criteria::IN);
		$criteria->add(StorageProfilePeer::STATUS, StorageProfile::STORAGE_STATUS_AUTOMATIC);

		return StorageProfilePeer::doSelect($criteria, $con);
	}
	
	public static function retrieveExternalByPartnerId($partnerId, $con = null)
	{
		$criteria = new Criteria(StorageProfilePeer::DATABASE_NAME);
		$criteria->add(StorageProfilePeer::PARTNER_ID, $partnerId);
		$criteria->add(StorageProfilePeer::STATUS, array(StorageProfile::STORAGE_STATUS_AUTOMATIC, StorageProfile::STORAGE_STATUS_MANUAL), Criteria::IN);

		return StorageProfilePeer::doSelect($criteria, $con);
	}
	
	public static function getCacheInvalidationKeys()
	{
		return array(array("storageProfile:id=%s", self::ID), array("storageProfile:partnerId=%s", self::PARTNER_ID));		
	}
}
