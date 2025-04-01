<?php
/**
 * @package Core
 * @subpackage model.enum
 */
interface SupportAccessMode extends BaseEnum
{
	//Access to manage the account is always allowed
	const ALLWAYS_ALLOWED = 0;
	
	//Access will be blocked unless security team opens overrides this in case of security/production incidents
	const BLOCKED = 1;
	
	//Access to manage the account will be opened and the time frame of it will controlled by the account admins
	const REQUIRES_USER_APPROVAL = 2;
}
