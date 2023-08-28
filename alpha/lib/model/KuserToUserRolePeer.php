<?php


/**
 * Skeleton subclass for performing query and update operations on the 'kuser_to_user_role' table.
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
class KuserToUserRolePeer extends BaseKuserToUserRolePeer implements IRelatedObjectPeer
{
	
	/**
	 * Creates default criteria filter
	 * To keep backward compatibility, do not fetch roles where app_guid is null
	 */
	public static function setDefaultCriteriaFilter()
	{
		if(self::$s_criteria_filter == null)
			self::$s_criteria_filter = new criteriaFilter();

		// to keep backward compatibility, do not retrieve results where app_guid !== null
		$c =  KalturaCriteria::create(KuserToUserRolePeer::OM_CLASS);
		$c->addAnd(KuserToUserRolePeer::APP_GUID, null, Criteria::ISNULL);

		self::$s_criteria_filter->setFilter($c);
	}
	
	/**
	 * Get objects by kuser and user role IDs
	 * @param int $kuserId
	 * @param int $userRoleId
	 * @return array Array of selected KuserToUserRole Objects
	 */
	public static function getByKuserAndUserRoleIds($kuserId, $userRoleId)
	{
		$c = new Criteria();
		$c->addAnd(self::KUSER_ID, $kuserId, Criteria::EQUAL);
		$c->addAnd(self::USER_ROLE_ID, $userRoleId, Criteria::EQUAL);
		return self::doSelect($c);
	}
	
	public static function getCacheInvalidationKeys()
	{
		return array(array("kuserToUserRole:kuserId=%s", self::KUSER_ID), array("kuserToUserRole:id=%s", self::ID));		
	}
	
	/* (non-PHPdoc)
	 * @see IRelatedObjectPeer::getRootObjects()
	 */
	public function getRootObjects(IRelatedObject $object)
	{
		/* @var $object KuserToUserRole */
		return array(
			$object->getkuser(),
			$object->getUserRole(),
		);
	}

	/* (non-PHPdoc)
	 * @see IRelatedObjectPeer::isReferenced()
	 */
	public function isReferenced(IRelatedObject $object)
	{
		return false;
	}
	
	/**
	 * Get object by kuser and appGuid
	 *
	 * @param $kuserId
	 * @param $appGuid
	 *
	 * @return KuserToUserRole|null
	 * @throws PropelException
	 */
	public static function getByKuserIdAndAppGuid($kuserId, $appGuid)
	{
		$c = new Criteria();
		$c->addAnd(self::KUSER_ID, $kuserId, Criteria::EQUAL);
		$c->addAnd(self::APP_GUID, $appGuid, Criteria::EQUAL);
		
		self::setUseCriteriaFilter(false);
		$res = self::doSelectOne($c);
		self::setUseCriteriaFilter(true);
		
		return $res;
	}
	
	/**
	 * @param $puserId
	 * @param $appGuid
	 * @return KuserToUserRole
	 *
	 * @throws kCoreException
	 * @throws PropelException
	 */
	public static function getByPuserIdAndAppGuid($puserId, $appGuid)
	{
		$puserId = trim($puserId);
		$appGuid = trim($appGuid);
		
		if (!kCurrentContext::$is_admin_session && kCurrentContext::$ks_uid != $puserId)
		{
			throw new kCoreException('[Cannot Retrieve Another User Using Non Admin Session]', kCoreException::CANNOT_RETRIEVE_ANOTHER_USER_USING_NON_ADMIN_SESSION, $puserId);
		}
		
		$kuser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::getCurrentPartnerId(), $puserId);
		
		if (!$kuser)
		{
			throw new kCoreException('[User ID Not Found]', kCoreException::INVALID_USER_ID, $puserId);
		}
		
		if (!kString::isValidMongoId($appGuid))
		{
			throw new kCoreException('[Invalid App Guid]', kCoreException::INVALID_APP_GUID, $appGuid);
		}
		
		// validate appGuid belong to ks partner
		$appRegistry = KuserToUserRolePeer::getAppGuidById($appGuid);
		if (!$appRegistry)
		{
			throw new kCoreException('[App Guid Not Found]', kCoreException::APP_GUID_NOT_FOUND, $appGuid);
		}
		
		$dbUserAppRole = KuserToUserRolePeer::getByKuserIdAndAppGuid($kuser->getId(), $appGuid);
		
		if (!$dbUserAppRole)
		{
			throw new kCoreException('[User App Role Not Found]', kCoreException::USER_APP_ROLE_NOT_FOUND, "$puserId,$appGuid");
		}
		
		return $dbUserAppRole;
	}
	
	/**
	 * @param kuser $kuser
	 * @param string $appGuid
	 * @param int $userRoleId
	 * @return void
	 *
	 * @throws kCoreException
	 * @throws PropelException
	 */
	public static function isValidForInsert(kuser $kuser, $appGuid, $userRoleId)
	{
		// groups are not supported
		if ($kuser->getType() === KuserType::GROUP)
		{
			throw new kCoreException('[User App Role Not Allowed For Group]', kCoreException::USER_APP_ROLE_NOT_ALLOWED_FOR_GROUP);
		}
		
		// validate userRoleId exist
		$userRole = UserRolePeer::retrieveByPK($userRoleId);
		
		if (!$userRole)
		{
			throw new kCoreException('[User Role Not Found]', kCoreException::USER_ROLE_NOT_FOUND);
		}
		
		// validate appGuid string
		if (!kString::isValidMongoId($appGuid))
		{
			throw new kCoreException('[Invalid App Guid]', kCoreException::INVALID_APP_GUID, $appGuid);
		}
		
		// validate appGuid belong to ks partner
		$appRegistry = KuserToUserRolePeer::getAppGuidById($appGuid);
		if (!$appRegistry)
		{
			throw new kCoreException('[App Guid Not Found]', kCoreException::APP_GUID_NOT_FOUND, $appGuid);
		}
		
		// validate user does not have a role for the requested appGuid
		$userAppRole = KuserToUserRolePeer::getByKuserIdAndAppGuid($kuser->getId(), $appGuid);
		if ($userAppRole)
		{
			$puserId = $kuser->getPuserId();
			throw new kCoreException('[User App Role Already Exists]', kCoreException::USER_APP_ROLE_ALREADY_EXISTS, "$puserId,$appGuid");
		}
	}
	
	public static function getAppGuidById($appGuid)
	{
		$appRegistryClient = new MicroServiceAppRegistry();
		$appRegistry = $appRegistryClient->get(kCurrentContext::getCurrentPartnerId(), $appGuid);
		
		if (isset($appRegistry->code) && $appRegistry->code == 'OBJECT_NOT_FOUND')
		{
			return false;
		}
		
		return $appRegistry;
	}
	
	public static function getKsPartnerAppGuidsFromCsv($appGuidsCsv)
	{
		$appGuidsResult = array();
		$appGuidsArray = explode(',', $appGuidsCsv);
		
		$filter = array(
			'idIn' => $appGuidsArray
		);
		
		$pager = array(
			'offset' => 0,
			'limit' => count($appGuidsArray)
		);
		
		$appRegistryClient = new MicroServiceAppRegistry();
		$appRegistries = $appRegistryClient->list(kCurrentContext::getCurrentPartnerId(), $filter, $pager);
		
		foreach ($appRegistries->objects as $appRegistry)
		{
			if (isset($appRegistry->id))
			{
				$appGuidsResult[] = $appRegistry->id;
			}
		}
		
		return count($appGuidsResult) ? implode(',', $appGuidsResult) : 'null'; // set to 'null' will not return results (if set to '' 'attachToCriteria' will remove it from mysql query)
	}
	
} // KuserToUserRolePeer
