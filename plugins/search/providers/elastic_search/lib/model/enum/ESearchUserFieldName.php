<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.enum
 */ 
interface ESearchUserFieldName extends BaseEnum
{
	const USER_SCREEN_NAME = 'screen_name';
	const USER_EMAIL = 'email';
	const USER_TYPE = 'type';
	const USER_TAGS = 'tags';
	const USER_STATUS = 'status';
	const USER_PARTNER_STATUS = 'partner_status';
	const USER_UPDATED_AT = 'updated_at';
	const USER_CREATED_AT = 'created_at';
	const USER_LAST_NAME = 'last_name';
	const USER_FIRST_NAME = 'first_name';
	const USER_PERMISSION_NAMES = 'permission_names';
	const USER_GROUP_IDS = 'group_ids';

}