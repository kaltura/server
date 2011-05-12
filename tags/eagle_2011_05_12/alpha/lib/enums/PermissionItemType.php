<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface PermissionItemType extends BaseEnum
{
	const API_ACTION_ITEM  = 'kApiActionPermissionItem';
	const API_PARAMETER_ITEM = 'kApiParameterPermissionItem';
}