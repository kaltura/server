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

	public static function retrieveCasesByObjectIdObjecType ($objectId, $objectType, $partnerId)
	{
		KalturaLog::info ("Retrieving cases for object ID [$objectId], object type [$objectType]");
		$criteria = new Criteria ();
		$criteria->add (BusinessProcessCasePeer::PARTNER_ID, $partnerId);
		$criteria->add (BusinessProcessCasePeer::OBJECT_ID, $objectId);
		$criteria->add (BusinessProcessCasePeer::OBJECT_TYPE, $objectType);
		
		return self::doSelect($criteria);
	}
} // BusinessProcessCasePeer
