<?php
/**
 * @package Core
 * @subpackage model.enum
 */
interface TwoFactorAuthenticationMode extends BaseEnum
{
	const ALL = 0;
	const ADMIN_USERS_ONLY = 1;
	const NON_ADMIN_USERS_ONLY = 2;
}