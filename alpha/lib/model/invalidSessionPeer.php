<?php


/**
 * Skeleton subclass for performing query and update operations on the 'invalid_session' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.model
 */
class invalidSessionPeer extends BaseinvalidSessionPeer {

	/**
	 * @param      string $ks
	 * @param      PropelPDO $con the connection to use
	 * @return     bool
	 */
	public static function isInvalid($ks, PropelPDO $con = null)
	{
		$criteria = new Criteria();
		$criteria->add(invalidSessionPeer::KS, base64_decode($ks));

		$cnt = invalidSessionPeer::doCount($criteria, $con);
		return ($cnt > 0);
	}
	
	/**
	 * @param      ks $ks
	 * @return     invalidSession
	 */
	public static function invalidateKs(ks $ks)
	{
		$invalidSession = new invalidSession();
		$invalidSession->setKs(base64_decode($ks->getOriginalString()));
		$invalidSession->setKsValidUntil($ks->valid_until);
		$invalidSession->save();
		
		return $invalidSession;
	}
	
} // invalidSessionPeer
