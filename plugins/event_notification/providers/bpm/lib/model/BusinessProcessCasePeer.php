<?php


/**
 * Skeleton subclass for performing query and update operations on the 'business_process_case' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.businessProcessNotification
 * @subpackage model
 */
class BusinessProcessCasePeer extends BaseBusinessProcessCasePeer {

	public static function getOMClass($withPrefix = true)
	{
		return parent::OM_CLASS;
	}

	/**
	 * @param mixed $objectId 
	 * @param int $objectType
	 * @param int $partnerId
	 * 
	 * @return array
	 */
	public static function retrieveCasesByObjectIdObjecType ($objectId, $objectType, $partnerId = null)
	{
		KalturaLog::info ("Retrieving cases for object ID [$objectId], object type [$objectType]");
		$criteria = new Criteria ();
		if(!is_null($partnerId))
			$criteria->add (BusinessProcessCasePeer::PARTNER_ID, $partnerId);
		
		$criteria->add (BusinessProcessCasePeer::OBJECT_ID, $objectId);
		$criteria->add (BusinessProcessCasePeer::OBJECT_TYPE, $objectType);
		
		return self::doSelect($criteria);
	}
	
	/**
	 * @param mixed $objectId 
	 * @param int $objectType
	 * @param int $serverId
	 * @param string $processId
	 * @param int $partnerId
	 * 
	 * @return array
	 */
	public static function retrieveCasesByObjectIdObjectTypeProcessIdServerId ($objectId, $objectType, $serverId, $processId, $partnerId = null)
	{
		KalturaLog::info ("Retrieving cases for object ID [$objectId], object type [$objectType], server Id [$serverId], process ID [$processId]");
		$criteria = new Criteria ();
		$criteria->add(BusinessProcessCasePeer::SERVER_ID, $serverId);
		$criteria->add(BusinessProcessCasePeer::PROCESS_ID, $processId);
		$criteria->add(BusinessProcessCasePeer::OBJECT_ID, $objectId);
		$criteria->add(BusinessProcessCasePeer::OBJECT_TYPE, $objectType);
		
		if (!is_null($partnerId))
			$criteria->add(BusinessProcessCasePeer::PARTNER_ID, $partnerId);
		
		return self::doSelect($criteria);
	}
} // BusinessProcessCasePeer
