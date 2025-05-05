<?php
/**
 * @package api
 * @subpackage objects
 */

class KalturaUserPermissionOnEntry extends KalturaObject
{
	/**
	 * @var KalturaUserPermissionOnEntryEnum
	 */
	public $userPermission;

	public function __construct()
	{
		$this->userPermission = KalturaUserPermissionOnEntryEnum::NONE;
	}
}
