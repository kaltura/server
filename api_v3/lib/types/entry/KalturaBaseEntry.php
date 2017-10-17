<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaBaseEntry extends KalturaObject implements IRelatedFilterable, IApiObjectFactory
{
	/**
	 * Auto generated 10 characters alphanumeric string
	 * 
	 * @var string
	 * @readonly
	 * @filter eq,in,notin
	 */
	public $id;
	
	/**
	 * Entry name (Min 1 chars)
	 * 
	 * @var string
	 * @filter like,mlikeor,mlikeand,eq,order
	 * @requiresPermission update
	 */
	public $name;
	
	/**
	 * Entry description
	 * 
	 * @var string
	 * @requiresPermission update
	 */
	public $description;
	
	/**
	 * 
	 * 
	 * @var int
	 * @readonly
	 * @filter eq,in
	 */
	public $partnerId;
	
	/**
	 * The ID of the user who is the owner of this entry 
	 * 
	 * @var string
	 * @filter eq,in,notin
	 */
	public $userId;
	
	/**
	 * The ID of the user who created this entry 
	 * 
	 * @var string
	 * @insertonly
	 * @filter eq
	 */
	public $creatorId;
	
	/**
	 * Entry tags
	 * 
	 * @var string
	 * @filter like,mlikeor,mlikeand
	 * @requiresPermission update
	 */
	public $tags;
	
	/**
	 * Entry admin tags can be updated only by administrators
	 * 
	 * @var string
	 * @filter like,mlikeor,mlikeand
	 */
	public $adminTags;
	
	/**
	 * Comma separated list of full names of categories to which this entry belongs. Only categories that don't have entitlement (privacy context) are listed, to retrieve the full list of categories, use the categoryEntry.list action. 
	 * 
	 * @var string
	 * @deprecated
	 * @filter matchand, matchor, notcontains
	 * @requiresPermission insert,update
	 */
	public $categories;
	
	/**
	 * Comma separated list of ids of categories to which this entry belongs. Only categories that don't have entitlement (privacy context) are listed, to retrieve the full list of categories, use the categoryEntry.list action. 
	 * 
	 * @var string
	 * @deprecated
	 * @filter matchand, matchor, notcontains, empty
	 * @requiresPermission insert,update
	 */
	public $categoriesIds;
	
	/**
	 * 
	 * @var KalturaEntryStatus
	 * @readonly
	 * @filter eq,not,in,notin
	 */
	public $status;
	
	/**
	 * Entry moderation status
	 * 
	 * @var KalturaEntryModerationStatus
	 * @readonly
	 * @filter eq,not,in,notin
	 */
	public $moderationStatus;
	
	/**
	 * Number of moderation requests waiting for this entry
	 * 
	 * @var int
	 * @readonly
	 * @filter order
	 */
	public $moderationCount;
	
	/**
	 * The type of the entry, this is auto filled by the derived entry object
	 * 
	 * @var KalturaEntryType
	 * @filter eq,in
	 */
	public $type;
	
	/**
	 * Entry creation date as Unix timestamp (In seconds)
	 * 
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $createdAt;
	
	/**
	 * Entry update date as Unix timestamp (In seconds)
	 * 
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $updatedAt;
	
	/**
	 * The calculated average rank. rank = totalRank / votes
	 * 
	 * @var float
	 * @readonly
	 * @filter order
	 */
	public $rank;
	
	/**
	 * The sum of all rank values submitted to the baseEntry.anonymousRank action
	 * 
	 * @var int
	 * @readonly
	 * @filter lte,gte,order
	 */
	public $totalRank;
	
	/**
	 * A count of all requests made to the baseEntry.anonymousRank action
	 *  
	 * @var int
	 * @readonly
	 */
	public $votes;
	
	/**
	 * 
	 * @var int
	 * @filter eq
	 */
	public $groupId;
	
	/**
	 * Can be used to store various partner related data as a string 
	 * 
	 * @var string
	 */
	public $partnerData;
	
	/**
	 * Download URL for the entry
	 * 
	 * @var string
	 * @readonly
	 */
	public $downloadUrl;
	
	/**
	 * Indexed search text for full text search
	 * @var string
	 * @readonly
	 * @filter matchand, matchor
	 */
	public $searchText;
	
	/**
	 * License type used for this entry
	 * 
	 * @var KalturaLicenseType
	 */
	public $licenseType;
	
	/**
	 * Version of the entry data
	 *
	 * @var int
	 * @readonly
	 */
	public $version;
	
	/**
	 * Thumbnail URL
	 * 
	 * @var string
	 * @readonly
	 */
	public $thumbnailUrl;
	
	/**
	 * The Access Control ID assigned to this entry (null when not set, send -1 to remove)  
	 * 
	 * @var int
	 * @filter eq,in
	 * @requiresPermission insert,update
	 */
	public $accessControlId;
	
	/**
	 * Entry scheduling start date (null when not set, send -1 to remove)
	 *  
	 * @var time
	 * @filter gte,lte,gteornull,lteornull,order
	 * @requiresPermission insert,update
	 */
	public $startDate;
	
	/**
	 * Entry scheduling end date (null when not set, send -1 to remove)
	 * 
	 * @var time
	 * @filter gte,lte,gteornull,lteornull,order
	 * @requiresPermission insert,update
	 */
	public $endDate;
	
	/**
	 * Entry external reference id
	 * 
	 * @var string
	 * @filter eq,in
	 * @requiresPermission insert,update
	 */
	public $referenceId;
	
	/**
	 * ID of temporary entry that will replace this entry when it's approved and ready for replacement
	 * 
	 * @var string
	 * @filter eq,in
	 * @readonly
	 */
	public $replacingEntryId;
	
	/**
	 * ID of the entry that will be replaced when the replacement approved and this entry is ready
	 * 
	 * @var string
	 * @filter eq,in
	 * @readonly
	 */
	public $replacedEntryId;
	
	/**
	 * Status of the replacement readiness and approval
	 * 
	 * @var KalturaEntryReplacementStatus
	 * @filter eq,in
	 * @readonly
	 */
	public $replacementStatus;
	
	/**
	 * Can be used to store various partner related data as a numeric value
	 * 
	 * @var int
	 * @filter gte,lte,order
	 */
	public $partnerSortValue;
	
	/**
	 * Override the default ingestion profile  
	 * 
	 * @var int
	 */
	public $conversionProfileId;
	
	/**
	 * IF not empty, points to an entry ID the should replace this current entry's id. 
	 *
	 * @var string
	 */
	public $redirectEntryId;

	/**
	 * ID of source root entry, used for clipped, skipped and cropped entries that created from another entry
	 *
	 * @var string
	 * @filter eq,in
	 * @readonly
	 */
	public $rootEntryId;
	
	/**
 	 * ID of source root entry, used for defining entires association
 	 * @var string
 	 * @filter eq
 	 */
	public $parentEntryId;
	
	/**
	 * clipping, skipping and cropping attributes that used to create this entry  
	 * 
	 * @var KalturaOperationAttributesArray
	 */
	public $operationAttributes;
	
	/**
	 * list of user ids that are entitled to edit the entry (no server enforcement) The difference between entitledUsersEdit, entitledUsersPublish and entitledUsersView is applicative only
	 * 
	 * @var string
	 * @filter matchand,matchor
	 */
	public $entitledUsersEdit;
		
	/**
	 * list of user ids that are entitled to publish the entry (no server enforcement) The difference between entitledUsersEdit, entitledUsersPublish and entitledUsersView is applicative only
	 * 
	 * @var string
	 * @filter matchand,matchor
	 */
	public $entitledUsersPublish;
	
	/**
	 * list of user ids that are entitled to view the entry (no server enforcement) The difference between entitledUsersEdit, entitledUsersPublish and entitledUsersView is applicative only
	 * 
	 * @var string
	 * @filter matchand,matchor
	 */
	public $entitledUsersView;

	/**
	 * Comma seperated string of the capabilities of the entry. Any capability needed can be added to this list.
	 *
	 * @dynamicType KalturaEntryCapability
	 * @var string
	 * @readonly
	 */
	public $capabilities;

	/**
	 * Template entry id 
	 *
	 * @var string
	 * @insertonly
	 */
	public $templateEntryId;

	/**
	 * should we display this entry in search
	 *
	 * @var KalturaEntryDisplayInSearchType
	 */
	public $displayInSearch;

	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array 
	 (
	 	"id", 
	 	"name", 
	 	"description",
	 	"tags",
	 	"adminTags",
	 	"partnerId",
	 	"moderationStatus",
	 	"moderationCount",
	 	"status", 
	 	"type", // this will need to be set according to the class
	 	"createdAt", 
	 	"updatedAt", 
	 	"rank" => "rankAsFloat", 
	 	"totalRank",
	 	"votes",
	 	"groupId",
	 	"partnerData", 
	 	"downloadUrl",
	 	"licenseType",
	 	"searchText",
	 	"version",
	 	"thumbnailUrl",
	 	"accessControlId",
	 	"startDate",
	 	"endDate",
	 	"referenceId",
		"replacingEntryId",
		"replacedEntryId",
		"replacementStatus",
		"partnerSortValue",
	 	"categories",
	 	"categoriesIds",
	 	"conversionProfileId" => "conversionQuality",
	 	"redirectEntryId",
	 	"rootEntryId",
	 	"parentEntryId",
	 	"entitledUsersEdit" => "entitledPusersEdit",
	 	"entitledUsersPublish" => "entitledPusersPublish",
	 	"entitledUsersView" => "entitledPusersView",
	 	"operationAttributes",
		"capabilities",
		"templateEntryId",
		"displayInSearch",
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
		{
			$dbObject = new entry();
		}
		
		parent::toObject($dbObject, $skip);
		
		if ($this->startDate === -1) // save -1 as null
			$dbObject->setStartDate(null);
			
		if ($this->endDate === -1) // save -1 as null
			$dbObject->setEndDate(null);
			
		
		if ($this->categoriesIds !== null && $this->categories === null)
		{
			$catsNames = array ();
			
			$cats = explode(",", $this->categoriesIds);
			
			foreach ($cats as $cat)
			{ 
				$catName = categoryPeer::retrieveByPK($cat);
				if (is_null($catName))
					throw new KalturaAPIException(KalturaErrors::CATEGORY_NOT_FOUND, $cat);
					
				$catsNames[] = $catName->getFullName();
			}
			
			$catNames = implode(",", $catsNames);
			$dbObject->setCategories($catNames);
		}
		if (!is_null($this->userId))
			$dbObject->setPuserId($this->userId);
			
		if (!is_null($this->creatorId))
			$dbObject->setCreatorPuserId($this->creatorId);
		
		return $dbObject;
	}
	
	public function doFromObject($sourceObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		if(!$sourceObject)
			return;
	
		entryPeer::addValidatedEntry($sourceObject->getId());		
		parent::doFromObject($sourceObject, $responseProfile);
		
		$partnerId = kCurrentContext::$ks_partner_id ? kCurrentContext::$ks_partner_id : kCurrentContext::$partner_id;
		
		if (implode(',', kEntitlementUtils::getKsPrivacyContext()) != kEntitlementUtils::getDefaultContextString($partnerId) )
		{
			$this->categories = null;
			$this->categoriesIds = null;
		}
		if (!kConf::hasParam('protect_userid_in_api') || !in_array($sourceObject->getPartnerId(), kConf::get('protect_userid_in_api')) || !in_array(kCurrentContext::getCurrentSessionType(), array(kSessionBase::SESSION_TYPE_NONE,kSessionBase::SESSION_TYPE_WIDGET))){
			if($this->shouldGet('userId', $responseProfile))
				$this->userId = $sourceObject->getPuserId();
			if($this->shouldGet('creatorId', $responseProfile))
				$this->creatorId = $sourceObject->getCreatorPuserId();
		}
	}
	
	public function validateObjectsExist(entry $sourceObject = null)
	{
		$this->validateConversionProfile($sourceObject);
	
		if(!is_null($this->accessControlId))
		{
			$accessControlProfile = accessControlPeer::retrieveByPK($this->accessControlId);
			if(!$accessControlProfile)
				throw new KalturaAPIException(KalturaErrors::ACCESS_CONTROL_ID_NOT_FOUND, $this->accessControlId);
		}
	}
	
	public function validateConversionProfile(entry $sourceObject = null)
	{
		if(is_null($this->conversionProfileId))
			return;
			
		if($sourceObject && $sourceObject->getStatus() != entryStatus::NO_CONTENT)
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_ENTRY_STATUS, $this->getFormattedPropertyNameWithClassName('conversionProfileId'), $sourceObject->getStatus());
		
		if($this->conversionProfileId != conversionProfile2::CONVERSION_PROFILE_NONE)
		{
			$conversionProfile = conversionProfile2Peer::retrieveByPK($this->conversionProfileId);
			if(!$conversionProfile || $conversionProfile->getType() != ConversionProfileType::MEDIA)
				throw new KalturaAPIException(KalturaErrors::CONVERSION_PROFILE_ID_NOT_FOUND, $this->conversionProfileId);
		}
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForInsert()
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validateUsers();
		$this->validateCategories();
		$this->validatePropertyMinLength('referenceId', 2, true);
		$this->validateObjectsExist();
		$this->validateParentEntryId();
	
//		if($this->referenceId)
//		{
//			$c = KalturaCriteria::create(entryPeer::OM_CLASS);
//			$c->add('entry.REFERENCE_ID', $this->referenceId);
//			$c->applyFilters();
//			if(count($c->getFetchedIds()))
//				throw new KalturaAPIException(KalturaErrors::REFERENCE_ID_ALREADY_EXISTS, $this->referenceId);
//		}

		$this->validateDisplayInSearch();
		
		return parent::validateForInsert($propertiesToSkip);
	}
	
	/**
	 * Validate that no forbiden attributes are added to an entry that has a parent entry assigned to it.
	 */
	
	public function validateParentEntryId() 
	{
		//An entry with a parent entry id cannot be assigned to categories nor have access control/scheduling
		if ($this->parentEntryId && ($this->categories || $this->categoriesIds || $this->accessControlId || $this->startDate || $this->endDate))
			throw new KalturaAPIException(KalturaErrors::ASSIGNING_INFO_TO_ENTRY_WITH_PARENT_IS_FORBIDDEN, $this->parentEntryId);
			
		//Parent entry id must exists before assigning it to a child entry
		if ($this->parentEntryId)
		{
			$entry = entryPeer::retrieveByPK($this->parentEntryId);
			if(!$entry)
				throw new KalturaAPIException(KalturaErrors::PARENT_ENTRY_ID_NOT_FOUND, $this->parentEntryId);
		}
	}
		
	/**
	 * To validate if user is entitled to the category � all needed is to select from the db.
	 * 
	 * @throws KalturaErrors::ENTRY_CATEGORY_FIELD_IS_DEPRECATED
	 */
	public function validateCategories()
	{
		$partnerId = kCurrentContext::$ks_partner_id ? kCurrentContext::$ks_partner_id : kCurrentContext::$partner_id;
		
		if (implode(',', kEntitlementUtils::getKsPrivacyContext()) !=  kEntitlementUtils::getDefaultContextString($partnerId) &&
			($this->categoriesIds != null || $this->categories != null))
			throw new KalturaAPIException(KalturaErrors::ENTRY_CATEGORY_FIELD_IS_DEPRECATED);
			
		if ($this->categoriesIds != null)
		{
			$catsNames = array ();
			
			$cats = explode(",", $this->categoriesIds);
			
			foreach ($cats as $cat)
			{ 
				$catName = categoryPeer::retrieveByPK($cat);
				if (is_null($catName))
					throw new KalturaAPIException(KalturaErrors::CATEGORY_NOT_FOUND, $cat);
			}
		}
		
		if ($this->categories != null)
		{
			$catsNames = array ();
			
			$cats = explode(",", $this->categories);
			
			foreach ($cats as $cat)
			{ 
				$catName = categoryPeer::getByFullNameExactMatch($cat);
				if (is_null($catName))
				{
					KalturaCriterion::disableTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
					$catName = categoryPeer::getByFullNameExactMatch($cat);
					if ($catName)
						throw new KalturaAPIException(KalturaErrors::CATEGORY_NOT_PERMITTED, $cat);
					KalturaCriterion::restoreTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
				}
			}
		}
	}
	
	public function validateUsers()
	{
		$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
		
		if(!$this->isNull('entitledUsersEdit'))
		{
			$entitledUsersEdit = explode(',', $this->entitledUsersEdit);

			foreach ($entitledUsersEdit as $puserId)
			{
				$puserId = trim($puserId);
				kuserPeer::createKuserForPartner($partnerId, $puserId);
			}
		}
			
		if(!$this->isNull('entitledUsersPublish'))
		{
			$entitledPusersPublish = explode(',', $this->entitledUsersPublish);
	
			foreach ($entitledPusersPublish as $puserId)
			{
				$puserId = trim($puserId);
				kuserPeer::createKuserForPartner($partnerId, $puserId);
			}
		}
		
		if(!$this->isNull('entitledUsersView'))
		{
			$entitledPusersView = explode(',', $this->entitledUsersView);
	
			foreach ($entitledPusersView as $puserId)
			{
				$puserId = trim($puserId);
				kuserPeer::createKuserForPartner($partnerId, $puserId);
			}
		}
		
	}

	/* (non-PHPdoc)
	 * Validate that the new value is EntryDisplayInSearchType::SYSTEM or EntryDisplayInSearchType::PARTNER_ONLY
	 * or that the value given is the one that exists in the DB
	 *
	 * @throws KalturaErrors::ENTRY_DISPLAY_IN_SEARCH_VALUE_NOT_ALLOWED
	 */
	public function validateDisplayInSearch(entry $sourceObject = null)
	{
		if ($this->displayInSearch === null)
			return;

		if ($this->displayInSearch === EntryDisplayInSearchType::PARTNER_ONLY ||
			$this->displayInSearch === EntryDisplayInSearchType::SYSTEM)
			return;

		// only for update scenario check against old object
		if ($sourceObject && $this->displayInSearch === $sourceObject->getDisplayInSearch())
			return;

		throw new KalturaAPIException(KalturaErrors::ENTRY_DISPLAY_IN_SEARCH_VALUE_NOT_ALLOWED, $this->displayInSearch);
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUpdate($source_object)
	 */
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		/* @var $sourceObject entry */
		$this->validateUsers();
		$this->validateCategories();
		$this->validateParentEntryId();
		$this->validatePropertyMinLength('referenceId', 2, true);
		
//		if($this->referenceId)
//		{
//			$c = KalturaCriteria::create(entryPeer::OM_CLASS);
//			$c->add('entry.ID', $sourceObject->getId(), Criteria::NOT_EQUAL);
//			$c->add('entry.REFERENCE_ID', $this->referenceId);
//			$c->applyFilters();
//			if(count($c->getFetchedIds()))
//				throw new KalturaAPIException(KalturaErrors::REFERENCE_ID_ALREADY_EXISTS, $this->referenceId);
//		}
				
		$this->validateObjectsExist($sourceObject);

		$this->validateDisplayInSearch($sourceObject);
		
		return parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}
	
	/* (non-PHPdoc)
	 * @see IFilterable::getExtraFilters()
	 */
	public function getExtraFilters()
	{
		return array(
			array("filter" => "mlikeor", "fields" => array("tags", "name")),
			array("filter" => "mlikeor", "fields" => array("tags", "adminTags")),
			array("filter" => "mlikeor", "fields" => array("tags", "adminTags", "name")),
			array("filter" => "mlikeand", "fields" => array("tags", "name")),
			array("filter" => "mlikeand", "fields" => array("tags", "adminTags")),
			array("filter" => "mlikeand", "fields" => array("tags", "adminTags", "name")),
			array("order" => "recent"),
			array("order" => "weight"),
		);
	}
	
	protected function getObjectPropertyName($propertyName)
	{
		if ($propertyName == 'creatorId')
			return 'creatorPuserId';
		return parent::getObjectPropertyName($propertyName);
	}
	
	/* (non-PHPdoc)
	 * @see IFilterable::getFilterDocs()
	 */
	public function getFilterDocs()
	{
		return array(
			"idEqual" => "This filter should be in use for retrieving only a specific entry (identified by its entryId).",
			"idIn" => "This filter should be in use for retrieving few specific entries (string should include comma separated list of entryId strings).",
			"userIdEqual" => "This filter parameter should be in use for retrieving only entries, uploaded by/assigned to a specific user (identified by user Id).",
			"typeIn" => "This filter should be in use for retrieving entries of few {@link ?object=KalturaEntryType KalturaEntryType} (string should include a comma separated list of {@link ?object=KalturaEntryType KalturaEntryType} enumerated parameters).",
			
			"statusEqual" => "This filter should be in use for retrieving only entries, at a specific {@link ?object=KalturaEntryStatus KalturaEntryStatus}.",
			"statusIn" => "This filter should be in use for retrieving only entries, at few specific {@link ?object=KalturaEntryStatus KalturaEntryStatus} (comma separated).",
			"statusNotEqual" => "This filter should be in use for retrieving only entries, not at a specific {@link ?object=KalturaEntryStatus KalturaEntryStatus}.",
			"statusNotIn" => "This filter should be in use for retrieving only entries, not at few specific {@link ?object=KalturaEntryStatus KalturaEntryStatus} (comma separated).",
			
			"nameLike" => "This filter should be in use for retrieving specific entries. It should include only one string to search for in entry names (no wildcards, spaces are treated as part of the string).",
			"nameMultiLikeOr" => "This filter should be in use for retrieving specific entries. It could include few (comma separated) strings for searching in entry names, while applying an OR logic to retrieve entries that contain at least one input string (no wildcards, spaces are treated as part of the string).",
			"nameMultiLikeAnd" => "This filter should be in use for retrieving specific entries. It could include few (comma separated) strings for searching in entry names, while applying an AND logic to retrieve entries that contain all input strings (no wildcards, spaces are treated as part of the string).",
			"nameEqual" => "This filter should be in use for retrieving entries with a specific name.",
		
			"tagsLike" => "This filter should be in use for retrieving specific entries. It should include only one string to search for in entry tags (no wildcards, spaces are treated as part of the string).",
			"tagsMultiLikeOr" => "This filter should be in use for retrieving specific entries. It could include few (comma separated) strings for searching in entry tags, while applying an OR logic to retrieve entries that contain at least one input string (no wildcards, spaces are treated as part of the string).",
			"tagsMultiLikeAnd" => "This filter should be in use for retrieving specific entries. It could include few (comma separated) strings for searching in entry tags, while applying an AND logic to retrieve entries that contain all input strings (no wildcards, spaces are treated as part of the string).",
		
			"adminTagsLike" => "This filter should be in use for retrieving specific entries. It should include only one string to search for in entry tags set by an ADMIN user (no wildcards, spaces are treated as part of the string).",
			"adminTagsMultiLikeOr" => "This filter should be in use for retrieving specific entries. It could include few (comma separated) strings for searching in entry tags, set by an ADMIN user, while applying an OR logic to retrieve entries that contain at least one input string (no wildcards, spaces are treated as part of the string).",
			"adminTagsMultiLikeAnd" => "This filter should be in use for retrieving specific entries. It could include few (comma separated) strings for searching in entry tags, set by an ADMIN user, while applying an AND logic to retrieve entries that contain all input strings (no wildcards, spaces are treated as part of the string).",
			
			"createdAtGreaterThanOrEqual" => "This filter parameter should be in use for retrieving only entries which were created at Kaltura system after a specific time/date (standard timestamp format).",
			"createdAtLessThanOrEqual" => "This filter parameter should be in use for retrieving only entries which were created at Kaltura system before a specific time/date (standard timestamp format).",
			
			"updatedAtGreaterThanEqual" => "This filter parameter should be in use for retrieving only entries which were created at Kaltura system after or at an exact time/date (standard timestamp format).",
			"updatedAtLessThenEqual" => "This filter parameter should be in use for retrieving only entries which were created at Kaltura system before or at an exact time/date (standard timestamp format).",
			
			"modifiedAtGreaterThanEqual" => "This filter parameter should be in use for retrieving only entries which were updated at Kaltura system after or at an exact time/date (standard timestamp format).",
			"modifiedAtLessThenEqual" => "This filter parameter should be in use for retrieving only entries which were updated at Kaltura system before or at an exact time/date (standard timestamp format).",
		
			"partnerIdEqual" => "This filter should be in use for retrieving only entries which were uploaded by/assigned to users of a specific Kaltura Partner (identified by Partner ID).",
			"partnerIdIn" => "This filter should be in use for retrieving only entries within Kaltura network which were uploaded by/assigned to users of few Kaltura Partners  (string should include comma separated list of PartnerIDs)",
			
			"tagsAndNameMultiLikeOr" => "This filter should be in use for retrieving specific entries. It could include few (comma separated) strings for searching in entry tags and names, while applying an OR logic to retrieve entries that contain at least one input string (no wildcards, spaces are treated as part of the string).",
			"tagsAndNameMultiLikeAnd" => "This filter should be in use for retrieving specific entries. It could include few (comma separated) strings for searching in entry tags and names, while applying an AND logic to retrieve entries that contain all input strings (no wildcards, spaces are treated as part of the string).",
		
			"tagsAndAdminTagsMultiLikeOr" => "This filter should be in use for retrieving specific entries. It could include few (comma separated) strings for searching in entry tags set by both users and ADMIN users, while applying an OR logic to retrieve entries that contain at least one input string (no wildcards, spaces are treated as part of the string).",
			"tagsAndAdminTagsAndNameMultiLikeOr" => "This filter should be in use for retrieving specific entries. It could include few (comma separated) strings for searching in entry tags set by both users and ADMIN users and in entry names, while applying an OR logic to retrieve entries that contain at least one input string (no wildcards, spaces are treated as part of the string).",
			
			"tagsAndAdminTagsMultiLikeAnd" => "This filter should be in use for retrieving specific entries. It could include few (comma separated) strings for searching in entry tags, set by both users and ADMIN users, while applying an AND logic to retrieve entries that contain all input strings (no wildcards, spaces are treated as part of the string).",
			"tagsAndAdminTagsAndNameMultiLikeAnd" => "This filter should be in use for retrieving specific entries. It could include few (comma separated) strings for searching in entry tags, set by both users and ADMIN users, and in entry names, while applying an AND logic to retrieve entries that contain all input strings (no wildcards, spaces are treated as part of the string).",
			
			"searchTextMatchAnd" => "This filter should be in use for retrieving specific entries while search match the input string within all of the following metadata attributes: name, description, tags, adminTags.",
			"searchTextMatchOr" => "This filter should be in use for retrieving specific entries while search match the input string within at least one of the following metadata attributes: name, description, tags, adminTags.",
		
			"categoriesMatchOr" => "All entries within these categories or their child categories.",
			"categoriesIdsMatchOr" => "All entries of the categories, excluding their child categories.\nTo include entries of the child categories, use categoryAncestorIdIn, or categoriesMatchOr.",
		);
	}
	
	public static function getInstance($sourceObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
	    $object = KalturaEntryFactory::getInstanceByType($sourceObject->getType());
	    if (!$object)
	        return null;
	    
	    $object->fromObject($sourceObject, $responseProfile);
	    return $object;
	}
}
