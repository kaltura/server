<?php


/**
 * Skeleton subclass for representing a row from the 'kuser_to_user_role' table.
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
class KuserToUserRole extends BaseKuserToUserRole {
	
	public function getCacheInvalidationKeys()
	{
		return array("kuserToUserRole:kuserId=".strtolower($this->getKuserId()), "kuserToUserRole:id=".strtolower($this->getId()));
	}
	
	public function setPuserId($puserId)
	{
		$kuser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::getCurrentPartnerId(), $puserId);
		
		if (!$kuser)
		{
			throw new kCoreException('[User ID Not Found]', kCoreException::INVALID_USER_ID, $puserId);
		}
		
		parent::setKuserId($kuser->getId());
	}
	
	public function getPuserId()
	{
		if (!$this->getKuserId())
		{
			return false;
		}
		
		$kuser = kuserPeer::retrieveByPK($this->getKuserId());
		
		if (!$kuser)
		{
			throw new kCoreException('[User ID Not Found]', kCoreException::INVALID_USER_ID);
		}
		
		return $kuser->getPuserId();
	}
	
	public function postDelete(PropelPDO $con = null)
	{
		kQueryCache::invalidateQueryCache($this);
		parent::postDelete($con);
	}
	
} // KuserToUserRole
