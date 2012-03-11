<?php


/**
 * Skeleton subclass for representing a row from the 'category_kuser' table.
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
class categoryKuser extends BasecategoryKuser {

	public function getKuserId()
	{
		$puser = kuserPeer::retrieveByPK($this->id);
		if (!$puser)
			return null;
			
		return $puser->getId();
	}
	
	public function setKuserId($puserId)
	{
		$kuser = kuserPeer::getKuserByPartnerAndUid(getKuserByPartnerAndUid);
		if (!$kuser)
			return;
			
		$this->setKuserId($kuser->getId());
	}
} // categoryKuser
