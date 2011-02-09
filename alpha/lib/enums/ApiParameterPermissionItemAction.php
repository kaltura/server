<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface ApiParameterPermissionItemAction extends BaseEnum
{
	const READ   = 'read';
	const UPDATE = 'update';
	const INSERT = 'insert';
}