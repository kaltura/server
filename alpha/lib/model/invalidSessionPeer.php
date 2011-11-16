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

	/**
	 * @param      string $ks
	 * @param      PropelPDO $con the connection to use
	 * @return     bool
	 */
	public static function isInvalid($ks, PropelPDO $con = null)
	{
		$ksDecoded = base64_decode($ks);
		list ( $hash , $real_str) = explode ( "|" , $ksDecoded , 2 );
			
		$criteria = new Criteria();
		$criteria->add(invalidSessionPeer::KS, $hash);
		$criteria->addAnd(invalidSessionPeer::KS_VALID_UNTIL, null, Criteria::NOT_EQUAL);
		$cnt = invalidSessionPeer::doCount($criteria, false, $con);
		return ($cnt > 0);
	}
	
	/**
	 * @param      string $ks
	 * @param      PropelPDO $con the connection to use
	 * @return     bool
	 */
	public static function isValidActionsLimit($ks, PropelPDO $con = null)
	{
		$ksDecoded = base64_decode($ks);
		list ( $hash , $real_str) = explode ( "|" , $ksDecoded , 2 );
		
		$criteria = new Criteria();
		$criteria->add(invalidSessionPeer::KS, $hash);
		$cnt = invalidSessionPeer::doSelectOne($criteria, $con);
		
		if ($cnt){
			$currentActionLimit = $cnt->getActionsLimit();
			
			if (!is_null($currentActionLimit) && $currentActionLimit <= 0)
				return false;
			
			$cnt->setActionsLimit($currentActionLimit - 1);
			$cnt->save();
		}
		
		return true;
		
	}
	
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
		$invalidSession->save();
		
		return $invalidSession;
	}

	
	
	/**
	 * @param      ks $ks
	 * @return     invalidSession
	 */
	public static function invalidateKs(ks $ks, PropelPDO $con = null)
	{
		$criteria = new Criteria();
		$criteria->add(invalidSessionPeer::KS, $ks->getHash());
		$invalidSession = invalidSessionPeer::doSelectOne($criteria, $con);
		
		if(!$invalidSession){
			$invalidSession = new invalidSession();
			$invalidSession->setKs($ks->getHash());
		}
				
		$invalidSession->setKsValidUntil($ks->valid_until);
		$invalidSession->save();
		
		return $invalidSession;
	}
	
} // invalidSessionPeer
