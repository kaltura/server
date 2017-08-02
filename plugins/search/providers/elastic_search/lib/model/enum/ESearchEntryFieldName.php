<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.enum
 */ 
interface ESearchEntryFieldName extends BaseEnum
{
	const ENTRY_ID = '_id';
	const ENTRY_NAME = 'name';
	const ENTRY_DESCRIPTION = 'description';
	const ENTRY_TAGS = 'tags';
	const ENTRY_CATEGORY_IDS = 'category_ids';
	const ENTRY_USER_ID = 'puser_id';
	const ENTRY_CREATOR_ID = 'creator_puser_id';
	const ENTRY_START_DATE = 'start_date';
	const ENTRY_END_DATE = 'end_date';
	const ENTRY_REFERENCE_ID = 'reference_id';
	const ENTRY_CONVERSION_PROFILE_ID = 'conversion_profile_id';
	const ENTRY_REDIRECT_ENTRY_ID = 'redirect_entry_id';
	const ENTRY_ENTITLED_USER_EDIT = 'entitled_kusers_edit';
	const ENTRY_ENTITLED_USER_PUBLISH = 'entitled_kusers_publish';
	const ENTRY_TEMPLATE_ENTRY_ID = 'template_entry_id';
	const ENTRY_DISPLAY_IN_SEARCH = 'display_in_search';
	const ENTRY_PARENT_ENTRY_ID = 'parent_id';
	const ENTRY_MEDIA_TYPE = 'media_type';
	const ENTRY_SOURCE_TYPE = 'source_type';
	const ENTRY_RECORDED_ENTRY_ID = 'recorded_entry_id';
	const ENTRY_PUSH_PUBLISH = 'push_publish';
	const ENTRY_LENGTH_IN_MSECS = 'length_in_msecs';
	const ENTRY_CREATED_AT = 'created_at';
	const ENTRY_UPDATED_AT = 'updated_at';
	const ENTRY_MODERATION_STATUS = 'moderation_status';
	const ENTRY_TYPE = 'entry_type';
	const ENTRY_CATEGORIES = 'categories';
}