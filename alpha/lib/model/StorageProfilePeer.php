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
	const STORAGE_DEFAULT_OM_CLASS = 'StorageProfile';
	const STORAGE_AMAZON_S3_OM_CLASS = 'AmazonS3StorageProfile';
	
	// cache classes by their type
	protected static $class_types_cache = array(
		StorageProfile::STORAGE_PROTOCOL_FTP => self::STORAGE_DEFAULT_OM_CLASS,
		StorageProfile::STORAGE_PROTOCOL_SCP => self::STORAGE_DEFAULT_OM_CLASS,
		StorageProfile::STORAGE_PROTOCOL_SFTP => self::STORAGE_DEFAULT_OM_CLASS,
		StorageProfile::STORAGE_PROTOCOL_S3 => self::STORAGE_AMAZON_S3_OM_CLASS,
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
	
	public static function retrieveAutomaticByPartnerId($partnerId, $con = null)
	{
		$criteria = new Criteria(StorageProfilePeer::DATABASE_NAME);
		$criteria->add(StorageProfilePeer::PARTNER_ID, $partnerId);
		$criteria->add(StorageProfilePeer::STATUS, StorageProfile::STORAGE_STATUS_AUTOMATIC);

		return StorageProfilePeer::doSelect($criteria, $con);
	}

	public static function retrieveExternalByPartnerId($partnerId, $ids = null, $con = null)
	{
		$criteria = new Criteria(StorageProfilePeer::DATABASE_NAME);
		$criteria->add(StorageProfilePeer::PARTNER_ID, $partnerId);
		$criteria->add(StorageProfilePeer::STATUS, array(StorageProfile::STORAGE_STATUS_AUTOMATIC, StorageProfile::STORAGE_STATUS_MANUAL), Criteria::IN);
		if (!is_null($ids) && !(empty($ids)))
			$criteria->add(StorageProfilePeer::ID, $ids, Criteria::IN);	
		return StorageProfilePeer::doSelect($criteria, $con);
	}

	public static function retrieveByIdAndPartnerId($storageId, $partnerId, $con = null)
	{
		$criteria = new Criteria(StorageProfilePeer::DATABASE_NAME);
		$criteria->add(StorageProfilePeer::ID, $storageId);
		$criteria->add(StorageProfilePeer::PARTNER_ID, $partnerId);
		
		return StorageProfilePeer::doSelectOne($criteria, $con);
	}
	
	public static function getCacheInvalidationKeys()
	{
		return array(array("storageProfile:id=%s", self::ID), array("storageProfile:partnerId=%s", self::PARTNER_ID));		
	}
}
