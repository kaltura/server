<?php
// ===================================================================================================
//                           _  __     _ _
//                          | |/ /__ _| | |_ _  _ _ _ __ _
//                          | ' </ _` | |  _| || | '_/ _` |
//                          |_|\_\__,_|_|\__|\_,_|_| \__,_|
//
// This file is part of the Kaltura Collaborative Media Suite which allows users
// to do with audio, video, and animation what Wiki platfroms allow them to do with
// text.
//
// Copyright (C) 2006-2011  Kaltura Inc.
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Affero General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Affero General Public License for more details.
//
// You should have received a copy of the GNU Affero General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// @ignore
// ===================================================================================================

/**
 * @package Scheduler
 * @subpackage Client
 */
require_once("KalturaClientBase.php");

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAccessControl extends KalturaObjectBase
{
	/**
	 * The id of the Access Control Profile
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * The name of the Access Control Profile
	 * 	 
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * System name of the Access Control Profile
	 * 	 
	 *
	 * @var string
	 */
	public $systemName = null;

	/**
	 * The description of the Access Control Profile
	 * 	 
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * Creation date as Unix timestamp (In seconds) 
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * True if this Conversion Profile is the default
	 * 	 
	 *
	 * @var KalturaNullableBoolean
	 */
	public $isDefault = null;

	/**
	 * Array of Access Control Restrictions
	 * 	 
	 *
	 * @var array of KalturaBaseRestriction
	 */
	public $restrictions;

	/**
	 * Indicates that the access control profile is new and should be handled using KalturaAccessControlProfile object and accessControlProfile service
	 * 	 
	 *
	 * @var bool
	 * @readonly
	 */
	public $containsUnsuportedRestrictions = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaAccessControlAction extends KalturaObjectBase
{
	/**
	 * The type of the access control action
	 * 	 
	 *
	 * @var KalturaAccessControlActionType
	 * @readonly
	 */
	public $type = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAccessControlContextTypeHolder extends KalturaObjectBase
{
	/**
	 * The type of the access control condition context
	 * 	 
	 *
	 * @var KalturaAccessControlContextType
	 */
	public $type = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAccessControlListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaAccessControl
	 * @readonly
	 */
	public $objects;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $totalCount = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAccessControlScope extends KalturaObjectBase
{
	/**
	 * URL to be used to test domain conditions.
	 * 	 
	 *
	 * @var string
	 */
	public $referrer = null;

	/**
	 * IP to be used to test geographic location conditions.
	 * 	 
	 *
	 * @var string
	 */
	public $ip = null;

	/**
	 * Kaltura session to be used to test session and user conditions.
	 * 	 
	 *
	 * @var string
	 */
	public $ks = null;

	/**
	 * Browser or client application to be used to test agent conditions.
	 * 	 
	 *
	 * @var string
	 */
	public $userAgent = null;

	/**
	 * Unix timestamp (In seconds) to be used to test entry scheduling, keep null to use now.
	 * 	 
	 *
	 * @var int
	 */
	public $time = null;

	/**
	 * Indicates what contexts should be tested. No contexts means any context.
	 * 	 
	 *
	 * @var array of KalturaAccessControlContextTypeHolder
	 */
	public $contexts;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAsset extends KalturaObjectBase
{
	/**
	 * The ID of the Flavor Asset
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $id = null;

	/**
	 * The entry ID of the Flavor Asset
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $entryId = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * The version of the Flavor Asset
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $version = null;

	/**
	 * The size (in KBytes) of the Flavor Asset
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $size = null;

	/**
	 * Tags used to identify the Flavor Asset in various scenarios
	 * 	 
	 *
	 * @var string
	 */
	public $tags = null;

	/**
	 * The file extension
	 * 	 
	 *
	 * @var string
	 * @insertonly
	 */
	public $fileExt = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $deletedAt = null;

	/**
	 * System description, error message, warnings and failure cause.
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $description = null;

	/**
	 * Partner private data
	 * 	 
	 *
	 * @var string
	 */
	public $partnerData = null;

	/**
	 * Partner friendly description
	 * 	 
	 *
	 * @var string
	 */
	public $partnerDescription = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAssetParams extends KalturaObjectBase
{
	/**
	 * The id of the Flavor Params
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * The name of the Flavor Params
	 * 	 
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * System name of the Flavor Params
	 * 	 
	 *
	 * @var string
	 */
	public $systemName = null;

	/**
	 * The description of the Flavor Params
	 * 	 
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * Creation date as Unix timestamp (In seconds)
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * True if those Flavor Params are part of system defaults
	 * 	 
	 *
	 * @var KalturaNullableBoolean
	 * @readonly
	 */
	public $isSystemDefault = null;

	/**
	 * The Flavor Params tags are used to identify the flavor for different usage (e.g. web, hd, mobile)
	 * 	 
	 *
	 * @var string
	 */
	public $tags = null;

	/**
	 * Array of partner permisison names that required for using this asset params
	 * 	 
	 *
	 * @var array of KalturaString
	 */
	public $requiredPermissions;

	/**
	 * Id of remote storage profile that used to get the source, zero indicates Kaltura data center
	 * 	 
	 *
	 * @var int
	 */
	public $sourceRemoteStorageProfileId = null;

	/**
	 * Comma seperated ids of remote storage profiles that the flavor distributed to, the distribution done by the conversion engine
	 * 	 
	 *
	 * @var int
	 */
	public $remoteStorageProfileIds = null;

	/**
	 * Media parser type to be used for post-conversion validation
	 * 	 
	 *
	 * @var KalturaMediaParserType
	 */
	public $mediaParserType = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBaseEntry extends KalturaObjectBase
{
	/**
	 * Auto generated 10 characters alphanumeric string
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $id = null;

	/**
	 * Entry name (Min 1 chars)
	 * 	 
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * Entry description
	 * 	 
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * The ID of the user who is the owner of this entry 
	 * 	 
	 *
	 * @var string
	 */
	public $userId = null;

	/**
	 * The ID of the user who created this entry 
	 * 	 
	 *
	 * @var string
	 * @insertonly
	 */
	public $creatorId = null;

	/**
	 * Entry tags
	 * 	 
	 *
	 * @var string
	 */
	public $tags = null;

	/**
	 * Entry admin tags can be updated only by administrators
	 * 	 
	 *
	 * @var string
	 */
	public $adminTags = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $categories = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $categoriesIds = null;

	/**
	 * 
	 *
	 * @var KalturaEntryStatus
	 * @readonly
	 */
	public $status = null;

	/**
	 * Entry moderation status
	 * 	 
	 *
	 * @var KalturaEntryModerationStatus
	 * @readonly
	 */
	public $moderationStatus = null;

	/**
	 * Number of moderation requests waiting for this entry
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $moderationCount = null;

	/**
	 * The type of the entry, this is auto filled by the derived entry object
	 * 	 
	 *
	 * @var KalturaEntryType
	 */
	public $type = null;

	/**
	 * Entry creation date as Unix timestamp (In seconds)
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * Entry update date as Unix timestamp (In seconds)
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;

	/**
	 * Calculated rank
	 * 	 
	 *
	 * @var float
	 * @readonly
	 */
	public $rank = null;

	/**
	 * The total (sum) of all votes
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $totalRank = null;

	/**
	 * Number of votes
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $votes = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $groupId = null;

	/**
	 * Can be used to store various partner related data as a string 
	 * 	 
	 *
	 * @var string
	 */
	public $partnerData = null;

	/**
	 * Download URL for the entry
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $downloadUrl = null;

	/**
	 * Indexed search text for full text search
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $searchText = null;

	/**
	 * License type used for this entry
	 * 	 
	 *
	 * @var KalturaLicenseType
	 */
	public $licenseType = null;

	/**
	 * Version of the entry data
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $version = null;

	/**
	 * Thumbnail URL
	 * 	 
	 *
	 * @var string
	 * @insertonly
	 */
	public $thumbnailUrl = null;

	/**
	 * The Access Control ID assigned to this entry (null when not set, send -1 to remove)  
	 * 	 
	 *
	 * @var int
	 */
	public $accessControlId = null;

	/**
	 * Entry scheduling start date (null when not set, send -1 to remove)
	 * 	 
	 *
	 * @var int
	 */
	public $startDate = null;

	/**
	 * Entry scheduling end date (null when not set, send -1 to remove)
	 * 	 
	 *
	 * @var int
	 */
	public $endDate = null;

	/**
	 * Entry external reference id
	 * 	 
	 *
	 * @var string
	 */
	public $referenceId = null;

	/**
	 * ID of temporary entry that will replace this entry when it's approved and ready for replacement
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $replacingEntryId = null;

	/**
	 * ID of the entry that will be replaced when the replacement approved and this entry is ready
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $replacedEntryId = null;

	/**
	 * Status of the replacement readiness and approval
	 * 	 
	 *
	 * @var KalturaEntryReplacementStatus
	 * @readonly
	 */
	public $replacementStatus = null;

	/**
	 * Can be used to store various partner related data as a numeric value
	 * 	 
	 *
	 * @var int
	 */
	public $partnerSortValue = null;

	/**
	 * Override the default ingestion profile  
	 * 	 
	 *
	 * @var int
	 */
	public $conversionProfileId = null;

	/**
	 * ID of source root entry, used for clipped, skipped and cropped entries that created from another entry  
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $rootEntryId = null;

	/**
	 * clipping, skipping and cropping attributes that used to create this entry  
	 * 	 
	 *
	 * @var array of KalturaOperationAttributes
	 */
	public $operationAttributes;

	/**
	 * list of user ids that are entitled to edit the entry (no server enforcement) The difference between entitledUsersEdit and entitledUsersPublish is applicative only
	 * 	 
	 *
	 * @var string
	 */
	public $entitledUsersEdit = null;

	/**
	 * list of user ids that are entitled to publish the entry (no server enforcement) The difference between entitledUsersEdit and entitledUsersPublish is applicative only
	 * 	 
	 *
	 * @var string
	 */
	public $entitledUsersPublish = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBaseEntryListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaBaseEntry
	 * @readonly
	 */
	public $objects;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $totalCount = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBaseJob extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $deletedAt = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $processorExpiration = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $executionAttempts = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $lockVersion = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaBaseRestriction extends KalturaObjectBase
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBatchGetExclusiveNotificationJobsResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaNotification
	 * @readonly
	 */
	public $notifications;

	/**
	 * 
	 *
	 * @var array of KalturaPartner
	 * @readonly
	 */
	public $partners;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBatchJobListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaBatchJob
	 * @readonly
	 */
	public $objects;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $totalCount = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBatchJobResponse extends KalturaObjectBase
{
	/**
	 * The main batch job
	 * 	 
	 *
	 * @var KalturaBatchJob
	 */
	public $batchJob;

	/**
	 * All batch jobs that reference the main job as root
	 * 	 
	 *
	 * @var array of KalturaBatchJob
	 */
	public $childBatchJobs;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBatchQueuesStatus extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var KalturaBatchJobType
	 */
	public $jobType = null;

	/**
	 * The worker configured id
	 * 	 
	 *
	 * @var int
	 */
	public $workerId = null;

	/**
	 * The friendly name of the type
	 * 	 
	 *
	 * @var string
	 */
	public $typeName = null;

	/**
	 * The size of the queue
	 * 	 
	 *
	 * @var int
	 */
	public $size = null;

	/**
	 * The avarage wait time
	 * 	 
	 *
	 * @var int
	 */
	public $waitTime = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBulkUpload extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $uploadedBy = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $uploadedByUserId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $uploadedOn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $numOfEntries = null;

	/**
	 * 
	 *
	 * @var KalturaBatchJobStatus
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $logFileUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $csvFileUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $bulkFileUrl = null;

	/**
	 * 
	 *
	 * @var KalturaBulkUploadType
	 */
	public $bulkUploadType = null;

	/**
	 * 
	 *
	 * @var array of KalturaBulkUploadResult
	 */
	public $results;

	/**
	 * 
	 *
	 * @var string
	 */
	public $error = null;

	/**
	 * 
	 *
	 * @var KalturaBatchJobErrorTypes
	 */
	public $errorType = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $errorNumber = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fileName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $numOfObjects = null;

	/**
	 * 
	 *
	 * @var KalturaBulkUploadObjectType
	 */
	public $bulkUploadObjectType = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaBulkUploadObjectData extends KalturaObjectBase
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBulkUploadPluginData extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $field = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $value = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBulkUploadResult extends KalturaObjectBase
{
	/**
	 * The id of the result
	 *      
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * The id of the parent job
	 * 	 
	 *
	 * @var int
	 */
	public $bulkUploadJobId = null;

	/**
	 * The index of the line in the CSV
	 * 	 
	 *
	 * @var int
	 */
	public $lineIndex = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerId = null;

	/**
	 * 
	 *
	 * @var KalturaBulkUploadResultStatus
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var KalturaBulkUploadAction
	 */
	public $action = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $objectId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $objectStatus = null;

	/**
	 * 
	 *
	 * @var KalturaBulkUploadResultObjectType
	 */
	public $bulkUploadResultObjectType = null;

	/**
	 * The data as recieved in the csv
	 * 	 
	 *
	 * @var string
	 */
	public $rowData = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $partnerData = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $objectErrorDescription = null;

	/**
	 * 
	 *
	 * @var array of KalturaBulkUploadPluginData
	 */
	public $pluginsData;

	/**
	 * 
	 *
	 * @var string
	 */
	public $errorDescription = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $errorCode = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $errorType = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaCategory extends KalturaObjectBase
{
	/**
	 * The id of the Category
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $parentId = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $depth = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * The name of the Category. 
	 * 	 The following characters are not allowed: '<', '>', ','
	 * 	 
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * The full name of the Category
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $fullName = null;

	/**
	 * The full ids of the Category
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $fullIds = null;

	/**
	 * Number of entries in this Category (including child categories)
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $entriesCount = null;

	/**
	 * Creation date as Unix timestamp (In seconds)
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * Update date as Unix timestamp (In seconds)
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;

	/**
	 * Category description
	 * 	 
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * Category tags
	 * 	 
	 *
	 * @var string
	 */
	public $tags = null;

	/**
	 * If category will be returned for list action.
	 * 	 
	 *
	 * @var KalturaAppearInListType
	 */
	public $appearInList = null;

	/**
	 * defines the privacy of the entries that assigned to this category
	 * 	 
	 *
	 * @var KalturaPrivacyType
	 */
	public $privacy = null;

	/**
	 * If Category members are inherited from parent category or set manualy. 
	 * 	 
	 *
	 * @var KalturaInheritanceType
	 */
	public $inheritanceType = null;

	/**
	 * Who can ask to join this category
	 * 	 
	 *
	 * @var KalturaUserJoinPolicyType
	 * @readonly
	 */
	public $userJoinPolicy = null;

	/**
	 * Default permissionLevel for new users
	 * 	 
	 *
	 * @var KalturaCategoryUserPermissionLevel
	 */
	public $defaultPermissionLevel = null;

	/**
	 * Category Owner (User id)
	 * 	 
	 *
	 * @var string
	 */
	public $owner = null;

	/**
	 * Number of entries that belong to this category directly
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $directEntriesCount = null;

	/**
	 * Category external id, controlled and managed by the partner.
	 * 	 
	 *
	 * @var string
	 */
	public $referenceId = null;

	/**
	 * who can assign entries to this category
	 * 	 
	 *
	 * @var KalturaContributionPolicyType
	 */
	public $contributionPolicy = null;

	/**
	 * Number of active members for this category
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $membersCount = null;

	/**
	 * Number of pending members for this category
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $pendingMembersCount = null;

	/**
	 * Set privacy context for search entries that assiged to private and public categories. the entries will be private if the search context is set with those categories.
	 * 	 
	 *
	 * @var string
	 */
	public $privacyContext = null;

	/**
	 * comma separated parents that defines a privacyContext for search
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $privacyContexts = null;

	/**
	 * Status
	 * 	 
	 *
	 * @var KalturaCategoryStatus
	 * @readonly
	 */
	public $status = null;

	/**
	 * the category id that this category inherit its members and members permission (for contribution and join)
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $inheritedParentId = null;

	/**
	 * Can be used to store various partner related data as a numeric value
	 * 	 
	 *
	 * @var int
	 */
	public $partnerSortValue = null;

	/**
	 * Can be used to store various partner related data as a string 
	 * 	 
	 *
	 * @var string
	 */
	public $partnerData = null;

	/**
	 * Enable client side applications to define how to sort the category child categories 
	 * 	 
	 *
	 * @var KalturaCategoryOrderBy
	 */
	public $defaultOrderBy = null;

	/**
	 * Number of direct children categories
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $directSubCategoriesCount = null;

	/**
	 * Entries moderation 
	 * 	 
	 *
	 * @var KalturaNullableBoolean
	 */
	public $moderation = null;

	/**
	 * Nunber of pending moderation entries
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $pendingEntriesCount = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaCategoryEntry extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $categoryId = null;

	/**
	 * entry id
	 * 	 
	 *
	 * @var string
	 */
	public $entryId = null;

	/**
	 * Creation date as Unix timestamp (In seconds)
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * The full ids of the Category
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $categoryFullIds = null;

	/**
	 * CategroyEntry status
	 * 	 
	 *
	 * @var KalturaCategoryEntryStatus
	 * @readonly
	 */
	public $status = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaCategoryEntryListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaCategoryEntry
	 * @readonly
	 */
	public $objects;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $totalCount = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaCategoryListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaCategory
	 * @readonly
	 */
	public $objects;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $totalCount = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaCategoryUser extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var int
	 * @insertonly
	 */
	public $categoryId = null;

	/**
	 * User id
	 * 	 
	 *
	 * @var string
	 * @insertonly
	 */
	public $userId = null;

	/**
	 * Partner id
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * Permission level
	 * 	 
	 *
	 * @var KalturaCategoryUserPermissionLevel
	 */
	public $permissionLevel = null;

	/**
	 * Status
	 * 	 
	 *
	 * @var KalturaCategoryUserStatus
	 * @readonly
	 */
	public $status = null;

	/**
	 * CategoryUser creation date as Unix timestamp (In seconds)
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * CategoryUser update date as Unix timestamp (In seconds)
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;

	/**
	 * Update method can be either manual or automatic to distinguish between manual operations (for example in KMC) on automatic - using bulk upload 
	 * 	 
	 *
	 * @var KalturaUpdateMethodType
	 */
	public $updateMethod = null;

	/**
	 * The full ids of the Category
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $categoryFullIds = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaCategoryUserListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaCategoryUser
	 * @readonly
	 */
	public $objects;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $totalCount = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaControlPanelCommand extends KalturaObjectBase
{
	/**
	 * The id of the Category
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * Creation date as Unix timestamp (In seconds)
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * Creator name
	 * 	 
	 *
	 * @var string
	 */
	public $createdBy = null;

	/**
	 * Update date as Unix timestamp (In seconds)
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;

	/**
	 * Updater name
	 * 	 
	 *
	 * @var string
	 */
	public $updatedBy = null;

	/**
	 * Creator id
	 * 	 
	 *
	 * @var int
	 */
	public $createdById = null;

	/**
	 * The id of the scheduler that the command refers to
	 * 	 
	 *
	 * @var int
	 */
	public $schedulerId = null;

	/**
	 * The id of the scheduler worker that the command refers to
	 * 	 
	 *
	 * @var int
	 */
	public $workerId = null;

	/**
	 * The id of the scheduler worker as configured in the ini file
	 * 	 
	 *
	 * @var int
	 */
	public $workerConfiguredId = null;

	/**
	 * The name of the scheduler worker that the command refers to
	 * 	 
	 *
	 * @var int
	 */
	public $workerName = null;

	/**
	 * The index of the batch process that the command refers to
	 * 	 
	 *
	 * @var int
	 */
	public $batchIndex = null;

	/**
	 * The command type - stop / start / config
	 * 	 
	 *
	 * @var KalturaControlPanelCommandType
	 */
	public $type = null;

	/**
	 * The command target type - data center / scheduler / job / job type
	 * 	 
	 *
	 * @var KalturaControlPanelCommandTargetType
	 */
	public $targetType = null;

	/**
	 * The command status
	 * 	 
	 *
	 * @var KalturaControlPanelCommandStatus
	 */
	public $status = null;

	/**
	 * The reason for the command
	 * 	 
	 *
	 * @var string
	 */
	public $cause = null;

	/**
	 * Command description
	 * 	 
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * Error description
	 * 	 
	 *
	 * @var string
	 */
	public $errorDescription = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaControlPanelCommandListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaControlPanelCommand
	 * @readonly
	 */
	public $objects;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $totalCount = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaConversionProfile extends KalturaObjectBase
{
	/**
	 * The id of the Conversion Profile
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * 
	 *
	 * @var KalturaConversionProfileStatus
	 */
	public $status = null;

	/**
	 * The name of the Conversion Profile
	 * 	 
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * System name of the Conversion Profile
	 * 	 
	 *
	 * @var string
	 */
	public $systemName = null;

	/**
	 * Comma separated tags
	 * 	 
	 *
	 * @var string
	 */
	public $tags = null;

	/**
	 * The description of the Conversion Profile
	 * 	 
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * ID of the default entry to be used for template data
	 * 	 
	 *
	 * @var string
	 */
	public $defaultEntryId = null;

	/**
	 * Creation date as Unix timestamp (In seconds) 
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * List of included flavor ids (comma separated)
	 * 	 
	 *
	 * @var string
	 */
	public $flavorParamsIds = null;

	/**
	 * Indicates that this conversion profile is system default
	 * 	 
	 *
	 * @var KalturaNullableBoolean
	 */
	public $isDefault = null;

	/**
	 * Indicates that this conversion profile is partner default
	 * 	 
	 *
	 * @var bool
	 * @readonly
	 */
	public $isPartnerDefault = null;

	/**
	 * Cropping dimensions
	 * 	 
	 *
	 * @var KalturaCropDimensions
	 */
	public $cropDimensions;

	/**
	 * Clipping start position (in miliseconds)
	 * 	 
	 *
	 * @var int
	 */
	public $clipStart = null;

	/**
	 * Clipping duration (in miliseconds)
	 * 	 
	 *
	 * @var int
	 */
	public $clipDuration = null;

	/**
	 * XSL to transform ingestion MRSS XML
	 * 	 
	 *
	 * @var string
	 */
	public $xslTransformation = null;

	/**
	 * ID of default storage profile to be used for linked net-storage file syncs
	 * 	 
	 *
	 * @var int
	 */
	public $storageProfileId = null;

	/**
	 * Media parser type to be used for extract media
	 * 	 
	 *
	 * @var KalturaMediaParserType
	 */
	public $mediaParserType = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaConversionProfileAssetParams extends KalturaObjectBase
{
	/**
	 * The id of the conversion profile
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $conversionProfileId = null;

	/**
	 * The id of the asset params
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $assetParamsId = null;

	/**
	 * The ingestion origin of the asset params
	 * 	 
	 *
	 * @var KalturaFlavorReadyBehaviorType
	 */
	public $readyBehavior = null;

	/**
	 * The ingestion origin of the asset params
	 * 	 
	 *
	 * @var KalturaAssetParamsOrigin
	 */
	public $origin = null;

	/**
	 * Asset params system name
	 * 	 
	 *
	 * @var string
	 */
	public $systemName = null;

	/**
	 * Starts conversion even if the decision layer reduced the configuration to comply with the source
	 * 	 
	 *
	 * @var KalturaNullableBoolean
	 */
	public $forceNoneComplied = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaConversionProfileAssetParamsListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaConversionProfileAssetParams
	 * @readonly
	 */
	public $objects;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $totalCount = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaConversionProfileListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaConversionProfile
	 * @readonly
	 */
	public $objects;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $totalCount = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaConvertCollectionFlavorData extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $flavorAssetId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $flavorParamsOutputId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $readyBehavior = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $videoBitrate = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $audioBitrate = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $destFileSyncLocalPath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $destFileSyncRemoteUrl = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaCropDimensions extends KalturaObjectBase
{
	/**
	 * Crop left point
	 * 	 
	 *
	 * @var int
	 */
	public $left = null;

	/**
	 * Crop top point
	 * 	 
	 *
	 * @var int
	 */
	public $top = null;

	/**
	 * Crop width
	 * 	 
	 *
	 * @var int
	 */
	public $width = null;

	/**
	 * Crop height
	 * 	 
	 *
	 * @var int
	 */
	public $height = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaEmailIngestionProfile extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $emailAddress = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $mailboxId = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $conversionProfile2Id = null;

	/**
	 * 
	 *
	 * @var KalturaEntryModerationStatus
	 */
	public $moderationStatus = null;

	/**
	 * 
	 *
	 * @var KalturaEmailIngestionProfileStatus
	 * @readonly
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $defaultCategory = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $defaultUserId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $defaultTags = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $defaultAdminTags = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $maxAttachmentSizeKbytes = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $maxAttachmentsPerMail = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaEntryContextDataResult extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var bool
	 */
	public $isSiteRestricted = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $isCountryRestricted = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $isSessionRestricted = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $isIpAddressRestricted = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $isUserAgentRestricted = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $previewLength = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $isScheduledNow = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $isAdmin = null;

	/**
	 * http/rtmp/hdnetwork
	 * 	 
	 *
	 * @var string
	 */
	public $streamerType = null;

	/**
	 * http/https, rtmp/rtmpe
	 * 	 
	 *
	 * @var string
	 */
	public $mediaProtocol = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $storageProfilesXML = null;

	/**
	 * Array of messages as received from the access control rules that invalidated
	 * 	 
	 *
	 * @var array of KalturaString
	 */
	public $accessControlMessages;

	/**
	 * Array of actions as received from the access control rules that invalidated
	 * 	 
	 *
	 * @var array of KalturaAccessControlAction
	 */
	public $accessControlActions;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaExclusiveLockKey extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $schedulerId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $workerId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $batchIndex = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaFileExistsResponse extends KalturaObjectBase
{
	/**
	 * Indicates if the file exists
	 * 	 
	 *
	 * @var bool
	 */
	public $exists = null;

	/**
	 * Indicates if the file size is right
	 * 	 
	 *
	 * @var bool
	 */
	public $sizeOk = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaFilter extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $orderBy = null;

	/**
	 * 
	 *
	 * @var KalturaSearchItem
	 */
	public $advancedSearch;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaFilterPager extends KalturaObjectBase
{
	/**
	 * The number of objects to retrieve. (Default is 30, maximum page size is 500).
	 * 	 
	 *
	 * @var int
	 */
	public $pageSize = null;

	/**
	 * The page number for which {pageSize} of objects should be retrieved (Default is 1).
	 * 	 
	 *
	 * @var int
	 */
	public $pageIndex = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaFlavorAssetListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaFlavorAsset
	 * @readonly
	 */
	public $objects;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $totalCount = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaFlavorAssetWithParams extends KalturaObjectBase
{
	/**
	 * The Flavor Asset (Can be null when there are params without asset)
	 * 	 
	 *
	 * @var KalturaFlavorAsset
	 */
	public $flavorAsset;

	/**
	 * The Flavor Params
	 * 	 
	 *
	 * @var KalturaFlavorParams
	 */
	public $flavorParams;

	/**
	 * The entry id
	 * 	 
	 *
	 * @var string
	 */
	public $entryId = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaFlavorParamsListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaFlavorParams
	 * @readonly
	 */
	public $objects;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $totalCount = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaFreeJobResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var KalturaBatchJob
	 * @readonly
	 */
	public $job;

	/**
	 * 
	 *
	 * @var KalturaBatchJobType
	 * @readonly
	 */
	public $jobType = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $queueSize = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaJobData extends KalturaObjectBase
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaKeyValue extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $key = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $value = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaLiveStreamBitrate extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $bitrate = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $width = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $height = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaMediaInfo extends KalturaObjectBase
{
	/**
	 * The id of the media info
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * The id of the related flavor asset
	 * 	 
	 *
	 * @var string
	 */
	public $flavorAssetId = null;

	/**
	 * The file size
	 * 	 
	 *
	 * @var int
	 */
	public $fileSize = null;

	/**
	 * The container format
	 * 	 
	 *
	 * @var string
	 */
	public $containerFormat = null;

	/**
	 * The container id
	 * 	 
	 *
	 * @var string
	 */
	public $containerId = null;

	/**
	 * The container profile
	 * 	 
	 *
	 * @var string
	 */
	public $containerProfile = null;

	/**
	 * The container duration
	 * 	 
	 *
	 * @var int
	 */
	public $containerDuration = null;

	/**
	 * The container bit rate
	 * 	 
	 *
	 * @var int
	 */
	public $containerBitRate = null;

	/**
	 * The video format
	 * 	 
	 *
	 * @var string
	 */
	public $videoFormat = null;

	/**
	 * The video codec id
	 * 	 
	 *
	 * @var string
	 */
	public $videoCodecId = null;

	/**
	 * The video duration
	 * 	 
	 *
	 * @var int
	 */
	public $videoDuration = null;

	/**
	 * The video bit rate
	 * 	 
	 *
	 * @var int
	 */
	public $videoBitRate = null;

	/**
	 * The video bit rate mode
	 * 	 
	 *
	 * @var KalturaBitRateMode
	 */
	public $videoBitRateMode = null;

	/**
	 * The video width
	 * 	 
	 *
	 * @var int
	 */
	public $videoWidth = null;

	/**
	 * The video height
	 * 	 
	 *
	 * @var int
	 */
	public $videoHeight = null;

	/**
	 * The video frame rate
	 * 	 
	 *
	 * @var float
	 */
	public $videoFrameRate = null;

	/**
	 * The video display aspect ratio (dar)
	 * 	 
	 *
	 * @var float
	 */
	public $videoDar = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $videoRotation = null;

	/**
	 * The audio format
	 * 	 
	 *
	 * @var string
	 */
	public $audioFormat = null;

	/**
	 * The audio codec id
	 * 	 
	 *
	 * @var string
	 */
	public $audioCodecId = null;

	/**
	 * The audio duration
	 * 	 
	 *
	 * @var int
	 */
	public $audioDuration = null;

	/**
	 * The audio bit rate
	 * 	 
	 *
	 * @var int
	 */
	public $audioBitRate = null;

	/**
	 * The audio bit rate mode
	 * 	 
	 *
	 * @var KalturaBitRateMode
	 */
	public $audioBitRateMode = null;

	/**
	 * The number of audio channels
	 * 	 
	 *
	 * @var int
	 */
	public $audioChannels = null;

	/**
	 * The audio sampling rate
	 * 	 
	 *
	 * @var int
	 */
	public $audioSamplingRate = null;

	/**
	 * The audio resolution
	 * 	 
	 *
	 * @var int
	 */
	public $audioResolution = null;

	/**
	 * The writing library
	 * 	 
	 *
	 * @var string
	 */
	public $writingLib = null;

	/**
	 * The data as returned by the mediainfo command line
	 * 	 
	 *
	 * @var string
	 */
	public $rawData = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $multiStreamInfo = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $scanType = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $multiStream = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaMediaInfoListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaMediaInfo
	 * @readonly
	 */
	public $objects;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $totalCount = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaModerationFlag extends KalturaObjectBase
{
	/**
	 * Moderation flag id
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * The user id that added the moderation flag
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $userId = null;

	/**
	 * The type of the moderation flag (entry or user)
	 * 	 
	 *
	 * @var KalturaModerationObjectType
	 * @readonly
	 */
	public $moderationObjectType = null;

	/**
	 * If moderation flag is set for entry, this is the flagged entry id
	 * 	 
	 *
	 * @var string
	 */
	public $flaggedEntryId = null;

	/**
	 * If moderation flag is set for user, this is the flagged user id
	 * 	 
	 *
	 * @var string
	 */
	public $flaggedUserId = null;

	/**
	 * The moderation flag status
	 * 	 
	 *
	 * @var KalturaModerationFlagStatus
	 * @readonly
	 */
	public $status = null;

	/**
	 * The comment that was added to the flag
	 * 	 
	 *
	 * @var string
	 */
	public $comments = null;

	/**
	 * 
	 *
	 * @var KalturaModerationFlagType
	 */
	public $flagType = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaModerationFlagListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaModerationFlag
	 * @readonly
	 */
	public $objects;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $totalCount = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaOperationAttributes extends KalturaObjectBase
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaPartner extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $website = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $notificationUrl = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $appearInSearch = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * deprecated - lastName and firstName replaces this field
	 * 	 
	 *
	 * @var string
	 */
	public $adminName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $adminEmail = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * 
	 *
	 * @var KalturaCommercialUseType
	 */
	public $commercialUse = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $landingPage = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $userLandingPage = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $contentCategories = null;

	/**
	 * 
	 *
	 * @var KalturaPartnerType
	 */
	public $type = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $phone = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $describeYourself = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $adultContent = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $defConversionProfileType = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $notify = null;

	/**
	 * 
	 *
	 * @var KalturaPartnerStatus
	 * @readonly
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $allowQuickEdit = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $mergeEntryLists = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $notificationsConfig = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $maxUploadSize = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerPackage = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $secret = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $adminSecret = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $cmsPassword = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $allowMultiNotification = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $adminLoginUsersQuota = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $adminUserId = null;

	/**
	 * firstName and lastName replace the old (deprecated) adminName
	 * 	 
	 *
	 * @var string
	 */
	public $firstName = null;

	/**
	 * lastName and firstName replace the old (deprecated) adminName
	 * 	 
	 *
	 * @var string
	 */
	public $lastName = null;

	/**
	 * country code (2char) - this field is optional
	 * 	 
	 *
	 * @var string
	 */
	public $country = null;

	/**
	 * state code (2char) - this field is optional
	 * 	 
	 *
	 * @var string
	 */
	public $state = null;

	/**
	 * 
	 *
	 * @var array of KalturaKeyValue
	 * @insertonly
	 */
	public $additionalParams;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaRemotePath extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $storageProfileId = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $uri = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaRemotePathListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaRemotePath
	 * @readonly
	 */
	public $objects;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $totalCount = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaResource extends KalturaObjectBase
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaScheduler extends KalturaObjectBase
{
	/**
	 * The id of the Scheduler
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * The id as configured in the batch config
	 * 	 
	 *
	 * @var int
	 */
	public $configuredId = null;

	/**
	 * The scheduler name
	 * 	 
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * The host name
	 * 	 
	 *
	 * @var string
	 */
	public $host = null;

	/**
	 * Array of the last statuses
	 * 	 
	 *
	 * @var array of KalturaSchedulerStatus
	 * @readonly
	 */
	public $statuses;

	/**
	 * Array of the last configs
	 * 	 
	 *
	 * @var array of KalturaSchedulerConfig
	 * @readonly
	 */
	public $configs;

	/**
	 * Array of the workers
	 * 	 
	 *
	 * @var array of KalturaSchedulerWorker
	 * @readonly
	 */
	public $workers;

	/**
	 * creation time
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * last status time
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $lastStatus = null;

	/**
	 * last status formated
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $lastStatusStr = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaSchedulerConfig extends KalturaObjectBase
{
	/**
	 * The id of the Category
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * Creator name
	 * 	 
	 *
	 * @var string
	 */
	public $createdBy = null;

	/**
	 * Updater name
	 * 	 
	 *
	 * @var string
	 */
	public $updatedBy = null;

	/**
	 * Id of the control panel command that created this config item 
	 * 	 
	 *
	 * @var string
	 */
	public $commandId = null;

	/**
	 * The status of the control panel command 
	 * 	 
	 *
	 * @var string
	 */
	public $commandStatus = null;

	/**
	 * The id of the scheduler 
	 * 	 
	 *
	 * @var int
	 */
	public $schedulerId = null;

	/**
	 * The configured id of the scheduler 
	 * 	 
	 *
	 * @var int
	 */
	public $schedulerConfiguredId = null;

	/**
	 * The name of the scheduler 
	 * 	 
	 *
	 * @var string
	 */
	public $schedulerName = null;

	/**
	 * The id of the job worker
	 * 	 
	 *
	 * @var int
	 */
	public $workerId = null;

	/**
	 * The configured id of the job worker
	 * 	 
	 *
	 * @var int
	 */
	public $workerConfiguredId = null;

	/**
	 * The name of the job worker
	 * 	 
	 *
	 * @var string
	 */
	public $workerName = null;

	/**
	 * The name of the variable
	 * 	 
	 *
	 * @var string
	 */
	public $variable = null;

	/**
	 * The part of the variable
	 * 	 
	 *
	 * @var string
	 */
	public $variablePart = null;

	/**
	 * The value of the variable
	 * 	 
	 *
	 * @var string
	 */
	public $value = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaSchedulerListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaScheduler
	 * @readonly
	 */
	public $objects;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $totalCount = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaSchedulerStatus extends KalturaObjectBase
{
	/**
	 * The id of the Category
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * The configured id of the scheduler
	 * 	 
	 *
	 * @var int
	 */
	public $schedulerConfiguredId = null;

	/**
	 * The configured id of the job worker
	 * 	 
	 *
	 * @var int
	 */
	public $workerConfiguredId = null;

	/**
	 * The type of the job worker.
	 * 	 
	 *
	 * @var KalturaBatchJobType
	 */
	public $workerType = null;

	/**
	 * The status type
	 * 	 
	 *
	 * @var KalturaSchedulerStatusType
	 */
	public $type = null;

	/**
	 * The status value
	 * 	 
	 *
	 * @var int
	 */
	public $value = null;

	/**
	 * The id of the scheduler
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $schedulerId = null;

	/**
	 * The id of the worker
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $workerId = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaSchedulerStatusResponse extends KalturaObjectBase
{
	/**
	 * The status of all queues on the server
	 * 	 
	 *
	 * @var array of KalturaBatchQueuesStatus
	 */
	public $queuesStatus;

	/**
	 * The commands that sent from the control panel
	 * 	 
	 *
	 * @var array of KalturaControlPanelCommand
	 */
	public $controlPanelCommands;

	/**
	 * The configuration that sent from the control panel
	 * 	 
	 *
	 * @var array of KalturaSchedulerConfig
	 */
	public $schedulerConfigs;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaSchedulerWorker extends KalturaObjectBase
{
	/**
	 * The id of the Worker
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * The id as configured in the batch config
	 * 	 
	 *
	 * @var int
	 */
	public $configuredId = null;

	/**
	 * The id of the Scheduler
	 * 	 
	 *
	 * @var int
	 */
	public $schedulerId = null;

	/**
	 * The id of the scheduler as configured in the batch config
	 * 	 
	 *
	 * @var int
	 */
	public $schedulerConfiguredId = null;

	/**
	 * The worker type
	 * 	 
	 *
	 * @var KalturaBatchJobType
	 */
	public $type = null;

	/**
	 * The friendly name of the type
	 * 	 
	 *
	 * @var string
	 */
	public $typeName = null;

	/**
	 * The scheduler name
	 * 	 
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * Array of the last statuses
	 * 	 
	 *
	 * @var array of KalturaSchedulerStatus
	 */
	public $statuses;

	/**
	 * Array of the last configs
	 * 	 
	 *
	 * @var array of KalturaSchedulerConfig
	 */
	public $configs;

	/**
	 * Array of jobs that locked to this worker
	 * 	 
	 *
	 * @var array of KalturaBatchJob
	 */
	public $lockedJobs;

	/**
	 * Avarage time between creation and queue time
	 * 	 
	 *
	 * @var int
	 */
	public $avgWait = null;

	/**
	 * Avarage time between queue time end finish time
	 * 	 
	 *
	 * @var int
	 */
	public $avgWork = null;

	/**
	 * last status time
	 * 	 
	 *
	 * @var int
	 */
	public $lastStatus = null;

	/**
	 * last status formated
	 * 	 
	 *
	 * @var string
	 */
	public $lastStatusStr = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaSchedulerWorkerListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaSchedulerWorker
	 * @readonly
	 */
	public $objects;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $totalCount = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaSearchItem extends KalturaObjectBase
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaStorageProfile extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $desciption = null;

	/**
	 * 
	 *
	 * @var KalturaStorageProfileStatus
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var KalturaStorageProfileProtocol
	 */
	public $protocol = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $storageUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $storageBaseDir = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $storageUsername = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $storagePassword = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $storageFtpPassiveMode = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $deliveryHttpBaseUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $deliveryRmpBaseUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $deliveryIisBaseUrl = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $minFileSize = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $maxFileSize = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $flavorParamsIds = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $maxConcurrentConnections = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $pathManagerClass = null;

	/**
	 * 
	 *
	 * @var array of KalturaKeyValue
	 */
	public $pathManagerParams;

	/**
	 * 
	 *
	 * @var string
	 */
	public $urlManagerClass = null;

	/**
	 * 
	 *
	 * @var array of KalturaKeyValue
	 */
	public $urlManagerParams;

	/**
	 * No need to create enum for temp field
	 * 	 
	 *
	 * @var int
	 */
	public $trigger = null;

	/**
	 * Delivery Priority
	 * 	 
	 *
	 * @var int
	 */
	public $deliveryPriority = null;

	/**
	 * 
	 *
	 * @var KalturaStorageProfileDeliveryStatus
	 */
	public $deliveryStatus = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $rtmpPrefix = null;

	/**
	 * 
	 *
	 * @var KalturaStorageProfileReadyBehavior
	 */
	public $readyBehavior = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaStorageProfileListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaStorageProfile
	 * @readonly
	 */
	public $objects;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $totalCount = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaString extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $value = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaThumbAssetListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaThumbAsset
	 * @readonly
	 */
	public $objects;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $totalCount = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaUser extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 * @insertonly
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $screenName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fullName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $email = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $dateOfBirth = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $country = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $state = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $city = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $zip = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $thumbnailUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tags = null;

	/**
	 * Admin tags can be updated only by using an admin session
	 * 	 
	 *
	 * @var string
	 */
	public $adminTags = null;

	/**
	 * 
	 *
	 * @var KalturaGender
	 */
	public $gender = null;

	/**
	 * 
	 *
	 * @var KalturaUserStatus
	 */
	public $status = null;

	/**
	 * Creation date as Unix timestamp (In seconds)
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * Last update date as Unix timestamp (In seconds)
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;

	/**
	 * Can be used to store various partner related data as a string 
	 * 	 
	 *
	 * @var string
	 */
	public $partnerData = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $indexedPartnerDataInt = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $indexedPartnerDataString = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $storageSize = null;

	/**
	 * 
	 *
	 * @var string
	 * @insertonly
	 */
	public $password = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $firstName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $lastName = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $isAdmin = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $lastLoginTime = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $statusUpdatedAt = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $deletedAt = null;

	/**
	 * 
	 *
	 * @var bool
	 * @readonly
	 */
	public $loginEnabled = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $roleIds = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $roleNames = null;

	/**
	 * 
	 *
	 * @var bool
	 * @readonly
	 */
	public $isAccountOwner = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $allowedPartnerIds = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $allowedPartnerPackages = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaUserListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaUser
	 * @readonly
	 */
	public $objects;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $totalCount = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaValue extends KalturaObjectBase
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaWorkerQueueFilter extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $schedulerId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $workerId = null;

	/**
	 * 
	 *
	 * @var KalturaBatchJobType
	 */
	public $jobType = null;

	/**
	 * 
	 *
	 * @var KalturaBatchJobFilter
	 */
	public $filter;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaAccessControlBaseFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemNameEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemNameIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAccessControlBlockAction extends KalturaAccessControlAction
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAccessControlPreviewAction extends KalturaAccessControlAction
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $limit = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaAccessControlProfileBaseFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemNameEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemNameIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtLessThanOrEqual = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAdminUser extends KalturaUser
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaAssetBaseFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entryIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entryIdIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $partnerIdIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $sizeGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $sizeLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsLike = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $deletedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $deletedAtLessThanOrEqual = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaAssetParamsBaseFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $systemNameEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemNameIn = null;

	/**
	 * 
	 *
	 * @var KalturaNullableBoolean
	 */
	public $isSystemDefaultEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsEqual = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAssetParamsOutput extends KalturaAssetParams
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $assetParamsId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $assetParamsVersion = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $assetId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $assetVersion = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $readyBehavior = null;

	/**
	 * The container format of the Flavor Params
	 * 	 
	 *
	 * @var KalturaContainerFormat
	 */
	public $format = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAssetParamsResourceContainer extends KalturaResource
{
	/**
	 * The content resource to associate with asset params
	 * 	 
	 *
	 * @var KalturaContentResource
	 */
	public $resource;

	/**
	 * The asset params to associate with the reaource
	 * 	 
	 *
	 * @var int
	 */
	public $assetParamsId = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAssetsParamsResourceContainers extends KalturaResource
{
	/**
	 * Array of resources associated with asset params ids
	 * 	 
	 *
	 * @var array of KalturaAssetParamsResourceContainer
	 */
	public $resources;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaBaseEntryBaseFilter extends KalturaFilter
{
	/**
	 * This filter should be in use for retrieving only a specific entry (identified by its entryId).
	 * 	 
	 *
	 * @var string
	 */
	public $idEqual = null;

	/**
	 * This filter should be in use for retrieving few specific entries (string should include comma separated list of entryId strings).
	 * 	 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idNotIn = null;

	/**
	 * This filter should be in use for retrieving specific entries. It should include only one string to search for in entry names (no wildcards, spaces are treated as part of the string).
	 * 	 
	 *
	 * @var string
	 */
	public $nameLike = null;

	/**
	 * This filter should be in use for retrieving specific entries. It could include few (comma separated) strings for searching in entry names, while applying an OR logic to retrieve entries that contain at least one input string (no wildcards, spaces are treated as part of the string).
	 * 	 
	 *
	 * @var string
	 */
	public $nameMultiLikeOr = null;

	/**
	 * This filter should be in use for retrieving specific entries. It could include few (comma separated) strings for searching in entry names, while applying an AND logic to retrieve entries that contain all input strings (no wildcards, spaces are treated as part of the string).
	 * 	 
	 *
	 * @var string
	 */
	public $nameMultiLikeAnd = null;

	/**
	 * This filter should be in use for retrieving entries with a specific name.
	 * 	 
	 *
	 * @var string
	 */
	public $nameEqual = null;

	/**
	 * This filter should be in use for retrieving only entries which were uploaded by/assigned to users of a specific Kaltura Partner (identified by Partner ID).
	 * 	 
	 *
	 * @var int
	 */
	public $partnerIdEqual = null;

	/**
	 * This filter should be in use for retrieving only entries within Kaltura network which were uploaded by/assigned to users of few Kaltura Partners  (string should include comma separated list of PartnerIDs)
	 * 	 
	 *
	 * @var string
	 */
	public $partnerIdIn = null;

	/**
	 * This filter parameter should be in use for retrieving only entries, uploaded by/assigned to a specific user (identified by user Id).
	 * 	 
	 *
	 * @var string
	 */
	public $userIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $creatorIdEqual = null;

	/**
	 * This filter should be in use for retrieving specific entries. It should include only one string to search for in entry tags (no wildcards, spaces are treated as part of the string).
	 * 	 
	 *
	 * @var string
	 */
	public $tagsLike = null;

	/**
	 * This filter should be in use for retrieving specific entries. It could include few (comma separated) strings for searching in entry tags, while applying an OR logic to retrieve entries that contain at least one input string (no wildcards, spaces are treated as part of the string).
	 * 	 
	 *
	 * @var string
	 */
	public $tagsMultiLikeOr = null;

	/**
	 * This filter should be in use for retrieving specific entries. It could include few (comma separated) strings for searching in entry tags, while applying an AND logic to retrieve entries that contain all input strings (no wildcards, spaces are treated as part of the string).
	 * 	 
	 *
	 * @var string
	 */
	public $tagsMultiLikeAnd = null;

	/**
	 * This filter should be in use for retrieving specific entries. It should include only one string to search for in entry tags set by an ADMIN user (no wildcards, spaces are treated as part of the string).
	 * 	 
	 *
	 * @var string
	 */
	public $adminTagsLike = null;

	/**
	 * This filter should be in use for retrieving specific entries. It could include few (comma separated) strings for searching in entry tags, set by an ADMIN user, while applying an OR logic to retrieve entries that contain at least one input string (no wildcards, spaces are treated as part of the string).
	 * 	 
	 *
	 * @var string
	 */
	public $adminTagsMultiLikeOr = null;

	/**
	 * This filter should be in use for retrieving specific entries. It could include few (comma separated) strings for searching in entry tags, set by an ADMIN user, while applying an AND logic to retrieve entries that contain all input strings (no wildcards, spaces are treated as part of the string).
	 * 	 
	 *
	 * @var string
	 */
	public $adminTagsMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $categoriesMatchAnd = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $categoriesMatchOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $categoriesIdsMatchAnd = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $categoriesIdsMatchOr = null;

	/**
	 * This filter should be in use for retrieving only entries, at a specific {
	 *
	 * @var KalturaEntryStatus
	 */
	public $statusEqual = null;

	/**
	 * This filter should be in use for retrieving only entries, not at a specific {
	 *
	 * @var KalturaEntryStatus
	 */
	public $statusNotEqual = null;

	/**
	 * This filter should be in use for retrieving only entries, at few specific {
	 *
	 * @var string
	 */
	public $statusIn = null;

	/**
	 * This filter should be in use for retrieving only entries, not at few specific {
	 *
	 * @var string
	 */
	public $statusNotIn = null;

	/**
	 * 
	 *
	 * @var KalturaEntryModerationStatus
	 */
	public $moderationStatusEqual = null;

	/**
	 * 
	 *
	 * @var KalturaEntryModerationStatus
	 */
	public $moderationStatusNotEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $moderationStatusIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $moderationStatusNotIn = null;

	/**
	 * 
	 *
	 * @var KalturaEntryType
	 */
	public $typeEqual = null;

	/**
	 * This filter should be in use for retrieving entries of few {
	 *
	 * @var string
	 */
	public $typeIn = null;

	/**
	 * This filter parameter should be in use for retrieving only entries which were created at Kaltura system after a specific time/date (standard timestamp format).
	 * 	 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * This filter parameter should be in use for retrieving only entries which were created at Kaltura system before a specific time/date (standard timestamp format).
	 * 	 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $totalRankLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $totalRankGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $groupIdEqual = null;

	/**
	 * This filter should be in use for retrieving specific entries while search match the input string within all of the following metadata attributes: name, description, tags, adminTags.
	 * 	 
	 *
	 * @var string
	 */
	public $searchTextMatchAnd = null;

	/**
	 * This filter should be in use for retrieving specific entries while search match the input string within at least one of the following metadata attributes: name, description, tags, adminTags.
	 * 	 
	 *
	 * @var string
	 */
	public $searchTextMatchOr = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $accessControlIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $accessControlIdIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $startDateGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $startDateLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $startDateGreaterThanOrEqualOrNull = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $startDateLessThanOrEqualOrNull = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $endDateGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $endDateLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $endDateGreaterThanOrEqualOrNull = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $endDateLessThanOrEqualOrNull = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $referenceIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $referenceIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $replacingEntryIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $replacingEntryIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $replacedEntryIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $replacedEntryIdIn = null;

	/**
	 * 
	 *
	 * @var KalturaEntryReplacementStatus
	 */
	public $replacementStatusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $replacementStatusIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerSortValueGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerSortValueLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $rootEntryIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $rootEntryIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsNameMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsAdminTagsMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsAdminTagsNameMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsNameMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsAdminTagsMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsAdminTagsNameMultiLikeAnd = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaBaseJobBaseFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $idGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $partnerIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $partnerIdNotIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $processorExpirationGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $processorExpirationLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $executionAttemptsGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $executionAttemptsLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $lockVersionGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $lockVersionLessThanOrEqual = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaBaseSyndicationFeedBaseFilter extends KalturaFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBatchJob extends KalturaBaseJob
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $entryId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entryName = null;

	/**
	 * 
	 *
	 * @var KalturaBatchJobType
	 * @readonly
	 */
	public $jobType = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $jobSubType = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $onStressDivertTo = null;

	/**
	 * 
	 *
	 * @var KalturaJobData
	 */
	public $data;

	/**
	 * 
	 *
	 * @var KalturaBatchJobStatus
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $abort = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $checkAgainTimeout = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $progress = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $message = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatesCount = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $priority = null;

	/**
	 * The id of identical job
	 * 	 
	 *
	 * @var int
	 */
	public $twinJobId = null;

	/**
	 * The id of the bulk upload job that initiated this job
	 * 	 
	 *
	 * @var int
	 */
	public $bulkJobId = null;

	/**
	 * When one job creates another - the parent should set this parentJobId to be its own id.
	 * 	 
	 *
	 * @var int
	 */
	public $parentJobId = null;

	/**
	 * The id of the root parent job
	 * 	 
	 *
	 * @var int
	 */
	public $rootJobId = null;

	/**
	 * The time that the job was pulled from the queue
	 * 	 
	 *
	 * @var int
	 */
	public $queueTime = null;

	/**
	 * The time that the job was finished or closed as failed
	 * 	 
	 *
	 * @var int
	 */
	public $finishTime = null;

	/**
	 * 
	 *
	 * @var KalturaBatchJobErrorTypes
	 */
	public $errType = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $errNumber = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $fileSize = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $lastWorkerRemote = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $schedulerId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $workerId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $batchIndex = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $lastSchedulerId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $lastWorkerId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $dc = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBooleanValue extends KalturaValue
{
	/**
	 * 
	 *
	 * @var bool
	 */
	public $value = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBulkDownloadJobData extends KalturaJobData
{
	/**
	 * Comma separated list of entry ids
	 * 	 
	 *
	 * @var string
	 */
	public $entryIds = null;

	/**
	 * Flavor params id to use for conversion
	 * 	 
	 *
	 * @var int
	 */
	public $flavorParamsId = null;

	/**
	 * The id of the requesting user
	 * 	 
	 *
	 * @var string
	 */
	public $puserId = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaBulkUploadBaseFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $uploadedOnGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $uploadedOnLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $uploadedOnEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;

	/**
	 * 
	 *
	 * @var KalturaBatchJobStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var KalturaBulkUploadObjectType
	 */
	public $bulkUploadObjectTypeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $bulkUploadObjectTypeIn = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBulkUploadCategoryData extends KalturaBulkUploadObjectData
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBulkUploadCategoryUserData extends KalturaBulkUploadObjectData
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBulkUploadEntryData extends KalturaBulkUploadObjectData
{
	/**
	 * Selected profile id for all bulk entries
	 *      
	 *
	 * @var int
	 */
	public $conversionProfileId = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBulkUploadJobData extends KalturaJobData
{
	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $userId = null;

	/**
	 * The screen name of the user
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $uploadedBy = null;

	/**
	 * Selected profile id for all bulk entries
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $conversionProfileId = null;

	/**
	 * Created by the API
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $resultsFileLocalPath = null;

	/**
	 * Created by the API
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $resultsFileUrl = null;

	/**
	 * Number of created entries
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $numOfEntries = null;

	/**
	 * Number of created objects
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $numOfObjects = null;

	/**
	 * The bulk upload file path
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $filePath = null;

	/**
	 * Type of object for bulk upload
	 * 	 
	 *
	 * @var KalturaBulkUploadObjectType
	 * @readonly
	 */
	public $bulkUploadObjectType = null;

	/**
	 * Name of the bulk upload file
	 * 	 
	 *
	 * @var string
	 */
	public $fileName = null;

	/**
	 * Data pertaining to the objects being uploaded
	 * 	 
	 *
	 * @var KalturaBulkUploadObjectData
	 * @readonly
	 */
	public $objectData;

	/**
	 * Type of bulk upload
	 * 	 
	 *
	 * @var KalturaBulkUploadType
	 * @readonly
	 */
	public $type = null;

	/**
	 * Recipients of the email for bulk upload success/failure
	 * 	 
	 *
	 * @var string
	 */
	public $emailRecipients = null;

	/**
	 * Number of objects that finished on error status
	 * 	 
	 *
	 * @var int
	 */
	public $numOfErrorObjects = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBulkUploadResultCategory extends KalturaBulkUploadResult
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $relativePath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $referenceId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tags = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $appearInList = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $privacy = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $inheritanceType = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $userJoinPolicy = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $defaultPermissionLevel = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $owner = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $contributionPolicy = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerSortValue = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $moderation = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBulkUploadResultCategoryUser extends KalturaBulkUploadResult
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $categoryId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $categoryReferenceId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $userId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $permissionLevel = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updateMethod = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $requiredObjectStatus = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBulkUploadResultEntry extends KalturaBulkUploadResult
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $entryId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $title = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tags = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $url = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $contentType = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $conversionProfileId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $accessControlProfileId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $category = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $scheduleStartDate = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $scheduleEndDate = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $entryStatus = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $thumbnailUrl = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $thumbnailSaved = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $sshPrivateKey = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $sshPublicKey = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $sshKeyPassphrase = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $creatorId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entitledUsersEdit = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entitledUsersPublish = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $ownerId = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBulkUploadResultUser extends KalturaBulkUploadResult
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $userId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $screenName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $email = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tags = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $dateOfBirth = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $country = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $state = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $city = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $zip = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $gender = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $firstName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $lastName = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBulkUploadUserData extends KalturaBulkUploadObjectData
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaCaptureThumbJobData extends KalturaJobData
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $srcFileSyncLocalPath = null;

	/**
	 * The translated path as used by the scheduler
	 * 	 
	 *
	 * @var string
	 */
	public $actualSrcFileSyncLocalPath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $srcFileSyncRemoteUrl = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $thumbParamsOutputId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $thumbAssetId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $srcAssetId = null;

	/**
	 * 
	 *
	 * @var KalturaAssetType
	 */
	public $srcAssetType = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $thumbPath = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaCategoryBaseFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $parentIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $parentIdIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $depthEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fullNameEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fullNameStartsWith = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fullNameIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fullIdsEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fullIdsStartsWith = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fullIdsMatchOr = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsLike = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var KalturaAppearInListType
	 */
	public $appearInListEqual = null;

	/**
	 * 
	 *
	 * @var KalturaPrivacyType
	 */
	public $privacyEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $privacyIn = null;

	/**
	 * 
	 *
	 * @var KalturaInheritanceType
	 */
	public $inheritanceTypeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $inheritanceTypeIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $referenceIdEqual = null;

	/**
	 * 
	 *
	 * @var KalturaContributionPolicyType
	 */
	public $contributionPolicyEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $membersCountGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $membersCountLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $pendingMembersCountGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $pendingMembersCountLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $privacyContextEqual = null;

	/**
	 * 
	 *
	 * @var KalturaCategoryStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $inheritedParentIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $inheritedParentIdIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerSortValueGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerSortValueLessThanOrEqual = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaCategoryEntryBaseFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $categoryIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $categoryIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entryIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entryIdIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $categoryFullIdsStartsWith = null;

	/**
	 * 
	 *
	 * @var KalturaCategoryEntryStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaCategoryUserBaseFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $categoryIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $categoryIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $userIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $userIdIn = null;

	/**
	 * 
	 *
	 * @var KalturaCategoryUserPermissionLevel
	 */
	public $permissionLevelEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $permissionLevelIn = null;

	/**
	 * 
	 *
	 * @var KalturaCategoryUserStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var KalturaUpdateMethodType
	 */
	public $updateMethodEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $updateMethodIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $categoryFullIdsStartsWith = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $categoryFullIdsEqual = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaClipAttributes extends KalturaOperationAttributes
{
	/**
	 * Offset in milliseconds
	 * 	 
	 *
	 * @var int
	 */
	public $offset = null;

	/**
	 * Duration in milliseconds
	 * 	 
	 *
	 * @var int
	 */
	public $duration = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaContentResource extends KalturaResource
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaControlPanelCommandBaseFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdByIdEqual = null;

	/**
	 * 
	 *
	 * @var KalturaControlPanelCommandType
	 */
	public $typeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $typeIn = null;

	/**
	 * 
	 *
	 * @var KalturaControlPanelCommandTargetType
	 */
	public $targetTypeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $targetTypeIn = null;

	/**
	 * 
	 *
	 * @var KalturaControlPanelCommandStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaConvartableJobData extends KalturaJobData
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $srcFileSyncLocalPath = null;

	/**
	 * The translated path as used by the scheduler
	 * 	 
	 *
	 * @var string
	 */
	public $actualSrcFileSyncLocalPath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $srcFileSyncRemoteUrl = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $engineVersion = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $flavorParamsOutputId = null;

	/**
	 * 
	 *
	 * @var KalturaFlavorParamsOutput
	 */
	public $flavorParamsOutput;

	/**
	 * 
	 *
	 * @var int
	 */
	public $mediaInfoId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $currentOperationSet = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $currentOperationIndex = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaConversionProfileAssetParamsBaseFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $conversionProfileIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $conversionProfileIdIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $assetParamsIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $assetParamsIdIn = null;

	/**
	 * 
	 *
	 * @var KalturaFlavorReadyBehaviorType
	 */
	public $readyBehaviorEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $readyBehaviorIn = null;

	/**
	 * 
	 *
	 * @var KalturaAssetParamsOrigin
	 */
	public $originEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $originIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemNameEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemNameIn = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaConversionProfileBaseFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var KalturaConversionProfileStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $nameEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemNameEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemNameIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $defaultEntryIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $defaultEntryIdIn = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaConvertProfileJobData extends KalturaJobData
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $inputFileSyncLocalPath = null;

	/**
	 * The height of last created thumbnail, will be used to comapare if this thumbnail is the best we can have
	 * 	 
	 *
	 * @var int
	 */
	public $thumbHeight = null;

	/**
	 * The bit rate of last created thumbnail, will be used to comapare if this thumbnail is the best we can have
	 * 	 
	 *
	 * @var int
	 */
	public $thumbBitrate = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaCountryRestriction extends KalturaBaseRestriction
{
	/**
	 * Country restriction type (Allow or deny)
	 * 	 
	 *
	 * @var KalturaCountryRestrictionType
	 */
	public $countryRestrictionType = null;

	/**
	 * Comma separated list of country codes to allow to deny 
	 * 	 
	 *
	 * @var string
	 */
	public $countryList = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDataEntry extends KalturaBaseEntry
{
	/**
	 * The data of the entry
	 * 	 
	 *
	 * @var string
	 */
	public $dataContent = null;

	/**
	 * indicator whether to return the object for get action with the dataContent field.
	 * 	 
	 *
	 * @var bool
	 * @insertonly
	 */
	public $retrieveDataContentByGet = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDeleteJobData extends KalturaJobData
{
	/**
	 * The filter should return the list of objects that need to be deleted.
	 * 	 
	 *
	 * @var KalturaFilter
	 */
	public $filter;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDirectoryRestriction extends KalturaBaseRestriction
{
	/**
	 * Kaltura directory restriction type
	 * 	 
	 *
	 * @var KalturaDirectoryRestrictionType
	 */
	public $directoryRestrictionType = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaEntryContextDataParams extends KalturaAccessControlScope
{
	/**
	 * Id of the current flavor.
	 * 	 
	 *
	 * @var string
	 */
	public $flavorAssetId = null;

	/**
	 * Playback streamer type: RTMP, HTTP, appleHttps, rtsp, sl.
	 * 	 
	 *
	 * @var string
	 */
	public $streamerType = null;

	/**
	 * Protocol of the specific media object.
	 * 	 
	 *
	 * @var string
	 */
	public $mediaProtocol = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaFlattenJobData extends KalturaJobData
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaFlavorAsset extends KalturaAsset
{
	/**
	 * The Flavor Params used to create this Flavor Asset
	 * 	 
	 *
	 * @var int
	 * @insertonly
	 */
	public $flavorParamsId = null;

	/**
	 * The width of the Flavor Asset 
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $width = null;

	/**
	 * The height of the Flavor Asset
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $height = null;

	/**
	 * The overall bitrate (in KBits) of the Flavor Asset 
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $bitrate = null;

	/**
	 * The frame rate (in FPS) of the Flavor Asset
	 * 	 
	 *
	 * @var float
	 * @readonly
	 */
	public $frameRate = null;

	/**
	 * True if this Flavor Asset is the original source
	 * 	 
	 *
	 * @var bool
	 * @readonly
	 */
	public $isOriginal = null;

	/**
	 * True if this Flavor Asset is playable in KDP
	 * 	 
	 *
	 * @var bool
	 * @readonly
	 */
	public $isWeb = null;

	/**
	 * The container format
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $containerFormat = null;

	/**
	 * The video codec
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $videoCodecId = null;

	/**
	 * The status of the Flavor Asset
	 * 	 
	 *
	 * @var KalturaFlavorAssetStatus
	 * @readonly
	 */
	public $status = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaFlavorParams extends KalturaAssetParams
{
	/**
	 * The video codec of the Flavor Params
	 * 	 
	 *
	 * @var KalturaVideoCodec
	 */
	public $videoCodec = null;

	/**
	 * The video bitrate (in KBits) of the Flavor Params
	 * 	 
	 *
	 * @var int
	 */
	public $videoBitrate = null;

	/**
	 * The audio codec of the Flavor Params
	 * 	 
	 *
	 * @var KalturaAudioCodec
	 */
	public $audioCodec = null;

	/**
	 * The audio bitrate (in KBits) of the Flavor Params
	 * 	 
	 *
	 * @var int
	 */
	public $audioBitrate = null;

	/**
	 * The number of audio channels for "downmixing"
	 * 	 
	 *
	 * @var int
	 */
	public $audioChannels = null;

	/**
	 * The audio sample rate of the Flavor Params
	 * 	 
	 *
	 * @var int
	 */
	public $audioSampleRate = null;

	/**
	 * The desired width of the Flavor Params
	 * 	 
	 *
	 * @var int
	 */
	public $width = null;

	/**
	 * The desired height of the Flavor Params
	 * 	 
	 *
	 * @var int
	 */
	public $height = null;

	/**
	 * The frame rate of the Flavor Params
	 * 	 
	 *
	 * @var int
	 */
	public $frameRate = null;

	/**
	 * The gop size of the Flavor Params
	 * 	 
	 *
	 * @var int
	 */
	public $gopSize = null;

	/**
	 * The list of conversion engines (comma separated)
	 * 	 
	 *
	 * @var string
	 */
	public $conversionEngines = null;

	/**
	 * The list of conversion engines extra params (separated with "|")
	 * 	 
	 *
	 * @var string
	 */
	public $conversionEnginesExtraParams = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $twoPass = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $deinterlice = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $rotate = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $operators = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $engineVersion = null;

	/**
	 * The container format of the Flavor Params
	 * 	 
	 *
	 * @var KalturaContainerFormat
	 */
	public $format = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $aspectRatioProcessingMode = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $forceFrameToMultiplication16 = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $isGopInSec = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $videoConstantBitrate = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $videoBitrateTolerance = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $clipOffset = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $clipDuration = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaImportJobData extends KalturaJobData
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $srcFileUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $destFileLocalPath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $flavorAssetId = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaIndexJobData extends KalturaJobData
{
	/**
	 * The filter should return the list of objects that need to be reindexed.
	 * 	 
	 *
	 * @var KalturaFilter
	 */
	public $filter;

	/**
	 * Indicates the last id that reindexed, used when the batch crached, to re-run from the last crash point.
	 * 	 
	 *
	 * @var int
	 */
	public $lastIndexId = null;

	/**
	 * Indicates that the object columns and attributes values should be recalculated before reindexed.
	 * 	 
	 *
	 * @var bool
	 */
	public $shouldUpdate = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaIntegerValue extends KalturaValue
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $value = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaIpAddressRestriction extends KalturaBaseRestriction
{
	/**
	 * Ip address restriction type (Allow or deny)
	 * 	 
	 *
	 * @var KalturaIpAddressRestrictionType
	 */
	public $ipAddressRestrictionType = null;

	/**
	 * Comma separated list of ip address to allow to deny 
	 * 	 
	 *
	 * @var string
	 */
	public $ipAddressList = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaMailJob extends KalturaBaseJob
{
	/**
	 * 
	 *
	 * @var KalturaMailType
	 */
	public $mailType = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $mailPriority = null;

	/**
	 * 
	 *
	 * @var KalturaMailJobStatus
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $recipientName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $recipientEmail = null;

	/**
	 * kuserId  
	 * 	 
	 *
	 * @var int
	 */
	public $recipientId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fromName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fromEmail = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $bodyParams = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $subjectParams = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $templatePath = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $culture = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $campaignId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $minSendDate = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaMailJobData extends KalturaJobData
{
	/**
	 * 
	 *
	 * @var KalturaMailType
	 */
	public $mailType = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $mailPriority = null;

	/**
	 * 
	 *
	 * @var KalturaMailJobStatus
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $recipientName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $recipientEmail = null;

	/**
	 * kuserId  
	 * 	 
	 *
	 * @var int
	 */
	public $recipientId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fromName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fromEmail = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $bodyParams = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $subjectParams = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $templatePath = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $culture = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $campaignId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $minSendDate = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $isHtml = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaMediaInfoBaseFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $flavorAssetIdEqual = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaMoveCategoryEntriesJobData extends KalturaJobData
{
	/**
	 * Source category id
	 * 	 
	 *
	 * @var int
	 */
	public $srcCategoryId = null;

	/**
	 * Destination category id
	 *      
	 *
	 * @var int
	 */
	public $destCategoryId = null;

	/**
	 * Saves the last category id that its entries moved completely
	 *      In case of crash the batch will restart from that point
	 *      
	 *
	 * @var int
	 */
	public $lastMovedCategoryId = null;

	/**
	 * Saves the last page index of the child categories filter pager
	 *      In case of crash the batch will restart from that point
	 *      
	 *
	 * @var int
	 */
	public $lastMovedCategoryPageIndex = null;

	/**
	 * Saves the last page index of the category entries filter pager
	 *      In case of crash the batch will restart from that point
	 *      
	 *
	 * @var int
	 */
	public $lastMovedCategoryEntryPageIndex = null;

	/**
	 * All entries from all child categories will be moved as well
	 *      
	 *
	 * @var bool
	 */
	public $moveFromChildren = null;

	/**
	 * Entries won't be deleted from the source entry
	 *      
	 *
	 * @var bool
	 */
	public $copyOnly = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaNotification extends KalturaBaseJob
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $puserId = null;

	/**
	 * 
	 *
	 * @var KalturaNotificationType
	 */
	public $type = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $objectId = null;

	/**
	 * 
	 *
	 * @var KalturaNotificationStatus
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $notificationData = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $numberOfAttempts = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $notificationResult = null;

	/**
	 * 
	 *
	 * @var KalturaNotificationObjectType
	 */
	public $objType = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaNotificationJobData extends KalturaJobData
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $userId = null;

	/**
	 * 
	 *
	 * @var KalturaNotificationType
	 */
	public $type = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $typeAsString = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $objectId = null;

	/**
	 * 
	 *
	 * @var KalturaNotificationStatus
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $data = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $numberOfAttempts = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $notificationResult = null;

	/**
	 * 
	 *
	 * @var KalturaNotificationObjectType
	 */
	public $objType = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaPartnerBaseFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idNotIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $nameLike = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $nameMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $nameMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $nameEqual = null;

	/**
	 * 
	 *
	 * @var KalturaPartnerStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerPackageEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerPackageGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerPackageLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $partnerNameDescriptionWebsiteAdminNameAdminEmailLike = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaPermissionBaseFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var KalturaPermissionType
	 */
	public $typeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $typeIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $nameEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $nameIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $friendlyNameLike = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $descriptionLike = null;

	/**
	 * 
	 *
	 * @var KalturaPermissionStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $partnerIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $dependsOnPermissionNamesMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $dependsOnPermissionNamesMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtLessThanOrEqual = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaPermissionItemBaseFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var KalturaPermissionItemType
	 */
	public $typeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $typeIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $partnerIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtLessThanOrEqual = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaPlayableEntry extends KalturaBaseEntry
{
	/**
	 * Number of plays
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $plays = null;

	/**
	 * Number of views
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $views = null;

	/**
	 * The width in pixels
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $width = null;

	/**
	 * The height in pixels
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $height = null;

	/**
	 * The duration in seconds
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $duration = null;

	/**
	 * The duration in miliseconds
	 * 	 
	 *
	 * @var int
	 */
	public $msDuration = null;

	/**
	 * The duration type (short for 0-4 mins, medium for 4-20 mins, long for 20+ mins)
	 * 	 
	 *
	 * @var KalturaDurationType
	 * @readonly
	 */
	public $durationType = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaPlaylist extends KalturaBaseEntry
{
	/**
	 * Content of the playlist - 
	 * 	 XML if the playlistType is dynamic 
	 * 	 text if the playlistType is static 
	 * 	 url if the playlistType is mRss 
	 * 	 
	 *
	 * @var string
	 */
	public $playlistContent = null;

	/**
	 * 
	 *
	 * @var array of KalturaMediaEntryFilterForPlaylist
	 */
	public $filters;

	/**
	 * Maximum count of results to be returned in playlist execution
	 * 	 
	 *
	 * @var int
	 */
	public $totalResults = null;

	/**
	 * Type of playlist
	 * 	 
	 *
	 * @var KalturaPlaylistType
	 */
	public $playlistType = null;

	/**
	 * Number of plays
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $plays = null;

	/**
	 * Number of views
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $views = null;

	/**
	 * The duration in seconds
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $duration = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaProvisionJobData extends KalturaJobData
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $streamID = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $backupStreamID = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $rtmp = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $encoderIP = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $backupEncoderIP = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $encoderPassword = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $encoderUsername = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $endDate = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $returnVal = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $mediaType = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $primaryBroadcastingUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $secondaryBroadcastingUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $streamName = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaReportBaseFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $partnerIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemNameEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemNameIn = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaSearchCondition extends KalturaSearchItem
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $field = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $value = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaSearchOperator extends KalturaSearchItem
{
	/**
	 * 
	 *
	 * @var KalturaSearchOperatorType
	 */
	public $type = null;

	/**
	 * 
	 *
	 * @var array of KalturaSearchItem
	 */
	public $items;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaSessionRestriction extends KalturaBaseRestriction
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaSiteRestriction extends KalturaBaseRestriction
{
	/**
	 * The site restriction type (allow or deny)
	 * 	 
	 *
	 * @var KalturaSiteRestrictionType
	 */
	public $siteRestrictionType = null;

	/**
	 * Comma separated list of sites (domains) to allow or deny
	 * 	 
	 *
	 * @var string
	 */
	public $siteList = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaStorageJobData extends KalturaJobData
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $serverUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $serverUsername = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $serverPassword = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $ftpPassiveMode = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $srcFileSyncLocalPath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $srcFileSyncId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $destFileSyncStoredPath = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaStorageProfileBaseFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $partnerIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemNameEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemNameIn = null;

	/**
	 * 
	 *
	 * @var KalturaStorageProfileStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;

	/**
	 * 
	 *
	 * @var KalturaStorageProfileProtocol
	 */
	public $protocolEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $protocolIn = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaStringValue extends KalturaValue
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $value = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaThumbAsset extends KalturaAsset
{
	/**
	 * The Flavor Params used to create this Flavor Asset
	 * 	 
	 *
	 * @var int
	 * @insertonly
	 */
	public $thumbParamsId = null;

	/**
	 * The width of the Flavor Asset 
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $width = null;

	/**
	 * The height of the Flavor Asset
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $height = null;

	/**
	 * The status of the asset
	 * 	 
	 *
	 * @var KalturaThumbAssetStatus
	 * @readonly
	 */
	public $status = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaThumbParams extends KalturaAssetParams
{
	/**
	 * 
	 *
	 * @var KalturaThumbCropType
	 */
	public $cropType = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $quality = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $cropX = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $cropY = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $cropWidth = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $cropHeight = null;

	/**
	 * 
	 *
	 * @var float
	 */
	public $videoOffset = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $width = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $height = null;

	/**
	 * 
	 *
	 * @var float
	 */
	public $scaleWidth = null;

	/**
	 * 
	 *
	 * @var float
	 */
	public $scaleHeight = null;

	/**
	 * Hexadecimal value
	 * 	 
	 *
	 * @var string
	 */
	public $backgroundColor = null;

	/**
	 * Id of the flavor params or the thumbnail params to be used as source for the thumbnail creation
	 * 	 
	 *
	 * @var int
	 */
	public $sourceParamsId = null;

	/**
	 * The container format of the Flavor Params
	 * 	 
	 *
	 * @var KalturaContainerFormat
	 */
	public $format = null;

	/**
	 * The image density (dpi) for example: 72 or 96
	 * 	 
	 *
	 * @var int
	 */
	public $density = null;

	/**
	 * Strip profiles and comments
	 * 	 
	 *
	 * @var bool
	 */
	public $stripProfiles = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaUiConfBaseFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $nameLike = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $partnerIdIn = null;

	/**
	 * 
	 *
	 * @var KalturaUiConfObjType
	 */
	public $objTypeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $objTypeIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var KalturaUiConfCreationMode
	 */
	public $creationModeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $creationModeIn = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaUploadTokenBaseFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $userIdEqual = null;

	/**
	 * 
	 *
	 * @var KalturaUploadTokenStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaUserAgentRestriction extends KalturaBaseRestriction
{
	/**
	 * User agent restriction type (Allow or deny)
	 * 	 
	 *
	 * @var KalturaUserAgentRestrictionType
	 */
	public $userAgentRestrictionType = null;

	/**
	 * A comma seperated list of user agent regular expressions
	 * 	 
	 *
	 * @var string
	 */
	public $userAgentRegexList = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaUserBaseFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $screenNameLike = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $screenNameStartsWith = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $emailLike = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $emailStartsWith = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var KalturaUserStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $firstNameStartsWith = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $lastNameStartsWith = null;

	/**
	 * 
	 *
	 * @var KalturaNullableBoolean
	 */
	public $isAdminEqual = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaUserLoginDataBaseFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $loginEmailEqual = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaUserRoleBaseFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $nameEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $nameIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $descriptionLike = null;

	/**
	 * 
	 *
	 * @var KalturaUserRoleStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $partnerIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtLessThanOrEqual = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaWidgetBaseFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $sourceWidgetIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $rootWidgetIdEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entryIdEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $uiConfIdEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $partnerDataLike = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAccessControlFilter extends KalturaAccessControlBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAccessControlProfileFilter extends KalturaAccessControlProfileBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAkamaiProvisionJobData extends KalturaProvisionJobData
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $wsdlUsername = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $wsdlPassword = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $cpcode = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $emailId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $primaryContact = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $secondaryContact = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAssetFilter extends KalturaAssetBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAssetParamsFilter extends KalturaAssetParamsBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAssetResource extends KalturaContentResource
{
	/**
	 * ID of the source asset 
	 * 	 
	 *
	 * @var string
	 */
	public $assetId = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBaseEntryFilter extends KalturaBaseEntryBaseFilter
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $freeText = null;

	/**
	 * 
	 *
	 * @var KalturaNullableBoolean
	 */
	public $isRoot = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $categoriesFullNameIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $categoryAncestorIdIn = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBaseJobFilter extends KalturaBaseJobBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBaseSyndicationFeedFilter extends KalturaBaseSyndicationFeedBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaBooleanField extends KalturaBooleanValue
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBulkUploadFilter extends KalturaBulkUploadBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaCategoryEntryAdvancedFilter extends KalturaSearchOperator
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $categoriesMatchOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $categoryEntryStatusIn = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaCategoryEntryFilter extends KalturaCategoryEntryBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaCategoryFilter extends KalturaCategoryBaseFilter
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $freeText = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $membersIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $nameOrReferenceIdStartsWith = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $managerEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $memberEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fullNameStartsWithIn = null;

	/**
	 * not includes the category itself (only sub categories)
	 * 	 
	 *
	 * @var string
	 */
	public $ancestorIdIn = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaCategoryUserFilter extends KalturaCategoryUserBaseFilter
{
	/**
	 * Return the list of categoryUser that are not inherited from parent category - only the direct categoryUsers.
	 * 	 
	 *
	 * @var bool
	 */
	public $categoryDirectMembers = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaControlPanelCommandFilter extends KalturaControlPanelCommandBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaConversionProfileAssetParamsFilter extends KalturaConversionProfileAssetParamsBaseFilter
{
	/**
	 * 
	 *
	 * @var KalturaConversionProfileFilter
	 */
	public $conversionProfileIdFilter;

	/**
	 * 
	 *
	 * @var KalturaAssetParamsFilter
	 */
	public $assetParamsIdFilter;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaConversionProfileFilter extends KalturaConversionProfileBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaConvertCollectionJobData extends KalturaConvartableJobData
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $destDirLocalPath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $destDirRemoteUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $destFileName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $inputXmlLocalPath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $inputXmlRemoteUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $commandLinesStr = null;

	/**
	 * 
	 *
	 * @var array of KalturaConvertCollectionFlavorData
	 */
	public $flavors;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaConvertJobData extends KalturaConvartableJobData
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $destFileSyncLocalPath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $destFileSyncRemoteUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $logFileSyncLocalPath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $logFileSyncRemoteUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $flavorAssetId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $remoteMediaId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $customData = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaDataCenterContentResource extends KalturaContentResource
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaEntryResource extends KalturaContentResource
{
	/**
	 * ID of the source entry 
	 * 	 
	 *
	 * @var string
	 */
	public $entryId = null;

	/**
	 * ID of the source flavor params, set to null to use the source flavor
	 * 	 
	 *
	 * @var int
	 */
	public $flavorParamsId = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaExtractMediaJobData extends KalturaConvartableJobData
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $flavorAssetId = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaFileSyncResource extends KalturaContentResource
{
	/**
	 * The object type of the file sync object 
	 * 	 
	 *
	 * @var int
	 */
	public $fileSyncObjectType = null;

	/**
	 * The object sub-type of the file sync object 
	 * 	 
	 *
	 * @var int
	 */
	public $objectSubType = null;

	/**
	 * The object id of the file sync object 
	 * 	 
	 *
	 * @var string
	 */
	public $objectId = null;

	/**
	 * The version of the file sync object 
	 * 	 
	 *
	 * @var string
	 */
	public $version = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaFlavorParamsOutput extends KalturaFlavorParams
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $flavorParamsId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $commandLinesStr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $flavorParamsVersion = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $flavorAssetId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $flavorAssetVersion = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $readyBehavior = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaIndexAdvancedFilter extends KalturaSearchOperator
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $indexIdGreaterThan = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaIntegerField extends KalturaIntegerValue
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaMediaEntry extends KalturaPlayableEntry
{
	/**
	 * The media type of the entry
	 * 	 
	 *
	 * @var KalturaMediaType
	 * @insertonly
	 */
	public $mediaType = null;

	/**
	 * Override the default conversion quality  
	 * 	 
	 *
	 * @var string
	 * @insertonly
	 */
	public $conversionQuality = null;

	/**
	 * The source type of the entry 
	 * 	 
	 *
	 * @var KalturaSourceType
	 * @insertonly
	 */
	public $sourceType = null;

	/**
	 * The search provider type used to import this entry
	 * 	 
	 *
	 * @var KalturaSearchProviderType
	 * @insertonly
	 */
	public $searchProviderType = null;

	/**
	 * The ID of the media in the importing site
	 * 	 
	 *
	 * @var string
	 * @insertonly
	 */
	public $searchProviderId = null;

	/**
	 * The user name used for credits
	 * 	 
	 *
	 * @var string
	 */
	public $creditUserName = null;

	/**
	 * The URL for credits
	 * 	 
	 *
	 * @var string
	 */
	public $creditUrl = null;

	/**
	 * The media date extracted from EXIF data (For images) as Unix timestamp (In seconds)
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $mediaDate = null;

	/**
	 * The URL used for playback. This is not the download URL.
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $dataUrl = null;

	/**
	 * Comma separated flavor params ids that exists for this media entry
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $flavorParamsIds = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaMediaFlavorParams extends KalturaFlavorParams
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaMediaInfoFilter extends KalturaMediaInfoBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaMixEntry extends KalturaPlayableEntry
{
	/**
	 * Indicates whether the user has submited a real thumbnail to the mix (Not the one that was generated automaticaly)
	 * 	 
	 *
	 * @var bool
	 * @readonly
	 */
	public $hasRealThumbnail = null;

	/**
	 * The editor type used to edit the metadata
	 * 	 
	 *
	 * @var KalturaEditorType
	 */
	public $editorType = null;

	/**
	 * The xml data of the mix
	 * 	 
	 *
	 * @var string
	 */
	public $dataContent = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaOperationResource extends KalturaContentResource
{
	/**
	 * Only KalturaEntryResource and KalturaAssetResource are supported
	 * 	 
	 *
	 * @var KalturaContentResource
	 */
	public $resource;

	/**
	 * 
	 *
	 * @var array of KalturaOperationAttributes
	 */
	public $operationAttributes;

	/**
	 * ID of alternative asset params to be used instead of the system default flavor params 
	 * 	 
	 *
	 * @var int
	 */
	public $assetParamsId = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaPartnerFilter extends KalturaPartnerBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaPermissionFilter extends KalturaPermissionBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaPermissionItemFilter extends KalturaPermissionItemBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaPostConvertJobData extends KalturaConvartableJobData
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $flavorAssetId = null;

	/**
	 * Indicates if a thumbnail should be created
	 * 	 
	 *
	 * @var bool
	 */
	public $createThumb = null;

	/**
	 * The path of the created thumbnail
	 * 	 
	 *
	 * @var string
	 */
	public $thumbPath = null;

	/**
	 * The position of the thumbnail in the media file
	 * 	 
	 *
	 * @var int
	 */
	public $thumbOffset = null;

	/**
	 * The height of the movie, will be used to comapare if this thumbnail is the best we can have
	 * 	 
	 *
	 * @var int
	 */
	public $thumbHeight = null;

	/**
	 * The bit rate of the movie, will be used to comapare if this thumbnail is the best we can have
	 * 	 
	 *
	 * @var int
	 */
	public $thumbBitrate = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $customData = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaPreviewRestriction extends KalturaSessionRestriction
{
	/**
	 * The preview restriction length 
	 * 	 
	 *
	 * @var int
	 */
	public $previewLength = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaRemoteStorageResources extends KalturaContentResource
{
	/**
	 * Array of remote stoage resources 
	 * 	 
	 *
	 * @var array of KalturaRemoteStorageResource
	 */
	public $resources;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaReportFilter extends KalturaReportBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaSearchComparableCondition extends KalturaSearchCondition
{
	/**
	 * 
	 *
	 * @var KalturaSearchConditionComparison
	 */
	public $comparison = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaSshImportJobData extends KalturaImportJobData
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $privateKey = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $publicKey = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $passPhrase = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaStorageDeleteJobData extends KalturaStorageJobData
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaStorageExportJobData extends KalturaStorageJobData
{
	/**
	 * 
	 *
	 * @var bool
	 */
	public $force = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaStorageProfileFilter extends KalturaStorageProfileBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaStringField extends KalturaStringValue
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaStringResource extends KalturaContentResource
{
	/**
	 * Textual content
	 * 	 
	 *
	 * @var string
	 */
	public $content = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaThumbParamsOutput extends KalturaThumbParams
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $thumbParamsId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $thumbParamsVersion = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $thumbAssetId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $thumbAssetVersion = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $rotate = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaUiConfFilter extends KalturaUiConfBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaUploadTokenFilter extends KalturaUploadTokenBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaUrlResource extends KalturaContentResource
{
	/**
	 * Remote URL, FTP, HTTP or HTTPS 
	 * 	 
	 *
	 * @var string
	 */
	public $url = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaUserFilter extends KalturaUserBaseFilter
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $idOrScreenNameStartsWith = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var KalturaNullableBoolean
	 */
	public $loginEnabledEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $roleIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $firstNameOrLastNameStartsWith = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaUserLoginDataFilter extends KalturaUserLoginDataBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaUserRoleFilter extends KalturaUserRoleBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaWidgetFilter extends KalturaWidgetBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaAdminUserBaseFilter extends KalturaUserFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaApiActionPermissionItemBaseFilter extends KalturaPermissionItemFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaApiParameterPermissionItemBaseFilter extends KalturaPermissionItemFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaAssetParamsOutputBaseFilter extends KalturaAssetParamsFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $assetParamsIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $assetParamsVersionEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $assetIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $assetVersionEqual = null;

	/**
	 * 
	 *
	 * @var KalturaContainerFormat
	 */
	public $formatEqual = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaBatchJobBaseFilter extends KalturaBaseJobFilter
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $entryIdEqual = null;

	/**
	 * 
	 *
	 * @var KalturaBatchJobType
	 */
	public $jobTypeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $jobTypeIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $jobTypeNotIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $jobSubTypeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $jobSubTypeIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $jobSubTypeNotIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $onStressDivertToEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $onStressDivertToIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $onStressDivertToNotIn = null;

	/**
	 * 
	 *
	 * @var KalturaBatchJobStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusNotIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $abortEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $checkAgainTimeoutGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $checkAgainTimeoutLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $progressGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $progressLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatesCountGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatesCountLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $priorityGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $priorityLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $priorityEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $priorityIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $priorityNotIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $twinJobIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $twinJobIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $twinJobIdNotIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $bulkJobIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $bulkJobIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $bulkJobIdNotIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $parentJobIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $parentJobIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $parentJobIdNotIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $rootJobIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $rootJobIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $rootJobIdNotIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $queueTimeGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $queueTimeLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $finishTimeGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $finishTimeLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var KalturaBatchJobErrorTypes
	 */
	public $errTypeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $errTypeIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $errTypeNotIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $errNumberEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $errNumberIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $errNumberNotIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $fileSizeLessThan = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $fileSizeGreaterThan = null;

	/**
	 * 
	 *
	 * @var KalturaNullableBoolean
	 */
	public $lastWorkerRemoteEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $schedulerIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $schedulerIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $schedulerIdNotIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $workerIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $workerIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $workerIdNotIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $batchIndexEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $batchIndexIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $batchIndexNotIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $lastSchedulerIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $lastSchedulerIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $lastSchedulerIdNotIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $lastWorkerIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $lastWorkerIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $lastWorkerIdNotIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $dcEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $dcIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $dcNotIn = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaCountryContextField extends KalturaStringField
{
	/**
	 * The ip geo coder engine to be used
	 * 	 
	 *
	 * @var KalturaGeoCoderType
	 */
	public $geoCoderType = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaDataEntryBaseFilter extends KalturaBaseEntryFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaEvalBooleanField extends KalturaBooleanField
{
	/**
	 * PHP code
	 * 	 
	 *
	 * @var string
	 */
	public $code = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaEvalStringField extends KalturaStringField
{
	/**
	 * PHP code
	 * 	 
	 *
	 * @var string
	 */
	public $code = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaFlavorAssetBaseFilter extends KalturaAssetFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $flavorParamsIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $flavorParamsIdIn = null;

	/**
	 * 
	 *
	 * @var KalturaFlavorAssetStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusNotIn = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaFlavorParamsBaseFilter extends KalturaAssetParamsFilter
{
	/**
	 * 
	 *
	 * @var KalturaContainerFormat
	 */
	public $formatEqual = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaGenericSyndicationFeedBaseFilter extends KalturaBaseSyndicationFeedFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaGoogleVideoSyndicationFeedBaseFilter extends KalturaBaseSyndicationFeedFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaITunesSyndicationFeedBaseFilter extends KalturaBaseSyndicationFeedFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaIpAddressContextField extends KalturaStringField
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaLiveStreamEntry extends KalturaMediaEntry
{
	/**
	 * The message to be presented when the stream is offline
	 * 	 
	 *
	 * @var string
	 */
	public $offlineMessage = null;

	/**
	 * The stream id as provided by the provider
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $streamRemoteId = null;

	/**
	 * The backup stream id as provided by the provider
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $streamRemoteBackupId = null;

	/**
	 * Array of supported bitrates
	 * 	 
	 *
	 * @var array of KalturaLiveStreamBitrate
	 */
	public $bitrates;

	/**
	 * 
	 *
	 * @var string
	 */
	public $primaryBroadcastingUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $secondaryBroadcastingUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $streamName = null;

	/**
	 * The stream url
	 * 	 
	 *
	 * @var string
	 */
	public $streamUrl = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaMailJobBaseFilter extends KalturaBaseJobFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaMediaFlavorParamsOutput extends KalturaFlavorParamsOutput
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaNotificationBaseFilter extends KalturaBaseJobFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaPlayableEntryBaseFilter extends KalturaBaseEntryFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $durationLessThan = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $durationGreaterThan = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $durationLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $durationGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $msDurationLessThan = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $msDurationGreaterThan = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $msDurationLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $msDurationGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $durationTypeMatchOr = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaPlaylistBaseFilter extends KalturaBaseEntryFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaRemoteStorageResource extends KalturaUrlResource
{
	/**
	 * ID of storage profile to be associated with the created file sync, used for file serving URL composing. 
	 * 	 
	 *
	 * @var int
	 */
	public $storageProfileId = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaServerFileResource extends KalturaDataCenterContentResource
{
	/**
	 * Full path to the local file 
	 * 	 
	 *
	 * @var string
	 */
	public $localFilePath = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaSshUrlResource extends KalturaUrlResource
{
	/**
	 * SSH private key
	 * 	 
	 *
	 * @var string
	 */
	public $privateKey = null;

	/**
	 * SSH public key
	 * 	 
	 *
	 * @var string
	 */
	public $publicKey = null;

	/**
	 * Passphrase for SSH keys
	 * 	 
	 *
	 * @var string
	 */
	public $keyPassphrase = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaThumbAssetBaseFilter extends KalturaAssetFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $thumbParamsIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $thumbParamsIdIn = null;

	/**
	 * 
	 *
	 * @var KalturaThumbAssetStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusNotIn = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaThumbParamsBaseFilter extends KalturaAssetParamsFilter
{
	/**
	 * 
	 *
	 * @var KalturaContainerFormat
	 */
	public $formatEqual = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaTimeContextField extends KalturaIntegerField
{
	/**
	 * Time offset in seconds since current time
	 * 	 
	 *
	 * @var int
	 */
	public $offset = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaTubeMogulSyndicationFeedBaseFilter extends KalturaBaseSyndicationFeedFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaUploadedFileTokenResource extends KalturaDataCenterContentResource
{
	/**
	 * Token that returned from upload.upload action or uploadToken.add action. 
	 * 	 
	 *
	 * @var string
	 */
	public $token = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaUserAgentContextField extends KalturaStringField
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaUserEmailContextField extends KalturaStringField
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaWebcamTokenResource extends KalturaDataCenterContentResource
{
	/**
	 * Token that returned from media server such as FMS or red5. 
	 * 	 
	 *
	 * @var string
	 */
	public $token = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaYahooSyndicationFeedBaseFilter extends KalturaBaseSyndicationFeedFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAdminUserFilter extends KalturaAdminUserBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaApiActionPermissionItemFilter extends KalturaApiActionPermissionItemBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaApiParameterPermissionItemFilter extends KalturaApiParameterPermissionItemBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAssetParamsOutputFilter extends KalturaAssetParamsOutputBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBatchJobFilter extends KalturaBatchJobBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDataEntryFilter extends KalturaDataEntryBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaFlavorAssetFilter extends KalturaFlavorAssetBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaFlavorParamsFilter extends KalturaFlavorParamsBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaGenericSyndicationFeedFilter extends KalturaGenericSyndicationFeedBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaGoogleVideoSyndicationFeedFilter extends KalturaGoogleVideoSyndicationFeedBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaITunesSyndicationFeedFilter extends KalturaITunesSyndicationFeedBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaLiveStreamAdminEntry extends KalturaLiveStreamEntry
{
	/**
	 * The broadcast primary ip
	 * 	 
	 *
	 * @var string
	 */
	public $encodingIP1 = null;

	/**
	 * The broadcast secondary ip
	 * 	 
	 *
	 * @var string
	 */
	public $encodingIP2 = null;

	/**
	 * The broadcast password
	 * 	 
	 *
	 * @var string
	 */
	public $streamPassword = null;

	/**
	 * The broadcast username
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $streamUsername = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaMailJobFilter extends KalturaMailJobBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaNotificationFilter extends KalturaNotificationBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaPlayableEntryFilter extends KalturaPlayableEntryBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaPlaylistFilter extends KalturaPlaylistBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaThumbAssetFilter extends KalturaThumbAssetBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaThumbParamsFilter extends KalturaThumbParamsBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaTubeMogulSyndicationFeedFilter extends KalturaTubeMogulSyndicationFeedBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaYahooSyndicationFeedFilter extends KalturaYahooSyndicationFeedBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBatchJobFilterExt extends KalturaBatchJobFilter
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $jobTypeAndSubTypeIn = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaFlavorParamsOutputBaseFilter extends KalturaFlavorParamsFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $flavorParamsIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $flavorParamsVersionEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $flavorAssetIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $flavorAssetVersionEqual = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaGenericXsltSyndicationFeedBaseFilter extends KalturaGenericSyndicationFeedFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaMediaEntryBaseFilter extends KalturaPlayableEntryFilter
{
	/**
	 * 
	 *
	 * @var KalturaMediaType
	 */
	public $mediaTypeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $mediaTypeIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $mediaDateGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $mediaDateLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $flavorParamsIdsMatchOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $flavorParamsIdsMatchAnd = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaMediaFlavorParamsBaseFilter extends KalturaFlavorParamsFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaMixEntryBaseFilter extends KalturaPlayableEntryFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaThumbParamsOutputBaseFilter extends KalturaThumbParamsFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $thumbParamsIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $thumbParamsVersionEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $thumbAssetIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $thumbAssetVersionEqual = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaFlavorParamsOutputFilter extends KalturaFlavorParamsOutputBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaGenericXsltSyndicationFeedFilter extends KalturaGenericXsltSyndicationFeedBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaMediaEntryFilter extends KalturaMediaEntryBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaMediaFlavorParamsFilter extends KalturaMediaFlavorParamsBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaMixEntryFilter extends KalturaMixEntryBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaThumbParamsOutputFilter extends KalturaThumbParamsOutputBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaLiveStreamEntryBaseFilter extends KalturaMediaEntryFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaMediaEntryFilterForPlaylist extends KalturaMediaEntryFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $limit = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaMediaFlavorParamsOutputBaseFilter extends KalturaFlavorParamsOutputFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaLiveStreamEntryFilter extends KalturaLiveStreamEntryBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaMediaFlavorParamsOutputFilter extends KalturaMediaFlavorParamsOutputBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaLiveStreamAdminEntryBaseFilter extends KalturaLiveStreamEntryFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaLiveStreamAdminEntryFilter extends KalturaLiveStreamAdminEntryBaseFilter
{

}

