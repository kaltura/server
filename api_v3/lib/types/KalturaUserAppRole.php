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
	
	private static $map_between_objects = array
	(
		"userId" => "puserId"
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
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
		
		return parent::toInsertableObject($kuserToUserRole, $skip);
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
		
		KalturaUserAppRole::isValidForInsert($kuser, $this->appGuid, $this->userRoleId);
		
		parent::validateForInsert($propertiesToSkip);
	}
	
	/**
	 * @throws KalturaAPIException
	 * @throws kCoreException
	 */
	public function validateForUpdate($kuserToUserRole, $propertiesToSkip = array())
	{
		if (!$this->userRoleId)
		{
			throw new KalturaAPIException(KalturaErrors::MISSING_MANDATORY_PARAMETER, 'userRoleId');
		}
		
		// validate userRoleId exist
		$userRole = UserRolePeer::retrieveByPK($this->userRoleId);
		
		if (!$userRole)
		{
			throw new KalturaAPIException(KalturaErrors::USER_ROLE_NOT_FOUND);
		}
		
		return parent::validateForUpdate($kuserToUserRole, $propertiesToSkip);
	}
	
	/**
	 * @throws kCoreException
	 */
	public function doFromObject($kuserToUserRoleObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		/* @var KuserToUserRole $kuserToUserRoleObject*/
		if(!$kuserToUserRoleObject)
		{
			return null;
		}
		
		parent::doFromObject($kuserToUserRoleObject, $responseProfile);
		
		$this->userId = $kuserToUserRoleObject->getPuserId();
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
	
	
	/**
	 * @param kuser $kuser
	 * @param string $appGuid
	 * @param int $userRoleId
	 * @return void
	 *
	 * @throws KalturaAPIException
	 * @throws kCoreException
	 * @throws PropelException
	 */
	private static function isValidForInsert(kuser $kuser, $appGuid, $userRoleId)
	{
		// groups are not supported
		if ($kuser->getType() === KuserType::GROUP)
		{
			throw new KalturaAPIException(KalturaErrors::USER_APP_ROLE_NOT_ALLOWED_FOR_GROUP);
		}
		
		// validate appGuid string
		if (!kString::isValidMongoId($appGuid))
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_APP_GUID, $appGuid);
		}
		
		// validate appGuid belong to ks partner
		$appGuidExist = MicroServiceAppRegistry::getExistingAppGuid(kCurrentContext::getCurrentPartnerId(), $appGuid);
		if (!$appGuidExist)
		{
			throw new KalturaAPIException(KalturaErrors::APP_GUID_NOT_FOUND, $appGuid);
		}
		
		// validate userRoleId exist
		$userRole = UserRolePeer::retrieveByPK($userRoleId);
		
		if (!$userRole)
		{
			throw new KalturaAPIException(KalturaErrors::USER_ROLE_NOT_FOUND);
		}
		
		// validate user does not have a role for the requested appGuid
		$userAppRole = KuserToUserRolePeer::getByKuserIdAndAppGuid($kuser->getId(), $appGuid);
		if ($userAppRole)
		{
			throw new KalturaAPIException(KalturaErrors::USER_APP_ROLE_ALREADY_EXISTS, $kuser->getPuserId(), $appGuid);
		}
	}
}

