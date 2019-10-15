<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.enum
 */ 
interface ESearchEntryFieldName extends BaseEnum
{
	const ID = '_id';
	const NAME = 'name';
	const DESCRIPTION = 'description';
	const TAGS = 'tags';
	const USER_ID = 'kuser_id';
	const CREATOR_ID = 'creator_kuser_id';
	const START_DATE = 'start_date';
	const END_DATE = 'end_date';
	const REFERENCE_ID = 'reference_id';
	const CONVERSION_PROFILE_ID = 'conversion_profile_id';
	const REDIRECT_ENTRY_ID = 'redirect_entry_id';
	const ENTITLED_USER_EDIT = 'entitled_kusers_edit';
	const ENTITLED_USER_PUBLISH = 'entitled_kusers_publish';
	const ENTITLED_USER_VIEW = 'entitled_kusers_view';
	const TEMPLATE_ENTRY_ID = 'template_entry_id';
	const PARENT_ENTRY_ID = 'parent_id';
	const MEDIA_TYPE = 'media_type';
	const SOURCE_TYPE = 'source_type';
	const RECORDED_ENTRY_ID = 'recorded_entry_id';
	const PUSH_PUBLISH = 'push_publish';
	const LENGTH_IN_MSECS = 'length_in_msecs';
	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';
	const MODERATION_STATUS = 'moderation_status';
	const ENTRY_TYPE = 'entry_type';
	const ADMIN_TAGS = 'admin_tags';
	const CREDIT = 'credit';
	const SITE_URL = 'site_url';
	const ACCESS_CONTROL_ID = 'access_control_id';
	const EXTERNAL_SOURCE_TYPE = 'external_source_type';
	const IS_QUIZ = 'is_quiz';
	const IS_LIVE = 'is_live';
	const USER_NAMES = "user_names";
	const DISPLAY_IN_SEARCH = 'display_in_search';
	const ROOT_ID = 'root_id';
	const PRIVACY_BY_CONTEXTS = 'privacy_by_contexts';
	const PARTNER_SORT_VALUE = 'partner_sort_value';
}
