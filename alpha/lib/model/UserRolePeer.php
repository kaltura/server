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
 * @package    lib.model
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
	 * Temporary function that will not allow a user to have 0 or more than 1 role.
	 * @param string $idsString
	 * @throws kPermissionException::ROLE_ID_MISSING
	 * @throws kPermissionException::ONLY_ONE_ROLE_PER_USER_ALLOWED
	 */
	public static function testValidRolesForUser($idsString)
	{
		$ids = explode(',', trim($idsString));
		
		if (count($ids) > 1)
		{
			throw new kPermissionException('', kPermissionException::ONLY_ONE_ROLE_PER_USER_ALLOWED);	
		}
		
		return true;
	}
	
	
	public static function getByStrId($strId)
	{
		$c = new Criteria();
		$c->addAnd(UserRolePeer::PARTNER_ID, PartnerPeer::GLOBAL_PARTNER, Criteria::EQUAL);
		$c->addAnd(UserRolePeer::STR_ID, $strId, Criteria::EQUAL);
		UserRolePeer::setUseCriteriaFilter(false);
		$userRole = UserRolePeer::doSelectOne($c);
		UserRolePeer::setUseCriteriaFilter(true);
		return $userRole;
	}
		
} // UserRolePeer
