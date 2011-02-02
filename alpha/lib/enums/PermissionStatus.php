<?php
/**
 * @package lib.model
 * @subpackage enum
 */ 
interface PermissionStatus extends BaseEnum
{
	const ACTIVE  = 1;
	const BLOCKED = 2;
	const DELETED = 3;
}