<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
class KalturaBaseEntryBaseFilter extends KalturaFilter
{
	private $map_between_objects = array
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
		"tagsLike" => "_like_tags",
		"tagsMultiLikeOr" => "_mlikeor_tags",
		"tagsMultiLikeAnd" => "_mlikeand_tags",
		"adminTagsLike" => "_like_admin_tags",
		"adminTagsMultiLikeOr" => "_mlikeor_admin_tags",
		"adminTagsMultiLikeAnd" => "_mlikeand_admin_tags",
		"categoriesMatchAnd" => "_matchand_categories",
		"categoriesMatchOr" => "_matchor_categories",
		"categoriesIdsMatchAnd" => "_matchand_categories_ids",
		"categoriesIdsMatchOr" => "_matchor_categories_ids",
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
		"tagsNameMultiLikeOr" => "_mlikeor_tags-name",
		"tagsAdminTagsMultiLikeOr" => "_mlikeor_tags-admin_tags",
		"tagsAdminTagsNameMultiLikeOr" => "_mlikeor_tags-admin_tags-name",
		"tagsNameMultiLikeAnd" => "_mlikeand_tags-name",
		"tagsAdminTagsMultiLikeAnd" => "_mlikeand_tags-admin_tags",
		"tagsAdminTagsNameMultiLikeAnd" => "_mlikeand_tags-admin_tags-name",
	);

	private $order_by_map = array
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
		"recent" => "recent",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), $this->map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), $this->order_by_map);
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
	 * 
	 * 
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
	 * 
	 * 
	 * @var string
	 */
	public $categoriesMatchAnd;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $categoriesMatchOr;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $categoriesIdsMatchAnd;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $categoriesIdsMatchOr;

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
	 * 
	 * 
	 * @var KalturaEntryModerationStatus
	 */
	public $moderationStatusEqual;

	/**
	 * 
	 * 
	 * @var KalturaEntryModerationStatus
	 */
	public $moderationStatusNotEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $moderationStatusIn;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $moderationStatusNotIn;

	/**
	 * 
	 * 
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
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual;

	/**
	 * This filter parameter should be in use for retrieving only entries which were created at Kaltura system before a specific time/date (standard timestamp format).
	 * 
	 * @var int
	 */
	public $createdAtLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $updatedAtLessThanOrEqual;

	/**
	 * 
	 * 
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
	 * 
	 * 
	 * @var int
	 */
	public $accessControlIdEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $accessControlIdIn;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $startDateGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $startDateLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $startDateGreaterThanOrEqualOrNull;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $startDateLessThanOrEqualOrNull;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $endDateGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $endDateLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $endDateGreaterThanOrEqualOrNull;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $endDateLessThanOrEqualOrNull;

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
