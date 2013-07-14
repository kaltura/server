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
 * @package Core
 * @subpackage model
 */
class PermissionToPermissionItemPeer extends BasePermissionToPermissionItemPeer
{
	
	public static function getCacheInvalidationKeys()
	{
		return array(array("permissionToPermissionItem:permissionId=%s", self::PERMISSION_ID));		
	}
	
	public static function retrieveByPermissionId($permisionId)
	{
		$c = new Criteria();
		$c->add ( self::PERMISSION_ID , $permisionId );
		
		return self::doSelect( $c );
	}
	
} // PermissionToPermissionItemPeer
