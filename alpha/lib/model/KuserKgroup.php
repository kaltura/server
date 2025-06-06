<?php


/**
 * Skeleton subclass for representing a row from the 'kuser_kgroup' table.
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
class KuserKgroup extends BaseKuserKgroup implements IRelatedObject
{
	const GROUP_USER_CREATION_MODE = 'creation_mode';
	const GROUP_USER_ROLE = 'user_role';
	const GROUP_TYPE = 'group_type';

	public function setPuserId($puserId)
	{
		if ( self::getPuserId() == $puserId )  // same value - don't set for nothing
			return;

		parent::setPuserId($puserId);

		$partnerId = kCurrentContext::getCurrentPartnerId();

		$kuser = kuserPeer::getKuserByPartnerAndUid($partnerId, $puserId);
		if (!$kuser)
			throw new kCoreException("Invalid user Id [{$puserId}]", kCoreException::INVALID_USER_ID );

		parent::setKuserId($kuser->getId());
	}

	public function setPgroupId($pgroupId)
	{
		if ( self::getPgroupId() == $pgroupId )  // same value - don't set for nothing
			return;

		parent::setPgroupId($pgroupId);

		$partnerId = kCurrentContext::getCurrentPartnerId();

		$kgroup = kuserPeer::getKuserByPartnerAndUid($partnerId, $pgroupId, false);
		if (!$kgroup)
			throw new kCoreException("Invalid group Id [{$pgroupId}]", kCoreException::INVALID_USER_ID );

		parent::setKgroupId($kgroup->getId());
	}

	public function getCacheInvalidationKeys()
	{
		return array("kuserKgroup:kuserId=".strtolower($this->getKuserId()), "kuserKgroup:kgroupId=".strtolower($this->getKgroupId()));
	}

	public function postInsert(PropelPDO $con = null)
	{
		parent::postInsert($con);

		if (!$this->alreadyInSave)
			$this->updateKuserIndex();
	}

	public function postUpdate(PropelPDO $con = null)
	{
		parent::postUpdate($con);

		if (!$this->alreadyInSave)
			$this->updateKuserIndex();
	}

	protected function updateKuserIndex()
	{
		$kuserId = $this->getKuserId();
		$kuser = kuserPeer::retrieveByPK($kuserId);
		if(!$kuser)
			throw new kCoreException('kuser not found');
		$kuser->indexToElastic();
	}

	public function setCreationMode($v)	{$this->putInCustomData (self::GROUP_USER_CREATION_MODE, $v);}

	public function getCreationMode(){return $this->getFromCustomData(self::GROUP_USER_CREATION_MODE,
		null, GroupUserCreationMode::MANUAL);}

	public function setUserRole($v)
	{
		$this->putInCustomData (self::GROUP_USER_ROLE, $v);
	}

	public function getUserRole()
	{
		return $this->getFromCustomData(self::GROUP_USER_ROLE, null, GroupUserRole::MEMBER);
	}

	public function setGroupType($v)
	{
		$this->putInCustomData(self::GROUP_TYPE, $v);
	}

	public function getGroupType()
	{
		return $this->getFromCustomData(self::GROUP_TYPE, null, GroupType::GROUP);
	}
}
