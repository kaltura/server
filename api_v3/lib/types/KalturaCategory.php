<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaCategory extends KalturaObject implements IFilterable 
{
	/**
	 * The id of the Category
	 * 
	 * @var int
	 * @readonly
	 * @filter eq,in
	 */
	public $id;
	
	/**
	 * 
	 * @var int
	 * @filter eq,in
	 */
	public $parentId;
	
	/**
	 * 
	 * @var int
	 * @readonly
	 * @filter order,eq
	 */
	public $depth;
	
	/**
	 * @var int
	 * @readonly
	 */
	public $partnerId;
	
	/**
	 * The name of the Category. 
	 * The following characters are not allowed: '<', '>', ','
	 * 
	 * @var string
	 */
	public $name;
	
	/**
	 * The full name of the Category
	 * 
	 * @var string
	 * @readonly
	 * @filter order,eq,likex
	 */
	public $fullName;
	
	/**
	 * Number of entries in this Category (including child categories)
	 * 
	 * @var int
	 * @readonly
	 */
	public $entriesCount;
	
	/**
	 * Creation date as Unix timestamp (In seconds)
	 *  
	 * @var int
	 * @readonly
	 * @filter order
	 */
	public $createdAt;
	
	/**
	 * Category description
	 * 
	 * @var string
	 */
	public $description;
	
	/**
	 * Category tags
	 * 
	 * @var string
	 * @filter like,mlikeor,mlikeand
	 */
	public $tags;
	
	/**
	 * Category listing - if category will be returned for list action.
	 * 
	 * @var KalturaListingType
	 * @filter eq
	 */
	public $listing;
	
	/**
	 * @var KalturaPrivacyType
	 * @filter eq,in
	 */
	public $privacy;
	
	/**
	 * If Category members are inherited from parent category or set manualy. 
	 * @var KalturaCategoryMembershipSettingType
	 * @filter eq,in
	 */
	public $membershipSetting;
	
	
	/**
	 * Who can ask to join this category
	 *  
	 * @var KalturaUserJoinPolicyType
	 */
	public $userJoinPolicy;
	
	/**
	 * Default permissionLevel for new users
	 *  
	 * @var KalturaCategoryUserPermissionLevel
	 */
	public $defaultPermissionLevel;
	
	/**
	 * Category Owner (User id)
	 *  
	 * @var int
	 */
	public $owner;
	
	/**
	 * Number of entries that belong to this category directly
	 *  
	 * @var int
	 * @readonly
	 */
	public $directEntriesCount;
	
	
	/**
	 * reference Id - external reference id
	 *  
	 * @var string
	 */
	public $referenceId;
	
	/**
	 * contribution Policy - who can assign entries to this category
	 *  
	 * @var KalturaContributionPolicyType
	 */
	public $contributionPolicy;
	
	/**
	 * Number of active members for this category
	 *  
	 * @var int
	 * @readonly
	 */
	public $membersCount;
	
	/**
	 * Number of pending members for this category
	 *
	 * @var int
	 * @readonly
	 */
	public $pendingMembersCount;
	
	/**
	 * Set privacy context for search
	 *  
	 * @var KalturaNullableBoolean
	 */
	public $privacyContext;
	
	/**
	 * Number of entries that belong to this category directly
	 *
	 * @var string
	 * @readonly
	 */
	public $privacyContexts;
	
	/**
	 * Status
	 * 
	 * @var KalturaCategoryStatus
	 * @readonly
	 * @filter eq,in
	 */
	public $status;
	
	private static $mapBetweenObjects = array
	(
		"id",
		"parentId",
		"depth",
		"name",
		"fullName",
		"partnerId",
		"entriesCount",
		"createdAt",
		"description",
		"tags",
		"listing",
		"privacy",
		"membershipSetting",
		"userJoinPolicy",
		"defaultPermissionLevel",
		"owner",
		"directEntriesCount",
		"referenceId",
		"contributionPolicy",
		"membersCount",
		"pendingMembersCount",
		"privacyContext",	
		"privacyContexts",
		"status"
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
	
	public function getExtraFilters()
	{
		return array();
	}
	
	public function getFilterDocs()
	{
		return array();
	}
	
	public function validateParentId(KalturaCategory $category)
	{
		if ($category->parentId === null)
			$category->parentId = 0;
			
		if ($category->parentId !== 0)
		{
			$parentCategoryDb = categoryPeer::retrieveByPK($category->parentId);
			if (!$parentCategoryDb)
				throw new KalturaAPIException(KalturaErrors::PARENT_CATEGORY_NOT_FOUND, $category->parentId);	
		}
		elseif ($category->membershipSetting == KalturaCategoryMembershipSettingType::INHERT)
		{
			//cannot inherit member with no parant
			throw new KalturaAPIException(KalturaErrors::CANNOT_INHERIT_MEMBERS_WHEN_PARENT_CATEGORY_IS_NOT_SET);
		}
		
		
	}
}