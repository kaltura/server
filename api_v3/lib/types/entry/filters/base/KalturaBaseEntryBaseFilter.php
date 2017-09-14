<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
abstract class KalturaBaseEntryBaseFilter extends KalturaRelatedFilter
{
	static private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"idIn" => "_in_id",
		"idNotIn" => "_notin_id",
		"nameLike" => "_like_name",
		"nameMultiLikeOr" => "_mlikeor_name",
		"nameMultiLikeAnd" => "_mlikeand_name",
		"nameEqual" => "_eq_name",
		"partnerIdEqual" => "_eq_partner_id",
		"partnerIdIn" => "_in_partner_id",
		"userIdEqual" => "_eq_user_id",
		"userIdIn" => "_in_user_id",
		"userIdNotIn" => "_notin_user_id",
		"creatorIdEqual" => "_eq_creator_id",
		"tagsLike" => "_like_tags",
		"tagsMultiLikeOr" => "_mlikeor_tags",
		"tagsMultiLikeAnd" => "_mlikeand_tags",
		"adminTagsLike" => "_like_admin_tags",
		"adminTagsMultiLikeOr" => "_mlikeor_admin_tags",
		"adminTagsMultiLikeAnd" => "_mlikeand_admin_tags",
		"categoriesMatchAnd" => "_matchand_categories",
		"categoriesMatchOr" => "_matchor_categories",
		"categoriesNotContains" => "_notcontains_categories",
		"categoriesIdsMatchAnd" => "_matchand_categories_ids",
		"categoriesIdsMatchOr" => "_matchor_categories_ids",
		"categoriesIdsNotContains" => "_notcontains_categories_ids",
		"categoriesIdsEmpty" => "_empty_categories_ids",
		"statusEqual" => "_eq_status",
		"statusNotEqual" => "_not_status",
		"statusIn" => "_in_status",
		"statusNotIn" => "_notin_status",
		"moderationStatusEqual" => "_eq_moderation_status",
		"moderationStatusNotEqual" => "_not_moderation_status",
		"moderationStatusIn" => "_in_moderation_status",
		"moderationStatusNotIn" => "_notin_moderation_status",
		"typeEqual" => "_eq_type",
		"typeIn" => "_in_type",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"createdAtLessThanOrEqual" => "_lte_created_at",
		"updatedAtGreaterThanOrEqual" => "_gte_updated_at",
		"updatedAtLessThanOrEqual" => "_lte_updated_at",
		"totalRankLessThanOrEqual" => "_lte_total_rank",
		"totalRankGreaterThanOrEqual" => "_gte_total_rank",
		"groupIdEqual" => "_eq_group_id",
		"searchTextMatchAnd" => "_matchand_search_text",
		"searchTextMatchOr" => "_matchor_search_text",
		"accessControlIdEqual" => "_eq_access_control_id",
		"accessControlIdIn" => "_in_access_control_id",
		"startDateGreaterThanOrEqual" => "_gte_start_date",
		"startDateLessThanOrEqual" => "_lte_start_date",
		"startDateGreaterThanOrEqualOrNull" => "_gteornull_start_date",
		"startDateLessThanOrEqualOrNull" => "_lteornull_start_date",
		"endDateGreaterThanOrEqual" => "_gte_end_date",
		"endDateLessThanOrEqual" => "_lte_end_date",
		"endDateGreaterThanOrEqualOrNull" => "_gteornull_end_date",
		"endDateLessThanOrEqualOrNull" => "_lteornull_end_date",
		"referenceIdEqual" => "_eq_reference_id",
		"referenceIdIn" => "_in_reference_id",
		"replacingEntryIdEqual" => "_eq_replacing_entry_id",
		"replacingEntryIdIn" => "_in_replacing_entry_id",
		"replacedEntryIdEqual" => "_eq_replaced_entry_id",
		"replacedEntryIdIn" => "_in_replaced_entry_id",
		"replacementStatusEqual" => "_eq_replacement_status",
		"replacementStatusIn" => "_in_replacement_status",
		"partnerSortValueGreaterThanOrEqual" => "_gte_partner_sort_value",
		"partnerSortValueLessThanOrEqual" => "_lte_partner_sort_value",
		"rootEntryIdEqual" => "_eq_root_entry_id",
		"rootEntryIdIn" => "_in_root_entry_id",
		"parentEntryIdEqual" => "_eq_parent_entry_id",
		"entitledUsersEditMatchAnd" => "_matchand_entitled_users_edit",
		"entitledUsersEditMatchOr" => "_matchor_entitled_users_edit",
		"entitledUsersPublishMatchAnd" => "_matchand_entitled_users_publish",
		"entitledUsersPublishMatchOr" => "_matchor_entitled_users_publish",
		"entitledUsersViewMatchAnd" => "_matchand_entitled_users_view",
		"entitledUsersViewMatchOr" => "_matchor_entitled_users_view",
		"tagsNameMultiLikeOr" => "_mlikeor_tags-name",
		"tagsAdminTagsMultiLikeOr" => "_mlikeor_tags-admin_tags",
		"tagsAdminTagsNameMultiLikeOr" => "_mlikeor_tags-admin_tags-name",
		"tagsNameMultiLikeAnd" => "_mlikeand_tags-name",
		"tagsAdminTagsMultiLikeAnd" => "_mlikeand_tags-admin_tags",
		"tagsAdminTagsNameMultiLikeAnd" => "_mlikeand_tags-admin_tags-name",
	);

	static private $order_by_map = array
	(
		"+name" => "+name",
		"-name" => "-name",
		"+moderationCount" => "+moderation_count",
		"-moderationCount" => "-moderation_count",
		"+createdAt" => "+created_at",
		"-createdAt" => "-created_at",
		"+updatedAt" => "+updated_at",
		"-updatedAt" => "-updated_at",
		"+rank" => "+rank",
		"-rank" => "-rank",
		"+totalRank" => "+total_rank",
		"-totalRank" => "-total_rank",
		"+startDate" => "+start_date",
		"-startDate" => "-start_date",
		"+endDate" => "+end_date",
		"-endDate" => "-end_date",
		"+partnerSortValue" => "+partner_sort_value",
		"-partnerSortValue" => "-partner_sort_value",
		"+recent" => "+recent",
		"-recent" => "-recent",
		"+weight" => "+weight",
		"-weight" => "-weight",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), self::$order_by_map);
	}

	/**
	 * This filter should be in use for retrieving only a specific entry (identified by its entryId).
	 * 
	 * @var string
	 */
	public $idEqual;

	/**
	 * This filter should be in use for retrieving few specific entries (string should include comma separated list of entryId strings).
	 * 
	 * @var string
	 */
	public $idIn;

	/**
	 * @var string
	 */
	public $idNotIn;

	/**
	 * This filter should be in use for retrieving specific entries. It should include only one string to search for in entry names (no wildcards, spaces are treated as part of the string).
	 * 
	 * @var string
	 */
	public $nameLike;

	/**
	 * This filter should be in use for retrieving specific entries. It could include few (comma separated) strings for searching in entry names, while applying an OR logic to retrieve entries that contain at least one input string (no wildcards, spaces are treated as part of the string).
	 * 
	 * @var string
	 */
	public $nameMultiLikeOr;

	/**
	 * This filter should be in use for retrieving specific entries. It could include few (comma separated) strings for searching in entry names, while applying an AND logic to retrieve entries that contain all input strings (no wildcards, spaces are treated as part of the string).
	 * 
	 * @var string
	 */
	public $nameMultiLikeAnd;

	/**
	 * This filter should be in use for retrieving entries with a specific name.
	 * 
	 * @var string
	 */
	public $nameEqual;

	/**
	 * This filter should be in use for retrieving only entries which were uploaded by/assigned to users of a specific Kaltura Partner (identified by Partner ID).
	 * 
	 * @var int
	 */
	public $partnerIdEqual;

	/**
	 * This filter should be in use for retrieving only entries within Kaltura network which were uploaded by/assigned to users of few Kaltura Partners  (string should include comma separated list of PartnerIDs)
	 * 
	 * @var string
	 */
	public $partnerIdIn;

	/**
	 * This filter parameter should be in use for retrieving only entries, uploaded by/assigned to a specific user (identified by user Id).
	 * 
	 * @var string
	 */
	public $userIdEqual;

	/**
	 * @var string
	 */
	public $userIdIn;

	/**
	 * @var string
	 */
	public $userIdNotIn;

	/**
	 * @var string
	 */
	public $creatorIdEqual;

	/**
	 * This filter should be in use for retrieving specific entries. It should include only one string to search for in entry tags (no wildcards, spaces are treated as part of the string).
	 * 
	 * @var string
	 */
	public $tagsLike;

	/**
	 * This filter should be in use for retrieving specific entries. It could include few (comma separated) strings for searching in entry tags, while applying an OR logic to retrieve entries that contain at least one input string (no wildcards, spaces are treated as part of the string).
	 * 
	 * @var string
	 */
	public $tagsMultiLikeOr;

	/**
	 * This filter should be in use for retrieving specific entries. It could include few (comma separated) strings for searching in entry tags, while applying an AND logic to retrieve entries that contain all input strings (no wildcards, spaces are treated as part of the string).
	 * 
	 * @var string
	 */
	public $tagsMultiLikeAnd;

	/**
	 * This filter should be in use for retrieving specific entries. It should include only one string to search for in entry tags set by an ADMIN user (no wildcards, spaces are treated as part of the string).
	 * 
	 * @var string
	 */
	public $adminTagsLike;

	/**
	 * This filter should be in use for retrieving specific entries. It could include few (comma separated) strings for searching in entry tags, set by an ADMIN user, while applying an OR logic to retrieve entries that contain at least one input string (no wildcards, spaces are treated as part of the string).
	 * 
	 * @var string
	 */
	public $adminTagsMultiLikeOr;

	/**
	 * This filter should be in use for retrieving specific entries. It could include few (comma separated) strings for searching in entry tags, set by an ADMIN user, while applying an AND logic to retrieve entries that contain all input strings (no wildcards, spaces are treated as part of the string).
	 * 
	 * @var string
	 */
	public $adminTagsMultiLikeAnd;

	/**
	 * @var string
	 */
	public $categoriesMatchAnd;

	/**
	 * All entries within these categories or their child categories.
	 * 
	 * @var string
	 */
	public $categoriesMatchOr;

	/**
	 * @var string
	 */
	public $categoriesNotContains;

	/**
	 * @var string
	 */
	public $categoriesIdsMatchAnd;

	/**
	 * All entries of the categories, excluding their child categories.
	 * To include entries of the child categories, use categoryAncestorIdIn, or categoriesMatchOr.
	 * 
	 * @var string
	 */
	public $categoriesIdsMatchOr;

	/**
	 * @var string
	 */
	public $categoriesIdsNotContains;

	/**
	 * @var KalturaNullableBoolean
	 */
	public $categoriesIdsEmpty;

	/**
	 * This filter should be in use for retrieving only entries, at a specific {@link ?object=KalturaEntryStatus KalturaEntryStatus}.
	 * 
	 * @var KalturaEntryStatus
	 */
	public $statusEqual;

	/**
	 * This filter should be in use for retrieving only entries, not at a specific {@link ?object=KalturaEntryStatus KalturaEntryStatus}.
	 * 
	 * @var KalturaEntryStatus
	 */
	public $statusNotEqual;

	/**
	 * This filter should be in use for retrieving only entries, at few specific {@link ?object=KalturaEntryStatus KalturaEntryStatus} (comma separated).
	 * 
	 * @dynamicType KalturaEntryStatus
	 * @var string
	 */
	public $statusIn;

	/**
	 * This filter should be in use for retrieving only entries, not at few specific {@link ?object=KalturaEntryStatus KalturaEntryStatus} (comma separated).
	 * 
	 * @dynamicType KalturaEntryStatus
	 * @var string
	 */
	public $statusNotIn;

	/**
	 * @var KalturaEntryModerationStatus
	 */
	public $moderationStatusEqual;

	/**
	 * @var KalturaEntryModerationStatus
	 */
	public $moderationStatusNotEqual;

	/**
	 * @var string
	 */
	public $moderationStatusIn;

	/**
	 * @var string
	 */
	public $moderationStatusNotIn;

	/**
	 * @var KalturaEntryType
	 */
	public $typeEqual;

	/**
	 * This filter should be in use for retrieving entries of few {@link ?object=KalturaEntryType KalturaEntryType} (string should include a comma separated list of {@link ?object=KalturaEntryType KalturaEntryType} enumerated parameters).
	 * 
	 * @dynamicType KalturaEntryType
	 * @var string
	 */
	public $typeIn;

	/**
	 * This filter parameter should be in use for retrieving only entries which were created at Kaltura system after a specific time/date (standard timestamp format).
	 * 
	 * @var time
	 */
	public $createdAtGreaterThanOrEqual;

	/**
	 * This filter parameter should be in use for retrieving only entries which were created at Kaltura system before a specific time/date (standard timestamp format).
	 * 
	 * @var time
	 */
	public $createdAtLessThanOrEqual;

	/**
	 * @var time
	 */
	public $updatedAtGreaterThanOrEqual;

	/**
	 * @var time
	 */
	public $updatedAtLessThanOrEqual;

	/**
	 * @var int
	 */
	public $totalRankLessThanOrEqual;

	/**
	 * @var int
	 */
	public $totalRankGreaterThanOrEqual;

	/**
	 * @var int
	 */
	public $groupIdEqual;

	/**
	 * This filter should be in use for retrieving specific entries while search match the input string within all of the following metadata attributes: name, description, tags, adminTags.
	 * 
	 * @var string
	 */
	public $searchTextMatchAnd;

	/**
	 * This filter should be in use for retrieving specific entries while search match the input string within at least one of the following metadata attributes: name, description, tags, adminTags.
	 * 
	 * @var string
	 */
	public $searchTextMatchOr;

	/**
	 * @var int
	 */
	public $accessControlIdEqual;

	/**
	 * @var string
	 */
	public $accessControlIdIn;

	/**
	 * @var time
	 */
	public $startDateGreaterThanOrEqual;

	/**
	 * @var time
	 */
	public $startDateLessThanOrEqual;

	/**
	 * @var time
	 */
	public $startDateGreaterThanOrEqualOrNull;

	/**
	 * @var time
	 */
	public $startDateLessThanOrEqualOrNull;

	/**
	 * @var time
	 */
	public $endDateGreaterThanOrEqual;

	/**
	 * @var time
	 */
	public $endDateLessThanOrEqual;

	/**
	 * @var time
	 */
	public $endDateGreaterThanOrEqualOrNull;

	/**
	 * @var time
	 */
	public $endDateLessThanOrEqualOrNull;

	/**
	 * @var string
	 */
	public $referenceIdEqual;

	/**
	 * @var string
	 */
	public $referenceIdIn;

	/**
	 * @var string
	 */
	public $replacingEntryIdEqual;

	/**
	 * @var string
	 */
	public $replacingEntryIdIn;

	/**
	 * @var string
	 */
	public $replacedEntryIdEqual;

	/**
	 * @var string
	 */
	public $replacedEntryIdIn;

	/**
	 * @var KalturaEntryReplacementStatus
	 */
	public $replacementStatusEqual;

	/**
	 * @dynamicType KalturaEntryReplacementStatus
	 * @var string
	 */
	public $replacementStatusIn;

	/**
	 * @var int
	 */
	public $partnerSortValueGreaterThanOrEqual;

	/**
	 * @var int
	 */
	public $partnerSortValueLessThanOrEqual;

	/**
	 * @var string
	 */
	public $rootEntryIdEqual;

	/**
	 * @var string
	 */
	public $rootEntryIdIn;

	/**
	 * @var string
	 */
	public $parentEntryIdEqual;

	/**
	 * @var string
	 */
	public $entitledUsersEditMatchAnd;

	/**
	 * @var string
	 */
	public $entitledUsersEditMatchOr;

	/**
	 * @var string
	 */
	public $entitledUsersPublishMatchAnd;

	/**
	 * @var string
	 */
	public $entitledUsersPublishMatchOr;

	/**
	 * @var string
	 */
	public $entitledUsersViewMatchAnd;

	/**
	 * @var string
	 */
	public $entitledUsersViewMatchOr;

	/**
	 * @var string
	 */
	public $tagsNameMultiLikeOr;

	/**
	 * @var string
	 */
	public $tagsAdminTagsMultiLikeOr;

	/**
	 * @var string
	 */
	public $tagsAdminTagsNameMultiLikeOr;

	/**
	 * @var string
	 */
	public $tagsNameMultiLikeAnd;

	/**
	 * @var string
	 */
	public $tagsAdminTagsMultiLikeAnd;

	/**
	 * @var string
	 */
	public $tagsAdminTagsNameMultiLikeAnd;
}
