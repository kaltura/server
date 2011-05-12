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
 * @package plugins.shortLink
 * @subpackage model
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

	protected function calculateId()
	{
		$allChars = '0123456789abcdefghijklmnopqrstuvwxyz';
		$dcChars = str_split($allChars, strlen($allChars) / count(kDataCenterMgr::getAllDcs()));
		
		$dc = kDataCenterMgr::getCurrentDc();
		$dcId = (int) $dc["id"];
		$currentDcChars = $dcChars[$dcId];
		
		for ($i = 0; $i < 10; $i++)
		{
			$dcChar = substr($currentDcChars, rand(0, strlen($currentDcChars) - 1), 1);
			if(!$dcChar)
				$dcChar = '0';
				
			$id = $dcChar . kString::generateStringId(3);
			$existingObject = ShortLinkPeer::retrieveByPK($id);
			
			if ($existingObject)
				KalturaLog::log("id [$id] already exists");
			else
				return $id;
		}
		
		throw new Exception("Could not find unique id for short link");
	}

	public function save(PropelPDO $con = null)
	{
		if ($this->isNew())
			$this->setId($this->calculateId());
			
		parent::save($con);
	}
		
} // ShortLink
