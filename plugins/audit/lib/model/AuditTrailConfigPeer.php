<?php


/**
 * Skeleton subclass for performing query and update operations on the 'audit_trail_config' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.model
 */
class AuditTrailConfigPeer extends BaseAuditTrailConfigPeer {


	/**
	 * Retrieve multiple objects by partner id.
	 *
	 * @param      int $partnerId
	 * @param      PropelPDO $con the connection to use
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function retrieveByPartnerId($partnerId, PropelPDO $con = null)
	{
		$criteria = new Criteria();
		$criteria->add(AuditTrailConfigPeer::PARTNER_ID, $partnerId);
		return AuditTrailConfigPeer::doSelect($criteria, $con);
	}
	
} // AuditTrailConfigPeer
