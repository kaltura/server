<?php


/**
 * Skeleton subclass for representing a row from the 'permission_item' table.
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
class PermissionItem extends BasePermissionItem
{
	
	const ALL_VALUES_IDENTIFIER = '*'; // means that a certain parameter is not limited to a specific value - can be used in different places
	
	public function __construct()
	{
		$this->setType(get_class($this));
	}		
} // PermissionItem
