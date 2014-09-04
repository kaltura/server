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
 * @package Core
 * @subpackage model
 */
class invalidSessionPeer extends BaseinvalidSessionPeer {
	
	const SESSION_ID_PRIVILEGE = "sessionId";
	
	/**
	 * @param      ks $ks
	 * @param	   int $limit
	 * @return     invalidSession
	 */
	public static function actionsLimitKs(ks $ks, $limit)
	{
		$invalidSession = new invalidSession();
		$invalidSession->setKs($ks->getHash());
		$invalidSession->setActionsLimit($limit);
		$invalidSession->setKsValidUntil($ks->valid_until);
		$invalidSession->setType(invalidSession::INVALID_SESSION_TYPE_KS);
		$invalidSession->save();
		
		return $invalidSession;
	}
	
	/**
	 * @param      ks $ks
	 * @return     invalidSession
	 */
	public static function invalidateKs(ks $ks, PropelPDO $con = null)
	{
		$result = self::invalidateByKey($ks->getHash(), invalidSession::INVALID_SESSION_TYPE_KS, $ks->valid_until, $con);
		$sessionId = $ks->getPrivilegeValue(self::SESSION_ID_PRIVILEGE);
		if($sessionId) {
			self::invalidateByKey($sessionId, invalidSession::INVALID_SESSION_TYPE_SESSION_ID, $ks->valid_until, $con);
		}
		
		return $result;
	}
	
	protected static function invalidateByKey($key, $type, $validUntil, PropelPDO $con = null) {
		$criteria = new Criteria();
		$criteria->add(invalidSessionPeer::KS, $key);
		$criteria->add(invalidSessionPeer::TYPE, $type);
		$invalidSession = invalidSessionPeer::doSelectOne($criteria, $con);
		
		if(!$invalidSession){
			$invalidSession = new invalidSession();
			$invalidSession->setKs($key);
			$invalidSession->setType($type);
			$invalidSession->setKsValidUntil($validUntil);
		}
		
		$invalidSession->setActionsLimit(null);
		$invalidSession->save();
		
		return $invalidSession;
	}
	
	public static function getCacheInvalidationKeys()
	{
		return array(array("invalidSession:ks=%s", self::KS));		
	}
	
} // invalidSessionPeer
