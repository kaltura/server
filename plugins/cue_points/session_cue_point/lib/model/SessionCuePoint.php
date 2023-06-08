<?php

/**
 * @package plugins.sessionCuePoint
 * @subpackage model
 */
class SessionCuePoint extends CuePoint
{
	const SESSION_OWNER = 'session_owner';
	
	public function __construct()
	{
		parent::__construct();
		$this->applyDefaultValues();
	}
	
	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or equivalent initialization method).
	 * @see __construct()
	 */
	public function applyDefaultValues()
	{
		$this->setType(SessionCuePointPlugin::getCuePointTypeCoreValue(SessionCuePointType::SESSION));
	}
	
	public function copyToClipEntry(entry $clipEntry, $clipStartTime, $clipDuration)
	{
		return false;
	}
	
	public function getSessionOwner()
	{
		$kuserId = $this->getFromCustomData(self::SESSION_OWNER);
		if (!$kuserId)
		{
			return null;
		}
		$kuser = kuserPeer::retrieveByPKNoFilter($kuserId);
		if (!$kuser)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID);
		}
		return $kuser->getPuserId();
	}
	
	public function setSessionOwner($v)
	{
		$kuser = kuserPeer::getKuserByPartnerAndUid($this->getPartnerId(), $v, true);
		if (!$kuser)
		{
			$kuser = kuserPeer::createKuserForPartner($this->getPartnerId(), $v);
		}
		return $this->putInCustomData(self::SESSION_OWNER, $kuser->getId());
	}
}
