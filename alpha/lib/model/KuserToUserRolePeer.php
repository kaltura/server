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
//	const APP_GUID_CACHE_KEY_PREFIX = 'appGuid_';
//	const APP_GUID_CACHE_TTL = 86400; // cached for 1 day
	
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
	
	public static function getCacheInvalidationKeys()
	{
		return array(array("kuserToUserRole:kuserId=%s", self::KUSER_ID), array("kuserToUserRole:id=%s", self::ID));
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
	
//	/**
//	 * @param $puserId
//	 * @param $appGuid
//	 * @return KuserToUserRole
//	 *
//	 * @throws kCoreException
//	 * @throws PropelException
//	 */
//	public static function getByPuserIdAndAppGuid($puserId, $appGuid)
//	{
//		$puserId = trim($puserId);
//		$appGuid = trim($appGuid);
//
//		if (!kCurrentContext::$is_admin_session && kCurrentContext::$ks_uid != $puserId)
//		{
//			throw new kCoreException('[Cannot Retrieve Another User Using Non Admin Session]', kCoreException::CANNOT_RETRIEVE_ANOTHER_USER_USING_NON_ADMIN_SESSION, $puserId);
//		}
//
//		$kuser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::getCurrentPartnerId(), $puserId);
//
//		if (!$kuser)
//		{
//			throw new kCoreException('[User ID Not Found]', kCoreException::INVALID_USER_ID, $puserId);
//		}
//
//		if (!kString::isValidMongoId($appGuid))
//		{
//			throw new kCoreException('[Invalid App Guid]', kCoreException::INVALID_APP_GUID, $appGuid);
//		}
//
//		// validate appGuid belong to ks partner
//		$appRegistry = KuserToUserRolePeer::getAppGuidById($appGuid);
//		if (!$appRegistry)
//		{
//			throw new kCoreException('[App Guid Not Found]', kCoreException::APP_GUID_NOT_FOUND, $appGuid);
//		}
//
//		$dbUserAppRole = KuserToUserRolePeer::getByKuserIdAndAppGuid($kuser->getId(), $appGuid);
//
//		if (!$dbUserAppRole)
//		{
//			throw new kCoreException('[User App Role Not Found]', kCoreException::USER_APP_ROLE_NOT_FOUND, "$puserId,$appGuid");
//		}
//
//		return $dbUserAppRole;
//	}
	
//	/**
//	 * @param kuser $kuser
//	 * @param string $appGuid
//	 * @param int $userRoleId
//	 * @return void
//	 *
//	 * @throws kCoreException
//	 * @throws PropelException
//	 */
//	public static function isValidForInsert(kuser $kuser, $appGuid, $userRoleId)
//	{
//		// groups are not supported
//		if ($kuser->getType() === KuserType::GROUP)
//		{
//			throw new kCoreException('[User App Role Not Allowed For Group]', kCoreException::USER_APP_ROLE_NOT_ALLOWED_FOR_GROUP);
//		}
//
//		// validate userRoleId exist
//		$userRole = UserRolePeer::retrieveByPK($userRoleId);
//
//		if (!$userRole)
//		{
//			throw new kCoreException('[User Role Not Found]', kCoreException::USER_ROLE_NOT_FOUND);
//		}
//
//		// validate appGuid string
//		if (!kString::isValidMongoId($appGuid))
//		{
//			throw new kCoreException('[Invalid App Guid]', kCoreException::INVALID_APP_GUID, $appGuid);
//		}
//
//		// validate appGuid belong to ks partner
//		$appRegistry = KuserToUserRolePeer::getAppGuidById($appGuid);
//		if (!$appRegistry)
//		{
//			throw new kCoreException('[App Guid Not Found]', kCoreException::APP_GUID_NOT_FOUND, $appGuid);
//		}
//
//		// validate user does not have a role for the requested appGuid
//		$userAppRole = KuserToUserRolePeer::getByKuserIdAndAppGuid($kuser->getId(), $appGuid);
//		if ($userAppRole)
//		{
//			$puserId = $kuser->getPuserId();
//			throw new kCoreException('[User App Role Already Exists]', kCoreException::USER_APP_ROLE_ALREADY_EXISTS, "$puserId,$appGuid");
//		}
//	}
	
//	public static function isValidForUpdate($userRoleId)
//	{
//		// validate userRoleId exist
//		$userRole = UserRolePeer::retrieveByPK($userRoleId);
//
//		if (!$userRole)
//		{
//			throw new kCoreException('[User Role Not Found]', kCoreException::USER_ROLE_NOT_FOUND);
//		}
//	}
	
//	/**
//	 * @throws kCoreException
//	 */
//	public static function getAppGuidById($appGuid)
//	{
//
//		$appGuidExists = KuserToUserRolePeer::getAppGuidFromCache($appGuid);
//		if ($appGuidExists)
//		{
//			return $appGuidExists;
//		}
//
//		$appRegistryClient = new MicroServiceAppRegistry();
//		$appRegistry = $appRegistryClient->get(kCurrentContext::getCurrentPartnerId(), $appGuid);
//
//		if (isset($appRegistry->code) && $appRegistry->code == 'OBJECT_NOT_FOUND')
//		{
//			return false;
//		}
//
//		if (!isset($appRegistry->id))
//		{
//			return false;
//		}
//
//		KuserToUserRolePeer::addAppGuidToCache($appRegistry->id);
//		return $appRegistry->id;
//	}
//
//	/**
//	 * @throws kCoreException
//	 */
//	public static function getKsPartnerAppGuidsFromCsv($appGuidsCsv)
//	{
//		$appGuidsResponse = array();
//		$appGuidsCached = array();
//		$appGuidsToQuery = explode(',', $appGuidsCsv);
//
//		// remove duplicate appGuids, if exists
//		$appGuidsToQuery = array_unique($appGuidsToQuery);
//
//		// filter appGuids that already stored in cache
//		foreach ($appGuidsToQuery as $key => $appGuid)
//		{
//			$appGuidExist = KuserToUserRolePeer::getAppGuidFromCache($appGuid);
//
//			// remove appGuids that already stored in cache before querying app-registry ms
//			if ($appGuidExist)
//			{
//				$appGuidsCached[] = $appGuid;
//				unset($appGuidsToQuery[$key]);
//			}
//		}
//
//		// reset array keys or else 'json_encode' will encode the non-consecutive as the key-value resulting in bad mongo query
//		$appGuidsToQuery = array_values($appGuidsToQuery);
//
//		// if all appGuids were found in cache, return original csv
//		if (!count($appGuidsToQuery))
//		{
//			return $appGuidsCsv;
//		}
//
//		$filter = array(
//			'idIn' => $appGuidsToQuery
//		);
//
//		$pager = array(
//			'offset' => 0,
//			'limit' => count($appGuidsToQuery)
//		);
//
//		$appRegistryClient = new MicroServiceAppRegistry();
//		$appRegistries = $appRegistryClient->list(kCurrentContext::getCurrentPartnerId(), $filter, $pager);
//
//		foreach ($appRegistries->objects as $appRegistry)
//		{
//			if (isset($appRegistry->id))
//			{
//				$appGuidsResponse[] = $appRegistry->id;
//				KuserToUserRolePeer::addAppGuidToCache($appRegistry->id);
//			}
//		}
//
//		$appGuidsFinal = array_merge($appGuidsCached, $appGuidsResponse);
//		return count($appGuidsFinal) ? implode(',', $appGuidsFinal) : 'null'; // set to 'null' will not return results (if set to '' 'attachToCriteria' will remove it from mysql query)
//	}
//
//	/**
//	 * @throws kCoreException
//	 */
//	private static function getAppGuidFromCache($appGuid)
//	{
//		$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_MICROSERVICES);
//		if (!$cache)
//		{
//			throw new kCoreException('[Failed to instantiate cache]', kCoreException::FAILED_TO_INSTANTIATE_MICROSERVICE_CACHE);
//		}
//
//		$cacheKey = self::APP_GUID_CACHE_KEY_PREFIX . $appGuid;
//		$cacheValue = $cache->get($cacheKey);
//		if (!$cacheValue)
//		{
//			KalturaLog::debug("Cache value for key [$cacheKey] not found");
//			return false;
//		}
//
//		KalturaLog::debug("Cache value for key [$cacheKey] found, value [$cacheValue]");
//		return $cacheValue;
//	}
//
//	/**
//	 * @throws kCoreException
//	 */
//	private static function addAppGuidToCache($appGuid)
//	{
//		$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_MICROSERVICES);
//		if (!$cache)
//		{
//			throw new kCoreException('[Failed to instantiate cache]', kCoreException::FAILED_TO_INSTANTIATE_MICROSERVICE_CACHE);
//		}
//
//		$cacheKey = self::APP_GUID_CACHE_KEY_PREFIX . $appGuid;
//		$res = $cache->add($cacheKey, true, self::APP_GUID_CACHE_TTL);
//
//		if (!$res)
//		{
//			KalturaLog::debug("Failed to save key [$cacheKey] to cache - already stored?");
//		}
//
//		KalturaLog::debug("Saved key [$cacheKey] to cache");
//	}
//
} // KuserToUserRolePeer
