<?php
class SystemUserErrors extends KalturaErrors
{
	/**
	 * System Service
	 */
	const SYSTEM_USER_NOT_FOUND = "SYSTEM_USER_NOT_FOUND,System user not found";
	
	const SYSTEM_USER_ALREADY_EXISTS = "SYSTEM_USER_ALREADY_EXISTS,System user already exists";
	
	const SYSTEM_USER_INVALID_CREDENTIALS = "SYSTEM_USER_INVALID_CREDENTIALS,Invalid credentials";
	
	const SYSTEM_USER_DISABLED = "SYSTEM_USER_DISABLED,System user is disabled";
}