<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.enum
 */ 
interface ESearchUserFieldName extends BaseEnum
{
	const USER_SCREEN_NAME = 'screen_name';
	const USER_EMAIL = 'email';
	const USER_TYPE = 'kuser_type';
	const USER_TAGS = 'tags';
	const USER_UPDATED_AT = 'updated_at';
	const USER_CREATED_AT = 'created_at';
	const USER_LAST_NAME = 'last_name';
	const USER_FIRST_NAME = 'first_name';
	const USER_PERMISSION_NAMES = 'permission_names';
	const USER_GROUP_IDS = 'group_ids';
	const USER_ROLE_IDS = 'role_ids';

}