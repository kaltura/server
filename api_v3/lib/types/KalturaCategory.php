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
	 * @filter gte,lte,order
	 */
	public $createdAt;
	
	/**
	 * Update date as Unix timestamp (In seconds)
	 *  
	 * @var int
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $updatedAt;
	
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
	 * If category will be returned for list action.
	 * 
	 * @var KalturaAppearInListType
	 * @filter eq
	 */
	public $appearInList;
	
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
	public $inheritanceType;
	
	
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
	 * Category external id, controlled and managed by the partner.
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
	 * @var string
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
	
	/**
	 * Status
	 * 
	 * @var int
	 * @readonly
	 * @filter eq,in
	 */
	public $inheritedParentId;
	
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
		"updatedAt",
		"description",
		"tags",
		"appearInList" => "displayInSearch",
		"privacy",
		"inheritanceType",
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
		"status",
		"inheritedParentId",
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
		elseif ($category->inheritanceType == KalturaInheritanceType::INHERIT)
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
		if ($this->inheritanceType == KalturaInheritanceType::INHERIT)
		{
			if ($this->userJoinPolicy != null ||
				$this->defaultPermissionLevel != null ||
				$this->owner != null ||
				$this->contributionPolicy != null)
			{
				throw new KalturaAPIException(KalturaErrors::CATEGORY_INHERIT_MEMBERS_CANNOT_UPDATE_INHERITED_ATTRIBUTES);
			}
		}
		
		if ($this->owner && $this->owner != '')
		{
			$kuser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::$ks_partner_id, $this->owner);
			if (!$kuser)
				throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID, $this->owner);
		}
		
		return parent::validateForInsert($propertiesToSkip);
	}
	
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		if (($sourceObject->getInheritanceType() == KalturaInheritanceType::INHERIT && $this->inheritanceType == null) || 
			($this->inheritanceType == KalturaInheritanceType::INHERIT))
		{
			if ($this->userJoinPolicy != null ||
				$this->defaultPermissionLevel != null ||
				$this->owner != null ||
				$this->contributionPolicy != null)
			{
				throw new KalturaAPIException(KalturaErrors::CATEGORY_INHERIT_MEMBERS_CANNOT_UPDATE_INHERITED_ATTRIBUTES);
			}
		}
		
		return parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}
}