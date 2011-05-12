<?php


/**
 * Skeleton subclass for performing query and update operations on the 'permission_item' table.
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
class PermissionItemPeer extends BasePermissionItemPeer
{

	public static function checkValidForParther($permissionIdsStr, $partnerId)
	{		
		$permissionItemIds = array_map('trim', explode(',', $permissionIdsStr));
		
		foreach ($permissionItemIds as $itemId)
		{
			if (!$itemId)
				continue;
			
			self::setUseCriteriaFilter(false);
			$permissionItem = self::retrieveByPK($itemId);
			self::setUseCriteriaFilter(true);
			if (!$permissionItem) {
				throw new kPermissionException('Permission item with id ['.$itemId.'] not found', kPermissionException::PERMISSION_ITEM_NOT_FOUND);
			}
			
			if (!in_array($permissionItem->getPartnerId(), array($partnerId, PartnerPeer::GLOBAL_PARTNER))) {
				throw new kPermissionException('Permission item with id ['.$itemId.'] not found', kPermissionException::PERMISSION_ITEM_NOT_FOUND);
			}
		}
	}
	
	
} // PermissionItemPeer
