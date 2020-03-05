<?php

/**
 * @package plugins.elasticSearch
 * @subpackage model.enum
 */
class ESearchEntryFilterFields extends KalturaEnum
{
	const ID = 'id';
	const STATUS = 'status';
	const USER_ID = 'user_id';
	const GROUP_ID = 'group_id';
	const CREATOR_ID = 'creator_id';
	const NAME = 'name';
	const TAGS = 'tags';
	const TAGS_NAME = 'tags-name';
	const ADMIN_TAGS = 'admin_tags';
	const TAGS_ADMIN_TAGS = 'tags-admin_tags';
	const TAGS_ADMIN_TAGS_NAME = 'tags-admin_tags-name';
	const PARTNER_ID = 'partner_id';
	const CONVERSION_PROFILE_ID ='convertion_profile_id';
	const REDIRECT_ENTRY_ID = 'redirect_from_entry_id';
	const ENTITLED_USER_EDIT = 'entitled_kusers_edit';
	const ENTITLED_USER_PUBLISH = 'entitled_kusers_publish';
	const ENTITLED_USER_VIEW = 'entitled_kusers_view';
	const DISPLAY_IN_SEARCH = 'display_in_search';
	const PARENT_ENTRY_ID = 'parent_entry_id';
	const MEDIA_TYPE = 'media_type';
	const SOURCE_TYPE = 'source';
	const LENGTH_IN_MSECS = 'length_in_mesec';
	const TYPE ='type';
	const MODERATION_STATUS = 'moderation_status';
	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';
	const ACCESS_CONTROL_ID = 'access_control_id';
	const USER_NAMES = 'user_names';
	const START_DATE = 'start_date';
	const END_DATE = 'end_date';
	const REFERENCE_ID = 'reference_id';
	const ROOT_ENTRY_ID = 'root_entry_id';
	const VIEWS = 'views';
	const PLAYS = 'plays';
	const ORDER_BY = '_order_by';
	const LIMIT = '_limit';
	const DURATION = 'duration';
	const CATEGORIES = 'categories';
	const CATEGORIES_IDS = 'categories_ids';
	const CATEGORIES_ANCESTOR_ID = 'category_ancestor_id';
	const CATEGORIES_FULL_NAME = 'categories_full_name';
	const PARTNER_SORT_VALUE = 'partner_sort_value';
	const SEARCH_TEXT = 'search_text';
	const FREE_TEXT	= '_free_text';
	const TOTAL_RANK = 'total_rank';
	const RANK = 'rank';
	const VOTES = 'votes';
	const LAST_PLAYED_AT = 'last_played_at';
	const DURATION_TYPE = 'duration_type';
	const EXTERNAL_SOURCE_TYPE = 'plugins_data';
}