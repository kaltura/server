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
class KuserToUserRolePeer extends BaseKuserToUserRolePeer
{
	
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
	

} // KuserToUserRolePeer
