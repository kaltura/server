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
	 * @filter eq,likex,in, order
	 */
	public $fullName;
	
	/**
	 * The full ids of the Category
	 * 
	 * @var string
	 * @readonly
	 * @filter eq,likex, matchor
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
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $createdAt;
	
	/**
	 * Update date as Unix timestamp (In seconds)
	 *  
	 * @var time
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
	 * @requiresPermission insert,update
	 */
	public $privacy;
	
	/**
	 * If Category members are inherited from parent category or set manualy. 
	 * @var KalturaInheritanceType
	 * @filter eq,in
	 * @requiresPermission insert,update
	 */
	public $inheritanceType;
	
	//userJoinPolicy is readonly only since product asked - and not because anything else. server code is working, and readonly doccomment can be remove
	
	/**
	 * Who can ask to join this category
	 *  
	 * @var KalturaUserJoinPolicyType
	 * @requiresPermission insert,update
	 * @readonly
	 */
	public $userJoinPolicy;
	
	/**
	 * Default permissionLevel for new users
	 *  
	 * @var KalturaCategoryUserPermissionLevel
	 * @requiresPermission insert,update
	 */
	public $defaultPermissionLevel;
	
	/**
	 * Category Owner (User id)
	 *  
	 * @var string
	 * @requiresPermission insert,update
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
	 * @filter eq,empty
	 */
	public $referenceId;
	
	/**
	 * who can assign entries to this category
	 *  
	 * @var KalturaContributionPolicyType
	 * @filter eq
	 * @requiresPermission insert,update
	 */
	public $contributionPolicy;
	
	/**
	 * Number of active members for this category
	 *  
	 * @var int
	 * @filter gte,lte,order
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
	 * @filter eq
	 * @requiresPermission insert,update
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
	 * The category id that this category inherit its members and members permission (for contribution and join)
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
	
	/**
	 * 
	 * Number of direct children categories
	 * @filter order
	 * @var int
	 * @readonly
	 */
	public $directSubCategoriesCount;
	
	/**
	 * Moderation to add entries to this category by users that are not of permission level Manager or Moderator.  
	 * @var KalturaNullableBoolean
	 */
	public $moderation;
	
	/**
	 * Nunber of pending moderation entries
	 * @var int
	 * @readonly
	 */
	public $pendingEntriesCount;
	
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
		"directSubCategoriesCount",
		"moderation",
		"pendingEntriesCount",
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
	
	/* (non-PHPdoc)
	 * @see IFilterable::getExtraFilters()
	 */
	public function getExtraFilters()
	{
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see IFilterable::getFilterDocs()
	 */
	public function getFilterDocs()
	{
		return array();
	}
	
	/**
	 * validate parent id exists and if not - cannot set category to inherit from parent.
	 */
	public function validateParentId()
	{
		if ($this->parentId === null)
			$this->parentId = 0;
			
		if ($this->parentId !== 0)
		{
			$parentCategoryDb = categoryPeer::retrieveByPK($this->parentId);
			if (!$parentCategoryDb)
				throw new KalturaAPIException(KalturaErrors::PARENT_CATEGORY_NOT_FOUND, $this->parentId);
		}
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForInsert()
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyMinLength("name", 1);
		$this->validatePropertyMaxLength("name", categoryPeer::MAX_CATEGORY_NAME);
		$this->validateCategory();
		
		if ($this->parentId !== null)
		{
			$this->validateParentId();
		}
		elseif ($this->inheritanceType == KalturaInheritanceType::INHERIT)
		{
			//cannot inherit member with no parant
			throw new KalturaAPIException(KalturaErrors::CANNOT_INHERIT_MEMBERS_WHEN_PARENT_CATEGORY_IS_NOT_SET);
		}
		
		return parent::validateForInsert($propertiesToSkip);
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUpdate()
	 */
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		if ($this->name !== null)
		{
			$this->validatePropertyMinLength("name", 1);
			$this->validatePropertyMaxLength("name", categoryPeer::MAX_CATEGORY_NAME);
		}
		
		if ($this->parentId !== null)
		{
			$this->validateParentId();
		}
		elseif ($this->inheritanceType == KalturaInheritanceType::INHERIT && 
		($this->parentId instanceof KalturaNullField || $sourceObject->getParentId() == null))
		{
			//cannot inherit member with no parant
			throw new KalturaAPIException(KalturaErrors::CANNOT_INHERIT_MEMBERS_WHEN_PARENT_CATEGORY_IS_NOT_SET);
		}
			
		$this->validateCategory($sourceObject);
			
		return parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}
	
	/**
	 * validate category fields
	 * 1. category that inherit memebers cannot set values to inherited fields.
	 * 2. validate the owner id exists as kuser
	 * 
	 * @param category $sourceObject
	 */
	private function validateCategory(category $sourceObject = null)
	{
		if($this->privacyContext != null && kEntitlementUtils::getEntitlementEnforcement())
			throw new KalturaAPIException(KalturaErrors::CANNOT_UPDATE_CATEGORY_PRIVACY_CONTEXT);
			
		if(!$this->privacyContext && (!$sourceObject || !$sourceObject->getPrivacyContexts()))
		{
			$isInheritedPrivacyContext = true;
			if ($this->parentId != null)
			{
				$parentCategory = categoryPeer::retrieveByPK($this->parentId);
				if(!$parentCategory)
					throw new KalturaAPIException(KalturaErrors::CATEGORY_NOT_FOUND, $this->parentId);
				
				if($parentCategory->getPrivacyContexts() == '')
					$isInheritedPrivacyContext = false;
			}
			else
			{
				$isInheritedPrivacyContext = false;
			}
			
			if(!$isInheritedPrivacyContext)
			{
				if($this->appearInList != KalturaAppearInListType::PARTNER_ONLY && !$this->isNull('appearInList'))
					throw new KalturaAPIException(KalturaErrors::CANNOT_SET_APPEAR_IN_LIST_FIELD_WITH_NO_PRIVACY_CONTEXT);
				
				if ($this->inheritanceType != KalturaInheritanceType::MANUAL && !$this->isNull('inheritanceType'))
					throw new KalturaAPIException(KalturaErrors::CANNOT_SET_INHERITANCE_TYPE_FIELD_WITH_NO_PRIVACY_CONTEXT);
					 
				if ($this->privacy != KalturaPrivacyType::ALL && !$this->isNull('privacy'))
					throw new KalturaAPIException(KalturaErrors::CANNOT_SET_PRIVACY_FIELD_WITH_NO_PRIVACY_CONTEXT);
					 
				if (!$this->isNull('owner'))
					throw new KalturaAPIException(KalturaErrors::CANNOT_SET_OWNER_FIELD_WITH_NO_PRIVACY_CONTEXT);
	
				if ($this->userJoinPolicy != KalturaUserJoinPolicyType::NOT_ALLOWED && !$this->isNull('userJoinPolicy'))
					throw new KalturaAPIException(KalturaErrors::CANNOT_SET_USER_JOIN_POLICY_FIELD_WITH_NO_PRIVACY_CONTEXT);
				
				if ($this->contributionPolicy != KalturaContributionPolicyType::ALL  && !$this->isNull('contributionPolicy'))
				   throw new KalturaAPIException(KalturaErrors::CANNOT_SET_CONTIRUBUTION_POLICY_FIELD_WITH_NO_PRIVACY_CONTEXT);
				   
				if ($this->defaultPermissionLevel != KalturaCategoryUserPermissionLevel::MEMBER && !$this->isNull('defaultPermissionLevel'))
				   throw new KalturaAPIException(KalturaErrors::CANNOT_SET_DEFAULT_PERMISSION_LEVEL_FIELD_WITH_NO_PRIVACY_CONTEXT);
			}
		}
		
		if(($this->inheritanceType != KalturaInheritanceType::MANUAL && $this->inheritanceType != null) || 
			($this->inheritanceType == null && $sourceObject && $sourceObject->getInheritanceType() != KalturaInheritanceType::MANUAL))
		{	
			if ($this->owner != null)
			{
				if (!$sourceObject)
					throw new KalturaAPIException(KalturaErrors::CANNOT_SET_OWNER_WHEN_CATEGORY_INHERIT_MEMBERS);
				elseif ($this->owner != $sourceObject->getKuserId())
					throw new KalturaAPIException(KalturaErrors::CANNOT_SET_OWNER_WHEN_CATEGORY_INHERIT_MEMBERS);
			}
				
			if ($this->userJoinPolicy != null)
			{
				if (!$sourceObject)
					throw new KalturaAPIException(KalturaErrors::CANNOT_SET_USER_JOIN_POLICY_WHEN_CATEGORY_INHERIT_MEMBERS);
				elseif ($this->userJoinPolicy != $sourceObject->getUserJoinPolicy())
					throw new KalturaAPIException(KalturaErrors::CANNOT_SET_USER_JOIN_POLICY_WHEN_CATEGORY_INHERIT_MEMBERS);
			}
				
			if ($this->defaultPermissionLevel != null)
			{
				if (!$sourceObject)
					throw new KalturaAPIException(KalturaErrors::CANNOT_SET_DEFAULT_PERMISSION_LEVEL_WHEN_CATEGORY_INHERIT_MEMBERS);
				elseif ($this->defaultPermissionLevel != $sourceObject->getDefaultPermissionLevel())
					throw new KalturaAPIException(KalturaErrors::CANNOT_SET_DEFAULT_PERMISSION_LEVEL_WHEN_CATEGORY_INHERIT_MEMBERS);
			}
		}
		
		if (!is_null($sourceObject))
		{
			$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
			$partner = PartnerPeer::retrieveByPK($partnerId);
			if (!$partner || $partner->getFeaturesStatusByType(IndexObjectType::LOCK_CATEGORY))
				throw new KalturaAPIException(KalturaErrors::CATEGORIES_LOCKED);		
		}

		if ($this->owner && $this->owner != '' && !($this->owner instanceof KalturaNullField) )
		{
			if(!preg_match(kuser::PUSER_ID_REGEXP, $this->owner))
				throw new KalturaAPIException(KalturaErrors::CANNOT_SET_OWNER_FIELD_WITH_USER_ID, $this->owner);
		
			$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
			kuserPeer::createKuserForPartner($partnerId, $this->owner);
		}
		
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toInsertableObject($object_to_fill, $props_to_skip)
	 */
	public function toInsertableObject($object_to_fill = null , $props_to_skip = array())
	{
		$hasPrivacyContext = false;
		if ($this->privacyContext)
		{
			$hasPrivacyContext = true;
		}
		elseif ($this->parentId != null)
		{
			$parentCategory = categoryPeer::retrieveByPK($this->parentId);
			if(!$parentCategory)
				throw new KalturaAPIException(KalturaErrors::CATEGORY_NOT_FOUND, $this->parentId);
			
			if($parentCategory->getPrivacyContexts())
				$hasPrivacyContext = true;
		}
		
		if ($hasPrivacyContext)
		{
			if (!$this->owner && $this->inheritanceType != KalturaInheritanceType::INHERIT)
			{
				if (kCurrentContext::getCurrentKsKuser())
					$this->owner = kCurrentContext::getCurrentKsKuser()->getPuserId();
			}
		}
		
		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}
	
 	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		$this->trimStringProperties(array("name"));
		
		return parent::toObject($object_to_fill, $props_to_skip);
	}	
}