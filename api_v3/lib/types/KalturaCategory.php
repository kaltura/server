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
	 * @filter order
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
	 * The full ids of the Category
	 * 
	 * @var string
	 * @readonly
	 * @filter order,eq,likex
	 */
	public $fullIds;
	
	/**
	 * Number of entries in this Category (including child categories)
	 * 
	 * @var int
	 * @filter order
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
	 * @filter order
	 * @readonly
	 */
	public $directEntriesCount;
	
	
	/**
	 * Category external id, controlled and managed by the partner.
	 *  
	 * @var string
	 * @filter eq
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
	 * @filter gte,lte
	 * @readonly
	 * 
	 */
	public $membersCount;
	
	/**
	 * Number of pending members for this category
	 *
	 * @var int
	 * @filter gte,lte
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
	
	/**
	 * Can be used to store various partner related data as a numeric value
	 * 
	 * @var int
	 * @filter gte,lte,order
	 */
	public $partnerSortValue;
	
	/**
	 * Can be used to store various partner related data as a string 
	 * 
	 * @var string
	 */
	public $partnerData;
	
	/**
	 * Enable client side applications to define how to sort the category child categories 
	 * 
	 * @var KalturaCategoryOrderBy
	 */
	public $defaultOrderBy;
	
	private static $mapBetweenObjects = array
	(
		"id",
		"parentId",
		"depth",
		"name",
		"fullName",
		"fullIds",
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
		"partnerSortValue",
		"partnerData",
		"defaultOrderBy",
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
	
		if($this->referenceId)
		{
			$c = KalturaCriteria::create(categoryPeer::OM_CLASS);
			$c->add('category.REFERENCE_ID', $this->referenceId);
			$c->applyFilters();
			if(count($c->getFetchedIds()))
				throw new KalturaAPIException(KalturaErrors::REFERENCE_ID_ALREADY_EXISTS, $this->referenceId);
		}
		
		return parent::validateForInsert($propertiesToSkip);
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUpdate()
	 */
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		/* @var $sourceObject category */
		
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
	
		if($this->referenceId)
		{
			$c = KalturaCriteria::create(categoryPeer::OM_CLASS);
			$c->add('category.ID', $sourceObject->getId(), Criteria::NOT_EQUAL);
			$c->add('category.REFERENCE_ID', $this->referenceId);
			$c->applyFilters();
			if(count($c->getFetchedIds()))
				throw new KalturaAPIException(KalturaErrors::REFERENCE_ID_ALREADY_EXISTS, $this->referenceId);
		}
		
		return parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}
}