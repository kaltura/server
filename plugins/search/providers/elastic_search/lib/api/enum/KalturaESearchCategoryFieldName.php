<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.enum
 */
class KalturaESearchCategoryFieldName extends KalturaStringEnum
{
	const ID = 'id';
	const PRIVACY = 'privacy';
	const PRIVACY_CONTEXT = 'privacy_context';
	const PRIVACY_CONTEXTS = 'privacy_contexts';
	const PARENT_ID = 'parent_id';
	const DEPTH = 'depth';
	const NAME = 'name';
	const FULL_NAME = 'full_name';
	const FULL_IDS = 'full_ids';
	const DESCRIPTION = 'description';
	const TAGS = 'tags';
	const DISPLAY_IN_SEARCH = 'display_in_search';
	const INHERITANCE_TYPE = 'inheritance_type';
	const USER_ID = 'user_id';
	const REFERENCE_ID = 'reference_id';
	const INHERITED_PARENT_ID = 'inherited_parent_id';
	const MODERATION = 'moderation';
	const CONTRIBUTION_POLICY = 'contribution_policy';
	const ENTRIES_COUNT = 'entries_count';
	const DIRECT_ENTRIES_COUNT = 'direct_entries_count';
	const DIRECT_SUB_CATEGORIES_COUNT = 'direct_sub_categories_count';
	const MEMBERS_COUNT = 'members_count';
	const PENDING_MEMBERS_COUNT = 'pending_members_count';
	const PENDING_ENTRIES_COUNT = 'pending_entries_count';
	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';
}