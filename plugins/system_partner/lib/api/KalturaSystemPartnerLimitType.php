<?php
/**
 * @package plugins.systemPartner
 * @subpackage api.enum
 */
class KalturaSystemPartnerLimitType extends KalturaStringEnum
{
	const ENTRIES = 'ENTRIES';
	const STREAM_ENTRIES = 'STREAM_ENTRIES';
	const BANDWIDTH = 'BANDWIDTH';
	const PUBLISHERS = 'PUBLISHERS';
	const ADMIN_LOGIN_USERS = 'ADMIN_LOGIN_USERS';
	const LOGIN_USERS = 'LOGIN_USERS';
	const USER_LOGIN_ATTEMPTS = 'USER_LOGIN_ATTEMPTS';
	const BULK_SIZE = 'BULK_SIZE';
}