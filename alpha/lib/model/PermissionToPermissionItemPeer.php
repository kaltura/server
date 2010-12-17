<?php


/**
 * Skeleton subclass for performing query and update operations on the 'permission_to_permission_item' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.model
 */
class PermissionToPermissionItemPeer extends BasePermissionToPermissionItemPeer
{

	/**
	 * Get objects by permission name and permission item ID
	 * @param string $permissionName
	 * @param int $permissionItemId
	 * @return array Array of selected PermissionToPermissionItem Objects
	 */
	public static function getByPermissionNameAndItemId($permissionName, $permissionItemId)
	{
		$c = new Criteria();
		$c->addAnd(self::PERMISSION_NAME,    $permissionName,   Criteria::EQUAL);
		$c->addAnd(self::PERMISSION_ITEM_ID, $permissionItemId, Criteria::EQUAL);
		return self::doSelect($c);
	}
	
	
} // PermissionToPermissionItemPeer
