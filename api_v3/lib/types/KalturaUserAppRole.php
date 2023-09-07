<?php
/**
 * @package api
 * @subpackage objects
 * @relatedService UserAppRoleService
 */

class KalturaUserAppRole extends KalturaAppRole
{
	/**
	 * @var string
	 * @insertonly
	 */
	public $userId;
	
	/**
	 * @param $kuserToUserRole
	 * @param $skip
	 * @return KuserToUserRole|null
	 *
	 * @throws KalturaAPIException
	 * @throws PropelException
	 * @throws kCoreException
	 */
	public function toInsertableObject($kuserToUserRole = null, $skip = array())
	{
		if (is_null($kuserToUserRole))
		{
			$kuserToUserRole = new KuserToUserRole();
		}
		
		$kuserToUserRole = parent::toInsertableObject($kuserToUserRole, $skip);
		
		/* @var KuserToUserRole $kuserToUserRole */
		$kuserToUserRole->setKuserId($this->kuserId);
		$kuserToUserRole->setAppGuid($this->appGuid);
		$kuserToUserRole->setUserRoleId($this->userRoleId);
		
		return $kuserToUserRole;
	}
	
	/**
	 * @throws PropelException
	 * @throws KalturaAPIException
	 * @throws kCoreException
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->userId = trim($this->userId);
		$this->appGuid = trim($this->appGuid);
		$this->userRoleId = trim($this->userRoleId);
		
		$this->verifyMandatoryParams($this->userId, $this->appGuid, $this->userRoleId);
		
		$partnerId = kCurrentContext::getCurrentPartnerId();
		
		// validate userId exists
		$kuser = kuserPeer::getKuserByPartnerAndUid($partnerId, $this->userId);
		
		if (!$kuser)
		{
			throw new KalturaAPIException(KalturaErrors::USER_ID_NOT_FOUND, $this->userId);
		}
		
		$this->kuserId = $kuser->getId();
		KuserToUserRolePeer::isValidForInsert($kuser, $this->appGuid, $this->userRoleId);
		
		parent::validateForInsert($propertiesToSkip);
	}
	
	public function toUpdatableObject($kuserToUserRole, $skip = array())
	{
		return parent::toUpdatableObject($kuserToUserRole, $skip);
	}
	
	/**
	 * @throws KalturaAPIException
	 * @throws kCoreException
	 */
	public function validateForUpdate($kuserToUserRole, $propertiesToSkip = array())
	{
		KuserToUserRolePeer::isValidForUpdate($this->userRoleId);
		return parent::validateForUpdate($kuserToUserRole, $propertiesToSkip);
	}
	
	public function doFromObject($kuserToUserRoleObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		/* @var KuserToUserRole $kuserToUserRoleObject*/
		if(!$kuserToUserRoleObject)
		{
			return false;
		}
		
		$this->userId = kuserPeer::retrieveByPK($kuserToUserRoleObject->getKuserId())->getPuserId();
		
		parent::doFromObject($kuserToUserRoleObject, $responseProfile);
	}
	
	private function verifyMandatoryParams($puserId, $appGuid ,$userRoleId)
	{
		$param = false;
		
		if (!$puserId)
		{
			$param = 'userId';
		}
		elseif (!$appGuid)
		{
			$param = 'appGuid';
		}
		elseif (!$userRoleId)
		{
			$param = 'userRoleId';
		}
		
		if ($param)
		{
			throw new KalturaAPIException(KalturaErrors::MISSING_MANDATORY_PARAMETER, $param);
		}
	}
}
