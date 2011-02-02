<?php
/**
 * @package lib.model
 * @subpackage enum
 */ 
interface ApiParameterPermissionItemAction extends BaseEnum
{
	const READ   = 'read';
	const UPDATE = 'update';
	const INSERT = 'insert';
}