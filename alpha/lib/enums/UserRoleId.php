<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface UserRoleId extends BaseEnum
{
	const PARTNER_ADMIN_ROLE     = 'PARTNER_ADMIN_ROLE';
	const BASE_USER_SESSION_ROLE = 'BASE_USER_SESSION_ROLE';
	const WIDGET_SESSION_ROLE   = 'WIDGET_SESSION_ROLE';
	const NO_SESSION_ROLE = 'NO_SESSION_ROLE';
	const REACH_VENDOR_ROLE     = 'REACH_VENDOR_ROLE';
}