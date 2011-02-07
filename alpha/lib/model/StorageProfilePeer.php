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
	public static function alternativeCon($con)
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
}
