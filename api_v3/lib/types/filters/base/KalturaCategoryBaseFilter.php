<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
abstract class KalturaCategoryBaseFilter extends KalturaFilter
{
	static private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"idIn" => "_in_id",
		"parentIdEqual" => "_eq_parent_id",
		"parentIdIn" => "_in_parent_id",
		"depthEqual" => "_eq_depth",
		"fullNameEqual" => "_eq_full_name",
		"fullNameStartsWith" => "_likex_full_name",
		"fullNameIn" => "_in_full_name",
		"fullIdsEqual" => "_eq_full_ids",
		"fullIdsStartsWith" => "_likex_full_ids",
		"fullIdsMatchOr" => "_matchor_full_ids",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"createdAtLessThanOrEqual" => "_lte_created_at",
		"updatedAtGreaterThanOrEqual" => "_gte_updated_at",
		"updatedAtLessThanOrEqual" => "_lte_updated_at",
		"tagsLike" => "_like_tags",
		"tagsMultiLikeOr" => "_mlikeor_tags",
		"tagsMultiLikeAnd" => "_mlikeand_tags",
		"appearInListEqual" => "_eq_appear_in_list",
		"privacyEqual" => "_eq_privacy",
		"privacyIn" => "_in_privacy",
		"inheritanceTypeEqual" => "_eq_inheritance_type",
		"inheritanceTypeIn" => "_in_inheritance_type",
		"referenceIdEqual" => "_eq_reference_id",
		"referenceIdEmpty" => "_empty_reference_id",
		"contributionPolicyEqual" => "_eq_contribution_policy",
		"membersCountGreaterThanOrEqual" => "_gte_members_count",
		"membersCountLessThanOrEqual" => "_lte_members_count",
		"pendingMembersCountGreaterThanOrEqual" => "_gte_pending_members_count",
		"pendingMembersCountLessThanOrEqual" => "_lte_pending_members_count",
		"privacyContextEqual" => "_eq_privacy_context",
		"statusEqual" => "_eq_status",
		"statusIn" => "_in_status",
		"inheritedParentIdEqual" => "_eq_inherited_parent_id",
		"inheritedParentIdIn" => "_in_inherited_parent_id",
		"partnerSortValueGreaterThanOrEqual" => "_gte_partner_sort_value",
		"partnerSortValueLessThanOrEqual" => "_lte_partner_sort_value",
	);

	static private $order_by_map = array
	(
		"+depth" => "+depth",
		"-depth" => "-depth",
		"+name" => "+name",
		"-name" => "-name",
		"+fullName" => "+full_name",
		"-fullName" => "-full_name",
		"+entriesCount" => "+entries_count",
		"-entriesCount" => "-entries_count",
		"+createdAt" => "+created_at",
		"-createdAt" => "-created_at",
		"+updatedAt" => "+updated_at",
		"-updatedAt" => "-updated_at",
		"+directEntriesCount" => "+direct_entries_count",
		"-directEntriesCount" => "-direct_entries_count",
		"+membersCount" => "+members_count",
		"-membersCount" => "-members_count",
		"+partnerSortValue" => "+partner_sort_value",
		"-partnerSortValue" => "-partner_sort_value",
		"+directSubCategoriesCount" => "+direct_sub_categories_count",
		"-directSubCategoriesCount" => "-direct_sub_categories_count",
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
	 * @var int
	 */
	public $idEqual;

	/**
	 * @var string
	 */
	public $idIn;

	/**
	 * @var int
	 */
	public $parentIdEqual;

	/**
	 * @var string
	 */
	public $parentIdIn;

	/**
	 * @var int
	 */
	public $depthEqual;

	/**
	 * @var string
	 */
	public $fullNameEqual;

	/**
	 * @var string
	 */
	public $fullNameStartsWith;

	/**
	 * @var string
	 */
	public $fullNameIn;

	/**
	 * @var string
	 */
	public $fullIdsEqual;

	/**
	 * @var string
	 */
	public $fullIdsStartsWith;

	/**
	 * @var string
	 */
	public $fullIdsMatchOr;

	/**
	 * @var time
	 */
	public $createdAtGreaterThanOrEqual;

	/**
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
	 * @var string
	 */
	public $tagsLike;

	/**
	 * @var string
	 */
	public $tagsMultiLikeOr;

	/**
	 * @var string
	 */
	public $tagsMultiLikeAnd;

	/**
	 * @var KalturaAppearInListType
	 */
	public $appearInListEqual;

	/**
	 * @var KalturaPrivacyType
	 */
	public $privacyEqual;

	/**
	 * @var string
	 */
	public $privacyIn;

	/**
	 * @var KalturaInheritanceType
	 */
	public $inheritanceTypeEqual;

	/**
	 * @var string
	 */
	public $inheritanceTypeIn;

	/**
	 * @var string
	 */
	public $referenceIdEqual;

	/**
	 * @var KalturaNullableBoolean
	 */
	public $referenceIdEmpty;

	/**
	 * @var KalturaContributionPolicyType
	 */
	public $contributionPolicyEqual;

	/**
	 * @var int
	 */
	public $membersCountGreaterThanOrEqual;

	/**
	 * @var int
	 */
	public $membersCountLessThanOrEqual;

	/**
	 * @var int
	 */
	public $pendingMembersCountGreaterThanOrEqual;

	/**
	 * @var int
	 */
	public $pendingMembersCountLessThanOrEqual;

	/**
	 * @var string
	 */
	public $privacyContextEqual;

	/**
	 * @var KalturaCategoryStatus
	 */
	public $statusEqual;

	/**
	 * @var string
	 */
	public $statusIn;

	/**
	 * @var int
	 */
	public $inheritedParentIdEqual;

	/**
	 * @var string
	 */
	public $inheritedParentIdIn;

	/**
	 * @var int
	 */
	public $partnerSortValueGreaterThanOrEqual;

	/**
	 * @var int
	 */
	public $partnerSortValueLessThanOrEqual;
}
