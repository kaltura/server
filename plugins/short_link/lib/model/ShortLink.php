<?php


/**
 * Skeleton subclass for representing a row from the 'short_link' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.model
 */
class ShortLink extends BaseShortLink {

	protected $puserId;
	
	/**
	 * @return string $puserId
	 */
	public function getPuserId()
	{
		if(!$this->puserId)
		{
			if(!$this->getKuserId())
				return null;
				
			$kuser = kuserPeer::retrieveByPK($this->getKuserId());
			$this->puserId = $kuser->getPuserId();
		}
		
		return $this->puserId;
	}

	/**
	 * Set the puser id and the kuser id
	 * If the kuser doesn't exist it will be created
	 * @param string $puserId
	 */
	public function setPuserId($puserId)
	{
		if(!$this->getPartnerId())
			throw new Exception("Partner id must be set in order to load puser [$puserId]");
			
		$this->puserId = $puserId;
		$kuser = kuserPeer::getKuserByPartnerAndUid($this->getPartnerId(), $puserId, true);
		if(!$kuser)
		{
			$isAdmin = kCurrentContext::$is_admin_session;
			$kuser = kuserPeer::createKuserForPartner($this->getPartnerId(), $puserId, $isAdmin);
		}
		$this->setKuserId($kuser->getId());
	}

	/* (non-PHPdoc)
	 * @see BaseShortLink::postUpdate()
	 */
	public function postUpdate(PropelPDO $con = null)
	{
		$objectDeleted = false;
		if($this->isColumnModified(ShortLinkPeer::STATUS) && $this->getStatus() == ShortLinkStatus::DELETED)
			$objectDeleted = true;
			
		$ret = parent::postUpdate($con);
		
		if ($objectDeleted)
			kEventsManager::raiseEvent(new kObjectDeletedEvent($this));
			
		return $ret;
	}
		
} // ShortLink
