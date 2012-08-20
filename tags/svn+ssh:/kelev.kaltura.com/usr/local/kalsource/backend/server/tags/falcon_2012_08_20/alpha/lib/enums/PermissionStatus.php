<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface PermissionStatus extends BaseEnum
{
	const ACTIVE  = 1;
	const BLOCKED = 2;
	const DELETED = 3;
}