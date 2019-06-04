<?php
/**
 * @package Core
 * @subpackage model.enum
 */

interface PartnerAuthenticationType extends BaseEnum
{
	const PASSWORD_ONLY = 0;
	const TWO_FACTOR_AUTH = 1;
	const SSO = 2;
}