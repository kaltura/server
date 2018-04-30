<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.enum
 */ 
interface ESearchCategoryFieldName extends BaseEnum
{
	const ID = '_id';
	const PRIVACY = 'privacy';
	const PRIVACY_CONTEXT = 'privacy_context';
	const PRIVACY_CONTEXTS = 'privacy_contexts';
	const KUSER_IDS = 'kuser_ids';
	const PARENT_ID = 'parent_id';
	const DEPTH = 'depth';
	const NAME = 'name';
	const FULL_NAME = 'full_name';
	const FULL_IDS = 'full_ids';
	const DESCRIPTION = 'description';
	const TAGS = 'tags';
	const DISPLAY_IN_SEARCH = 'display_in_search';
	const INHERITANCE_TYPE = 'inheritance_type';
	const KUSER_ID = 'kuser_id';
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