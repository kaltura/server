<?php
/**
 * @package plugins.entryPermissionLevel
 * @subpackage model
 */
class PermissionLevel extends UserEntry
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