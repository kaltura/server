<?php


/**
 * Skeleton subclass for performing query and update operations on the 'user_role' table.
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
class UserRolePeer extends BaseUserRolePeer
{
	
	public static function setDefaultCriteriaFilter ()
	{
		parent::setDefaultCriteriaFilter();
		if ( self::$s_criteria_filter == null )
		{
			self::$s_criteria_filter = new criteriaFilter ();
		}
		
		$c = new myCriteria();
		$c->addAnd ( UserRolePeer::STATUS, UserRoleStatus::DELETED, Criteria::NOT_EQUAL);
		self::$s_criteria_filter->setFilter ( $c );
	}
	
	
	/**
	 * Function that will not allow a user to have 0 roles.
	 * @param string $idsString
	 * @throws kPermissionException::ROLE_ID_MISSING
	 * @throws kPermissionException::INVALID_PARTNER_ID
	 */
	public static function testValidRolesForUser($idsString, $partnerId)
	{
		if(!$idsString)
		{
			KalturaLog::warning("Role IDs string is empty.");
			return true;
		}

		if (empty($partnerId))
		{
			throw new kPermissionException("Partner ID is missing or invalid.", kPermissionException::INVALID_PARTNER_ID);
		}
		
		$ids = explode(',', trim($idsString));
		
		foreach ($ids as $id)
		{
			$userRole = UserRolePeer::retrieveByPK($id);
			if (!$userRole || !in_array($userRole->getPartnerId(),array($partnerId, PartnerPeer::GLOBAL_PARTNER) ) )
			{
				throw new kPermissionException("A user role with ID [$id] does not exist", kPermissionException::USER_ROLE_NOT_FOUND);
			}
		}
		
		return true;
	}
	
	
	public static function getIdByStrId($strId)
	{
		// try to get strId to id mapping form cache
		$cacheKey = 'UserRolePeer_role_str_id_'.$strId;
		if (kConf::get('enable_cache') && kApcWrapper::apcEnabled())
		{
			$id = kApcWrapper::apcFetch($cacheKey); // try to fetch from cache
			if ($id) {
				KalturaLog::debug("UserRole str_id [$strId] mapped to id [$id] - fetched from cache");
				return $id;
			}
		}
		
		// not found in cache - get from database
		$c = new Criteria();
		$c->addSelectColumn(UserRolePeer::ID);
		$c->addAnd(UserRolePeer::STR_ID, $strId, Criteria::EQUAL);
		$c->setLimit(1);
		$stmt = UserRolePeer::doSelectStmt($c);
		$id = $stmt->fetch(PDO::FETCH_COLUMN);
		
		if ($id) {
			// store the found id in cache for later use
			if (kConf::get('enable_cache') && kApcWrapper::apcEnabled())
			{
				$success = kApcWrapper::apcStore($cacheKey, $id, kConf::get('apc_cache_ttl'));
				if ($success) {
					KalturaLog::debug("UserRole str_id [$strId] mapped to id [$id] - stored in cache");
				}
			}
		}
		
		if (!$id) {
			KalturaLog::log("UserRole with str_id [$strId] not found in DB!");
		}
		return $id;
	}
	
	
	/**
	 * Will return a UserRole object with the given $roleName and given $partnerId (or partner 0)
	 * @param string $roleName
	 * @param int $partnerId
	 * @return UserRole
	 */
	public static function getByNameAndPartnerId($roleName, $partnerId)
	{
		$c = new Criteria();
		$c->addAnd(UserRolePeer::PARTNER_ID, array_map('strval',  array($partnerId, PartnerPeer::GLOBAL_PARTNER)), Criteria::IN);
		$c->addAnd(UserRolePeer::NAME, $roleName, Criteria::EQUAL);
		$c->addAnd(UserRolePeer::STATUS, UserRoleStatus::DELETED, Criteria::NOT_EQUAL);
		UserRolePeer::setUseCriteriaFilter(false);
		$userRole = UserRolePeer::doSelectOne($c);
		UserRolePeer::setUseCriteriaFilter(true);
		return $userRole;
	}
	
	public static function getCacheInvalidationKeys()
	{
		return array(array("userRole:id=%s", self::ID), array("userRole:systemName=%s", self::SYSTEM_NAME));
	}
} // UserRolePeer
