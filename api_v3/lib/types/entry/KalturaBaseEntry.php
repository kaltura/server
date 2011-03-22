<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaBaseEntry extends KalturaObject implements IFilterable 
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
	 * @filter eq
	 */
	public $userId;
	
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
	 * 
	 * @var string
	 * @filter matchand, matchor
	 * @requiresPermission insert,update
	 */
	public $categories;
	
	/**
	 * 
	 * @var string
	 * @filter matchand, matchor
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
	 * @var int
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $createdAt;
	
	/**
	 * Entry update date as Unix timestamp (In seconds)
	 * 
	 * @var int
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $updatedAt;
	
	/**
	 * Calculated rank
	 * 
	 * @var float
	 * @readonly
	 * @filter order
	 */
	public $rank;
	
	/**
	 * The total (sum) of all votes
	 * 
	 * @var int
	 * @readonly
	 */
	public $totalRank;
	
	/**
	 * Number of votes
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
	public $licenseType = KalturaLicenseType::UNKNOWN;
	
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
	 * @insertonly
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
	 * @var int
	 * @filter gte,lte,gteornull,lteornull
	 * @requiresPermission insert,update
	 */
	public $startDate;
	
	/**
	 * Entry scheduling end date (null when not set, send -1 to remove)
	 * 
	 * @var int
	 * @filter gte,lte,gteornull,lteornull
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
	 * ID of temporary entry that will replace this entry when it’s approved and ready for replacement
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
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array 
	 (
	 	"id", 
	 	"name", 
	 	"description",
	 	"userId" => "puserId", // what should be extracted is only the puserId NOT kuserId
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
	 	"categories",
	 	"categoriesIds",
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new entry();
			
		parent::toObject($dbObject, $skip);
		
		if ($this->startDate === -1) // save -1 as null
			$dbObject->setStartDate(null);
			
		if ($this->endDate === -1) // save -1 as null
			$dbObject->setEndDate(null);
			
		return $dbObject;
	}
	
	public function fromObject($sourceObject)
	{
		if(!$sourceObject)
			return;
			
		parent::fromObject($sourceObject);
		
		$this->startDate = $sourceObject->getStartDate(null);
		$this->endDate = $sourceObject->getEndDate(null);
	}
	
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
		);
	}
	
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
		);
	}
}