<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.enum
 */ 
interface ESearchUserFieldName extends BaseEnum
{
	const SCREEN_NAME = 'screen_name';
	const EMAIL = 'email';
	const TYPE = 'kuser_type';
	const TAGS = 'tags';
	const UPDATED_AT = 'updated_at';
	const CREATED_AT = 'created_at';
	const LAST_NAME = 'last_name_ft';
	const FIRST_NAME = 'first_name_ft';
	const FULL_NAME = 'full_name';
	const PERMISSION_NAMES = 'permission_names';
	const GROUP_IDS = 'group_ids';
	const ROLE_IDS = 'role_ids';
	const PUSER_ID = 'puser_id';
	const TITLE = 'title';
	const COMPANY = 'company';
	const COUNTRY = 'country';
	const EXTERNAL_ID = 'external_id';
	const IS_HASHED = 'is_hashed';
	const IS_ADMIN = 'is_admin';
	const LOGIN_ENABLED = 'login_enabled';
	const CAPABILITIES = 'capabilities';
}
