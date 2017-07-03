<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.enum
 */ 
interface ESearchCategoryFieldName extends BaseEnum
{
	const CATEGORY_PRIVACY = 'privacy';
	const CATEGORY_PRIVACY_CONTEXT = 'privacy_context';
	const CATEGORY_PRIVACY_CONTEXTS = 'privacy_contexts';
	const CATEGORY_KUSER_IDS = 'kuser_ids';
	const CATEGORY_PARENT_ID = 'parent_id';
	const CATEGORY_DEPTH = 'depth';
	const CATEGORY_NAME = 'name';
	const CATEGORY_FULL_NAME = 'full_name';
	const CATEGORY_FULL_IDS = 'full_ids';
	const CATEGORY_DESCRIPTION = 'description';
	const CATEGORY_TAGS = 'tags';
	const CATEGORY_DISPLAY_IN_SEARCH = 'display_in_search';
	const CATEGORY_INHERITANCE_TYPE = 'inheritance_type';
	const CATEGORY_KUSER_ID = 'kuser_id';
	const CATEGORY_REFERENCE_ID = 'reference_id';
	const CATEGORY_INHERITED_PARENT_ID = 'inherited_parent_id';
	const CATEGORY_MODERATION = 'moderation';
	const CATEGORY_CONTRIBUTION_POLICY = 'contribution_policy';
	const CATEGORY_METADATA = 'metadata';
	const CATEGORY_ENTRIES_COUNT = 'entries_count';
	const CATEGORY_DIRECT_ENTRIES_COUNT = 'direct_entries_count';
	const CATEGORY_DIRECT_SUB_CATEGORIES_COUNT = 'direct_sub_categories_count';
	const CATEGORY_MEMBERS_COUNT = 'members_count';
	const CATEGORY_PENDING_MEMBERS_COUNT = 'pending_members_count';
	const CATEGORY_PENDING_ENTRIES_COUNT = 'pending_entries_count';
	const CATEGORY_CREATED_AT = 'created_at';
	const CATEGORY_UPDATED_AT = 'updated_at';
}