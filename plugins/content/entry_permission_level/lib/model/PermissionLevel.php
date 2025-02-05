<?php
/**
 * @package plugins.entryPermissionLevel
 * @subpackage model
 */
class PermissionLevel
{
	protected $permissionLevel;
	
	public function getPermissionLevel()
	{
		return $this->permissionLevel;
	}
	
	public function setPermissionLevel($permissionLevel)
	{
		$this->permissionLevel = $permissionLevel;
	}
}
