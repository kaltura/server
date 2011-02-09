<?php
/**
 * @package lib.model
 * @subpackage enum
 */ 
interface PermissionType extends BaseEnum
{
	const NORMAL           = '1';
	const SPECIAL_FEATURE  = '2';
	const PLUGIN           = '3';
	const PARTNER_GROUP    = '4';
}