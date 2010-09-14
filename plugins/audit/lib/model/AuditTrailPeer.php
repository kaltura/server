<?php


/**
 * Skeleton subclass for performing query and update operations on the 'audit_trail' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.model
 */
class AuditTrailPeer extends BaseAuditTrailPeer {

	/**
	 * Retrieve multiple objects by request id.
	 *
	 * @param      string $requestId
	 * @param      PropelPDO $con the connection to use
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function retrieveByRequestId($requestId, PropelPDO $con = null)
	{
		$criteria = new Criteria();
		$criteria->add(AuditTrailPeer::REQUEST_ID, $requestId);
		return AuditTrailPeer::doSelect($criteria, $con);
	}
	
} // AuditTrailPeer
