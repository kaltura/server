<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaPartnerAuthenticationType extends KalturaEnum
{
	const PASSWORD_ONLY = 0;
	const TWO_FACTOR_AUTH = 1;
	const SSO = 2;
}
