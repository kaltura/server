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
	
	/**
	 * Temporary function that will not allow a user to have 0 or more than 1 role.
	 * @param string $idsString
	 * @throws kPermissionException::ROLE_ID_MISSING
	 * @throws kPermissionException::ONLY_ONE_ROLE_PER_USER_ALLOWED
	 */
	public function testValidRolesForUser($idsString)
	{
		//TODO: remove return true
		return true;
		
		
		$ids = explode(',', trim($idsString));
		
		if (count($ids) <= 0)
		{
			throw new kPermissionException('', kPermissionException::ROLE_ID_MISSING);
		}
		
		if (count($ids) > 1)
		{
			throw new kPermissionException('', kPermissionException::ONLY_ONE_ROLE_PER_USER_ALLOWED);	
		}
		
		return true;
	}

} // UserRolePeer
