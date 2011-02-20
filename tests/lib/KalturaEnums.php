<?php
/**
 * @package External
 * @subpackage Kaltura
 */
require_once("KalturaClientBase.php");

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaAccessControlOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaAdminUserOrderBy
{
	const ID_ASC = "+id";
	const ID_DESC = "-id";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
}

/**
 * @package External
 * @subpackage Kaltura
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
 * @package External
 * @subpackage Kaltura
 */
class KalturaApiParameterPermissionItemAction
{
	const READ = "read";
	const UPDATE = "update";
	const INSERT = "insert";
}

/**
 * @package External
 * @subpackage Kaltura
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
 * @package External
 * @subpackage Kaltura
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
 * @package External
 * @subpackage Kaltura
 */
class KalturaAssetParamsOrderBy
{
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaAssetParamsOutputOrderBy
{
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaAssetType
{
	const FLAVOR = "1";
	const THUMBNAIL = "2";
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaAudioCodec
{
	const NONE = "";
	const MP3 = "mp3";
	const AAC = "aac";
	const VORBIS = "vorbis";
	const WMA = "wma";
	const COPY = "copy";
}

/**
 * @package External
 * @subpackage Kaltura
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
}

/**
 * @package External
 * @subpackage Kaltura
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
 * @package External
 * @subpackage Kaltura
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
 * @package External
 * @subpackage Kaltura
 */
class KalturaBatchJobAppErrors
{
	const OUTPUT_FILE_DOESNT_EXIST = 11;
	const OUTPUT_FILE_WRONG_SIZE = 12;
	const CANNOT_CREATE_DIRECTORY = 13;
	const NFS_FILE_DOESNT_EXIST = 21;
	const EXTRACT_MEDIA_FAILED = 31;
	const CLOSER_TIMEOUT = 41;
	const ENGINE_NOT_FOUND = 51;
	const REMOTE_FILE_NOT_FOUND = 61;
	const REMOTE_DOWNLOAD_FAILED = 62;
	const CSV_FILE_NOT_FOUND = 71;
	const CONVERSION_FAILED = 81;
	const THUMBNAIL_NOT_CREATED = 91;
}

/**
 * @package External
 * @subpackage Kaltura
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
 * @package External
 * @subpackage Kaltura
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
 * @package External
 * @subpackage Kaltura
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
 * @package External
 * @subpackage Kaltura
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
	const PULL = "12";
	const REMOTE_CONVERT = "13";
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
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaBitRateMode
{
	const CBR = 1;
	const VBR = 2;
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaBulkUploadCsvVersion
{
	const V1 = "1";
	const V2 = "2";
	const V3 = "3";
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaCategoryOrderBy
{
	const DEPTH_ASC = "+depth";
	const DEPTH_DESC = "-depth";
	const FULL_NAME_ASC = "+fullName";
	const FULL_NAME_DESC = "-fullName";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaCommercialUseType
{
	const COMMERCIAL_USE = 1;
	const NON_COMMERCIAL_USE = 0;
}

/**
 * @package External
 * @subpackage Kaltura
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
	const JPG = "jpg";
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaControlPanelCommandOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaControlPanelCommandStatus
{
	const PENDING = 1;
	const HANDLED = 2;
	const DONE = 3;
	const FAILED = 4;
}

/**
 * @package External
 * @subpackage Kaltura
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
 * @package External
 * @subpackage Kaltura
 */
class KalturaControlPanelCommandType
{
	const STOP = 1;
	const START = 2;
	const CONFIG = 3;
	const KILL = 4;
}

/**
 * @package External
 * @subpackage Kaltura
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
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaConversionProfileOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaCountryRestrictionType
{
	const RESTRICT_COUNTRY_LIST = 0;
	const ALLOW_COUNTRY_LIST = 1;
}

/**
 * @package External
 * @subpackage Kaltura
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
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaDirectoryRestrictionType
{
	const DONT_DISPLAY = 0;
	const DISPLAY_WITH_LINK = 1;
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaDurationType
{
	const NOT_AVAILABLE = "notavailable";
	const SHORT = "short";
	const MEDIUM = "medium";
	const LONG = "long";
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaEditorType
{
	const SIMPLE = 1;
	const ADVANCED = 2;
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaEmailIngestionProfileStatus
{
	const INACTIVE = 0;
	const ACTIVE = 1;
}

/**
 * @package External
 * @subpackage Kaltura
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
 * @package External
 * @subpackage Kaltura
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
}

/**
 * @package External
 * @subpackage Kaltura
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
 * @package External
 * @subpackage Kaltura
 */
class KalturaExportProtocol
{
	const KALTURA_DC = 0;
	const FTP = 1;
	const SCP = 2;
	const SFTP = 3;
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaFileSyncObjectType
{
	const ENTRY = "1";
	const UICONF = "2";
	const BATCHJOB = "3";
	const FLAVOR_ASSET = "4";
	const METADATA = "5";
	const METADATA_PROFILE = "6";
}

/**
 * @package External
 * @subpackage Kaltura
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
 * @package External
 * @subpackage Kaltura
 */
class KalturaFlavorAssetStatus
{
	const ERROR = -1;
	const QUEUED = 0;
	const CONVERTING = 1;
	const READY = 2;
	const DELETED = 3;
	const NOT_APPLICABLE = 4;
	const TEMP = 5;
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaFlavorParamsOrderBy
{
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaFlavorParamsOutputOrderBy
{
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaGender
{
	const UNKNOWN = 0;
	const MALE = 1;
	const FEMALE = 2;
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaGoogleSyndicationFeedAdultValues
{
	const YES = "Yes";
	const NO = "No";
}

/**
 * @package External
 * @subpackage Kaltura
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
 * @package External
 * @subpackage Kaltura
 */
class KalturaITunesSyndicationFeedAdultValues
{
	const YES = "yes";
	const NO = "no";
	const CLEAN = "clean";
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaITunesSyndicationFeedCategories
{
	const ARTS = "Arts";
	const ARTS_DESIGN = "Arts/Design";
	const ARTS_FASHION_BEAUTY = "Arts/Fashion &amp; Beauty";
	const ARTS_FOOD = "Arts/Food";
	const ARTS_LITERATURE = "Arts/Literature";
	const ARTS_PERFORMING_ARTS = "Arts/Performing Arts";
	const ARTS_VISUAL_ARTS = "Arts/Visual Arts";
	const BUSINESS = "Business";
	const BUSINESS_BUSINESS_NEWS = "Business/Business News";
	const BUSINESS_CAREERS = "Business/Careers";
	const BUSINESS_INVESTING = "Business/Investing";
	const BUSINESS_MANAGEMENT_MARKETING = "Business/Management &amp; Marketing";
	const BUSINESS_SHOPPING = "Business/Shopping";
	const COMEDY = "Comedy";
	const EDUCATION = "Education";
	const EDUCATION_TECHNOLOGY = "Education/Education Technology";
	const EDUCATION_HIGHER_EDUCATION = "Education/Higher Education";
	const EDUCATION_K_12 = "Education/K-12";
	const EDUCATION_LANGUAGE_COURSES = "Education/Language Courses";
	const EDUCATION_TRAINING = "Education/Training";
	const GAMES_HOBBIES = "Games &amp; Hobbies";
	const GAMES_HOBBIES_AUTOMOTIVE = "Games &amp; Hobbies/Automotive";
	const GAMES_HOBBIES_AVIATION = "Games &amp; Hobbies/Aviation";
	const GAMES_HOBBIES_HOBBIES = "Games &amp; Hobbies/Hobbies";
	const GAMES_HOBBIES_OTHER_GAMES = "Games &amp; Hobbies/Other Games";
	const GAMES_HOBBIES_VIDEO_GAMES = "Games &amp; Hobbies/Video Games";
	const GOVERNMENT_ORGANIZATIONS = "Government &amp; Organizations";
	const GOVERNMENT_ORGANIZATIONS_LOCAL = "Government &amp; Organizations/Local";
	const GOVERNMENT_ORGANIZATIONS_NATIONAL = "Government &amp; Organizations/National";
	const GOVERNMENT_ORGANIZATIONS_NON_PROFIT = "Government &amp; Organizations/Non-Profit";
	const GOVERNMENT_ORGANIZATIONS_REGIONAL = "Government &amp; Organizations/Regional";
	const HEALTH = "Health";
	const HEALTH_ALTERNATIVE_HEALTH = "Health/Alternative Health";
	const HEALTH_FITNESS_NUTRITION = "Health/Fitness &amp; Nutrition";
	const HEALTH_SELF_HELP = "Health/Self-Help";
	const HEALTH_SEXUALITY = "Health/Sexuality";
	const KIDS_FAMILY = "Kids &amp; Family";
	const MUSIC = "Music";
	const NEWS_POLITICS = "News &amp; Politics";
	const RELIGION_SPIRITUALITY = "Religion &amp; Spirituality";
	const RELIGION_SPIRITUALITY_BUDDHISM = "Religion &amp; Spirituality/Buddhism";
	const RELIGION_SPIRITUALITY_CHRISTIANITY = "Religion &amp; Spirituality/Christianity";
	const RELIGION_SPIRITUALITY_HINDUISM = "Religion &amp; Spirituality/Hinduism";
	const RELIGION_SPIRITUALITY_ISLAM = "Religion &amp; Spirituality/Islam";
	const RELIGION_SPIRITUALITY_JUDAISM = "Religion &amp; Spirituality/Judaism";
	const RELIGION_SPIRITUALITY_OTHER = "Religion &amp; Spirituality/Other";
	const RELIGION_SPIRITUALITY_SPIRITUALITY = "Religion &amp; Spirituality/Spirituality";
	const SCIENCE_MEDICINE = "Science &amp; Medicine";
	const SCIENCE_MEDICINE_MEDICINE = "Science &amp; Medicine/Medicine";
	const SCIENCE_MEDICINE_NATURAL_SCIENCES = "Science &amp; Medicine/Natural Sciences";
	const SCIENCE_MEDICINE_SOCIAL_SCIENCES = "Science &amp; Medicine/Social Sciences";
	const SOCIETY_CULTURE = "Society &amp; Culture";
	const SOCIETY_CULTURE_HISTORY = "Society &amp; Culture/History";
	const SOCIETY_CULTURE_PERSONAL_JOURNALS = "Society &amp; Culture/Personal Journals";
	const SOCIETY_CULTURE_PHILOSOPHY = "Society &amp; Culture/Philosophy";
	const SOCIETY_CULTURE_PLACES_TRAVEL = "Society &amp; Culture/Places &amp; Travel";
	const SPORTS_RECREATION = "Sports &amp; Recreation";
	const SPORTS_RECREATION_AMATEUR = "Sports &amp; Recreation/Amateur";
	const SPORTS_RECREATION_COLLEGE_HIGH_SCHOOL = "Sports &amp; Recreation/College &amp; High School";
	const SPORTS_RECREATION_OUTDOOR = "Sports &amp; Recreation/Outdoor";
	const SPORTS_RECREATION_PROFESSIONAL = "Sports &amp; Recreation/Professional";
	const TECHNOLOGY = "Technology";
	const TECHNOLOGY_GADGETS = "Technology/Gadgets";
	const TECHNOLOGY_TECH_NEWS = "Technology/Tech News";
	const TECHNOLOGY_PODCASTING = "Technology/Podcasting";
	const TECHNOLOGY_SOFTWARE_HOW_TO = "Technology/Software How-To";
	const TV_FILM = "TV &amp; Film";
}

/**
 * @package External
 * @subpackage Kaltura
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
 * @package External
 * @subpackage Kaltura
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
 * @package External
 * @subpackage Kaltura
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
}

/**
 * @package External
 * @subpackage Kaltura
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
}

/**
 * @package External
 * @subpackage Kaltura
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
 * @package External
 * @subpackage Kaltura
 */
class KalturaMailJobStatus
{
	const PENDING = 1;
	const SENT = 2;
	const ERROR = 3;
	const QUEUED = 4;
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaMailType
{
	const MAIL_TYPE_KALTURA_NEWSLETTER = 10;
	const MAIL_TYPE_ADDED_TO_FAVORITES = 11;
	const MAIL_TYPE_ADDED_TO_CLIP_FAVORITES = 12;
	const MAIL_TYPE_NEW_COMMENT_IN_PROFILE = 13;
	const MAIL_TYPE_CLIP_ADDED_YOUR_KALTURA = 20;
	const MAIL_TYPE_VIDEO_ADDED = 21;
	const MAIL_TYPE_ROUGHCUT_CREATED = 22;
	const MAIL_TYPE_ADDED_KALTURA_TO_YOUR_FAVORITES = 23;
	const MAIL_TYPE_NEW_COMMENT_IN_KALTURA = 24;
	const MAIL_TYPE_CLIP_ADDED = 30;
	const MAIL_TYPE_VIDEO_CREATED = 31;
	const MAIL_TYPE_ADDED_KALTURA_TO_HIS_FAVORITES = 32;
	const MAIL_TYPE_NEW_COMMENT_IN_KALTURA_YOU_CONTRIBUTED = 33;
	const MAIL_TYPE_CLIP_CONTRIBUTED = 40;
	const MAIL_TYPE_ROUGHCUT_CREATED_SUBSCRIBED = 41;
	const MAIL_TYPE_ADDED_KALTURA_TO_HIS_FAVORITES_SUBSCRIBED = 42;
	const MAIL_TYPE_NEW_COMMENT_IN_KALTURA_YOU_SUBSCRIBED = 43;
	const MAIL_TYPE_REGISTER_CONFIRM = 50;
	const MAIL_TYPE_PASSWORD_RESET = 51;
	const MAIL_TYPE_LOGIN_MAIL_RESET = 52;
	const MAIL_TYPE_REGISTER_CONFIRM_VIDEO_SERVICE = 54;
	const MAIL_TYPE_VIDEO_READY = 60;
	const MAIL_TYPE_VIDEO_IS_READY = 62;
	const MAIL_TYPE_BULK_DOWNLOAD_READY = 63;
	const MAIL_TYPE_NOTIFY_ERR = 70;
	const MAIL_TYPE_ACCOUNT_UPGRADE_CONFIRM = 80;
	const MAIL_TYPE_VIDEO_SERVICE_NOTICE = 81;
	const MAIL_TYPE_VIDEO_SERVICE_NOTICE_LIMIT_REACHED = 82;
	const MAIL_TYPE_VIDEO_SERVICE_NOTICE_ACCOUNT_LOCKED = 83;
	const MAIL_TYPE_VIDEO_SERVICE_NOTICE_ACCOUNT_DELETED = 84;
	const MAIL_TYPE_VIDEO_SERVICE_NOTICE_UPGRADE_OFFER = 85;
	const MAIL_TYPE_ACCOUNT_REACTIVE_CONFIRM = 86;
	const MAIL_TYPE_SYSTEM_USER_RESET_PASSWORD = 110;
	const MAIL_TYPE_SYSTEM_USER_RESET_PASSWORD_SUCCESS = 111;
	const MAIL_TYPE_SYSTEM_USER_NEW_PASSWORD = 112;
	const MAIL_TYPE_SYSTEM_USER_CREDENTIALS_SAVED = 113;
}

/**
 * @package External
 * @subpackage Kaltura
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
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaMediaFlavorParamsOrderBy
{
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaMediaFlavorParamsOutputOrderBy
{
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaMediaInfoOrderBy
{
}

/**
 * @package External
 * @subpackage Kaltura
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
 * @package External
 * @subpackage Kaltura
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
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaModerationFlagStatus
{
	const PENDING = 1;
	const MODERATED = 2;
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaModerationFlagType
{
	const SEXUAL_CONTENT = 1;
	const VIOLENT_REPULSIVE = 2;
	const HARMFUL_DANGEROUS = 3;
	const SPAM_COMMERCIALS = 4;
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaModerationObjectType
{
	const ENTRY = 2;
	const USER = 3;
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaNotificationObjectType
{
	const ENTRY = 1;
	const KSHOW = 2;
	const USER = 3;
	const BATCH_JOB = 4;
}

/**
 * @package External
 * @subpackage Kaltura
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
 * @package External
 * @subpackage Kaltura
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
 * @package External
 * @subpackage Kaltura
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
 * @package External
 * @subpackage Kaltura
 */
class KalturaNullableBoolean
{
	const NULL_VALUE = -1;
	const FALSE_VALUE = 0;
	const TRUE_VALUE = 1;
}

/**
 * @package External
 * @subpackage Kaltura
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
 * @package External
 * @subpackage Kaltura
 */
class KalturaPartnerStatus
{
	const ACTIVE = 1;
	const BLOCKED = 2;
	const FULL_BLOCK = 3;
}

/**
 * @package External
 * @subpackage Kaltura
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
}

/**
 * @package External
 * @subpackage Kaltura
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
 * @package External
 * @subpackage Kaltura
 */
class KalturaPermissionItemType
{
	const API_ACTION_ITEM = "kApiActionPermissionItem";
	const API_PARAMETER_ITEM = "kApiParameterPermissionItem";
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaPermissionName
{
	const FEATURE_ANALYTICS_TAB = "FEATURE_ANALYTICS_TAB";
	const FEATURE_508_PLAYERS = "FEATURE_508_PLAYERS";
	const FEATURE_LIVE_STREAM = "FEATURE_LIVE_STREAM";
	const FEATURE_VAST = "FEATURE_VAST";
	const FEATURE_SILVERLIGHT = "FEATURE_SILVERLIGHT";
	const FEATURE_PS2_PERMISSIONS_VALIDATION = "FEATURE_PS2_PERMISSIONS_VALIDATION";
	const USER_SESSION_PERMISSION = "BASE_USER_SESSION_PERMISSION";
	const ALWAYS_ALLOWED_ACTIONS = "ALWAYS_ALLOWED_ACTIONS";
	const SYSTEM_FILESYNC = "SYSTEM_FILESYNC";
	const SYSTEM_INTERNAL = "SYSTEM_INTERNAL";
	const KMC_ACCESS = "KMC_ACCESS";
	const KMC_READ_ONLY = "KMC_READ_ONLY";
	const SYSTEM_ADMIN_BASE = "SYSTEM_ADMIN_BASE";
	const SYSTEM_ADMIN_PUBLISHER_BASE = "SYSTEM_ADMIN_PUBLISHER_BASE";
	const SYSTEM_ADMIN_PUBLISHER_KMC_ACCESS = "SYSTEM_ADMIN_PUBLISHER_KMC_ACCESS";
	const SYSTEM_ADMIN_PUBLISHER_CONFIG = "SYSTEM_ADMIN_PUBLISHER_CONFIG";
	const SYSTEM_ADMIN_PUBLISHER_BLOCK = "SYSTEM_ADMIN_PUBLISHER_BLOCK";
	const SYSTEM_ADMIN_PUBLISHER_REMOVE = "SYSTEM_ADMIN_PUBLISHER_REMOVE";
	const SYSTEM_ADMIN_PUBLISHER_ADD = "SYSTEM_ADMIN_PUBLISHER_ADD";
	const SYSTEM_ADMIN_PUBLISHER_USAGE = "SYSTEM_ADMIN_PUBLISHER_USAGE";
	const SYSTEM_ADMIN_USER_MANAGE = "SYSTEM_ADMIN_USER_MANAGE";
	const SYSTEM_ADMIN_SYSTEM_MONITOR = "SYSTEM_ADMIN_SYSTEM_MONITOR";
	const SYSTEM_ADMIN_DEVELOPERS_TAB = "SYSTEM_ADMIN_DEVELOPERS_TAB";
	const SYSTEM_ADMIN_BATCH_CONTROL = "SYSTEM_ADMIN_BATCH_CONTROL";
	const SYSTEM_ADMIN_BATCH_CONTROL_INPROGRESS = "SYSTEM_ADMIN_BATCH_CONTROL_INPROGRESS";
	const SYSTEM_ADMIN_BATCH_CONTROL_FAILED = "SYSTEM_ADMIN_BATCH_CONTROL_FAILED";
	const SYSTEM_ADMIN_BATCH_CONTROL_SETUP = "SYSTEM_ADMIN_BATCH_CONTROL_SETUP";
	const SYSTEM_ADMIN_STORAGE = "SYSTEM_ADMIN_STORAGE";
	const SYSTEM_ADMIN_VIRUS_SCAN = "SYSTEM_ADMIN_VIRUS_SCAN";
	const SYSTEM_ADMIN_EMAIL_INGESTION = "SYSTEM_ADMIN_EMAIL_INGESTION";
	const SYSTEM_ADMIN_CONTENT_DISTRIBUTION_BASE = "SYSTEM_ADMIN_CONTENT_DISTRIBUTION_BASE";
	const SYSTEM_ADMIN_CONTENT_DISTRIBUTION_MODIFY = "SYSTEM_ADMIN_CONTENT_DISTRIBUTION_MODIFY";
	const SYSTEM_ADMIN_PERMISSIONS_MANAGE = "SYSTEM_ADMIN_PERMISSIONS_MANAGE";
	const SYSTEM_ADMIN_ENTRY_INVESTIGATION = "SYSTEM_ADMIN_ENTRY_INVESTIGATION";
	const BATCH_BASE = "BATCH_BASE";
	const CONTENT_INGEST_UPLOAD = "CONTENT_INGEST_UPLOAD";
	const CONTENT_INGEST_BULK_UPLOAD = "CONTENT_INGEST_BULK_UPLOAD";
	const CONTENT_INGEST_FEED = "CONTENT_INGEST_FEED";
	const CONTENT_MANAGE_DISTRIBUTION_BASE = "CONTENT_MANAGE_DISTRIBUTION_BASE";
	const CONTENT_MANAGE_DISTRIBUTION_WHERE = "CONTENT_MANAGE_DISTRIBUTION_WHERE";
	const CONTENT_MANAGE_DISTRIBUTION_SEND = "CONTENT_MANAGE_DISTRIBUTION_SEND";
	const CONTENT_MANAGE_DISTRIBUTION_REMOVE = "CONTENT_MANAGE_DISTRIBUTION_REMOVE";
	const CONTENT_MANAGE_DISTRIBUTION_PROFILE_MODIFY = "CONTENT_MANAGE_DISTRIBUTION_PROFILE_MODIFY";
	const CONTENT_MANAGE_VIRUS_SCAN = "CONTENT_MANAGE_VIRUS_SCAN";
	const CONTENT_MANAGE_MIX = "CONTENT_MANAGE_MIX";
	const CONTENT_MANAGE_BASE = "CONTENT_MANAGE_BASE";
	const CONTENT_MANAGE_METADATA = "CONTENT_MANAGE_METADATA";
	const CONTENT_MANAGE_ASSIGN_CATEGORIES = "CONTENT_MANAGE_ASSIGN_CATEGORIES";
	const CONTENT_MANAGE_THUMBNAIL = "CONTENT_MANAGE_THUMBNAIL";
	const CONTENT_MANAGE_SCHEDULE = "CONTENT_MANAGE_SCHEDULE";
	const CONTENT_MANAGE_ACCESS_CONTROL = "CONTENT_MANAGE_ACCESS_CONTROL";
	const CONTENT_MANAGE_CUSTOM_DATA = "CONTENT_MANAGE_CUSTOM_DATA";
	const CONTENT_MANAGE_DELETE = "CONTENT_MANAGE_DELETE";
	const CONTENT_MANAGE_RECONVERT = "CONTENT_MANAGE_RECONVERT";
	const CONTENT_MANAGE_EDIT_CATEGORIES = "CONTENT_MANAGE_EDIT_CATEGORIES";
	const CONTENT_MANAGE_ANNOTATION = "CONTENT_MANAGE_ANNOTATION";
	const CONTENT_MANAGE_SHARE = "CONTENT_MANAGE_SHARE";
	const CONTENT_MANAGE_DOWNLOAD = "CONTENT_MANAGE_DOWNLOAD";
	const LIVE_STREAM_ADD = "LIVE_STREAM_ADD";
	const LIVE_STREAM_UPDATE = "LIVE_STREAM_UPDATE";
	const CONTENT_MODERATE_BASE = "CONTENT_MODERATE_BASE";
	const CONTENT_MODERATE_METADATA = "CONTENT_MODERATE_METADATA";
	const CONTENT_MODERATE_CUSTOM_DATA = "CONTENT_MODERATE_CUSTOM_DATA";
	const CONTENT_MODERATE_APPROVE_REJECT = "CONTENT_MODERATE_APPROVE_REJECT";
	const PLAYLIST_BASE = "PLAYLIST_BASE";
	const PLAYLIST_ADD = "PLAYLIST_ADD";
	const PLAYLIST_UPDATE = "PLAYLIST_UPDATE";
	const PLAYLIST_DELETE = "PLAYLIST_DELETE";
	const SYNDICATION_BASE = "SYNDICATION_BASE";
	const SYNDICATION_ADD = "SYNDICATION_ADD";
	const SYNDICATION_UPDATE = "SYNDICATION_UPDATE";
	const SYNDICATION_DELETE = "SYNDICATION_DELETE";
	const STUDIO_BASE = "STUDIO_BASE";
	const STUDIO_ADD_UICONF = "STUDIO_ADD_UICONF";
	const STUDIO_UPDATE_UICONF = "STUDIO_UPDATE_UICONF";
	const STUDIO_DELETE_UICONF = "STUDIO_DELETE_UICONF";
	const ACCOUNT_BASE = "ACCOUNT_BASE";
	const ACCOUNT_UPDATE_SETTINGS = "ACCOUNT_UPDATE_SETTINGS";
	const INTEGRATION_BASE = "INTEGRATION_BASE";
	const INTEGRATION_UPDATE_SETTINGS = "INTEGRATION_UPDATE_SETTINGS";
	const ACCESS_CONTROL_BASE = "ACCESS_CONTROL_BASE";
	const ACCESS_CONTROL_ADD = "ACCESS_CONTROL_ADD";
	const ACCESS_CONTROL_UPDATE = "ACCESS_CONTROL_UPDATE";
	const ACCESS_CONTROL_DELETE = "ACCESS_CONTROL_DELETE";
	const TRANSCODING_BASE = "TRANSCODING_BASE";
	const TRANSCODING_ADD = "TRANSCODING_ADD";
	const TRANSCODING_UPDATE = "TRANSCODING_UPDATE";
	const TRANSCODING_DELETE = "TRANSCODING_DELETE";
	const CUSTOM_DATA_PROFILE_BASE = "CUSTOM_DATA_PROFILE_BASE";
	const CUSTOM_DATA_PROFILE_ADD = "CUSTOM_DATA_PROFILE_ADD";
	const CUSTOM_DATA_PROFILE_UPDATE = "CUSTOM_DATA_PROFILE_UPDATE";
	const CUSTOM_DATA_PROFILE_DELETE = "CUSTOM_DATA_PROFILE_DELETE";
	const CUSTOM_DATA_FIELD_ADD = "CUSTOM_DATA_FIELD_ADD";
	const CUSTOM_DATA_FIELD_UPDATE = "CUSTOM_DATA_FIELD_UPDATE";
	const CUSTOM_DATA_FIELD_DELETE = "CUSTOM_DATA_FIELD_DELETE";
	const ADMIN_BASE = "ADMIN_BASE";
	const ADMIN_USER_ADD = "ADMIN_USER_ADD";
	const ADMIN_USER_UPDATE = "ADMIN_USER_UPDATE";
	const ADMIN_USER_DELETE = "ADMIN_USER_DELETE";
	const ADMIN_ROLE_ADD = "ADMIN_ROLE_ADD";
	const ADMIN_ROLE_UPDATE = "ADMIN_ROLE_UPDATE";
	const ADMIN_ROLE_DELETE = "ADMIN_ROLE_DELETE";
	const ADMIN_PERMISSION_ADD = "ADMIN_PERMISSION_ADD";
	const ADMIN_PERMISSION_UPDATE = "ADMIN_PERMISSION_UPDATE";
	const ADMIN_PERMISSION_DELETE = "ADMIN_PERMISSION_DELETE";
	const ADMIN_PUBLISHER_MANAGE = "ADMIN_PUBLISHER_MANAGE";
	const ANALYTICS_BASE = "ANALYTICS_BASE";
	const WIDGET_ADMIN = "WIDGET_ADMIN";
	const SEARCH_SERVICE = "SEARCH_SERVICE";
	const ANALYTICS_SEND_DATA = "ANALYTICS_SEND_DATA";
	const AUDIT_TRAIL_BASE = "AUDIT_TRAIL_BASE";
	const AUDIT_TRAIL_ADD = "AUDIT_TRAIL_ADD";
	const ADVERTISING_BASE = "ADVERTISING_BASE";
	const ADVERTISING_UPDATE_SETTINGS = "ADVERTISING_UPDATE_SETTINGS";
	const PLAYLIST_EMBED_CODE = "PLAYLIST_EMBED_CODE";
	const STUDIO_BRAND_UICONF = "STUDIO_BRAND_UICONF";
	const STUDIO_SELECT_CONTENT = "STUDIO_SELECT_CONTENT";
	const CONTENT_MANAGE_EMBED_CODE = "CONTENT_MANAGE_EMBED_CODE";
	const ADMIN_WHITE_BRANDING = "ADMIN_WHITE_BRANDING";
}

/**
 * @package External
 * @subpackage Kaltura
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
 * @package External
 * @subpackage Kaltura
 */
class KalturaPermissionStatus
{
	const ACTIVE = 1;
	const BLOCKED = 2;
	const DELETED = 3;
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaPermissionType
{
	const NORMAL = 1;
	const SPECIAL_FEATURE = 2;
	const PLUGIN = 3;
	const PARTNER_GROUP = 4;
}

/**
 * @package External
 * @subpackage Kaltura
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
}

/**
 * @package External
 * @subpackage Kaltura
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
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaPlaylistType
{
	const DYNAMIC = 10;
	const STATIC_LIST = 3;
	const EXTERNAL = 101;
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaReportType
{
	const TOP_CONTENT = 1;
	const CONTENT_DROPOFF = 2;
	const CONTENT_INTERACTIONS = 3;
	const MAP_OVERLAY = 4;
	const TOP_CONTRIBUTORS = 5;
	const TOP_SYNDICATION = 6;
	const CONTENT_CONTRIBUTIONS = 7;
	const ADMIN_CONSOLE = 10;
}

/**
 * @package External
 * @subpackage Kaltura
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
 * @package External
 * @subpackage Kaltura
 */
class KalturaSearchConditionComparison
{
	const EQUEL = 1;
	const GREATER_THAN = 2;
	const GREATER_THAN_OR_EQUEL = 3;
	const LESS_THAN = 4;
	const LESS_THAN_OR_EQUEL = 5;
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaSearchOperatorType
{
	const SEARCH_AND = 1;
	const SEARCH_OR = 2;
}

/**
 * @package External
 * @subpackage Kaltura
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
 * @package External
 * @subpackage Kaltura
 */
class KalturaSessionType
{
	const USER = 0;
	const ADMIN = 2;
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaSiteRestrictionType
{
	const RESTRICT_SITE_LIST = 0;
	const ALLOW_SITE_LIST = 1;
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaSourceType
{
	const FILE = 1;
	const WEBCAM = 2;
	const URL = 5;
	const SEARCH_PROVIDER = 6;
	const AKAMAI_LIVE = 29;
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaStatsEventType
{
	const WIDGET_LOADED = 1;
	const MEDIA_LOADED = 2;
	const PLAY = 3;
	const PLAY_REACHED_25 = 4;
	const PLAY_REACHED_50 = 5;
	const PLAY_REACHED_75 = 6;
	const PLAY_REACHED_100 = 7;
	const OPEN_EDIT = 8;
	const OPEN_VIRAL = 9;
	const OPEN_DOWNLOAD = 10;
	const OPEN_REPORT = 11;
	const BUFFER_START = 12;
	const BUFFER_END = 13;
	const OPEN_FULL_SCREEN = 14;
	const CLOSE_FULL_SCREEN = 15;
	const REPLAY = 16;
	const SEEK = 17;
	const OPEN_UPLOAD = 18;
	const SAVE_PUBLISH = 19;
	const CLOSE_EDITOR = 20;
	const PRE_BUMPER_PLAYED = 21;
	const POST_BUMPER_PLAYED = 22;
	const BUMPER_CLICKED = 23;
	const PREROLL_STARTED = 24;
	const MIDROLL_STARTED = 25;
	const POSTROLL_STARTED = 26;
	const OVERLAY_STARTED = 27;
	const PREROLL_CLICKED = 28;
	const MIDROLL_CLICKED = 29;
	const POSTROLL_CLICKED = 30;
	const OVERLAY_CLICKED = 31;
	const PREROLL_25 = 32;
	const PREROLL_50 = 33;
	const PREROLL_75 = 34;
	const MIDROLL_25 = 35;
	const MIDROLL_50 = 36;
	const MIDROLL_75 = 37;
	const POSTROLL_25 = 38;
	const POSTROLL_50 = 39;
	const POSTROLL_75 = 40;
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaStatsKmcEventType
{
	const CONTENT_PAGE_VIEW = 1001;
	const CONTENT_ADD_PLAYLIST = 1010;
	const CONTENT_EDIT_PLAYLIST = 1011;
	const CONTENT_DELETE_PLAYLIST = 1012;
	const CONTENT_DELETE_ITEM = 1058;
	const CONTENT_DELETE_MIX = 1059;
	const CONTENT_EDIT_ENTRY = 1013;
	const CONTENT_CHANGE_THUMBNAIL = 1014;
	const CONTENT_ADD_TAGS = 1015;
	const CONTENT_REMOVE_TAGS = 1016;
	const CONTENT_ADD_ADMIN_TAGS = 1017;
	const CONTENT_REMOVE_ADMIN_TAGS = 1018;
	const CONTENT_DOWNLOAD = 1019;
	const CONTENT_APPROVE_MODERATION = 1020;
	const CONTENT_REJECT_MODERATION = 1021;
	const CONTENT_BULK_UPLOAD = 1022;
	const CONTENT_ADMIN_KCW_UPLOAD = 1023;
	const CONTENT_CONTENT_GO_TO_PAGE = 1057;
	const CONTENT_ENTRY_DRILLDOWN = 1088;
	const CONTENT_OPEN_PREVIEW_AND_EMBED = 1089;
	const ACCOUNT_CHANGE_PARTNER_INFO = 1030;
	const ACCOUNT_CHANGE_LOGIN_INFO = 1031;
	const ACCOUNT_CONTACT_US_USAGE = 1032;
	const ACCOUNT_UPDATE_SERVER_SETTINGS = 1033;
	const ACCOUNT_ACCOUNT_OVERVIEW = 1034;
	const ACCOUNT_ACCESS_CONTROL = 1035;
	const ACCOUNT_TRANSCODING_SETTINGS = 1036;
	const ACCOUNT_ACCOUNT_UPGRADE = 1037;
	const ACCOUNT_SAVE_SERVER_SETTINGS = 1038;
	const ACCOUNT_ACCESS_CONTROL_DELETE = 1039;
	const ACCOUNT_SAVE_TRANSCODING_SETTINGS = 1040;
	const LOGIN = 1041;
	const DASHBOARD_IMPORT_CONTENT = 1042;
	const DASHBOARD_UPDATE_CONTENT = 1043;
	const DASHBOARD_ACCOUNT_CONTACT_US = 1044;
	const DASHBOARD_VIEW_REPORTS = 1045;
	const DASHBOARD_EMBED_PLAYER = 1046;
	const DASHBOARD_EMBED_PLAYLIST = 1047;
	const DASHBOARD_CUSTOMIZE_PLAYERS = 1048;
	const APP_STUDIO_NEW_PLAYER_SINGLE_VIDEO = 1050;
	const APP_STUDIO_NEW_PLAYER_PLAYLIST = 1051;
	const APP_STUDIO_NEW_PLAYER_MULTI_TAB_PLAYLIST = 1052;
	const APP_STUDIO_EDIT_PLAYER_SINGLE_VIDEO = 1053;
	const APP_STUDIO_EDIT_PLAYER_PLAYLIST = 1054;
	const APP_STUDIO_EDIT_PLAYER_MULTI_TAB_PLAYLIST = 1055;
	const APP_STUDIO_DUPLICATE_PLAYER = 1056;
	const REPORTS_AND_ANALYTICS_BANDWIDTH_USAGE_TAB = 1070;
	const REPORTS_AND_ANALYTICS_CONTENT_REPORTS_TAB = 1071;
	const REPORTS_AND_ANALYTICS_USERS_AND_COMMUNITY_REPORTS_TAB = 1072;
	const REPORTS_AND_ANALYTICS_TOP_CONTRIBUTORS = 1073;
	const REPORTS_AND_ANALYTICS_MAP_OVERLAYS = 1074;
	const REPORTS_AND_ANALYTICS_TOP_SYNDICATIONS = 1075;
	const REPORTS_AND_ANALYTICS_TOP_CONTENT = 1076;
	const REPORTS_AND_ANALYTICS_CONTENT_DROPOFF = 1077;
	const REPORTS_AND_ANALYTICS_CONTENT_INTERACTIONS = 1078;
	const REPORTS_AND_ANALYTICS_CONTENT_CONTRIBUTIONS = 1079;
	const REPORTS_AND_ANALYTICS_VIDEO_DRILL_DOWN = 1080;
	const REPORTS_AND_ANALYTICS_CONTENT_DRILL_DOWN_INTERACTION = 1081;
	const REPORTS_AND_ANALYTICS_CONTENT_CONTRIBUTIONS_DRILLDOWN = 1082;
	const REPORTS_AND_ANALYTICS_VIDEO_DRILL_DOWN_DROPOFF = 1083;
	const REPORTS_AND_ANALYTICS_MAP_OVERLAYS_DRILLDOWN = 1084;
	const REPORTS_AND_ANALYTICS_TOP_SYNDICATIONS_DRILL_DOWN = 1085;
	const REPORTS_AND_ANALYTICS_BANDWIDTH_USAGE_VIEW_MONTHLY = 1086;
	const REPORTS_AND_ANALYTICS_BANDWIDTH_USAGE_VIEW_YEARLY = 1087;
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaSyndicationFeedStatus
{
	const DELETED = -1;
	const ACTIVE = 1;
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaSyndicationFeedType
{
	const GOOGLE_VIDEO = 1;
	const YAHOO = 2;
	const ITUNES = 3;
	const TUBE_MOGUL = 4;
}

/**
 * @package External
 * @subpackage Kaltura
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
 * @package External
 * @subpackage Kaltura
 */
class KalturaThumbCropType
{
	const RESIZE = 1;
	const RESIZE_WITH_PADDING = 2;
	const CROP = 3;
	const CROP_FROM_TOP = 4;
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaThumbParamsOrderBy
{
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaThumbParamsOutputOrderBy
{
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaTubeMogulSyndicationFeedCategories
{
	const ARTS_AND_ANIMATION = "Arts &amp; Animation";
	const COMEDY = "Comedy";
	const ENTERTAINMENT = "Entertainment";
	const MUSIC = "Music";
	const NEWS_AND_BLOGS = "News &amp; Blogs";
	const SCIENCE_AND_TECHNOLOGY = "Science &amp; Technology";
	const SPORTS = "Sports";
	const TRAVEL_AND_PLACES = "Travel &amp; Places";
	const VIDEO_GAMES = "Video Games";
	const ANIMALS_AND_PETS = "Animals &amp; Pets";
	const AUTOS = "Autos";
	const VLOGS_PEOPLE = "Vlogs &amp; People";
	const HOW_TO_INSTRUCTIONAL_DIY = "How To/Instructional/DIY";
	const COMMERCIALS_PROMOTIONAL = "Commercials/Promotional";
	const FAMILY_AND_KIDS = "Family &amp; Kids";
}

/**
 * @package External
 * @subpackage Kaltura
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
 * @package External
 * @subpackage Kaltura
 */
class KalturaUiConfCreationMode
{
	const WIZARD = 2;
	const ADVANCED = 3;
}

/**
 * @package External
 * @subpackage Kaltura
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
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaUiConfOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaUploadErrorCode
{
	const NO_ERROR = 0;
	const GENERAL_ERROR = 1;
	const PARTIAL_UPLOAD = 2;
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaUploadTokenOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
}

/**
 * @package External
 * @subpackage Kaltura
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
 * @package External
 * @subpackage Kaltura
 */
class KalturaUserOrderBy
{
	const ID_ASC = "+id";
	const ID_DESC = "-id";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
}

/**
 * @package External
 * @subpackage Kaltura
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
 * @package External
 * @subpackage Kaltura
 */
class KalturaUserRoleStatus
{
	const ACTIVE = 1;
	const BLOCKED = 2;
	const DELETED = 3;
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaUserStatus
{
	const BLOCKED = 0;
	const ACTIVE = 1;
	const DELETED = 2;
}

/**
 * @package External
 * @subpackage Kaltura
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
	const COPY = "copy";
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaWidgetOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaWidgetSecurityType
{
	const NONE = 1;
	const TIMEHASH = 2;
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaYahooSyndicationFeedAdultValues
{
	const ADULT = "adult";
	const NON_ADULT = "nonadult";
}

/**
 * @package External
 * @subpackage Kaltura
 */
class KalturaYahooSyndicationFeedCategories
{
	const ACTION = "Action";
	const ART_AND_ANIMATION = "Art &amp; Animation";
	const ENTERTAINMENT_AND_TV = "Entertainment &amp; TV";
	const FOOD = "Food";
	const GAMES = "Games";
	const HOW_TO = "How-To";
	const MUSIC = "Music";
	const PEOPLE_AND_VLOGS = "People &amp; Vlogs";
	const SCIENCE_AND_ENVIRONMENT = "Science &amp; Environment";
	const TRANSPORTATION = "Transportation";
	const ANIMALS = "Animals";
	const COMMERCIALS = "Commercials";
	const FAMILY = "Family";
	const FUNNY_VIDEOS = "Funny Videos";
	const HEALTH_AND_BEAUTY = "Health &amp; Beauty";
	const MOVIES_AND_SHORTS = "Movies &amp; Shorts";
	const NEWS_AND_POLITICS = "News &amp; Politics";
	const PRODUCTS_AND_TECH = "Products &amp; Tech.";
	const SPORTS = "Sports";
	const TRAVEL = "Travel";
}

/**
 * @package External
 * @subpackage Kaltura
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

