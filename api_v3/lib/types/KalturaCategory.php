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
	 * Category displayInSearch - if category will be returned for list action.
	 * 
	 * @var KalturaDisplayInSearchType
	 * @filter eq
	 */
	public $displayInSearch;
	
	/**
	 * defines the privacy of the entries that assigned to this category
	 * 
	 * @var KalturaPrivacyType
	 * @filter eq,in
	 */
	public $privacy;
	
	/**
	 * If Category members are inherited from parent category or set manualy. 
	 * @var KalturaInheritanceType
	 * @filter eq,in
	 */
	public $inheritance;
	
	
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
	 * @var string
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
	 * who can assign entries to this category
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
	 * Set privacy context for search entries that assiged to private and public categories. the entries will be private if the search context is set with those categories.
	 *  
	 * @var KalturaNullableBoolean
	 */
	public $privacyContext;
	
	/**
	 * comma separated parents that defines a privacyContext for search
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
		"displayInSearch",
		"privacy",
		"inheritance",
		"userJoinPolicy",
		"defaultPermissionLevel",
		"owner" => "puserId",
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
		elseif ($category->inheritance == KalturaInheritanceType::INHERT)
		{
			//cannot inherit member with no parant
			throw new KalturaAPIException(KalturaErrors::CANNOT_INHERIT_MEMBERS_WHEN_PARENT_CATEGORY_IS_NOT_SET);
		}
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForInsert()
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		if ($this->owner && $this->owner != '')
		{
			$kuser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::$ks_partner_id, $this->owner);
			if (!$kuser)
				throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID, $this->owner);
		}
		
		return parent::validateForInsert($propertiesToSkip);
	}
}