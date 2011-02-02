<?php
/**
 * @package lib.model
 * @subpackage enum
 */ 
interface PermissionItemType extends BaseEnum
{
	const API_ACTION_ITEM  = 'kApiActionPermissionItem';
	const API_PARAMETER_ITEM = 'kApiParameterPermissionItem';
}