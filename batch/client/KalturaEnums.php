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
class KalturaAppearInListType
{
	const PARTNER_ONLY = 1;
	const CATEGORY_MEMBERS_ONLY = 3;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAssetParamsOrigin
{
	const CONVERT = 0;
	const INGEST = 1;
	const CONVERT_WHEN_MISSING = 2;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAssetStatus
{
	const ERROR = -1;
	const QUEUED = 0;
	const READY = 2;
	const DELETED = 3;
	const IMPORTING = 7;
	const EXPORTING = 9;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBatchJobAppErrors
{
	const OUTPUT_FILE_DOESNT_EXIST = 11;
	const OUTPUT_FILE_WRONG_SIZE = 12;
	const CANNOT_CREATE_DIRECTORY = 13;
	const FILE_ALREADY_EXISTS = 14;
	const NFS_FILE_DOESNT_EXIST = 21;
	const EXTRACT_MEDIA_FAILED = 31;
	const CLOSER_TIMEOUT = 41;
	const ENGINE_NOT_FOUND = 51;
	const REMOTE_FILE_NOT_FOUND = 61;
	const REMOTE_DOWNLOAD_FAILED = 62;
	const BULK_FILE_NOT_FOUND = 71;
	const BULK_VALIDATION_FAILED = 72;
	const BULK_PARSE_ITEMS_FAILED = 73;
	const BULK_UNKNOWN_ERROR = 74;
	const BULK_INVLAID_BULK_REQUEST_COUNT = 75;
	const BULK_NO_ENTRIES_HANDLED = 76;
	const BULK_ACTION_NOT_SUPPORTED = 77;
	const BULK_MISSING_MANDATORY_PARAMETER = 78;
	const BULK_ITEM_VALIDATION_FAILED = 79;
	const BULK_ITEM_NOT_FOUND = 701;
	const BULK_ELEMENT_NOT_FOUND = 702;
	const CONVERSION_FAILED = 81;
	const THUMBNAIL_NOT_CREATED = 91;
	const MISSING_PARAMETERS = 92;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBatchJobErrorTypes
{
	const APP = 0;
	const RUNTIME = 1;
	const HTTP = 2;
	const CURL = 3;
	const KALTURA_API = 4;
	const KALTURA_CLIENT = 5;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBatchJobStatus
{
	const PENDING = 0;
	const QUEUED = 1;
	const PROCESSING = 2;
	const PROCESSED = 3;
	const MOVEFILE = 4;
	const FINISHED = 5;
	const FAILED = 6;
	const ABORTED = 7;
	const ALMOST_DONE = 8;
	const RETRY = 9;
	const FATAL = 10;
	const DONT_PROCESS = 11;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBitRateMode
{
	const CBR = 1;
	const VBR = 2;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaCategoryEntryStatus
{
	const PENDING = 1;
	const ACTIVE = 2;
	const DELETED = 3;
	const REJECTED = 4;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaCategoryStatus
{
	const UPDATING = 1;
	const ACTIVE = 2;
	const DELETED = 3;
	const PURGED = 4;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaCategoryUserPermissionLevel
{
	const MANAGER = 0;
	const MODERATOR = 1;
	const CONTRIBUTOR = 2;
	const MEMBER = 3;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaCategoryUserStatus
{
	const ACTIVE = 1;
	const PENDING = 2;
	const NOT_ACTIVE = 3;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaCommercialUseType
{
	const COMMERCIAL_USE = 1;
	const NON_COMMERCIAL_USE = 0;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaContributionPolicyType
{
	const ALL = 1;
	const MEMBERS_WITH_CONTRIBUTION_PERMISSION = 2;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaControlPanelCommandStatus
{
	const PENDING = 1;
	const HANDLED = 2;
	const DONE = 3;
	const FAILED = 4;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaControlPanelCommandTargetType
{
	const DATA_CENTER = 1;
	const SCHEDULER = 2;
	const JOB_TYPE = 3;
	const JOB = 4;
	const BATCH = 5;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaControlPanelCommandType
{
	const STOP = 1;
	const START = 2;
	const CONFIG = 3;
	const KILL = 4;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaCopyObjectType
{
	const ENTRY = 1;
	const CATEGORY = 2;
	const CATEGORY_USER = 3;
	const CATEGORY_ENTRY = 4;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaCountryRestrictionType
{
	const RESTRICT_COUNTRY_LIST = 0;
	const ALLOW_COUNTRY_LIST = 1;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDeleteObjectType
{
	const CATEGORY_ENTRY = 1;
	const CATEGORY_USER = 2;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDirectoryRestrictionType
{
	const DONT_DISPLAY = 0;
	const DISPLAY_WITH_LINK = 1;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaEditorType
{
	const SIMPLE = 1;
	const ADVANCED = 2;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaEmailIngestionProfileStatus
{
	const INACTIVE = 0;
	const ACTIVE = 1;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaEntryModerationStatus
{
	const PENDING_MODERATION = 1;
	const APPROVED = 2;
	const REJECTED = 3;
	const FLAGGED_FOR_REVIEW = 5;
	const AUTO_APPROVED = 6;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaExportProtocol
{
	const KALTURA_DC = 0;
	const FTP = 1;
	const SCP = 2;
	const SFTP = 3;
	const S3 = 6;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaFlavorAssetStatus
{
	const CONVERTING = 1;
	const NOT_APPLICABLE = 4;
	const TEMP = 5;
	const WAIT_FOR_CONVERT = 6;
	const VALIDATING = 8;
	const ERROR = -1;
	const QUEUED = 0;
	const READY = 2;
	const DELETED = 3;
	const IMPORTING = 7;
	const EXPORTING = 9;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaFlavorReadyBehaviorType
{
	const NO_IMPACT = 0;
	const REQUIRED = 1;
	const OPTIONAL = 2;
	const INHERIT_FLAVOR_PARAMS = 0;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaGender
{
	const UNKNOWN = 0;
	const MALE = 1;
	const FEMALE = 2;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaIndexObjectType
{
	const LOCK_CATEGORY = 1;
	const CATEGORY = 2;
	const CATEGORY_ENTRY = 3;
	const ENTRY = 4;
	const CATEGORY_USER = 5;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaInheritanceType
{
	const INHERIT = 1;
	const MANUAL = 2;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaIpAddressRestrictionType
{
	const RESTRICT_LIST = 0;
	const ALLOW_LIST = 1;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaLicenseType
{
	const UNKNOWN = -1;
	const NONE = 0;
	const COPYRIGHTED = 1;
	const PUBLIC_DOMAIN = 2;
	const CREATIVECOMMONS_ATTRIBUTION = 3;
	const CREATIVECOMMONS_ATTRIBUTION_SHARE_ALIKE = 4;
	const CREATIVECOMMONS_ATTRIBUTION_NO_DERIVATIVES = 5;
	const CREATIVECOMMONS_ATTRIBUTION_NON_COMMERCIAL = 6;
	const CREATIVECOMMONS_ATTRIBUTION_NON_COMMERCIAL_SHARE_ALIKE = 7;
	const CREATIVECOMMONS_ATTRIBUTION_NON_COMMERCIAL_NO_DERIVATIVES = 8;
	const GFDL = 9;
	const GPL = 10;
	const AFFERO_GPL = 11;
	const LGPL = 12;
	const BSD = 13;
	const APACHE = 14;
	const MOZILLA = 15;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaMailJobStatus
{
	const PENDING = 1;
	const SENT = 2;
	const ERROR = 3;
	const QUEUED = 4;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaMediaType
{
	const VIDEO = 1;
	const IMAGE = 2;
	const AUDIO = 5;
	const LIVE_STREAM_FLASH = 201;
	const LIVE_STREAM_WINDOWS_MEDIA = 202;
	const LIVE_STREAM_REAL_MEDIA = 203;
	const LIVE_STREAM_QUICKTIME = 204;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaModerationFlagStatus
{
	const PENDING = 1;
	const MODERATED = 2;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaModerationFlagType
{
	const SEXUAL_CONTENT = 1;
	const VIOLENT_REPULSIVE = 2;
	const HARMFUL_DANGEROUS = 3;
	const SPAM_COMMERCIALS = 4;
	const COPYRIGHT = 5;
	const TERMS_OF_USE_VIOLATION = 6;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaModerationObjectType
{
	const ENTRY = 2;
	const USER = 3;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaNotificationObjectType
{
	const ENTRY = 1;
	const KSHOW = 2;
	const USER = 3;
	const BATCH_JOB = 4;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaNotificationStatus
{
	const PENDING = 1;
	const SENT = 2;
	const ERROR = 3;
	const SHOULD_RESEND = 4;
	const ERROR_RESENDING = 5;
	const SENT_SYNCH = 6;
	const QUEUED = 7;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaNotificationType
{
	const ENTRY_ADD = 1;
	const ENTR_UPDATE_PERMISSIONS = 2;
	const ENTRY_DELETE = 3;
	const ENTRY_BLOCK = 4;
	const ENTRY_UPDATE = 5;
	const ENTRY_UPDATE_THUMBNAIL = 6;
	const ENTRY_UPDATE_MODERATION = 7;
	const USER_ADD = 21;
	const USER_BANNED = 26;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaNullableBoolean
{
	const NULL_VALUE = -1;
	const FALSE_VALUE = 0;
	const TRUE_VALUE = 1;
	const TRUE_VALUE_STRING = true;
	const FALSE_VALUE_STRING = false;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaPartnerGroupType
{
	const PUBLISHER = 1;
	const VAR_GROUP = 2;
	const GROUP = 3;
	const TEMPLATE = 4;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaPartnerStatus
{
	const ACTIVE = 1;
	const BLOCKED = 2;
	const FULL_BLOCK = 3;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaPartnerType
{
	const KMC = 1;
	const WIKI = 100;
	const WORDPRESS = 101;
	const DRUPAL = 102;
	const DEKIWIKI = 103;
	const MOODLE = 104;
	const COMMUNITY_EDITION = 105;
	const JOOMLA = 106;
	const BLACKBOARD = 107;
	const SAKAI = 108;
	const ADMIN_CONSOLE = 109;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaPermissionStatus
{
	const ACTIVE = 1;
	const BLOCKED = 2;
	const DELETED = 3;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaPermissionType
{
	const NORMAL = 1;
	const SPECIAL_FEATURE = 2;
	const PLUGIN = 3;
	const PARTNER_GROUP = 4;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaPlaylistType
{
	const DYNAMIC = 10;
	const STATIC_LIST = 3;
	const EXTERNAL = 101;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaPrivacyType
{
	const ALL = 1;
	const AUTHENTICATED_USERS = 2;
	const MEMBERS_ONLY = 3;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaSchedulerStatusType
{
	const RUNNING_BATCHES_COUNT = 1;
	const RUNNING_BATCHES_CPU = 2;
	const RUNNING_BATCHES_MEMORY = 3;
	const RUNNING_BATCHES_NETWORK = 4;
	const RUNNING_BATCHES_DISC_IO = 5;
	const RUNNING_BATCHES_DISC_SPACE = 6;
	const RUNNING_BATCHES_IS_RUNNING = 7;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaSearchOperatorType
{
	const SEARCH_AND = 1;
	const SEARCH_OR = 2;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaSearchProviderType
{
	const FLICKR = 3;
	const YOUTUBE = 4;
	const MYSPACE = 7;
	const PHOTOBUCKET = 8;
	const JAMENDO = 9;
	const CCMIXTER = 10;
	const NYPL = 11;
	const CURRENT = 12;
	const MEDIA_COMMONS = 13;
	const KALTURA = 20;
	const KALTURA_USER_CLIPS = 21;
	const ARCHIVE_ORG = 22;
	const KALTURA_PARTNER = 23;
	const METACAFE = 24;
	const SEARCH_PROXY = 28;
	const PARTNER_SPECIFIC = 100;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaSessionType
{
	const USER = 0;
	const ADMIN = 2;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaSiteRestrictionType
{
	const RESTRICT_SITE_LIST = 0;
	const ALLOW_SITE_LIST = 1;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaStorageProfileDeliveryStatus
{
	const ACTIVE = 1;
	const BLOCKED = 2;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaStorageProfileProtocol
{
	const KALTURA_DC = 0;
	const FTP = 1;
	const SCP = 2;
	const SFTP = 3;
	const S3 = 6;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaStorageProfileReadyBehavior
{
	const NO_IMPACT = 0;
	const REQUIRED = 1;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaStorageProfileStatus
{
	const DISABLED = 1;
	const AUTOMATIC = 2;
	const MANUAL = 3;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaThumbAssetStatus
{
	const CAPTURING = 1;
	const ERROR = -1;
	const QUEUED = 0;
	const READY = 2;
	const DELETED = 3;
	const IMPORTING = 7;
	const EXPORTING = 9;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaThumbCropType
{
	const RESIZE = 1;
	const RESIZE_WITH_PADDING = 2;
	const CROP = 3;
	const CROP_FROM_TOP = 4;
	const RESIZE_WITH_FORCE = 5;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaUiConfCreationMode
{
	const WIZARD = 2;
	const ADVANCED = 3;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaUiConfObjType
{
	const PLAYER = 1;
	const CONTRIBUTION_WIZARD = 2;
	const SIMPLE_EDITOR = 3;
	const ADVANCED_EDITOR = 4;
	const PLAYLIST = 5;
	const APP_STUDIO = 6;
	const KRECORD = 7;
	const PLAYER_V3 = 8;
	const KMC_ACCOUNT = 9;
	const KMC_ANALYTICS = 10;
	const KMC_CONTENT = 11;
	const KMC_DASHBOARD = 12;
	const KMC_LOGIN = 13;
	const PLAYER_SL = 14;
	const CLIENTSIDE_ENCODER = 15;
	const KMC_GENERAL = 16;
	const KMC_ROLES_AND_PERMISSIONS = 17;
	const CLIPPER = 18;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaUpdateMethodType
{
	const MANUAL = 0;
	const AUTOMATIC = 1;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaUploadTokenStatus
{
	const PENDING = 0;
	const PARTIAL_UPLOAD = 1;
	const FULL_UPLOAD = 2;
	const CLOSED = 3;
	const TIMED_OUT = 4;
	const DELETED = 5;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaUserAgentRestrictionType
{
	const RESTRICT_LIST = 0;
	const ALLOW_LIST = 1;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaUserJoinPolicyType
{
	const AUTO_JOIN = 1;
	const REQUEST_TO_JOIN = 2;
	const NOT_ALLOWED = 3;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaUserRoleStatus
{
	const ACTIVE = 1;
	const BLOCKED = 2;
	const DELETED = 3;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaUserStatus
{
	const BLOCKED = 0;
	const ACTIVE = 1;
	const DELETED = 2;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAccessControlActionType
{
	const BLOCK = "1";
	const PREVIEW = "2";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAccessControlContextType
{
	const PLAY = "1";
	const DOWNLOAD = "2";
	const THUMBNAIL = "3";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAccessControlOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAccessControlProfileOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAdminUserOrderBy
{
	const ID_ASC = "+id";
	const ID_DESC = "-id";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaApiActionPermissionItemOrderBy
{
	const ID_ASC = "+id";
	const ID_DESC = "-id";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaApiParameterPermissionItemOrderBy
{
	const ID_ASC = "+id";
	const ID_DESC = "-id";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAssetOrderBy
{
	const SIZE_ASC = "+size";
	const SIZE_DESC = "-size";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
	const DELETED_AT_ASC = "+deletedAt";
	const DELETED_AT_DESC = "-deletedAt";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAssetParamsOrderBy
{
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAssetParamsOutputOrderBy
{
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAssetType
{
	const FLAVOR = "1";
	const THUMBNAIL = "2";
	const DOCUMENT = "document.Document";
	const SWF = "document.SWF";
	const PDF = "document.PDF";
	const IMAGE = "document.Image";
	const CAPTION = "caption.Caption";
	const ATTACHMENT = "attachment.Attachment";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAudioCodec
{
	const NONE = "";
	const MP3 = "mp3";
	const AAC = "aac";
	const VORBIS = "vorbis";
	const WMA = "wma";
	const WMAPRO = "wmapro";
	const AMRNB = "amrnb";
	const MPEG2 = "mpeg2";
	const AC3 = "ac3";
	const COPY = "copy";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBaseEntryOrderBy
{
	const NAME_ASC = "+name";
	const NAME_DESC = "-name";
	const MODERATION_COUNT_ASC = "+moderationCount";
	const MODERATION_COUNT_DESC = "-moderationCount";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
	const RANK_ASC = "+rank";
	const RANK_DESC = "-rank";
	const TOTAL_RANK_ASC = "+totalRank";
	const TOTAL_RANK_DESC = "-totalRank";
	const START_DATE_ASC = "+startDate";
	const START_DATE_DESC = "-startDate";
	const END_DATE_ASC = "+endDate";
	const END_DATE_DESC = "-endDate";
	const PARTNER_SORT_VALUE_ASC = "+partnerSortValue";
	const PARTNER_SORT_VALUE_DESC = "-partnerSortValue";
	const RECENT_ASC = "+recent";
	const RECENT_DESC = "-recent";
	const WEIGHT_ASC = "+weight";
	const WEIGHT_DESC = "-weight";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBaseJobOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
	const PROCESSOR_EXPIRATION_ASC = "+processorExpiration";
	const PROCESSOR_EXPIRATION_DESC = "-processorExpiration";
	const EXECUTION_ATTEMPTS_ASC = "+executionAttempts";
	const EXECUTION_ATTEMPTS_DESC = "-executionAttempts";
	const LOCK_VERSION_ASC = "+lockVersion";
	const LOCK_VERSION_DESC = "-lockVersion";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBaseSyndicationFeedOrderBy
{
	const PLAYLIST_ID_ASC = "+playlistId";
	const PLAYLIST_ID_DESC = "-playlistId";
	const NAME_ASC = "+name";
	const NAME_DESC = "-name";
	const TYPE_ASC = "+type";
	const TYPE_DESC = "-type";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBatchJobOrderBy
{
	const STATUS_ASC = "+status";
	const STATUS_DESC = "-status";
	const CHECK_AGAIN_TIMEOUT_ASC = "+checkAgainTimeout";
	const CHECK_AGAIN_TIMEOUT_DESC = "-checkAgainTimeout";
	const PROGRESS_ASC = "+progress";
	const PROGRESS_DESC = "-progress";
	const UPDATES_COUNT_ASC = "+updatesCount";
	const UPDATES_COUNT_DESC = "-updatesCount";
	const PRIORITY_ASC = "+priority";
	const PRIORITY_DESC = "-priority";
	const QUEUE_TIME_ASC = "+queueTime";
	const QUEUE_TIME_DESC = "-queueTime";
	const FINISH_TIME_ASC = "+finishTime";
	const FINISH_TIME_DESC = "-finishTime";
	const FILE_SIZE_ASC = "+fileSize";
	const FILE_SIZE_DESC = "-fileSize";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
	const PROCESSOR_EXPIRATION_ASC = "+processorExpiration";
	const PROCESSOR_EXPIRATION_DESC = "-processorExpiration";
	const EXECUTION_ATTEMPTS_ASC = "+executionAttempts";
	const EXECUTION_ATTEMPTS_DESC = "-executionAttempts";
	const LOCK_VERSION_ASC = "+lockVersion";
	const LOCK_VERSION_DESC = "-lockVersion";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBatchJobType
{
	const CONVERT = "0";
	const IMPORT = "1";
	const DELETE = "2";
	const FLATTEN = "3";
	const BULKUPLOAD = "4";
	const DVDCREATOR = "5";
	const DOWNLOAD = "6";
	const OOCONVERT = "7";
	const CONVERT_PROFILE = "10";
	const POSTCONVERT = "11";
	const EXTRACT_MEDIA = "14";
	const MAIL = "15";
	const NOTIFICATION = "16";
	const CLEANUP = "17";
	const SCHEDULER_HELPER = "18";
	const BULKDOWNLOAD = "19";
	const DB_CLEANUP = "20";
	const PROVISION_PROVIDE = "21";
	const CONVERT_COLLECTION = "22";
	const STORAGE_EXPORT = "23";
	const PROVISION_DELETE = "24";
	const STORAGE_DELETE = "25";
	const EMAIL_INGESTION = "26";
	const METADATA_IMPORT = "27";
	const METADATA_TRANSFORM = "28";
	const FILESYNC_IMPORT = "29";
	const CAPTURE_THUMB = "30";
	const DELETE_FILE = "31";
	const INDEX = "32";
	const MOVE_CATEGORY_ENTRIES = "33";
	const COPY = "34";
	const VIRUS_SCAN = "virusScan.VirusScan";
	const DISTRIBUTION_SUBMIT = "contentDistribution.DistributionSubmit";
	const DISTRIBUTION_UPDATE = "contentDistribution.DistributionUpdate";
	const DISTRIBUTION_DELETE = "contentDistribution.DistributionDelete";
	const DISTRIBUTION_FETCH_REPORT = "contentDistribution.DistributionFetchReport";
	const DISTRIBUTION_ENABLE = "contentDistribution.DistributionEnable";
	const DISTRIBUTION_DISABLE = "contentDistribution.DistributionDisable";
	const DISTRIBUTION_SYNC = "contentDistribution.DistributionSync";
	const PARSE_CAPTION_ASSET = "captionSearch.parseCaptionAsset";
	const DROP_FOLDER_WATCHER = "dropFolder.DropFolderWatcher";
	const DROP_FOLDER_HANDLER = "dropFolder.DropFolderHandler";
	const EVENT_NOTIFICATION_HANDLER = "eventNotification.EventNotificationHandler";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBulkUploadAction
{
	const ADD = "1";
	const UPDATE = "2";
	const DELETE = "3";
	const REPLACE = "4";
	const TRANSFORM_XSLT = "5";
	const ADD_OR_UPDATE = "6";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBulkUploadObjectType
{
	const ENTRY = "1";
	const CATEGORY = "2";
	const USER = "3";
	const CATEGORY_USER = "4";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBulkUploadOrderBy
{
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBulkUploadResultObjectType
{
	const ENTRY = "1";
	const CATEGORY = "2";
	const USER = "3";
	const CATEGORY_USER = "4";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBulkUploadResultStatus
{
	const ERROR = "1";
	const OK = "2";
	const IN_PROGRESS = "3";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaBulkUploadType
{
	const CSV = "bulkUploadCsv.CSV";
	const XML = "bulkUploadXml.XML";
	const DROP_FOLDER_XML = "dropFolderXmlBulkUpload.DROP_FOLDER_XML";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaCategoryEntryOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaCategoryOrderBy
{
	const DEPTH_ASC = "+depth";
	const DEPTH_DESC = "-depth";
	const NAME_ASC = "+name";
	const NAME_DESC = "-name";
	const FULL_NAME_ASC = "+fullName";
	const FULL_NAME_DESC = "-fullName";
	const ENTRIES_COUNT_ASC = "+entriesCount";
	const ENTRIES_COUNT_DESC = "-entriesCount";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
	const DIRECT_ENTRIES_COUNT_ASC = "+directEntriesCount";
	const DIRECT_ENTRIES_COUNT_DESC = "-directEntriesCount";
	const MEMBERS_COUNT_ASC = "+membersCount";
	const MEMBERS_COUNT_DESC = "-membersCount";
	const PARTNER_SORT_VALUE_ASC = "+partnerSortValue";
	const PARTNER_SORT_VALUE_DESC = "-partnerSortValue";
	const DIRECT_SUB_CATEGORIES_COUNT_ASC = "+directSubCategoriesCount";
	const DIRECT_SUB_CATEGORIES_COUNT_DESC = "-directSubCategoriesCount";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaCategoryUserOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaContainerFormat
{
	const FLV = "flv";
	const MP4 = "mp4";
	const AVI = "avi";
	const MOV = "mov";
	const MP3 = "mp3";
	const _3GP = "3gp";
	const OGG = "ogg";
	const WMV = "wmv";
	const WMA = "wma";
	const ISMV = "ismv";
	const MKV = "mkv";
	const WEBM = "webm";
	const MPEG = "mpeg";
	const MPEGTS = "mpegts";
	const APPLEHTTP = "applehttp";
	const SWF = "swf";
	const PDF = "pdf";
	const BMP = "bmp";
	const PNG = "png";
	const JPG = "jpg";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaControlPanelCommandOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaConversionEngineType
{
	const KALTURA_COM = "0";
	const ON2 = "1";
	const FFMPEG = "2";
	const MENCODER = "3";
	const ENCODING_COM = "4";
	const EXPRESSION_ENCODER3 = "5";
	const FFMPEG_VP8 = "98";
	const FFMPEG_AUX = "99";
	const PDF2SWF = "201";
	const PDF_CREATOR = "202";
	const IMAGEMAGICK = "document.ImageMagick";
	const QUICK_TIME_PLAYER_TOOLS = "quickTimeTools.QuickTimeTools";
	const FAST_START = "fastStart.FastStart";
	const EXPRESSION_ENCODER = "expressionEncoder.ExpressionEncoder";
	const AVIDEMUX = "avidemux.Avidemux";
	const SEGMENTER = "segmenter.Segmenter";
	const INLET_ARMADA = "inletArmada.InletArmada";
	const VLC = "vlc.Vlc";
	const MP4BOX = "mp4box.Mp4box";
	const INLET_ARMADA_ABC = "inletArmadaAbc.InletArmadaAbc";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaConversionProfileAssetParamsOrderBy
{
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaConversionProfileOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaConversionProfileStatus
{
	const DISABLED = "1";
	const ENABLED = "2";
	const DELETED = "3";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDataEntryOrderBy
{
	const NAME_ASC = "+name";
	const NAME_DESC = "-name";
	const MODERATION_COUNT_ASC = "+moderationCount";
	const MODERATION_COUNT_DESC = "-moderationCount";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
	const RANK_ASC = "+rank";
	const RANK_DESC = "-rank";
	const TOTAL_RANK_ASC = "+totalRank";
	const TOTAL_RANK_DESC = "-totalRank";
	const START_DATE_ASC = "+startDate";
	const START_DATE_DESC = "-startDate";
	const END_DATE_ASC = "+endDate";
	const END_DATE_DESC = "-endDate";
	const PARTNER_SORT_VALUE_ASC = "+partnerSortValue";
	const PARTNER_SORT_VALUE_DESC = "-partnerSortValue";
	const RECENT_ASC = "+recent";
	const RECENT_DESC = "-recent";
	const WEIGHT_ASC = "+weight";
	const WEIGHT_DESC = "-weight";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDurationType
{
	const NOT_AVAILABLE = "notavailable";
	const SHORT = "short";
	const MEDIUM = "medium";
	const LONG = "long";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDynamicEnum
{
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaEntryReplacementStatus
{
	const NONE = "0";
	const APPROVED_BUT_NOT_READY = "1";
	const READY_BUT_NOT_APPROVED = "2";
	const NOT_READY_AND_NOT_APPROVED = "3";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaEntryStatus
{
	const ERROR_IMPORTING = "-2";
	const ERROR_CONVERTING = "-1";
	const IMPORT = "0";
	const PRECONVERT = "1";
	const READY = "2";
	const DELETED = "3";
	const PENDING = "4";
	const MODERATE = "5";
	const BLOCKED = "6";
	const NO_CONTENT = "7";
	const INFECTED = "virusScan.Infected";
	const SCAN_FAILURE = "virusScan.ScanFailure";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaEntryType
{
	const AUTOMATIC = "-1";
	const MEDIA_CLIP = "1";
	const MIX = "2";
	const PLAYLIST = "5";
	const DATA = "6";
	const LIVE_STREAM = "7";
	const DOCUMENT = "10";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaFileSyncObjectType
{
	const ENTRY = "1";
	const UICONF = "2";
	const BATCHJOB = "3";
	const ASSET = "4";
	const METADATA = "5";
	const METADATA_PROFILE = "6";
	const SYNDICATION_FEED = "7";
	const CONVERSION_PROFILE = "8";
	const FLAVOR_ASSET = "4";
	const GENERIC_DISTRIBUTION_ACTION = "contentDistribution.GenericDistributionAction";
	const ENTRY_DISTRIBUTION = "contentDistribution.EntryDistribution";
	const DISTRIBUTION_PROFILE = "contentDistribution.DistributionProfile";
	const EMAIL_NOTIFICATION_TEMPLATE = "emailNotification.EmailNotificationTemplate";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaFlavorAssetOrderBy
{
	const SIZE_ASC = "+size";
	const SIZE_DESC = "-size";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
	const DELETED_AT_ASC = "+deletedAt";
	const DELETED_AT_DESC = "-deletedAt";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaFlavorParamsOrderBy
{
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaFlavorParamsOutputOrderBy
{
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaGenericSyndicationFeedOrderBy
{
	const PLAYLIST_ID_ASC = "+playlistId";
	const PLAYLIST_ID_DESC = "-playlistId";
	const NAME_ASC = "+name";
	const NAME_DESC = "-name";
	const TYPE_ASC = "+type";
	const TYPE_DESC = "-type";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaGenericXsltSyndicationFeedOrderBy
{
	const PLAYLIST_ID_ASC = "+playlistId";
	const PLAYLIST_ID_DESC = "-playlistId";
	const NAME_ASC = "+name";
	const NAME_DESC = "-name";
	const TYPE_ASC = "+type";
	const TYPE_DESC = "-type";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaGeoCoderType
{
	const KALTURA = "1";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaGoogleVideoSyndicationFeedOrderBy
{
	const PLAYLIST_ID_ASC = "+playlistId";
	const PLAYLIST_ID_DESC = "-playlistId";
	const NAME_ASC = "+name";
	const NAME_DESC = "-name";
	const TYPE_ASC = "+type";
	const TYPE_DESC = "-type";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaITunesSyndicationFeedOrderBy
{
	const PLAYLIST_ID_ASC = "+playlistId";
	const PLAYLIST_ID_DESC = "-playlistId";
	const NAME_ASC = "+name";
	const NAME_DESC = "-name";
	const TYPE_ASC = "+type";
	const TYPE_DESC = "-type";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaLanguage
{
	const AB = "Abkhazian";
	const AA = "Afar";
	const AF = "Afrikaans";
	const SQ = "Albanian";
	const AM = "Amharic";
	const AR = "Arabic";
	const HY = "Armenian";
	const AS_ = "Assamese";
	const AY = "Aymara";
	const AZ = "Azerbaijani";
	const BA = "Bashkir";
	const EU = "Basque";
	const BN = "Bengali (Bangla)";
	const DZ = "Bhutani";
	const BH = "Bihari";
	const BI = "Bislama";
	const BR = "Breton";
	const BG = "Bulgarian";
	const MY = "Burmese";
	const BE = "Byelorussian (Belarusian)";
	const KM = "Cambodian";
	const CA = "Catalan";
	const ZH = "Chinese";
	const CO = "Corsican";
	const HR = "Croatian";
	const CS = "Czech";
	const DA = "Danish";
	const NL = "Dutch";
	const EN = "English";
	const EO = "Esperanto";
	const ET = "Estonian";
	const FO = "Faeroese";
	const FA = "Farsi";
	const FJ = "Fiji";
	const FI = "Finnish";
	const FR = "French";
	const FY = "Frisian";
	const GL = "Galician";
	const GD = "Gaelic (Scottish)";
	const GV = "Gaelic (Manx)";
	const KA = "Georgian";
	const DE = "German";
	const EL = "Greek";
	const KL = "Greenlandic";
	const GN = "Guarani";
	const GU = "Gujarati";
	const HA = "Hausa";
	const HE = "Hebrew";
	const IW = "Hebrew";
	const HI = "Hindi";
	const HU = "Hungarian";
	const IS = "Icelandic";
	const ID = "Indonesian";
	const IN = "Indonesian";
	const IA = "Interlingua";
	const IE = "Interlingue";
	const IU = "Inuktitut";
	const IK = "Inupiak";
	const GA = "Irish";
	const IT = "Italian";
	const JA = "Japanese";
	const JV = "Javanese";
	const KN = "Kannada";
	const KS = "Kashmiri";
	const KK = "Kazakh";
	const RW = "Kinyarwanda (Ruanda)";
	const KY = "Kirghiz";
	const RN = "Kirundi (Rundi)";
	const KO = "Korean";
	const KU = "Kurdish";
	const LO = "Laothian";
	const LA = "Latin";
	const LV = "Latvian (Lettish)";
	const LI = "Limburgish ( Limburger)";
	const LN = "Lingala";
	const LT = "Lithuanian";
	const MK = "Macedonian";
	const MG = "Malagasy";
	const MS = "Malay";
	const ML = "Malayalam";
	const MT = "Maltese";
	const MI = "Maori";
	const MR = "Marathi";
	const MO = "Moldavian";
	const MN = "Mongolian";
	const NA = "Nauru";
	const NE = "Nepali";
	const NO = "Norwegian";
	const OC = "Occitan";
	const OR_ = "Oriya";
	const OM = "Oromo (Afan, Galla)";
	const PS = "Pashto (Pushto)";
	const PL = "Polish";
	const PT = "Portuguese";
	const PA = "Punjabi";
	const QU = "Quechua";
	const RM = "Rhaeto-Romance";
	const RO = "Romanian";
	const RU = "Russian";
	const SM = "Samoan";
	const SG = "Sangro";
	const SA = "Sanskrit";
	const SR = "Serbian";
	const SH = "Serbo-Croatian";
	const ST = "Sesotho";
	const TN = "Setswana";
	const SN = "Shona";
	const SD = "Sindhi";
	const SI = "Sinhalese";
	const SS = "Siswati";
	const SK = "Slovak";
	const SL = "Slovenian";
	const SO = "Somali";
	const ES = "Spanish";
	const SU = "Sundanese";
	const SW = "Swahili (Kiswahili)";
	const SV = "Swedish";
	const TL = "Tagalog";
	const TG = "Tajik";
	const TA = "Tamil";
	const TT = "Tatar";
	const TE = "Telugu";
	const TH = "Thai";
	const BO = "Tibetan";
	const TI = "Tigrinya";
	const TO = "Tonga";
	const TS = "Tsonga";
	const TR = "Turkish";
	const TK = "Turkmen";
	const TW = "Twi";
	const UG = "Uighur";
	const UK = "Ukrainian";
	const UR = "Urdu";
	const UZ = "Uzbek";
	const VI = "Vietnamese";
	const VO = "Volapuk";
	const CY = "Welsh";
	const WO = "Wolof";
	const XH = "Xhosa";
	const YI = "Yiddish";
	const JI = "Yiddish";
	const YO = "Yoruba";
	const ZU = "Zulu";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaLanguageCode
{
	const AB = "ab";
	const AA = "aa";
	const AF = "af";
	const SQ = "sq";
	const AM = "am";
	const AR = "ar";
	const HY = "hy";
	const AS_ = "as";
	const AY = "ay";
	const AZ = "az";
	const BA = "ba";
	const EU = "eu";
	const BN = "bn";
	const DZ = "dz";
	const BH = "bh";
	const BI = "bi";
	const BR = "br";
	const BG = "bg";
	const MY = "my";
	const BE = "be";
	const KM = "km";
	const CA = "ca";
	const ZH = "zh";
	const CO = "co";
	const HR = "hr";
	const CS = "cs";
	const DA = "da";
	const NL = "nl";
	const EN = "en";
	const EO = "eo";
	const ET = "et";
	const FO = "fo";
	const FA = "fa";
	const FJ = "fj";
	const FI = "fi";
	const FR = "fr";
	const FY = "fy";
	const GL = "gl";
	const GD = "gd";
	const GV = "gv";
	const KA = "ka";
	const DE = "de";
	const EL = "el";
	const KL = "kl";
	const GN = "gn";
	const GU = "gu";
	const HA = "ha";
	const HE = "he";
	const IW = "iw";
	const HI = "hi";
	const HU = "hu";
	const IS = "is";
	const ID = "id";
	const IN = "in";
	const IA = "ia";
	const IE = "ie";
	const IU = "iu";
	const IK = "ik";
	const GA = "ga";
	const IT = "it";
	const JA = "ja";
	const JV = "jv";
	const KN = "kn";
	const KS = "ks";
	const KK = "kk";
	const RW = "rw";
	const KY = "ky";
	const RN = "rn";
	const KO = "ko";
	const KU = "ku";
	const LO = "lo";
	const LA = "la";
	const LV = "lv";
	const LI = "li";
	const LN = "ln";
	const LT = "lt";
	const MK = "mk";
	const MG = "mg";
	const MS = "ms";
	const ML = "ml";
	const MT = "mt";
	const MI = "mi";
	const MR = "mr";
	const MO = "mo";
	const MN = "mn";
	const NA = "na";
	const NE = "ne";
	const NO = "no";
	const OC = "oc";
	const OR_ = "or";
	const OM = "om";
	const PS = "ps";
	const PL = "pl";
	const PT = "pt";
	const PA = "pa";
	const QU = "qu";
	const RM = "rm";
	const RO = "ro";
	const RU = "ru";
	const SM = "sm";
	const SG = "sg";
	const SA = "sa";
	const SR = "sr";
	const SH = "sh";
	const ST = "st";
	const TN = "tn";
	const SN = "sn";
	const SD = "sd";
	const SI = "si";
	const SS = "ss";
	const SK = "sk";
	const SL = "sl";
	const SO = "so";
	const ES = "es";
	const SU = "su";
	const SW = "sw";
	const SV = "sv";
	const TL = "tl";
	const TG = "tg";
	const TA = "ta";
	const TT = "tt";
	const TE = "te";
	const TH = "th";
	const BO = "bo";
	const TI = "ti";
	const TO = "to";
	const TS = "ts";
	const TR = "tr";
	const TK = "tk";
	const TW = "tw";
	const UG = "ug";
	const UK = "uk";
	const UR = "ur";
	const UZ = "uz";
	const VI = "vi";
	const VO = "vo";
	const CY = "cy";
	const WO = "wo";
	const XH = "xh";
	const YI = "yi";
	const JI = "ji";
	const YO = "yo";
	const ZU = "zu";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaLiveStreamAdminEntryOrderBy
{
	const MEDIA_TYPE_ASC = "+mediaType";
	const MEDIA_TYPE_DESC = "-mediaType";
	const PLAYS_ASC = "+plays";
	const PLAYS_DESC = "-plays";
	const VIEWS_ASC = "+views";
	const VIEWS_DESC = "-views";
	const DURATION_ASC = "+duration";
	const DURATION_DESC = "-duration";
	const MS_DURATION_ASC = "+msDuration";
	const MS_DURATION_DESC = "-msDuration";
	const NAME_ASC = "+name";
	const NAME_DESC = "-name";
	const MODERATION_COUNT_ASC = "+moderationCount";
	const MODERATION_COUNT_DESC = "-moderationCount";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
	const RANK_ASC = "+rank";
	const RANK_DESC = "-rank";
	const TOTAL_RANK_ASC = "+totalRank";
	const TOTAL_RANK_DESC = "-totalRank";
	const START_DATE_ASC = "+startDate";
	const START_DATE_DESC = "-startDate";
	const END_DATE_ASC = "+endDate";
	const END_DATE_DESC = "-endDate";
	const PARTNER_SORT_VALUE_ASC = "+partnerSortValue";
	const PARTNER_SORT_VALUE_DESC = "-partnerSortValue";
	const RECENT_ASC = "+recent";
	const RECENT_DESC = "-recent";
	const WEIGHT_ASC = "+weight";
	const WEIGHT_DESC = "-weight";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaLiveStreamEntryOrderBy
{
	const MEDIA_TYPE_ASC = "+mediaType";
	const MEDIA_TYPE_DESC = "-mediaType";
	const PLAYS_ASC = "+plays";
	const PLAYS_DESC = "-plays";
	const VIEWS_ASC = "+views";
	const VIEWS_DESC = "-views";
	const DURATION_ASC = "+duration";
	const DURATION_DESC = "-duration";
	const MS_DURATION_ASC = "+msDuration";
	const MS_DURATION_DESC = "-msDuration";
	const NAME_ASC = "+name";
	const NAME_DESC = "-name";
	const MODERATION_COUNT_ASC = "+moderationCount";
	const MODERATION_COUNT_DESC = "-moderationCount";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
	const RANK_ASC = "+rank";
	const RANK_DESC = "-rank";
	const TOTAL_RANK_ASC = "+totalRank";
	const TOTAL_RANK_DESC = "-totalRank";
	const START_DATE_ASC = "+startDate";
	const START_DATE_DESC = "-startDate";
	const END_DATE_ASC = "+endDate";
	const END_DATE_DESC = "-endDate";
	const PARTNER_SORT_VALUE_ASC = "+partnerSortValue";
	const PARTNER_SORT_VALUE_DESC = "-partnerSortValue";
	const RECENT_ASC = "+recent";
	const RECENT_DESC = "-recent";
	const WEIGHT_ASC = "+weight";
	const WEIGHT_DESC = "-weight";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaMailJobOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
	const PROCESSOR_EXPIRATION_ASC = "+processorExpiration";
	const PROCESSOR_EXPIRATION_DESC = "-processorExpiration";
	const EXECUTION_ATTEMPTS_ASC = "+executionAttempts";
	const EXECUTION_ATTEMPTS_DESC = "-executionAttempts";
	const LOCK_VERSION_ASC = "+lockVersion";
	const LOCK_VERSION_DESC = "-lockVersion";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaMailType
{
	const MAIL_TYPE_KALTURA_NEWSLETTER = "10";
	const MAIL_TYPE_ADDED_TO_FAVORITES = "11";
	const MAIL_TYPE_ADDED_TO_CLIP_FAVORITES = "12";
	const MAIL_TYPE_NEW_COMMENT_IN_PROFILE = "13";
	const MAIL_TYPE_CLIP_ADDED_YOUR_KALTURA = "20";
	const MAIL_TYPE_VIDEO_ADDED = "21";
	const MAIL_TYPE_ROUGHCUT_CREATED = "22";
	const MAIL_TYPE_ADDED_KALTURA_TO_YOUR_FAVORITES = "23";
	const MAIL_TYPE_NEW_COMMENT_IN_KALTURA = "24";
	const MAIL_TYPE_CLIP_ADDED = "30";
	const MAIL_TYPE_VIDEO_CREATED = "31";
	const MAIL_TYPE_ADDED_KALTURA_TO_HIS_FAVORITES = "32";
	const MAIL_TYPE_NEW_COMMENT_IN_KALTURA_YOU_CONTRIBUTED = "33";
	const MAIL_TYPE_CLIP_CONTRIBUTED = "40";
	const MAIL_TYPE_ROUGHCUT_CREATED_SUBSCRIBED = "41";
	const MAIL_TYPE_ADDED_KALTURA_TO_HIS_FAVORITES_SUBSCRIBED = "42";
	const MAIL_TYPE_NEW_COMMENT_IN_KALTURA_YOU_SUBSCRIBED = "43";
	const MAIL_TYPE_REGISTER_CONFIRM = "50";
	const MAIL_TYPE_PASSWORD_RESET = "51";
	const MAIL_TYPE_LOGIN_MAIL_RESET = "52";
	const MAIL_TYPE_REGISTER_CONFIRM_VIDEO_SERVICE = "54";
	const MAIL_TYPE_VIDEO_READY = "60";
	const MAIL_TYPE_VIDEO_IS_READY = "62";
	const MAIL_TYPE_BULK_DOWNLOAD_READY = "63";
	const MAIL_TYPE_BULKUPLOAD_FINISHED = "64";
	const MAIL_TYPE_BULKUPLOAD_FAILED = "65";
	const MAIL_TYPE_BULKUPLOAD_ABORTED = "66";
	const MAIL_TYPE_NOTIFY_ERR = "70";
	const MAIL_TYPE_ACCOUNT_UPGRADE_CONFIRM = "80";
	const MAIL_TYPE_VIDEO_SERVICE_NOTICE = "81";
	const MAIL_TYPE_VIDEO_SERVICE_NOTICE_LIMIT_REACHED = "82";
	const MAIL_TYPE_VIDEO_SERVICE_NOTICE_ACCOUNT_LOCKED = "83";
	const MAIL_TYPE_VIDEO_SERVICE_NOTICE_ACCOUNT_DELETED = "84";
	const MAIL_TYPE_VIDEO_SERVICE_NOTICE_UPGRADE_OFFER = "85";
	const MAIL_TYPE_ACCOUNT_REACTIVE_CONFIRM = "86";
	const MAIL_TYPE_SYSTEM_USER_RESET_PASSWORD = "110";
	const MAIL_TYPE_SYSTEM_USER_RESET_PASSWORD_SUCCESS = "111";
	const MAIL_TYPE_SYSTEM_USER_NEW_PASSWORD = "112";
	const MAIL_TYPE_SYSTEM_USER_CREDENTIALS_SAVED = "113";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaMediaEntryOrderBy
{
	const MEDIA_TYPE_ASC = "+mediaType";
	const MEDIA_TYPE_DESC = "-mediaType";
	const PLAYS_ASC = "+plays";
	const PLAYS_DESC = "-plays";
	const VIEWS_ASC = "+views";
	const VIEWS_DESC = "-views";
	const DURATION_ASC = "+duration";
	const DURATION_DESC = "-duration";
	const MS_DURATION_ASC = "+msDuration";
	const MS_DURATION_DESC = "-msDuration";
	const NAME_ASC = "+name";
	const NAME_DESC = "-name";
	const MODERATION_COUNT_ASC = "+moderationCount";
	const MODERATION_COUNT_DESC = "-moderationCount";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
	const RANK_ASC = "+rank";
	const RANK_DESC = "-rank";
	const TOTAL_RANK_ASC = "+totalRank";
	const TOTAL_RANK_DESC = "-totalRank";
	const START_DATE_ASC = "+startDate";
	const START_DATE_DESC = "-startDate";
	const END_DATE_ASC = "+endDate";
	const END_DATE_DESC = "-endDate";
	const PARTNER_SORT_VALUE_ASC = "+partnerSortValue";
	const PARTNER_SORT_VALUE_DESC = "-partnerSortValue";
	const RECENT_ASC = "+recent";
	const RECENT_DESC = "-recent";
	const WEIGHT_ASC = "+weight";
	const WEIGHT_DESC = "-weight";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaMediaFlavorParamsOrderBy
{
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaMediaFlavorParamsOutputOrderBy
{
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaMediaInfoOrderBy
{
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaMediaParserType
{
	const MEDIAINFO = "0";
	const FFMPEG = "1";
	const REMOTE_MEDIAINFO = "remoteMediaInfo.RemoteMediaInfo";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaMixEntryOrderBy
{
	const PLAYS_ASC = "+plays";
	const PLAYS_DESC = "-plays";
	const VIEWS_ASC = "+views";
	const VIEWS_DESC = "-views";
	const DURATION_ASC = "+duration";
	const DURATION_DESC = "-duration";
	const MS_DURATION_ASC = "+msDuration";
	const MS_DURATION_DESC = "-msDuration";
	const NAME_ASC = "+name";
	const NAME_DESC = "-name";
	const MODERATION_COUNT_ASC = "+moderationCount";
	const MODERATION_COUNT_DESC = "-moderationCount";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
	const RANK_ASC = "+rank";
	const RANK_DESC = "-rank";
	const TOTAL_RANK_ASC = "+totalRank";
	const TOTAL_RANK_DESC = "-totalRank";
	const START_DATE_ASC = "+startDate";
	const START_DATE_DESC = "-startDate";
	const END_DATE_ASC = "+endDate";
	const END_DATE_DESC = "-endDate";
	const PARTNER_SORT_VALUE_ASC = "+partnerSortValue";
	const PARTNER_SORT_VALUE_DESC = "-partnerSortValue";
	const RECENT_ASC = "+recent";
	const RECENT_DESC = "-recent";
	const WEIGHT_ASC = "+weight";
	const WEIGHT_DESC = "-weight";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaNotificationOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
	const PROCESSOR_EXPIRATION_ASC = "+processorExpiration";
	const PROCESSOR_EXPIRATION_DESC = "-processorExpiration";
	const EXECUTION_ATTEMPTS_ASC = "+executionAttempts";
	const EXECUTION_ATTEMPTS_DESC = "-executionAttempts";
	const LOCK_VERSION_ASC = "+lockVersion";
	const LOCK_VERSION_DESC = "-lockVersion";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaPartnerOrderBy
{
	const ID_ASC = "+id";
	const ID_DESC = "-id";
	const NAME_ASC = "+name";
	const NAME_DESC = "-name";
	const WEBSITE_ASC = "+website";
	const WEBSITE_DESC = "-website";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const ADMIN_NAME_ASC = "+adminName";
	const ADMIN_NAME_DESC = "-adminName";
	const ADMIN_EMAIL_ASC = "+adminEmail";
	const ADMIN_EMAIL_DESC = "-adminEmail";
	const STATUS_ASC = "+status";
	const STATUS_DESC = "-status";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaPermissionItemOrderBy
{
	const ID_ASC = "+id";
	const ID_DESC = "-id";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaPermissionItemType
{
	const API_ACTION_ITEM = "kApiActionPermissionItem";
	const API_PARAMETER_ITEM = "kApiParameterPermissionItem";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaPermissionOrderBy
{
	const ID_ASC = "+id";
	const ID_DESC = "-id";
	const NAME_ASC = "+name";
	const NAME_DESC = "-name";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaPlayableEntryOrderBy
{
	const PLAYS_ASC = "+plays";
	const PLAYS_DESC = "-plays";
	const VIEWS_ASC = "+views";
	const VIEWS_DESC = "-views";
	const DURATION_ASC = "+duration";
	const DURATION_DESC = "-duration";
	const MS_DURATION_ASC = "+msDuration";
	const MS_DURATION_DESC = "-msDuration";
	const NAME_ASC = "+name";
	const NAME_DESC = "-name";
	const MODERATION_COUNT_ASC = "+moderationCount";
	const MODERATION_COUNT_DESC = "-moderationCount";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
	const RANK_ASC = "+rank";
	const RANK_DESC = "-rank";
	const TOTAL_RANK_ASC = "+totalRank";
	const TOTAL_RANK_DESC = "-totalRank";
	const START_DATE_ASC = "+startDate";
	const START_DATE_DESC = "-startDate";
	const END_DATE_ASC = "+endDate";
	const END_DATE_DESC = "-endDate";
	const PARTNER_SORT_VALUE_ASC = "+partnerSortValue";
	const PARTNER_SORT_VALUE_DESC = "-partnerSortValue";
	const RECENT_ASC = "+recent";
	const RECENT_DESC = "-recent";
	const WEIGHT_ASC = "+weight";
	const WEIGHT_DESC = "-weight";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaPlaylistOrderBy
{
	const NAME_ASC = "+name";
	const NAME_DESC = "-name";
	const MODERATION_COUNT_ASC = "+moderationCount";
	const MODERATION_COUNT_DESC = "-moderationCount";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
	const RANK_ASC = "+rank";
	const RANK_DESC = "-rank";
	const TOTAL_RANK_ASC = "+totalRank";
	const TOTAL_RANK_DESC = "-totalRank";
	const START_DATE_ASC = "+startDate";
	const START_DATE_DESC = "-startDate";
	const END_DATE_ASC = "+endDate";
	const END_DATE_DESC = "-endDate";
	const PARTNER_SORT_VALUE_ASC = "+partnerSortValue";
	const PARTNER_SORT_VALUE_DESC = "-partnerSortValue";
	const RECENT_ASC = "+recent";
	const RECENT_DESC = "-recent";
	const WEIGHT_ASC = "+weight";
	const WEIGHT_DESC = "-weight";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaReportOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaSchemaType
{
	const SYNDICATION = "syndication";
	const SERVE_API = "cuePoint.serveAPI";
	const INGEST_API = "cuePoint.ingestAPI";
	const BULK_UPLOAD_XML = "bulkUploadXml.bulkUploadXML";
	const BULK_UPLOAD_RESULT_XML = "bulkUploadXml.bulkUploadResultXML";
	const DROP_FOLDER_XML = "dropFolderXmlBulkUpload.dropFolderXml";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaSearchConditionComparison
{
	const EQUAL = "1";
	const GREATER_THAN = "2";
	const GREATER_THAN_OR_EQUAL = "3";
	const LESS_THAN = "4";
	const LESS_THAN_OR_EQUAL = "5";
	const EQUEL = "1";
	const GREATER_THAN_OR_EQUEL = "3";
	const LESS_THAN_OR_EQUEL = "5";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaSourceType
{
	const FILE = "1";
	const WEBCAM = "2";
	const URL = "5";
	const SEARCH_PROVIDER = "6";
	const AKAMAI_LIVE = "29";
	const MANUAL_LIVE_STREAM = "30";
	const LIMELIGHT_LIVE = "limeLight.LIVE_STREAM";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaStorageProfileOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaTaggedObjectType
{
	const ENTRY = "1";
	const CATEGORY = "2";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaThumbAssetOrderBy
{
	const SIZE_ASC = "+size";
	const SIZE_DESC = "-size";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
	const DELETED_AT_ASC = "+deletedAt";
	const DELETED_AT_DESC = "-deletedAt";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaThumbParamsOrderBy
{
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaThumbParamsOutputOrderBy
{
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaTubeMogulSyndicationFeedOrderBy
{
	const PLAYLIST_ID_ASC = "+playlistId";
	const PLAYLIST_ID_DESC = "-playlistId";
	const NAME_ASC = "+name";
	const NAME_DESC = "-name";
	const TYPE_ASC = "+type";
	const TYPE_DESC = "-type";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaUiConfOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaUploadTokenOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaUserLoginDataOrderBy
{
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaUserOrderBy
{
	const ID_ASC = "+id";
	const ID_DESC = "-id";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaUserRoleOrderBy
{
	const ID_ASC = "+id";
	const ID_DESC = "-id";
	const NAME_ASC = "+name";
	const NAME_DESC = "-name";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaVideoCodec
{
	const NONE = "";
	const VP6 = "vp6";
	const H263 = "h263";
	const H264 = "h264";
	const H264B = "h264b";
	const H264M = "h264m";
	const H264H = "h264h";
	const FLV = "flv";
	const MPEG4 = "mpeg4";
	const THEORA = "theora";
	const WMV2 = "wmv2";
	const WMV3 = "wmv3";
	const WVC1A = "wvc1a";
	const VP8 = "vp8";
	const MPEG2 = "mpeg2";
	const COPY = "copy";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaWidgetOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaYahooSyndicationFeedOrderBy
{
	const PLAYLIST_ID_ASC = "+playlistId";
	const PLAYLIST_ID_DESC = "-playlistId";
	const NAME_ASC = "+name";
	const NAME_DESC = "-name";
	const TYPE_ASC = "+type";
	const TYPE_DESC = "-type";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
}

