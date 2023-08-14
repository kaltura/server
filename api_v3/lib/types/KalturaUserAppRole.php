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
		$puserId = trim($this->userId);
		$appGuid = trim($this->appGuid);
		$userRoleId = trim($this->userRoleId);
		
		$this->verifyMandatoryParams($puserId, $appGuid, $userRoleId);
		
		$partnerId = kCurrentContext::getCurrentPartnerId();
		
		// validate userId exists
		$kuser = kuserPeer::getKuserByPartnerAndUid($partnerId, $puserId);
		
		if (!$kuser)
		{
			throw new KalturaAPIException(KalturaErrors::USER_ID_NOT_FOUND, $puserId);
		}
		
		try
		{
			KuserToUserRolePeer::isValidForInsert($kuser, $appGuid, $userRoleId);
		}
		catch (kCoreException $ex)
		{
			switch ($ex->getCode())
			{
				case kCoreException::USER_APP_ROLE_NOT_ALLOWED_FOR_GROUP:
					throw new KalturaAPIException(KalturaErrors::USER_APP_ROLE_NOT_ALLOWED_FOR_GROUP);
				
				case kCoreException::USER_ROLE_NOT_FOUND:
					throw new KalturaAPIException(KalturaErrors::USER_ROLE_NOT_FOUND);
				
				case kCoreException::INVALID_APP_GUID:
					throw new KalturaAPIException(KalturaErrors::INVALID_APP_GUID, $appGuid);
				
				case kCoreException::USER_APP_ROLE_ALREADY_EXISTS:
					throw new KalturaAPIException(KalturaErrors::USER_APP_ROLE_ALREADY_EXISTS, $kuser->getPuserId(), $appGuid);
				
				default:
					throw $ex;
			}
		}
		
		if (is_null($kuserToUserRole))
		{
			$kuserToUserRole = new KuserToUserRole();
		}
		
		$kuserToUserRole = parent::toInsertableObject($kuserToUserRole, $skip);
		
		/* @var KuserToUserRole $kuserToUserRole */
		$kuserToUserRole->setKuserId($kuser->getId());
		$kuserToUserRole->setAppGuid($appGuid);
		$kuserToUserRole->setUserRoleId($userRoleId);
		
		return $kuserToUserRole;
	}
	
	public function toUpdatableObject($kuserToUserRole, $skip = array())
	{
		return parent::toUpdatableObject($kuserToUserRole, $skip);
	}
	
	public function doFromObject($kuserToUserRoleObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		/* @var KuserToUserRole $kuserToUserRoleObject*/
		if(!$kuserToUserRoleObject)
			return;
		
		parent::doFromObject($kuserToUserRoleObject, $responseProfile);
		
		$this->userId = kuserPeer::retrieveByPK($kuserToUserRoleObject->getKuserId())->getPuserId();
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