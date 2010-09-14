function KalturaAccessControlOrderBy()
{
}
KalturaAccessControlOrderBy.prototype.CREATED_AT_ASC = "+createdAt";
KalturaAccessControlOrderBy.prototype.CREATED_AT_DESC = "-createdAt";

function KalturaAudioCodec()
{
}
KalturaAudioCodec.prototype.NONE = "";
KalturaAudioCodec.prototype.MP3 = "mp3";
KalturaAudioCodec.prototype.AAC = "aac";

function KalturaBaseEntryOrderBy()
{
}
KalturaBaseEntryOrderBy.prototype.NAME_ASC = "+name";
KalturaBaseEntryOrderBy.prototype.NAME_DESC = "-name";
KalturaBaseEntryOrderBy.prototype.MODERATION_COUNT_ASC = "+moderationCount";
KalturaBaseEntryOrderBy.prototype.MODERATION_COUNT_DESC = "-moderationCount";
KalturaBaseEntryOrderBy.prototype.CREATED_AT_ASC = "+createdAt";
KalturaBaseEntryOrderBy.prototype.CREATED_AT_DESC = "-createdAt";
KalturaBaseEntryOrderBy.prototype.RANK_ASC = "+rank";
KalturaBaseEntryOrderBy.prototype.RANK_DESC = "-rank";

function KalturaBaseJobOrderBy()
{
}
KalturaBaseJobOrderBy.prototype.CREATED_AT_ASC = "+createdAt";
KalturaBaseJobOrderBy.prototype.CREATED_AT_DESC = "-createdAt";
KalturaBaseJobOrderBy.prototype.EXECUTION_ATTEMPTS_ASC = "+executionAttempts";
KalturaBaseJobOrderBy.prototype.EXECUTION_ATTEMPTS_DESC = "-executionAttempts";

function KalturaBaseSyndicationFeedOrderBy()
{
}
KalturaBaseSyndicationFeedOrderBy.prototype.PLAYLIST_ID_ASC = "+playlistId";
KalturaBaseSyndicationFeedOrderBy.prototype.PLAYLIST_ID_DESC = "-playlistId";
KalturaBaseSyndicationFeedOrderBy.prototype.NAME_ASC = "+name";
KalturaBaseSyndicationFeedOrderBy.prototype.NAME_DESC = "-name";
KalturaBaseSyndicationFeedOrderBy.prototype.TYPE_ASC = "+type";
KalturaBaseSyndicationFeedOrderBy.prototype.TYPE_DESC = "-type";
KalturaBaseSyndicationFeedOrderBy.prototype.CREATED_AT_ASC = "+createdAt";
KalturaBaseSyndicationFeedOrderBy.prototype.CREATED_AT_DESC = "-createdAt";

function KalturaBatchJobErrorTypes()
{
}
KalturaBatchJobErrorTypes.prototype.APP = 0;
KalturaBatchJobErrorTypes.prototype.RUNTIME = 1;
KalturaBatchJobErrorTypes.prototype.HTTP = 2;
KalturaBatchJobErrorTypes.prototype.CURL = 3;

function KalturaBatchJobOrderBy()
{
}
KalturaBatchJobOrderBy.prototype.STATUS_ASC = "+status";
KalturaBatchJobOrderBy.prototype.STATUS_DESC = "-status";
KalturaBatchJobOrderBy.prototype.QUEUE_TIME_ASC = "+queueTime";
KalturaBatchJobOrderBy.prototype.QUEUE_TIME_DESC = "-queueTime";
KalturaBatchJobOrderBy.prototype.FINISH_TIME_ASC = "+finishTime";
KalturaBatchJobOrderBy.prototype.FINISH_TIME_DESC = "-finishTime";
KalturaBatchJobOrderBy.prototype.CREATED_AT_ASC = "+createdAt";
KalturaBatchJobOrderBy.prototype.CREATED_AT_DESC = "-createdAt";
KalturaBatchJobOrderBy.prototype.EXECUTION_ATTEMPTS_ASC = "+executionAttempts";
KalturaBatchJobOrderBy.prototype.EXECUTION_ATTEMPTS_DESC = "-executionAttempts";

function KalturaBatchJobStatus()
{
}
KalturaBatchJobStatus.prototype.PENDING = 0;
KalturaBatchJobStatus.prototype.QUEUED = 1;
KalturaBatchJobStatus.prototype.PROCESSING = 2;
KalturaBatchJobStatus.prototype.PROCESSED = 3;
KalturaBatchJobStatus.prototype.MOVEFILE = 4;
KalturaBatchJobStatus.prototype.FINISHED = 5;
KalturaBatchJobStatus.prototype.FAILED = 6;
KalturaBatchJobStatus.prototype.ABORTED = 7;
KalturaBatchJobStatus.prototype.ALMOST_DONE = 8;
KalturaBatchJobStatus.prototype.RETRY = 9;
KalturaBatchJobStatus.prototype.FATAL = 10;

function KalturaBatchJobType()
{
}
KalturaBatchJobType.prototype.CONVERT = 0;
KalturaBatchJobType.prototype.IMPORT = 1;
KalturaBatchJobType.prototype.DELETE = 2;
KalturaBatchJobType.prototype.FLATTEN = 3;
KalturaBatchJobType.prototype.BULKUPLOAD = 4;
KalturaBatchJobType.prototype.DVDCREATOR = 5;
KalturaBatchJobType.prototype.DOWNLOAD = 6;
KalturaBatchJobType.prototype.OOCONVERT = 7;
KalturaBatchJobType.prototype.CONVERT_PROFILE = 10;
KalturaBatchJobType.prototype.POSTCONVERT = 11;
KalturaBatchJobType.prototype.PULL = 12;
KalturaBatchJobType.prototype.REMOTE_CONVERT = 13;
KalturaBatchJobType.prototype.EXTRACT_MEDIA = 14;
KalturaBatchJobType.prototype.MAIL = 15;
KalturaBatchJobType.prototype.NOTIFICATION = 16;
KalturaBatchJobType.prototype.CLEANUP = 17;
KalturaBatchJobType.prototype.SCHEDULER_HELPER = 18;
KalturaBatchJobType.prototype.BULKDOWNLOAD = 19;
KalturaBatchJobType.prototype.PROJECT = 1000;

function KalturaBulkUploadCsvVersion()
{
}
KalturaBulkUploadCsvVersion.prototype.V1 = "1";
KalturaBulkUploadCsvVersion.prototype.V2 = "2";

function KalturaCategoryOrderBy()
{
}
KalturaCategoryOrderBy.prototype.DEPTH_ASC = "+depth";
KalturaCategoryOrderBy.prototype.DEPTH_DESC = "-depth";
KalturaCategoryOrderBy.prototype.FULL_NAME_ASC = "+fullName";
KalturaCategoryOrderBy.prototype.FULL_NAME_DESC = "-fullName";
KalturaCategoryOrderBy.prototype.CREATED_AT_ASC = "+createdAt";
KalturaCategoryOrderBy.prototype.CREATED_AT_DESC = "-createdAt";

function KalturaCommercialUseType()
{
}
KalturaCommercialUseType.prototype.COMMERCIAL_USE = "commercial_use";
KalturaCommercialUseType.prototype.NON_COMMERCIAL_USE = "non-commercial_use";

function KalturaContainerFormat()
{
}
KalturaContainerFormat.prototype.FLV = "flv";
KalturaContainerFormat.prototype.MP4 = "mp4";
KalturaContainerFormat.prototype.AVI = "avi";
KalturaContainerFormat.prototype.MOV = "mov";
KalturaContainerFormat.prototype._3GP = "3gp";

function KalturaConversionProfileOrderBy()
{
}
KalturaConversionProfileOrderBy.prototype.CREATED_AT_ASC = "+createdAt";
KalturaConversionProfileOrderBy.prototype.CREATED_AT_DESC = "-createdAt";

function KalturaCountryRestrictionType()
{
}
KalturaCountryRestrictionType.prototype.RESTRICT_COUNTRY_LIST = 0;
KalturaCountryRestrictionType.prototype.ALLOW_COUNTRY_LIST = 1;

function KalturaDataEntryOrderBy()
{
}
KalturaDataEntryOrderBy.prototype.NAME_ASC = "+name";
KalturaDataEntryOrderBy.prototype.NAME_DESC = "-name";
KalturaDataEntryOrderBy.prototype.MODERATION_COUNT_ASC = "+moderationCount";
KalturaDataEntryOrderBy.prototype.MODERATION_COUNT_DESC = "-moderationCount";
KalturaDataEntryOrderBy.prototype.CREATED_AT_ASC = "+createdAt";
KalturaDataEntryOrderBy.prototype.CREATED_AT_DESC = "-createdAt";
KalturaDataEntryOrderBy.prototype.RANK_ASC = "+rank";
KalturaDataEntryOrderBy.prototype.RANK_DESC = "-rank";

function KalturaDirectoryRestrictionType()
{
}
KalturaDirectoryRestrictionType.prototype.DONT_DISPLAY = 0;
KalturaDirectoryRestrictionType.prototype.DISPLAY_WITH_LINK = 1;

function KalturaDocumentEntryOrderBy()
{
}
KalturaDocumentEntryOrderBy.prototype.NAME_ASC = "+name";
KalturaDocumentEntryOrderBy.prototype.NAME_DESC = "-name";
KalturaDocumentEntryOrderBy.prototype.MODERATION_COUNT_ASC = "+moderationCount";
KalturaDocumentEntryOrderBy.prototype.MODERATION_COUNT_DESC = "-moderationCount";
KalturaDocumentEntryOrderBy.prototype.CREATED_AT_ASC = "+createdAt";
KalturaDocumentEntryOrderBy.prototype.CREATED_AT_DESC = "-createdAt";
KalturaDocumentEntryOrderBy.prototype.RANK_ASC = "+rank";
KalturaDocumentEntryOrderBy.prototype.RANK_DESC = "-rank";

function KalturaDocumentType()
{
}
KalturaDocumentType.prototype.DOCUMENT = 11;
KalturaDocumentType.prototype.SWF = 12;

function KalturaDurationType()
{
}
KalturaDurationType.prototype.NOT_AVAILABLE = "notavailable";
KalturaDurationType.prototype.SHORT = "short";
KalturaDurationType.prototype.MEDIUM = "medium";
KalturaDurationType.prototype.LONG = "long";

function KalturaEditorType()
{
}
KalturaEditorType.prototype.SIMPLE = 1;
KalturaEditorType.prototype.ADVANCED = 2;

function KalturaEntryModerationStatus()
{
}
KalturaEntryModerationStatus.prototype.PENDING_MODERATION = 1;
KalturaEntryModerationStatus.prototype.APPROVED = 2;
KalturaEntryModerationStatus.prototype.REJECTED = 3;
KalturaEntryModerationStatus.prototype.FLAGGED_FOR_REVIEW = 5;
KalturaEntryModerationStatus.prototype.AUTO_APPROVED = 6;

function KalturaEntryStatus()
{
}
KalturaEntryStatus.prototype.ERROR_IMPORTING = -2;
KalturaEntryStatus.prototype.ERROR_CONVERTING = -1;
KalturaEntryStatus.prototype.IMPORT = 0;
KalturaEntryStatus.prototype.PRECONVERT = 1;
KalturaEntryStatus.prototype.READY = 2;
KalturaEntryStatus.prototype.DELETED = 3;
KalturaEntryStatus.prototype.PENDING = 4;
KalturaEntryStatus.prototype.MODERATE = 5;
KalturaEntryStatus.prototype.BLOCKED = 6;

function KalturaEntryType()
{
}
KalturaEntryType.prototype.AUTOMATIC = -1;
KalturaEntryType.prototype.MEDIA_CLIP = 1;
KalturaEntryType.prototype.MIX = 2;
KalturaEntryType.prototype.PLAYLIST = 5;
KalturaEntryType.prototype.DATA = 6;
KalturaEntryType.prototype.DOCUMENT = 10;

function KalturaFlavorAssetStatus()
{
}
KalturaFlavorAssetStatus.prototype.ERROR = -1;
KalturaFlavorAssetStatus.prototype.QUEUED = 0;
KalturaFlavorAssetStatus.prototype.CONVERTING = 1;
KalturaFlavorAssetStatus.prototype.READY = 2;
KalturaFlavorAssetStatus.prototype.DELETED = 3;
KalturaFlavorAssetStatus.prototype.NOT_APPLICABLE = 4;

function KalturaFlavorParamsOrderBy()
{
}

function KalturaFlavorParamsOutputOrderBy()
{
}

function KalturaGender()
{
}
KalturaGender.prototype.UNKNOWN = 0;
KalturaGender.prototype.MALE = 1;
KalturaGender.prototype.FEMALE = 2;

function KalturaGoogleSyndicationFeedAdultValues()
{
}
KalturaGoogleSyndicationFeedAdultValues.prototype.YES = "Yes";
KalturaGoogleSyndicationFeedAdultValues.prototype.NO = "No";

function KalturaGoogleVideoSyndicationFeedOrderBy()
{
}
KalturaGoogleVideoSyndicationFeedOrderBy.prototype.PLAYLIST_ID_ASC = "+playlistId";
KalturaGoogleVideoSyndicationFeedOrderBy.prototype.PLAYLIST_ID_DESC = "-playlistId";
KalturaGoogleVideoSyndicationFeedOrderBy.prototype.NAME_ASC = "+name";
KalturaGoogleVideoSyndicationFeedOrderBy.prototype.NAME_DESC = "-name";
KalturaGoogleVideoSyndicationFeedOrderBy.prototype.TYPE_ASC = "+type";
KalturaGoogleVideoSyndicationFeedOrderBy.prototype.TYPE_DESC = "-type";
KalturaGoogleVideoSyndicationFeedOrderBy.prototype.CREATED_AT_ASC = "+createdAt";
KalturaGoogleVideoSyndicationFeedOrderBy.prototype.CREATED_AT_DESC = "-createdAt";

function KalturaITunesSyndicationFeedAdultValues()
{
}
KalturaITunesSyndicationFeedAdultValues.prototype.YES = "yes";
KalturaITunesSyndicationFeedAdultValues.prototype.NO = "no";
KalturaITunesSyndicationFeedAdultValues.prototype.CLEAN = "clean";

function KalturaITunesSyndicationFeedCategories()
{
}
KalturaITunesSyndicationFeedCategories.prototype.ARTS = "Arts";
KalturaITunesSyndicationFeedCategories.prototype.ARTS_DESIGN = "Arts/Design";
KalturaITunesSyndicationFeedCategories.prototype.ARTS_FASHION_BEAUTY = "Arts/Fashion &amp; Beauty";
KalturaITunesSyndicationFeedCategories.prototype.ARTS_FOOD = "Arts/Food";
KalturaITunesSyndicationFeedCategories.prototype.ARTS_LITERATURE = "Arts/Literature";
KalturaITunesSyndicationFeedCategories.prototype.ARTS_PERFORMING_ARTS = "Arts/Performing Arts";
KalturaITunesSyndicationFeedCategories.prototype.ARTS_VISUAL_ARTS = "Arts/Visual Arts";
KalturaITunesSyndicationFeedCategories.prototype.BUSINESS = "Business";
KalturaITunesSyndicationFeedCategories.prototype.BUSINESS_BUSINESS_NEWS = "Business/Business News";
KalturaITunesSyndicationFeedCategories.prototype.BUSINESS_CAREERS = "Business/Careers";
KalturaITunesSyndicationFeedCategories.prototype.BUSINESS_INVESTING = "Business/Investing";
KalturaITunesSyndicationFeedCategories.prototype.BUSINESS_MANAGEMENT_MARKETING = "Business/Management &amp; Marketing";
KalturaITunesSyndicationFeedCategories.prototype.BUSINESS_SHOPPING = "Business/Shopping";
KalturaITunesSyndicationFeedCategories.prototype.COMEDY = "Comedy";
KalturaITunesSyndicationFeedCategories.prototype.EDUCATION = "Education";
KalturaITunesSyndicationFeedCategories.prototype.EDUCATION_TECHNOLOGY = "Education/Education Technology";
KalturaITunesSyndicationFeedCategories.prototype.EDUCATION_HIGHER_EDUCATION = "Education/Higher Education";
KalturaITunesSyndicationFeedCategories.prototype.EDUCATION_K_12 = "Education/K-12";
KalturaITunesSyndicationFeedCategories.prototype.EDUCATION_LANGUAGE_COURSES = "Education/Language Courses";
KalturaITunesSyndicationFeedCategories.prototype.EDUCATION_TRAINING = "Education/Training";
KalturaITunesSyndicationFeedCategories.prototype.GAMES_HOBBIES = "Games &amp; Hobbies";
KalturaITunesSyndicationFeedCategories.prototype.GAMES_HOBBIES_AUTOMOTIVE = "Games &amp; Hobbies/Automotive";
KalturaITunesSyndicationFeedCategories.prototype.GAMES_HOBBIES_AVIATION = "Games &amp; Hobbies/Aviation";
KalturaITunesSyndicationFeedCategories.prototype.GAMES_HOBBIES_HOBBIES = "Games &amp; Hobbies/Hobbies";
KalturaITunesSyndicationFeedCategories.prototype.GAMES_HOBBIES_OTHER_GAMES = "Games &amp; Hobbies/Other Games";
KalturaITunesSyndicationFeedCategories.prototype.GAMES_HOBBIES_VIDEO_GAMES = "Games &amp; Hobbies/Video Games";
KalturaITunesSyndicationFeedCategories.prototype.GOVERNMENT_ORGANIZATIONS = "Government &amp; Organizations";
KalturaITunesSyndicationFeedCategories.prototype.GOVERNMENT_ORGANIZATIONS_LOCAL = "Government &amp; Organizations/Local";
KalturaITunesSyndicationFeedCategories.prototype.GOVERNMENT_ORGANIZATIONS_NATIONAL = "Government &amp; Organizations/National";
KalturaITunesSyndicationFeedCategories.prototype.GOVERNMENT_ORGANIZATIONS_NON_PROFIT = "Government &amp; Organizations/Non-Profit";
KalturaITunesSyndicationFeedCategories.prototype.GOVERNMENT_ORGANIZATIONS_REGIONAL = "Government &amp; Organizations/Regional";
KalturaITunesSyndicationFeedCategories.prototype.HEALTH = "Health";
KalturaITunesSyndicationFeedCategories.prototype.HEALTH_ALTERNATIVE_HEALTH = "Health/Alternative Health";
KalturaITunesSyndicationFeedCategories.prototype.HEALTH_FITNESS_NUTRITION = "Health/Fitness &amp; Nutrition";
KalturaITunesSyndicationFeedCategories.prototype.HEALTH_SELF_HELP = "Health/Self-Help";
KalturaITunesSyndicationFeedCategories.prototype.HEALTH_SEXUALITY = "Health/Sexuality";
KalturaITunesSyndicationFeedCategories.prototype.KIDS_FAMILY = "Kids &amp; Family";
KalturaITunesSyndicationFeedCategories.prototype.MUSIC = "Music";
KalturaITunesSyndicationFeedCategories.prototype.NEWS_POLITICS = "News &amp; Politics";
KalturaITunesSyndicationFeedCategories.prototype.RELIGION_SPIRITUALITY = "Religion &amp; Spirituality";
KalturaITunesSyndicationFeedCategories.prototype.RELIGION_SPIRITUALITY_BUDDHISM = "Religion &amp; Spirituality/Buddhism";
KalturaITunesSyndicationFeedCategories.prototype.RELIGION_SPIRITUALITY_CHRISTIANITY = "Religion &amp; Spirituality/Christianity";
KalturaITunesSyndicationFeedCategories.prototype.RELIGION_SPIRITUALITY_HINDUISM = "Religion &amp; Spirituality/Hinduism";
KalturaITunesSyndicationFeedCategories.prototype.RELIGION_SPIRITUALITY_ISLAM = "Religion &amp; Spirituality/Islam";
KalturaITunesSyndicationFeedCategories.prototype.RELIGION_SPIRITUALITY_JUDAISM = "Religion &amp; Spirituality/Judaism";
KalturaITunesSyndicationFeedCategories.prototype.RELIGION_SPIRITUALITY_OTHER = "Religion &amp; Spirituality/Other";
KalturaITunesSyndicationFeedCategories.prototype.RELIGION_SPIRITUALITY_SPIRITUALITY = "Religion &amp; Spirituality/Spirituality";
KalturaITunesSyndicationFeedCategories.prototype.SCIENCE_MEDICINE = "Science &amp; Medicine";
KalturaITunesSyndicationFeedCategories.prototype.SCIENCE_MEDICINE_MEDICINE = "Science &amp; Medicine/Medicine";
KalturaITunesSyndicationFeedCategories.prototype.SCIENCE_MEDICINE_NATURAL_SCIENCES = "Science &amp; Medicine/Natural Sciences";
KalturaITunesSyndicationFeedCategories.prototype.SCIENCE_MEDICINE_SOCIAL_SCIENCES = "Science &amp; Medicine/Social Sciences";
KalturaITunesSyndicationFeedCategories.prototype.SOCIETY_CULTURE = "Society &amp; Culture";
KalturaITunesSyndicationFeedCategories.prototype.SOCIETY_CULTURE_HISTORY = "Society &amp; Culture/History";
KalturaITunesSyndicationFeedCategories.prototype.SOCIETY_CULTURE_PERSONAL_JOURNALS = "Society &amp; Culture/Personal Journals";
KalturaITunesSyndicationFeedCategories.prototype.SOCIETY_CULTURE_PHILOSOPHY = "Society &amp; Culture/Philosophy";
KalturaITunesSyndicationFeedCategories.prototype.SOCIETY_CULTURE_PLACES_TRAVEL = "Society &amp; Culture/Places &amp; Travel";
KalturaITunesSyndicationFeedCategories.prototype.SPORTS_RECREATION = "Sports &amp; Recreation";
KalturaITunesSyndicationFeedCategories.prototype.SPORTS_RECREATION_AMATEUR = "Sports &amp; Recreation/Amateur";
KalturaITunesSyndicationFeedCategories.prototype.SPORTS_RECREATION_COLLEGE_HIGH_SCHOOL = "Sports &amp; Recreation/College &amp; High School";
KalturaITunesSyndicationFeedCategories.prototype.SPORTS_RECREATION_OUTDOOR = "Sports &amp; Recreation/Outdoor";
KalturaITunesSyndicationFeedCategories.prototype.SPORTS_RECREATION_PROFESSIONAL = "Sports &amp; Recreation/Professional";
KalturaITunesSyndicationFeedCategories.prototype.TECHNOLOGY = "Technology";
KalturaITunesSyndicationFeedCategories.prototype.TECHNOLOGY_GADGETS = "Technology/Gadgets";
KalturaITunesSyndicationFeedCategories.prototype.TECHNOLOGY_TECH_NEWS = "Technology/Tech News";
KalturaITunesSyndicationFeedCategories.prototype.TECHNOLOGY_PODCASTING = "Technology/Podcasting";
KalturaITunesSyndicationFeedCategories.prototype.TECHNOLOGY_SOFTWARE_HOW_TO = "Technology/Software How-To";
KalturaITunesSyndicationFeedCategories.prototype.TV_FILM = "TV &amp; Film";

function KalturaITunesSyndicationFeedOrderBy()
{
}
KalturaITunesSyndicationFeedOrderBy.prototype.PLAYLIST_ID_ASC = "+playlistId";
KalturaITunesSyndicationFeedOrderBy.prototype.PLAYLIST_ID_DESC = "-playlistId";
KalturaITunesSyndicationFeedOrderBy.prototype.NAME_ASC = "+name";
KalturaITunesSyndicationFeedOrderBy.prototype.NAME_DESC = "-name";
KalturaITunesSyndicationFeedOrderBy.prototype.TYPE_ASC = "+type";
KalturaITunesSyndicationFeedOrderBy.prototype.TYPE_DESC = "-type";
KalturaITunesSyndicationFeedOrderBy.prototype.CREATED_AT_ASC = "+createdAt";
KalturaITunesSyndicationFeedOrderBy.prototype.CREATED_AT_DESC = "-createdAt";

function KalturaLicenseType()
{
}
KalturaLicenseType.prototype.UNKNOWN = -1;
KalturaLicenseType.prototype.NONE = 0;
KalturaLicenseType.prototype.COPYRIGHTED = 1;
KalturaLicenseType.prototype.PUBLIC_DOMAIN = 2;
KalturaLicenseType.prototype.CREATIVECOMMONS_ATTRIBUTION = 3;
KalturaLicenseType.prototype.CREATIVECOMMONS_ATTRIBUTION_SHARE_ALIKE = 4;
KalturaLicenseType.prototype.CREATIVECOMMONS_ATTRIBUTION_NO_DERIVATIVES = 5;
KalturaLicenseType.prototype.CREATIVECOMMONS_ATTRIBUTION_NON_COMMERCIAL = 6;
KalturaLicenseType.prototype.CREATIVECOMMONS_ATTRIBUTION_NON_COMMERCIAL_SHARE_ALIKE = 7;
KalturaLicenseType.prototype.CREATIVECOMMONS_ATTRIBUTION_NON_COMMERCIAL_NO_DERIVATIVES = 8;
KalturaLicenseType.prototype.GFDL = 9;
KalturaLicenseType.prototype.GPL = 10;
KalturaLicenseType.prototype.AFFERO_GPL = 11;
KalturaLicenseType.prototype.LGPL = 12;
KalturaLicenseType.prototype.BSD = 13;
KalturaLicenseType.prototype.APACHE = 14;
KalturaLicenseType.prototype.MOZILLA = 15;

function KalturaMailJobOrderBy()
{
}
KalturaMailJobOrderBy.prototype.CREATED_AT_ASC = "+createdAt";
KalturaMailJobOrderBy.prototype.CREATED_AT_DESC = "-createdAt";
KalturaMailJobOrderBy.prototype.EXECUTION_ATTEMPTS_ASC = "+executionAttempts";
KalturaMailJobOrderBy.prototype.EXECUTION_ATTEMPTS_DESC = "-executionAttempts";

function KalturaMailJobStatus()
{
}
KalturaMailJobStatus.prototype.PENDING = 1;
KalturaMailJobStatus.prototype.SENT = 2;
KalturaMailJobStatus.prototype.ERROR = 3;
KalturaMailJobStatus.prototype.QUEUED = 4;

function KalturaMailType()
{
}
KalturaMailType.prototype.MAIL_TYPE_KALTURA_NEWSLETTER = 10;
KalturaMailType.prototype.MAIL_TYPE_ADDED_TO_FAVORITES = 11;
KalturaMailType.prototype.MAIL_TYPE_ADDED_TO_CLIP_FAVORITES = 12;
KalturaMailType.prototype.MAIL_TYPE_NEW_COMMENT_IN_PROFILE = 13;
KalturaMailType.prototype.MAIL_TYPE_CLIP_ADDED_YOUR_KALTURA = 20;
KalturaMailType.prototype.MAIL_TYPE_VIDEO_ADDED = 21;
KalturaMailType.prototype.MAIL_TYPE_ROUGHCUT_CREATED = 22;
KalturaMailType.prototype.MAIL_TYPE_ADDED_KALTURA_TO_YOUR_FAVORITES = 23;
KalturaMailType.prototype.MAIL_TYPE_NEW_COMMENT_IN_KALTURA = 24;
KalturaMailType.prototype.MAIL_TYPE_CLIP_ADDED = 30;
KalturaMailType.prototype.MAIL_TYPE_VIDEO_CREATED = 31;
KalturaMailType.prototype.MAIL_TYPE_ADDED_KALTURA_TO_HIS_FAVORITES = 32;
KalturaMailType.prototype.MAIL_TYPE_NEW_COMMENT_IN_KALTURA_YOU_CONTRIBUTED = 33;
KalturaMailType.prototype.MAIL_TYPE_CLIP_CONTRIBUTED = 40;
KalturaMailType.prototype.MAIL_TYPE_ROUGHCUT_CREATED_SUBSCRIBED = 41;
KalturaMailType.prototype.MAIL_TYPE_ADDED_KALTURA_TO_HIS_FAVORITES_SUBSCRIBED = 42;
KalturaMailType.prototype.MAIL_TYPE_NEW_COMMENT_IN_KALTURA_YOU_SUBSCRIBED = 43;
KalturaMailType.prototype.MAIL_TYPE_REGISTER_CONFIRM = 50;
KalturaMailType.prototype.MAIL_TYPE_PASSWORD_RESET = 51;
KalturaMailType.prototype.MAIL_TYPE_LOGIN_MAIL_RESET = 52;
KalturaMailType.prototype.MAIL_TYPE_REGISTER_CONFIRM_VIDEO_SERVICE = 54;
KalturaMailType.prototype.MAIL_TYPE_VIDEO_READY = 60;
KalturaMailType.prototype.MAIL_TYPE_VIDEO_IS_READY = 62;
KalturaMailType.prototype.MAIL_TYPE_BULK_DOWNLOAD_READY = 63;
KalturaMailType.prototype.MAIL_TYPE_NOTIFY_ERR = 70;
KalturaMailType.prototype.MAIL_TYPE_ACCOUNT_UPGRADE_CONFIRM = 80;
KalturaMailType.prototype.MAIL_TYPE_VIDEO_SERVICE_NOTICE = 81;
KalturaMailType.prototype.MAIL_TYPE_VIDEO_SERVICE_NOTICE_LIMIT_REACHED = 82;
KalturaMailType.prototype.MAIL_TYPE_VIDEO_SERVICE_NOTICE_ACCOUNT_LOCKED = 83;
KalturaMailType.prototype.MAIL_TYPE_VIDEO_SERVICE_NOTICE_ACCOUNT_DELETED = 84;
KalturaMailType.prototype.MAIL_TYPE_VIDEO_SERVICE_NOTICE_UPGRADE_OFFER = 85;
KalturaMailType.prototype.MAIL_TYPE_ACCOUNT_REACTIVE_CONFIRM = 86;
KalturaMailType.prototype.MAIL_TYPE_SYSTEM_USER_RESET_PASSWORD = 110;
KalturaMailType.prototype.MAIL_TYPE_SYSTEM_USER_RESET_PASSWORD_SUCCESS = 111;

function KalturaPlayableEntryOrderBy()
{
}
KalturaPlayableEntryOrderBy.prototype.PLAYS_ASC = "+plays";
KalturaPlayableEntryOrderBy.prototype.PLAYS_DESC = "-plays";
KalturaPlayableEntryOrderBy.prototype.VIEWS_ASC = "+views";
KalturaPlayableEntryOrderBy.prototype.VIEWS_DESC = "-views";
KalturaPlayableEntryOrderBy.prototype.DURATION_ASC = "+duration";
KalturaPlayableEntryOrderBy.prototype.DURATION_DESC = "-duration";
KalturaPlayableEntryOrderBy.prototype.NAME_ASC = "+name";
KalturaPlayableEntryOrderBy.prototype.NAME_DESC = "-name";
KalturaPlayableEntryOrderBy.prototype.MODERATION_COUNT_ASC = "+moderationCount";
KalturaPlayableEntryOrderBy.prototype.MODERATION_COUNT_DESC = "-moderationCount";
KalturaPlayableEntryOrderBy.prototype.CREATED_AT_ASC = "+createdAt";
KalturaPlayableEntryOrderBy.prototype.CREATED_AT_DESC = "-createdAt";
KalturaPlayableEntryOrderBy.prototype.RANK_ASC = "+rank";
KalturaPlayableEntryOrderBy.prototype.RANK_DESC = "-rank";

function KalturaMediaEntryOrderBy()
{
}
KalturaMediaEntryOrderBy.prototype.MEDIA_TYPE_ASC = "+mediaType";
KalturaMediaEntryOrderBy.prototype.MEDIA_TYPE_DESC = "-mediaType";
KalturaMediaEntryOrderBy.prototype.PLAYS_ASC = "+plays";
KalturaMediaEntryOrderBy.prototype.PLAYS_DESC = "-plays";
KalturaMediaEntryOrderBy.prototype.VIEWS_ASC = "+views";
KalturaMediaEntryOrderBy.prototype.VIEWS_DESC = "-views";
KalturaMediaEntryOrderBy.prototype.DURATION_ASC = "+duration";
KalturaMediaEntryOrderBy.prototype.DURATION_DESC = "-duration";
KalturaMediaEntryOrderBy.prototype.NAME_ASC = "+name";
KalturaMediaEntryOrderBy.prototype.NAME_DESC = "-name";
KalturaMediaEntryOrderBy.prototype.MODERATION_COUNT_ASC = "+moderationCount";
KalturaMediaEntryOrderBy.prototype.MODERATION_COUNT_DESC = "-moderationCount";
KalturaMediaEntryOrderBy.prototype.CREATED_AT_ASC = "+createdAt";
KalturaMediaEntryOrderBy.prototype.CREATED_AT_DESC = "-createdAt";
KalturaMediaEntryOrderBy.prototype.RANK_ASC = "+rank";
KalturaMediaEntryOrderBy.prototype.RANK_DESC = "-rank";

function KalturaMediaType()
{
}
KalturaMediaType.prototype.VIDEO = 1;
KalturaMediaType.prototype.IMAGE = 2;
KalturaMediaType.prototype.AUDIO = 5;

function KalturaMixEntryOrderBy()
{
}
KalturaMixEntryOrderBy.prototype.PLAYS_ASC = "+plays";
KalturaMixEntryOrderBy.prototype.PLAYS_DESC = "-plays";
KalturaMixEntryOrderBy.prototype.VIEWS_ASC = "+views";
KalturaMixEntryOrderBy.prototype.VIEWS_DESC = "-views";
KalturaMixEntryOrderBy.prototype.DURATION_ASC = "+duration";
KalturaMixEntryOrderBy.prototype.DURATION_DESC = "-duration";
KalturaMixEntryOrderBy.prototype.NAME_ASC = "+name";
KalturaMixEntryOrderBy.prototype.NAME_DESC = "-name";
KalturaMixEntryOrderBy.prototype.MODERATION_COUNT_ASC = "+moderationCount";
KalturaMixEntryOrderBy.prototype.MODERATION_COUNT_DESC = "-moderationCount";
KalturaMixEntryOrderBy.prototype.CREATED_AT_ASC = "+createdAt";
KalturaMixEntryOrderBy.prototype.CREATED_AT_DESC = "-createdAt";
KalturaMixEntryOrderBy.prototype.RANK_ASC = "+rank";
KalturaMixEntryOrderBy.prototype.RANK_DESC = "-rank";

function KalturaModerationFlagStatus()
{
}
KalturaModerationFlagStatus.prototype.PENDING = 1;
KalturaModerationFlagStatus.prototype.MODERATED = 2;

function KalturaModerationFlagType()
{
}
KalturaModerationFlagType.prototype.SEXUAL_CONTENT = 1;
KalturaModerationFlagType.prototype.VIOLENT_REPULSIVE = 2;
KalturaModerationFlagType.prototype.HARMFUL_DANGEROUS = 3;
KalturaModerationFlagType.prototype.SPAM_COMMERCIALS = 4;

function KalturaModerationObjectType()
{
}
KalturaModerationObjectType.prototype.ENTRY = 2;
KalturaModerationObjectType.prototype.USER = 3;

function KalturaNotificationObjectType()
{
}
KalturaNotificationObjectType.prototype.ENTRY = 1;
KalturaNotificationObjectType.prototype.KSHOW = 2;
KalturaNotificationObjectType.prototype.USER = 3;
KalturaNotificationObjectType.prototype.BATCH_JOB = 4;

function KalturaNotificationOrderBy()
{
}
KalturaNotificationOrderBy.prototype.CREATED_AT_ASC = "+createdAt";
KalturaNotificationOrderBy.prototype.CREATED_AT_DESC = "-createdAt";
KalturaNotificationOrderBy.prototype.EXECUTION_ATTEMPTS_ASC = "+executionAttempts";
KalturaNotificationOrderBy.prototype.EXECUTION_ATTEMPTS_DESC = "-executionAttempts";

function KalturaNotificationStatus()
{
}
KalturaNotificationStatus.prototype.PENDING = 1;
KalturaNotificationStatus.prototype.SENT = 2;
KalturaNotificationStatus.prototype.ERROR = 3;
KalturaNotificationStatus.prototype.SHOULD_RESEND = 4;
KalturaNotificationStatus.prototype.ERROR_RESENDING = 5;
KalturaNotificationStatus.prototype.SENT_SYNCH = 6;
KalturaNotificationStatus.prototype.QUEUED = 7;

function KalturaNotificationType()
{
}
KalturaNotificationType.prototype.ENTRY_ADD = 1;
KalturaNotificationType.prototype.ENTR_UPDATE_PERMISSIONS = 2;
KalturaNotificationType.prototype.ENTRY_DELETE = 3;
KalturaNotificationType.prototype.ENTRY_BLOCK = 4;
KalturaNotificationType.prototype.ENTRY_UPDATE = 5;
KalturaNotificationType.prototype.ENTRY_UPDATE_THUMBNAIL = 6;
KalturaNotificationType.prototype.ENTRY_UPDATE_MODERATION = 7;
KalturaNotificationType.prototype.USER_ADD = 21;
KalturaNotificationType.prototype.USER_BANNED = 26;

function KalturaNullableBoolean()
{
}
KalturaNullableBoolean.prototype.NULL_VALUE = -1;
KalturaNullableBoolean.prototype.FALSE_VALUE = 0;
KalturaNullableBoolean.prototype.TRUE_VALUE = 1;

function KalturaPartnerOrderBy()
{
}
KalturaPartnerOrderBy.prototype.ID_ASC = "+id";
KalturaPartnerOrderBy.prototype.ID_DESC = "-id";
KalturaPartnerOrderBy.prototype.NAME_ASC = "+name";
KalturaPartnerOrderBy.prototype.NAME_DESC = "-name";
KalturaPartnerOrderBy.prototype.WEBSITE_ASC = "+website";
KalturaPartnerOrderBy.prototype.WEBSITE_DESC = "-website";
KalturaPartnerOrderBy.prototype.CREATED_AT_ASC = "+createdAt";
KalturaPartnerOrderBy.prototype.CREATED_AT_DESC = "-createdAt";
KalturaPartnerOrderBy.prototype.ADMIN_NAME_ASC = "+adminName";
KalturaPartnerOrderBy.prototype.ADMIN_NAME_DESC = "-adminName";
KalturaPartnerOrderBy.prototype.ADMIN_EMAIL_ASC = "+adminEmail";
KalturaPartnerOrderBy.prototype.ADMIN_EMAIL_DESC = "-adminEmail";
KalturaPartnerOrderBy.prototype.STATUS_ASC = "+status";
KalturaPartnerOrderBy.prototype.STATUS_DESC = "-status";

function KalturaPartnerType()
{
}
KalturaPartnerType.prototype.KMC = 1;
KalturaPartnerType.prototype.WIKI = 100;
KalturaPartnerType.prototype.WORDPRESS = 101;
KalturaPartnerType.prototype.DRUPAL = 102;
KalturaPartnerType.prototype.DEKIWIKI = 103;
KalturaPartnerType.prototype.MOODLE = 104;
KalturaPartnerType.prototype.COMMUNITY_EDITION = 105;
KalturaPartnerType.prototype.JOOMLA = 106;

function KalturaPlaylistOrderBy()
{
}
KalturaPlaylistOrderBy.prototype.NAME_ASC = "+name";
KalturaPlaylistOrderBy.prototype.NAME_DESC = "-name";
KalturaPlaylistOrderBy.prototype.MODERATION_COUNT_ASC = "+moderationCount";
KalturaPlaylistOrderBy.prototype.MODERATION_COUNT_DESC = "-moderationCount";
KalturaPlaylistOrderBy.prototype.CREATED_AT_ASC = "+createdAt";
KalturaPlaylistOrderBy.prototype.CREATED_AT_DESC = "-createdAt";
KalturaPlaylistOrderBy.prototype.RANK_ASC = "+rank";
KalturaPlaylistOrderBy.prototype.RANK_DESC = "-rank";

function KalturaPlaylistType()
{
}
KalturaPlaylistType.prototype.DYNAMIC = 10;
KalturaPlaylistType.prototype.STATIC_LIST = 3;
KalturaPlaylistType.prototype.EXTERNAL = 101;

function KalturaReportType()
{
}
KalturaReportType.prototype.TOP_CONTENT = 1;
KalturaReportType.prototype.CONTENT_DROPOFF = 2;
KalturaReportType.prototype.CONTENT_INTERACTIONS = 3;
KalturaReportType.prototype.MAP_OVERLAY = 4;
KalturaReportType.prototype.TOP_CONTRIBUTORS = 5;
KalturaReportType.prototype.TOP_SYNDICATION = 6;
KalturaReportType.prototype.CONTENT_CONTRIBUTIONS = 7;

function KalturaSearchProviderType()
{
}
KalturaSearchProviderType.prototype.FLICKR = 3;
KalturaSearchProviderType.prototype.YOUTUBE = 4;
KalturaSearchProviderType.prototype.MYSPACE = 7;
KalturaSearchProviderType.prototype.PHOTOBUCKET = 8;
KalturaSearchProviderType.prototype.JAMENDO = 9;
KalturaSearchProviderType.prototype.CCMIXTER = 10;
KalturaSearchProviderType.prototype.NYPL = 11;
KalturaSearchProviderType.prototype.CURRENT = 12;
KalturaSearchProviderType.prototype.MEDIA_COMMONS = 13;
KalturaSearchProviderType.prototype.KALTURA = 20;
KalturaSearchProviderType.prototype.KALTURA_USER_CLIPS = 21;
KalturaSearchProviderType.prototype.ARCHIVE_ORG = 22;
KalturaSearchProviderType.prototype.KALTURA_PARTNER = 23;
KalturaSearchProviderType.prototype.METACAFE = 24;
KalturaSearchProviderType.prototype.SEARCH_PROXY = 28;

function KalturaSessionType()
{
}
KalturaSessionType.prototype.USER = 0;
KalturaSessionType.prototype.ADMIN = 2;

function KalturaSiteRestrictionType()
{
}
KalturaSiteRestrictionType.prototype.RESTRICT_SITE_LIST = 0;
KalturaSiteRestrictionType.prototype.ALLOW_SITE_LIST = 1;

function KalturaSourceType()
{
}
KalturaSourceType.prototype.FILE = 1;
KalturaSourceType.prototype.WEBCAM = 2;
KalturaSourceType.prototype.URL = 5;
KalturaSourceType.prototype.SEARCH_PROVIDER = 6;

function KalturaStatsEventType()
{
}
KalturaStatsEventType.prototype.WIDGET_LOADED = 1;
KalturaStatsEventType.prototype.MEDIA_LOADED = 2;
KalturaStatsEventType.prototype.PLAY = 3;
KalturaStatsEventType.prototype.PLAY_REACHED_25 = 4;
KalturaStatsEventType.prototype.PLAY_REACHED_50 = 5;
KalturaStatsEventType.prototype.PLAY_REACHED_75 = 6;
KalturaStatsEventType.prototype.PLAY_REACHED_100 = 7;
KalturaStatsEventType.prototype.OPEN_EDIT = 8;
KalturaStatsEventType.prototype.OPEN_VIRAL = 9;
KalturaStatsEventType.prototype.OPEN_DOWNLOAD = 10;
KalturaStatsEventType.prototype.OPEN_REPORT = 11;
KalturaStatsEventType.prototype.BUFFER_START = 12;
KalturaStatsEventType.prototype.BUFFER_END = 13;
KalturaStatsEventType.prototype.OPEN_FULL_SCREEN = 14;
KalturaStatsEventType.prototype.CLOSE_FULL_SCREEN = 15;
KalturaStatsEventType.prototype.REPLAY = 16;
KalturaStatsEventType.prototype.SEEK = 17;
KalturaStatsEventType.prototype.OPEN_UPLOAD = 18;
KalturaStatsEventType.prototype.SAVE_PUBLISH = 19;
KalturaStatsEventType.prototype.CLOSE_EDITOR = 20;
KalturaStatsEventType.prototype.PRE_BUMPER_PLAYED = 21;
KalturaStatsEventType.prototype.POST_BUMPER_PLAYED = 22;
KalturaStatsEventType.prototype.BUMPER_CLICKED = 23;
KalturaStatsEventType.prototype.FUTURE_USE_1 = 24;
KalturaStatsEventType.prototype.FUTURE_USE_2 = 25;
KalturaStatsEventType.prototype.FUTURE_USE_3 = 26;

function KalturaStatsKmcEventType()
{
}
KalturaStatsKmcEventType.prototype.CONTENT_PAGE_VIEW = 1001;
KalturaStatsKmcEventType.prototype.CONTENT_ADD_PLAYLIST = 1010;
KalturaStatsKmcEventType.prototype.CONTENT_EDIT_PLAYLIST = 1011;
KalturaStatsKmcEventType.prototype.CONTENT_DELETE_PLAYLIST = 1012;
KalturaStatsKmcEventType.prototype.CONTENT_DELETE_ITEM = 1058;
KalturaStatsKmcEventType.prototype.CONTENT_EDIT_ENTRY = 1013;
KalturaStatsKmcEventType.prototype.CONTENT_CHANGE_THUMBNAIL = 1014;
KalturaStatsKmcEventType.prototype.CONTENT_ADD_TAGS = 1015;
KalturaStatsKmcEventType.prototype.CONTENT_REMOVE_TAGS = 1016;
KalturaStatsKmcEventType.prototype.CONTENT_ADD_ADMIN_TAGS = 1017;
KalturaStatsKmcEventType.prototype.CONTENT_REMOVE_ADMIN_TAGS = 1018;
KalturaStatsKmcEventType.prototype.CONTENT_DOWNLOAD = 1019;
KalturaStatsKmcEventType.prototype.CONTENT_APPROVE_MODERATION = 1020;
KalturaStatsKmcEventType.prototype.CONTENT_REJECT_MODERATION = 1021;
KalturaStatsKmcEventType.prototype.CONTENT_BULK_UPLOAD = 1022;
KalturaStatsKmcEventType.prototype.CONTENT_ADMIN_KCW_UPLOAD = 1023;
KalturaStatsKmcEventType.prototype.CONTENT_CONTENT_GO_TO_PAGE = 1057;
KalturaStatsKmcEventType.prototype.ACCOUNT_CHANGE_PARTNER_INFO = 1030;
KalturaStatsKmcEventType.prototype.ACCOUNT_CHANGE_LOGIN_INFO = 1031;
KalturaStatsKmcEventType.prototype.ACCOUNT_CONTACT_US_USAGE = 1032;
KalturaStatsKmcEventType.prototype.ACCOUNT_UPDATE_SERVER_SETTINGS = 1033;
KalturaStatsKmcEventType.prototype.ACCOUNT_ACCOUNT_OVERVIEW = 1034;
KalturaStatsKmcEventType.prototype.ACCOUNT_ACCESS_CONTROL = 1035;
KalturaStatsKmcEventType.prototype.ACCOUNT_TRANSCODING_SETTINGS = 1036;
KalturaStatsKmcEventType.prototype.ACCOUNT_ACCOUNT_UPGRADE = 1037;
KalturaStatsKmcEventType.prototype.ACCOUNT_SAVE_SERVER_SETTINGS = 1038;
KalturaStatsKmcEventType.prototype.ACCOUNT_ACCESS_CONTROL_DELETE = 1039;
KalturaStatsKmcEventType.prototype.ACCOUNT_SAVE_TRANSCODING_SETTINGS = 1040;
KalturaStatsKmcEventType.prototype.LOGIN = 1041;
KalturaStatsKmcEventType.prototype.DASHBOARD_IMPORT_CONTENT = 1042;
KalturaStatsKmcEventType.prototype.DASHBOARD_UPDATE_CONTENT = 1043;
KalturaStatsKmcEventType.prototype.DASHBOARD_ACCOUNT_CONTACT_US = 1044;
KalturaStatsKmcEventType.prototype.DASHBOARD_VIEW_REPORTS = 1045;
KalturaStatsKmcEventType.prototype.DASHBOARD_EMBED_PLAYER = 1046;
KalturaStatsKmcEventType.prototype.DASHBOARD_EMBED_PLAYLIST = 1047;
KalturaStatsKmcEventType.prototype.DASHBOARD_CUSTOMIZE_PLAYERS = 1048;
KalturaStatsKmcEventType.prototype.APP_STUDIO_NEW_PLAYER_SINGLE_VIDEO = 1050;
KalturaStatsKmcEventType.prototype.APP_STUDIO_NEW_PLAYER_PLAYLIST = 1051;
KalturaStatsKmcEventType.prototype.APP_STUDIO_NEW_PLAYER_MULTI_TAB_PLAYLIST = 1052;
KalturaStatsKmcEventType.prototype.APP_STUDIO_EDIT_PLAYER_SINGLE_VIDEO = 1053;
KalturaStatsKmcEventType.prototype.APP_STUDIO_EDIT_PLAYER_PLAYLIST = 1054;
KalturaStatsKmcEventType.prototype.APP_STUDIO_EDIT_PLAYER_MULTI_TAB_PLAYLIST = 1055;
KalturaStatsKmcEventType.prototype.APP_STUDIO_DUPLICATE_PLAYER = 1056;
KalturaStatsKmcEventType.prototype.REPORTS_AND_ANALYTICS_BANDWIDTH_USAGE_TAB = 1070;
KalturaStatsKmcEventType.prototype.REPORTS_AND_ANALYTICS_CONTENT_REPORTS_TAB = 1071;
KalturaStatsKmcEventType.prototype.REPORTS_AND_ANALYTICS_USERS_AND_COMMUNITY_REPORTS_TAB = 1072;
KalturaStatsKmcEventType.prototype.REPORTS_AND_ANALYTICS_TOP_CONTRIBUTORS = 1073;
KalturaStatsKmcEventType.prototype.REPORTS_AND_ANALYTICS_MAP_OVERLAYS = 1074;
KalturaStatsKmcEventType.prototype.REPORTS_AND_ANALYTICS_TOP_SYNDICATIONS = 1075;
KalturaStatsKmcEventType.prototype.REPORTS_AND_ANALYTICS_TOP_CONTENT = 1076;
KalturaStatsKmcEventType.prototype.REPORTS_AND_ANALYTICS_CONTENT_DROPOFF = 1077;
KalturaStatsKmcEventType.prototype.REPORTS_AND_ANALYTICS_CONTENT_INTERACTIONS = 1078;
KalturaStatsKmcEventType.prototype.REPORTS_AND_ANALYTICS_CONTENT_CONTRIBUTIONS = 1079;
KalturaStatsKmcEventType.prototype.REPORTS_AND_ANALYTICS_VIDEO_DRILL_DOWN = 1080;
KalturaStatsKmcEventType.prototype.REPORTS_AND_ANALYTICS_CONTENT_DRILL_DOWN_INTERACTION = 1081;
KalturaStatsKmcEventType.prototype.REPORTS_AND_ANALYTICS_CONTENT_CONTRIBUTIONS_DRILLDOWN = 1082;
KalturaStatsKmcEventType.prototype.REPORTS_AND_ANALYTICS_VIDEO_DRILL_DOWN_DROPOFF = 1083;
KalturaStatsKmcEventType.prototype.REPORTS_AND_ANALYTICS_MAP_OVERLAYS_DRILLDOWN = 1084;
KalturaStatsKmcEventType.prototype.REPORTS_AND_ANALYTICS_TOP_SYNDICATIONS_DRILL_DOWN = 1085;
KalturaStatsKmcEventType.prototype.REPORTS_AND_ANALYTICS_BANDWIDTH_USAGE_VIEW_MONTHLY = 1086;
KalturaStatsKmcEventType.prototype.REPORTS_AND_ANALYTICS_BANDWIDTH_USAGE_VIEW_YEARLY = 1087;

function KalturaSyndicationFeedStatus()
{
}
KalturaSyndicationFeedStatus.prototype.DELETED = -1;
KalturaSyndicationFeedStatus.prototype.ACTIVE = 1;

function KalturaSyndicationFeedType()
{
}
KalturaSyndicationFeedType.prototype.GOOGLE_VIDEO = 1;
KalturaSyndicationFeedType.prototype.YAHOO = 2;
KalturaSyndicationFeedType.prototype.ITUNES = 3;
KalturaSyndicationFeedType.prototype.TUBE_MOGUL = 4;

function KalturaSystemUserOrderBy()
{
}
KalturaSystemUserOrderBy.prototype.ID_ASC = "+id";
KalturaSystemUserOrderBy.prototype.ID_DESC = "-id";
KalturaSystemUserOrderBy.prototype.STATUS_ASC = "+status";
KalturaSystemUserOrderBy.prototype.STATUS_DESC = "-status";

function KalturaSystemUserStatus()
{
}
KalturaSystemUserStatus.prototype.BLOCKED = 0;
KalturaSystemUserStatus.prototype.ACTIVE = 1;

function KalturaTubeMogulSyndicationFeedCategories()
{
}
KalturaTubeMogulSyndicationFeedCategories.prototype.ARTS_AND_ANIMATION = "Arts &amp; Animation";
KalturaTubeMogulSyndicationFeedCategories.prototype.COMEDY = "Comedy";
KalturaTubeMogulSyndicationFeedCategories.prototype.ENTERTAINMENT = "Entertainment";
KalturaTubeMogulSyndicationFeedCategories.prototype.MUSIC = "Music";
KalturaTubeMogulSyndicationFeedCategories.prototype.NEWS_AND_BLOGS = "News &amp; Blogs";
KalturaTubeMogulSyndicationFeedCategories.prototype.SCIENCE_AND_TECHNOLOGY = "Science &amp; Technology";
KalturaTubeMogulSyndicationFeedCategories.prototype.SPORTS = "Sports";
KalturaTubeMogulSyndicationFeedCategories.prototype.TRAVEL_AND_PLACES = "Travel &amp; Places";
KalturaTubeMogulSyndicationFeedCategories.prototype.VIDEO_GAMES = "Video Games";
KalturaTubeMogulSyndicationFeedCategories.prototype.ANIMALS_AND_PETS = "Animals &amp; Pets";
KalturaTubeMogulSyndicationFeedCategories.prototype.AUTOS = "Autos";
KalturaTubeMogulSyndicationFeedCategories.prototype.VLOGS_PEOPLE = "Vlogs &amp; People";
KalturaTubeMogulSyndicationFeedCategories.prototype.HOW_TO_INSTRUCTIONAL_DIY = "How To/Instructional/DIY";
KalturaTubeMogulSyndicationFeedCategories.prototype.COMMERCIALS_PROMOTIONAL = "Commercials/Promotional";
KalturaTubeMogulSyndicationFeedCategories.prototype.FAMILY_AND_KIDS = "Family &amp; Kids";

function KalturaTubeMogulSyndicationFeedOrderBy()
{
}
KalturaTubeMogulSyndicationFeedOrderBy.prototype.PLAYLIST_ID_ASC = "+playlistId";
KalturaTubeMogulSyndicationFeedOrderBy.prototype.PLAYLIST_ID_DESC = "-playlistId";
KalturaTubeMogulSyndicationFeedOrderBy.prototype.NAME_ASC = "+name";
KalturaTubeMogulSyndicationFeedOrderBy.prototype.NAME_DESC = "-name";
KalturaTubeMogulSyndicationFeedOrderBy.prototype.TYPE_ASC = "+type";
KalturaTubeMogulSyndicationFeedOrderBy.prototype.TYPE_DESC = "-type";
KalturaTubeMogulSyndicationFeedOrderBy.prototype.CREATED_AT_ASC = "+createdAt";
KalturaTubeMogulSyndicationFeedOrderBy.prototype.CREATED_AT_DESC = "-createdAt";

function KalturaUiConfCreationMode()
{
}
KalturaUiConfCreationMode.prototype.WIZARD = 2;
KalturaUiConfCreationMode.prototype.ADVANCED = 3;

function KalturaUiConfObjType()
{
}
KalturaUiConfObjType.prototype.PLAYER = 1;
KalturaUiConfObjType.prototype.CONTRIBUTION_WIZARD = 2;
KalturaUiConfObjType.prototype.SIMPLE_EDITOR = 3;
KalturaUiConfObjType.prototype.ADVANCED_EDITOR = 4;
KalturaUiConfObjType.prototype.PLAYLIST = 5;
KalturaUiConfObjType.prototype.APP_STUDIO = 6;

function KalturaUiConfOrderBy()
{
}
KalturaUiConfOrderBy.prototype.CREATED_AT_ASC = "+createdAt";
KalturaUiConfOrderBy.prototype.CREATED_AT_DESC = "-createdAt";

function KalturaUploadErrorCode()
{
}
KalturaUploadErrorCode.prototype.NO_ERROR = 0;
KalturaUploadErrorCode.prototype.GENERAL_ERROR = 1;
KalturaUploadErrorCode.prototype.PARTIAL_UPLOAD = 2;

function KalturaUserOrderBy()
{
}
KalturaUserOrderBy.prototype.CREATED_AT_ASC = "+createdAt";
KalturaUserOrderBy.prototype.CREATED_AT_DESC = "-createdAt";

function KalturaUserStatus()
{
}
KalturaUserStatus.prototype.BLOCKED = 0;
KalturaUserStatus.prototype.ACTIVE = 1;
KalturaUserStatus.prototype.DELETED = 2;

function KalturaVideoCodec()
{
}
KalturaVideoCodec.prototype.NONE = "";
KalturaVideoCodec.prototype.VP6 = "vp6";
KalturaVideoCodec.prototype.H263 = "h263";
KalturaVideoCodec.prototype.H264 = "h264";
KalturaVideoCodec.prototype.FLV = "flv";

function KalturaWidgetOrderBy()
{
}
KalturaWidgetOrderBy.prototype.CREATED_AT_ASC = "+createdAt";
KalturaWidgetOrderBy.prototype.CREATED_AT_DESC = "-createdAt";

function KalturaWidgetSecurityType()
{
}
KalturaWidgetSecurityType.prototype.NONE = 1;
KalturaWidgetSecurityType.prototype.TIMEHASH = 2;

function KalturaYahooSyndicationFeedAdultValues()
{
}
KalturaYahooSyndicationFeedAdultValues.prototype.ADULT = "adult";
KalturaYahooSyndicationFeedAdultValues.prototype.NON_ADULT = "nonadult";

function KalturaYahooSyndicationFeedCategories()
{
}
KalturaYahooSyndicationFeedCategories.prototype.ACTION = "Action";
KalturaYahooSyndicationFeedCategories.prototype.ART_AND_ANIMATION = "Art &amp; Animation";
KalturaYahooSyndicationFeedCategories.prototype.ENTERTAINMENT_AND_TV = "Entertainment &amp; TV";
KalturaYahooSyndicationFeedCategories.prototype.FOOD = "Food";
KalturaYahooSyndicationFeedCategories.prototype.GAMES = "Games";
KalturaYahooSyndicationFeedCategories.prototype.HOW_TO = "How-To";
KalturaYahooSyndicationFeedCategories.prototype.MUSIC = "Music";
KalturaYahooSyndicationFeedCategories.prototype.PEOPLE_AND_VLOGS = "People &amp; Vlogs";
KalturaYahooSyndicationFeedCategories.prototype.SCIENCE_AND_ENVIRONMENT = "Science &amp; Environment";
KalturaYahooSyndicationFeedCategories.prototype.TRANSPORTATION = "Transportation";
KalturaYahooSyndicationFeedCategories.prototype.ANIMALS = "Animals";
KalturaYahooSyndicationFeedCategories.prototype.COMMERCIALS = "Commercials";
KalturaYahooSyndicationFeedCategories.prototype.FAMILY = "Family";
KalturaYahooSyndicationFeedCategories.prototype.FUNNY_VIDEOS = "Funny Videos";
KalturaYahooSyndicationFeedCategories.prototype.HEALTH_AND_BEAUTY = "Health &amp; Beauty";
KalturaYahooSyndicationFeedCategories.prototype.MOVIES_AND_SHORTS = "Movies &amp; Shorts";
KalturaYahooSyndicationFeedCategories.prototype.NEWS_AND_POLITICS = "News &amp; Politics";
KalturaYahooSyndicationFeedCategories.prototype.PRODUCTS_AND_TECH = "Products &amp; Tech.";
KalturaYahooSyndicationFeedCategories.prototype.SPORTS = "Sports";
KalturaYahooSyndicationFeedCategories.prototype.TRAVEL = "Travel";

function KalturaYahooSyndicationFeedOrderBy()
{
}
KalturaYahooSyndicationFeedOrderBy.prototype.PLAYLIST_ID_ASC = "+playlistId";
KalturaYahooSyndicationFeedOrderBy.prototype.PLAYLIST_ID_DESC = "-playlistId";
KalturaYahooSyndicationFeedOrderBy.prototype.NAME_ASC = "+name";
KalturaYahooSyndicationFeedOrderBy.prototype.NAME_DESC = "-name";
KalturaYahooSyndicationFeedOrderBy.prototype.TYPE_ASC = "+type";
KalturaYahooSyndicationFeedOrderBy.prototype.TYPE_DESC = "-type";
KalturaYahooSyndicationFeedOrderBy.prototype.CREATED_AT_ASC = "+createdAt";
KalturaYahooSyndicationFeedOrderBy.prototype.CREATED_AT_DESC = "-createdAt";

function KalturaAccessControl()
{
}
KalturaAccessControl.prototype = new KalturaObjectBase();
/**
 * The id of the Access Control Profile
	 * 
 *
 * @var int
 * @readonly
 */
KalturaAccessControl.prototype.id = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaAccessControl.prototype.partnerId = null;

/**
 * The name of the Access Control Profile
	 * 
 *
 * @var string
 */
KalturaAccessControl.prototype.name = null;

/**
 * The description of the Access Control Profile
	 * 
 *
 * @var string
 */
KalturaAccessControl.prototype.description = null;

/**
 * Creation date as Unix timestamp (In seconds) 
	 * 
 *
 * @var int
 * @readonly
 */
KalturaAccessControl.prototype.createdAt = null;

/**
 * True if this Conversion Profile is the default
	 * 
 *
 * @var KalturaNullableBoolean
 */
KalturaAccessControl.prototype.isDefault = null;

/**
 * Array of Access Control Restrictions
	 * 
 *
 * @var KalturaRestrictionArray
 */
KalturaAccessControl.prototype.restrictions = null;


function KalturaFilter()
{
}
KalturaFilter.prototype = new KalturaObjectBase();
/**
 * 
 *
 * @var string
 */
KalturaFilter.prototype.orderBy = null;


function KalturaAccessControlFilter()
{
}
KalturaAccessControlFilter.prototype = new KalturaFilter();
/**
 * 
 *
 * @var int
 */
KalturaAccessControlFilter.prototype.idEqual = null;

/**
 * 
 *
 * @var string
 */
KalturaAccessControlFilter.prototype.idIn = null;

/**
 * 
 *
 * @var int
 */
KalturaAccessControlFilter.prototype.createdAtGreaterThanOrEqual = null;

/**
 * 
 *
 * @var int
 */
KalturaAccessControlFilter.prototype.createdAtLessThanOrEqual = null;


function KalturaAccessControlListResponse()
{
}
KalturaAccessControlListResponse.prototype = new KalturaObjectBase();
/**
 * 
 *
 * @var KalturaAccessControlArray
 * @readonly
 */
KalturaAccessControlListResponse.prototype.objects = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaAccessControlListResponse.prototype.totalCount = null;


function KalturaAdminUser()
{
}
KalturaAdminUser.prototype = new KalturaObjectBase();
/**
 * 
 *
 * @var string
 * @readonly
 */
KalturaAdminUser.prototype.password = null;

/**
 * 
 *
 * @var string
 * @readonly
 */
KalturaAdminUser.prototype.email = null;

/**
 * 
 *
 * @var string
 */
KalturaAdminUser.prototype.screenName = null;


function KalturaBaseEntry()
{
}
KalturaBaseEntry.prototype = new KalturaObjectBase();
/**
 * Auto generated 10 characters alphanumeric string
	 * 
 *
 * @var string
 * @readonly
 */
KalturaBaseEntry.prototype.id = null;

/**
 * Entry name (Min 1 chars)
	 * 
 *
 * @var string
 */
KalturaBaseEntry.prototype.name = null;

/**
 * Entry description
	 * 
 *
 * @var string
 */
KalturaBaseEntry.prototype.description = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaBaseEntry.prototype.partnerId = null;

/**
 * The ID of the user who is the owner of this entry 
	 * 
 *
 * @var string
 */
KalturaBaseEntry.prototype.userId = null;

/**
 * Entry tags
	 * 
 *
 * @var string
 */
KalturaBaseEntry.prototype.tags = null;

/**
 * Entry admin tags can be updated only by administrators
	 * 
 *
 * @var string
 */
KalturaBaseEntry.prototype.adminTags = null;

/**
 * 
 *
 * @var string
 */
KalturaBaseEntry.prototype.categories = null;

/**
 * 
 *
 * @var KalturaEntryStatus
 * @readonly
 */
KalturaBaseEntry.prototype.status = null;

/**
 * Entry moderation status
	 * 
 *
 * @var KalturaEntryModerationStatus
 * @readonly
 */
KalturaBaseEntry.prototype.moderationStatus = null;

/**
 * Number of moderation requests waiting for this entry
	 * 
 *
 * @var int
 * @readonly
 */
KalturaBaseEntry.prototype.moderationCount = null;

/**
 * The type of the entry, this is auto filled by the derived entry object
	 * 
 *
 * @var KalturaEntryType
 * @readonly
 */
KalturaBaseEntry.prototype.type = null;

/**
 * Entry creation date as Unix timestamp (In seconds)
	 * 
 *
 * @var int
 * @readonly
 */
KalturaBaseEntry.prototype.createdAt = null;

/**
 * Calculated rank
	 * 
 *
 * @var float
 * @readonly
 */
KalturaBaseEntry.prototype.rank = null;

/**
 * The total (sum) of all votes
	 * 
 *
 * @var int
 * @readonly
 */
KalturaBaseEntry.prototype.totalRank = null;

/**
 * Number of votes
	 * 
 *
 * @var int
 * @readonly
 */
KalturaBaseEntry.prototype.votes = null;

/**
 * 
 *
 * @var int
 */
KalturaBaseEntry.prototype.groupId = null;

/**
 * Can be used to store various partner related data as a string 
	 * 
 *
 * @var string
 */
KalturaBaseEntry.prototype.partnerData = null;

/**
 * Download URL for the entry
	 * 
 *
 * @var string
 * @readonly
 */
KalturaBaseEntry.prototype.downloadUrl = null;

/**
 * Indexed search text for full text search
 *
 * @var string
 * @readonly
 */
KalturaBaseEntry.prototype.searchText = null;

/**
 * License type used for this entry
	 * 
 *
 * @var KalturaLicenseType
 */
KalturaBaseEntry.prototype.licenseType = null;

/**
 * Version of the entry data
 *
 * @var int
 * @readonly
 */
KalturaBaseEntry.prototype.version = null;

/**
 * Thumbnail URL
	 * 
 *
 * @var string
 * @readonly
 */
KalturaBaseEntry.prototype.thumbnailUrl = null;

/**
 * The Access Control ID assigned to this entry (null when not set, send -1 to remove)  
	 * 
 *
 * @var int
 */
KalturaBaseEntry.prototype.accessControlId = null;

/**
 * Entry scheduling start date (null when not set, send -1 to remove)
	 * 
 *
 * @var int
 */
KalturaBaseEntry.prototype.startDate = null;

/**
 * Entry scheduling end date (null when not set, send -1 to remove)
	 * 
 *
 * @var int
 */
KalturaBaseEntry.prototype.endDate = null;


function KalturaBaseEntryFilter()
{
}
KalturaBaseEntryFilter.prototype = new KalturaFilter();
/**
 * This filter should be in use for retrieving only a specific entry (identified by its entryId).
	 * @var string
 *
 * @var string
 */
KalturaBaseEntryFilter.prototype.idEqual = null;

/**
 * This filter should be in use for retrieving few specific entries (string should include comma separated list of entryId strings).
	 * @var string
 *
 * @var string
 */
KalturaBaseEntryFilter.prototype.idIn = null;

/**
 * This filter should be in use for retrieving specific entries while applying an SQL 'LIKE' pattern matching on entry names. It should include only one pattern for matching entry names against.
	 * @var string
 *
 * @var string
 */
KalturaBaseEntryFilter.prototype.nameLike = null;

/**
 * This filter should be in use for retrieving specific entries, while applying an SQL 'LIKE' pattern matching on entry names. It could include few (comma separated) patterns for matching entry names against, while applying an OR logic to retrieve entries that match at least one input pattern.
	 * @var string
 *
 * @var string
 */
KalturaBaseEntryFilter.prototype.nameMultiLikeOr = null;

/**
 * This filter should be in use for retrieving specific entries, while applying an SQL 'LIKE' pattern matching on entry names. It could include few (comma separated) patterns for matching entry names against, while applying an AND logic to retrieve entries that match all input patterns.
	 * @var string
 *
 * @var string
 */
KalturaBaseEntryFilter.prototype.nameMultiLikeAnd = null;

/**
 * This filter should be in use for retrieving entries with a specific name.
	 * @var string
 *
 * @var string
 */
KalturaBaseEntryFilter.prototype.nameEqual = null;

/**
 * This filter should be in use for retrieving only entries which were uploaded by/assigned to users of a specific Kaltura Partner (identified by Partner ID).
	 * @var int
 *
 * @var int
 */
KalturaBaseEntryFilter.prototype.partnerIdEqual = null;

/**
 * This filter should be in use for retrieving only entries within Kaltura network which were uploaded by/assigned to users of few Kaltura Partners  (string should include comma separated list of PartnerIDs)
	 * @var string
 *
 * @var string
 */
KalturaBaseEntryFilter.prototype.partnerIdIn = null;

/**
 * This filter parameter should be in use for retrieving only entries, uploaded by/assigned to a specific user (identified by user Id).
	 * @var string
 *
 * @var string
 */
KalturaBaseEntryFilter.prototype.userIdEqual = null;

/**
 * This filter should be in use for retrieving specific entries while applying an SQL 'LIKE' pattern matching on entry tags. It should include only one pattern for matching entry tags against.
	 * @var string
 *
 * @var string
 */
KalturaBaseEntryFilter.prototype.tagsLike = null;

/**
 * This filter should be in use for retrieving specific entries, while applying an SQL 'LIKE' pattern matching on tags.  It could include few (comma separated) patterns for matching entry tags against, while applying an OR logic to retrieve entries that match at least one input pattern.
	 * @var string
 *
 * @var string
 */
KalturaBaseEntryFilter.prototype.tagsMultiLikeOr = null;

/**
 * This filter should be in use for retrieving specific entries, while applying an SQL 'LIKE' pattern matching on tags.  It could include few (comma separated) patterns for matching entry tags against, while applying an AND logic to retrieve entries that match all input patterns.
	 * @var string
 *
 * @var string
 */
KalturaBaseEntryFilter.prototype.tagsMultiLikeAnd = null;

/**
 * This filter should be in use for retrieving specific entries while applying an SQL 'LIKE' pattern matching on entry tags, set by an ADMIN user. It should include only one pattern for matching entry tags against.
	 * @var string
 *
 * @var string
 */
KalturaBaseEntryFilter.prototype.adminTagsLike = null;

/**
 * This filter should be in use for retrieving specific entries, while applying an SQL 'LIKE' pattern matching on tags, set by an ADMIN user.  It could include few (comma separated) patterns for matching entry tags against, while applying an OR logic to retrieve entries that match at least one input pattern.
	 * @var string
 *
 * @var string
 */
KalturaBaseEntryFilter.prototype.adminTagsMultiLikeOr = null;

/**
 * This filter should be in use for retrieving specific entries, while applying an SQL 'LIKE' pattern matching on tags, set by an ADMIN user.  It could include few (comma separated) patterns for matching entry tags against, while applying an AND logic to retrieve entries that match all input patterns.
	 * @var string
 *
 * @var string
 */
KalturaBaseEntryFilter.prototype.adminTagsMultiLikeAnd = null;

/**
 * 
 *
 * @var string
 */
KalturaBaseEntryFilter.prototype.categoriesMatchAnd = null;

/**
 * 
 *
 * @var string
 */
KalturaBaseEntryFilter.prototype.categoriesMatchOr = null;

/**
 * This filter should be in use for retrieving only entries, at a specific {@link ?object=KalturaEntryStatus KalturaEntryStatus}.
	 * @var KalturaEntryStatus
 *
 * @var KalturaEntryStatus
 */
KalturaBaseEntryFilter.prototype.statusEqual = null;

/**
 * This filter should be in use for retrieving only entries, not at a specific {@link ?object=KalturaEntryStatus KalturaEntryStatus}.
	 * @var KalturaEntryStatus
 *
 * @var KalturaEntryStatus
 */
KalturaBaseEntryFilter.prototype.statusNotEqual = null;

/**
 * This filter should be in use for retrieving only entries, at few specific {@link ?object=KalturaEntryStatus KalturaEntryStatus} (comma separated).
	 * @var string
 *
 * @var string
 */
KalturaBaseEntryFilter.prototype.statusIn = null;

/**
 * This filter should be in use for retrieving only entries, not at few specific {@link ?object=KalturaEntryStatus KalturaEntryStatus} (comma separated).
	 * @var KalturaEntryStatus
 *
 * @var KalturaEntryStatus
 */
KalturaBaseEntryFilter.prototype.statusNotIn = null;

/**
 * 
 *
 * @var KalturaEntryModerationStatus
 */
KalturaBaseEntryFilter.prototype.moderationStatusEqual = null;

/**
 * 
 *
 * @var KalturaEntryModerationStatus
 */
KalturaBaseEntryFilter.prototype.moderationStatusNotEqual = null;

/**
 * 
 *
 * @var string
 */
KalturaBaseEntryFilter.prototype.moderationStatusIn = null;

/**
 * 
 *
 * @var KalturaEntryModerationStatus
 */
KalturaBaseEntryFilter.prototype.moderationStatusNotIn = null;

/**
 * 
 *
 * @var KalturaEntryType
 */
KalturaBaseEntryFilter.prototype.typeEqual = null;

/**
 * This filter should be in use for retrieving entries of few {@link ?object=KalturaEntryType KalturaEntryType} (string should include a comma separated list of {@link ?object=KalturaEntryType KalturaEntryType} enumerated parameters).
	 * @var string
 *
 * @var string
 */
KalturaBaseEntryFilter.prototype.typeIn = null;

/**
 * This filter parameter should be in use for retrieving only entries which were created at Kaltura system after a specific time/date (standard timestamp format).
	 * @var int
 *
 * @var int
 */
KalturaBaseEntryFilter.prototype.createdAtGreaterThanOrEqual = null;

/**
 * This filter parameter should be in use for retrieving only entries which were created at Kaltura system before a specific time/date (standard timestamp format).
	 * @var int
 *
 * @var int
 */
KalturaBaseEntryFilter.prototype.createdAtLessThanOrEqual = null;

/**
 * 
 *
 * @var int
 */
KalturaBaseEntryFilter.prototype.groupIdEqual = null;

/**
 * This filter should be in use for retrieving specific entries while search match the input string within all of the following metadata attributes: name, description, tags, adminTags.
	 * @var string
 *
 * @var string
 */
KalturaBaseEntryFilter.prototype.searchTextMatchAnd = null;

/**
 * This filter should be in use for retrieving specific entries while search match the input string within at least one of the following metadata attributes: name, description, tags, adminTags.
	 * @var string
 *
 * @var string
 */
KalturaBaseEntryFilter.prototype.searchTextMatchOr = null;

/**
 * 
 *
 * @var int
 */
KalturaBaseEntryFilter.prototype.accessControlIdEqual = null;

/**
 * 
 *
 * @var string
 */
KalturaBaseEntryFilter.prototype.accessControlIdIn = null;

/**
 * 
 *
 * @var int
 */
KalturaBaseEntryFilter.prototype.startDateGreaterThanOrEqual = null;

/**
 * 
 *
 * @var int
 */
KalturaBaseEntryFilter.prototype.startDateLessThanOrEqual = null;

/**
 * 
 *
 * @var int
 */
KalturaBaseEntryFilter.prototype.startDateGreaterThanOrEqualOrNull = null;

/**
 * 
 *
 * @var int
 */
KalturaBaseEntryFilter.prototype.startDateLessThanOrEqualOrNull = null;

/**
 * 
 *
 * @var int
 */
KalturaBaseEntryFilter.prototype.endDateGreaterThanOrEqual = null;

/**
 * 
 *
 * @var int
 */
KalturaBaseEntryFilter.prototype.endDateLessThanOrEqual = null;

/**
 * 
 *
 * @var int
 */
KalturaBaseEntryFilter.prototype.endDateGreaterThanOrEqualOrNull = null;

/**
 * 
 *
 * @var int
 */
KalturaBaseEntryFilter.prototype.endDateLessThanOrEqualOrNull = null;

/**
 * 
 *
 * @var string
 */
KalturaBaseEntryFilter.prototype.tagsNameMultiLikeOr = null;

/**
 * 
 *
 * @var string
 */
KalturaBaseEntryFilter.prototype.tagsAdminTagsMultiLikeOr = null;

/**
 * 
 *
 * @var string
 */
KalturaBaseEntryFilter.prototype.tagsAdminTagsNameMultiLikeOr = null;

/**
 * 
 *
 * @var string
 */
KalturaBaseEntryFilter.prototype.tagsNameMultiLikeAnd = null;

/**
 * 
 *
 * @var string
 */
KalturaBaseEntryFilter.prototype.tagsAdminTagsMultiLikeAnd = null;

/**
 * 
 *
 * @var string
 */
KalturaBaseEntryFilter.prototype.tagsAdminTagsNameMultiLikeAnd = null;


function KalturaBaseEntryListResponse()
{
}
KalturaBaseEntryListResponse.prototype = new KalturaObjectBase();
/**
 * 
 *
 * @var KalturaBaseEntryArray
 * @readonly
 */
KalturaBaseEntryListResponse.prototype.objects = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaBaseEntryListResponse.prototype.totalCount = null;


function KalturaBaseJob()
{
}
KalturaBaseJob.prototype = new KalturaObjectBase();
/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaBaseJob.prototype.id = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaBaseJob.prototype.partnerId = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaBaseJob.prototype.createdAt = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaBaseJob.prototype.updatedAt = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaBaseJob.prototype.processorExpiration = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaBaseJob.prototype.executionAttempts = null;


function KalturaBaseJobFilter()
{
}
KalturaBaseJobFilter.prototype = new KalturaFilter();
/**
 * 
 *
 * @var int
 */
KalturaBaseJobFilter.prototype.idEqual = null;

/**
 * 
 *
 * @var int
 */
KalturaBaseJobFilter.prototype.idGreaterThanOrEqual = null;

/**
 * 
 *
 * @var int
 */
KalturaBaseJobFilter.prototype.partnerIdEqual = null;

/**
 * 
 *
 * @var string
 */
KalturaBaseJobFilter.prototype.partnerIdIn = null;

/**
 * 
 *
 * @var int
 */
KalturaBaseJobFilter.prototype.createdAtGreaterThanOrEqual = null;

/**
 * 
 *
 * @var int
 */
KalturaBaseJobFilter.prototype.createdAtLessThanOrEqual = null;


function KalturaBaseRestriction()
{
}
KalturaBaseRestriction.prototype = new KalturaObjectBase();

function KalturaBaseSyndicationFeed()
{
}
KalturaBaseSyndicationFeed.prototype = new KalturaObjectBase();
/**
 * 
 *
 * @var string
 * @readonly
 */
KalturaBaseSyndicationFeed.prototype.id = null;

/**
 * 
 *
 * @var string
 * @readonly
 */
KalturaBaseSyndicationFeed.prototype.feedUrl = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaBaseSyndicationFeed.prototype.partnerId = null;

/**
 * link a playlist that will set what content the feed will include
	 * if empty, all content will be included in feed
	 * 
 *
 * @var string
 */
KalturaBaseSyndicationFeed.prototype.playlistId = null;

/**
 * feed name
	 * 
 *
 * @var string
 */
KalturaBaseSyndicationFeed.prototype.name = null;

/**
 * feed status
	 * 
 *
 * @var KalturaSyndicationFeedStatus
 * @readonly
 */
KalturaBaseSyndicationFeed.prototype.status = null;

/**
 * feed type
	 * 
 *
 * @var KalturaSyndicationFeedType
 * @readonly
 */
KalturaBaseSyndicationFeed.prototype.type = null;

/**
 * Base URL for each video, on the partners site
	 * This is required by all syndication types.
 *
 * @var string
 */
KalturaBaseSyndicationFeed.prototype.landingPage = null;

/**
 * Creation date as Unix timestamp (In seconds)
	 * 
 *
 * @var int
 * @readonly
 */
KalturaBaseSyndicationFeed.prototype.createdAt = null;

/**
 * allow_embed tells google OR yahoo weather to allow embedding the video on google OR yahoo video results
	 * or just to provide a link to the landing page.
	 * it is applied on the video-player_loc property in the XML (google)
	 * and addes media-player tag (yahoo)
 *
 * @var bool
 */
KalturaBaseSyndicationFeed.prototype.allowEmbed = null;

/**
 * Select a uiconf ID as player skin to include in the kwidget url
 *
 * @var int
 */
KalturaBaseSyndicationFeed.prototype.playerUiconfId = null;

/**
 * 
 *
 * @var int
 */
KalturaBaseSyndicationFeed.prototype.flavorParamId = null;

/**
 * 
 *
 * @var bool
 */
KalturaBaseSyndicationFeed.prototype.transcodeExistingContent = null;

/**
 * 
 *
 * @var bool
 */
KalturaBaseSyndicationFeed.prototype.addToDefaultConversionProfile = null;

/**
 * 
 *
 * @var string
 */
KalturaBaseSyndicationFeed.prototype.categories = null;


function KalturaBaseSyndicationFeedFilter()
{
}
KalturaBaseSyndicationFeedFilter.prototype = new KalturaFilter();

function KalturaBaseSyndicationFeedListResponse()
{
}
KalturaBaseSyndicationFeedListResponse.prototype = new KalturaObjectBase();
/**
 * 
 *
 * @var KalturaBaseSyndicationFeedArray
 * @readonly
 */
KalturaBaseSyndicationFeedListResponse.prototype.objects = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaBaseSyndicationFeedListResponse.prototype.totalCount = null;


function KalturaBatchJob()
{
}
KalturaBatchJob.prototype = new KalturaBaseJob();
/**
 * 
 *
 * @var string
 */
KalturaBatchJob.prototype.entryId = null;

/**
 * 
 *
 * @var KalturaBatchJobType
 * @readonly
 */
KalturaBatchJob.prototype.jobType = null;

/**
 * 
 *
 * @var int
 */
KalturaBatchJob.prototype.jobSubType = null;

/**
 * 
 *
 * @var int
 */
KalturaBatchJob.prototype.onStressDivertTo = null;

/**
 * 
 *
 * @var KalturaJobData
 */
KalturaBatchJob.prototype.data = null;

/**
 * 
 *
 * @var KalturaBatchJobStatus
 */
KalturaBatchJob.prototype.status = null;

/**
 * 
 *
 * @var int
 */
KalturaBatchJob.prototype.abort = null;

/**
 * 
 *
 * @var int
 */
KalturaBatchJob.prototype.checkAgainTimeout = null;

/**
 * 
 *
 * @var int
 */
KalturaBatchJob.prototype.progress = null;

/**
 * 
 *
 * @var string
 */
KalturaBatchJob.prototype.message = null;

/**
 * 
 *
 * @var string
 */
KalturaBatchJob.prototype.description = null;

/**
 * 
 *
 * @var int
 */
KalturaBatchJob.prototype.updatesCount = null;

/**
 * 
 *
 * @var int
 */
KalturaBatchJob.prototype.priority = null;

/**
 * 
 *
 * @var int
 */
KalturaBatchJob.prototype.workGroupId = null;

/**
 * The id of identical job
 *
 * @var int
 */
KalturaBatchJob.prototype.twinJobId = null;

/**
 * The id of the bulk upload job that initiated this job
 *
 * @var int
 */
KalturaBatchJob.prototype.bulkJobId = null;

/**
 * When one job creates another - the parent should set this parentJobId to be its own id.
 *
 * @var int
 */
KalturaBatchJob.prototype.parentJobId = null;

/**
 * The id of the root parent job
 *
 * @var int
 */
KalturaBatchJob.prototype.rootJobId = null;

/**
 * The time that the job was pulled from the queue
 *
 * @var int
 */
KalturaBatchJob.prototype.queueTime = null;

/**
 * The time that the job was finished or closed as failed
 *
 * @var int
 */
KalturaBatchJob.prototype.finishTime = null;

/**
 * 
 *
 * @var KalturaBatchJobErrorTypes
 */
KalturaBatchJob.prototype.errType = null;

/**
 * 
 *
 * @var int
 */
KalturaBatchJob.prototype.errNumber = null;

/**
 * 
 *
 * @var int
 */
KalturaBatchJob.prototype.fileSize = null;

/**
 * 
 *
 * @var bool
 */
KalturaBatchJob.prototype.lastWorkerRemote = null;


function KalturaBatchJobFilter()
{
}
KalturaBatchJobFilter.prototype = new KalturaBaseJobFilter();
/**
 * 
 *
 * @var string
 */
KalturaBatchJobFilter.prototype.entryIdEqual = null;

/**
 * 
 *
 * @var KalturaBatchJobType
 */
KalturaBatchJobFilter.prototype.jobTypeEqual = null;

/**
 * 
 *
 * @var string
 */
KalturaBatchJobFilter.prototype.jobTypeIn = null;

/**
 * 
 *
 * @var int
 */
KalturaBatchJobFilter.prototype.jobSubTypeEqual = null;

/**
 * 
 *
 * @var string
 */
KalturaBatchJobFilter.prototype.jobSubTypeIn = null;

/**
 * 
 *
 * @var string
 */
KalturaBatchJobFilter.prototype.onStressDivertToIn = null;

/**
 * 
 *
 * @var KalturaBatchJobStatus
 */
KalturaBatchJobFilter.prototype.statusEqual = null;

/**
 * 
 *
 * @var string
 */
KalturaBatchJobFilter.prototype.statusIn = null;

/**
 * 
 *
 * @var string
 */
KalturaBatchJobFilter.prototype.workGroupIdIn = null;

/**
 * 
 *
 * @var int
 */
KalturaBatchJobFilter.prototype.queueTimeGreaterThanOrEqual = null;

/**
 * 
 *
 * @var int
 */
KalturaBatchJobFilter.prototype.queueTimeLessThanOrEqual = null;

/**
 * 
 *
 * @var int
 */
KalturaBatchJobFilter.prototype.finishTimeGreaterThanOrEqual = null;

/**
 * 
 *
 * @var int
 */
KalturaBatchJobFilter.prototype.finishTimeLessThanOrEqual = null;

/**
 * 
 *
 * @var string
 */
KalturaBatchJobFilter.prototype.errTypeIn = null;

/**
 * 
 *
 * @var int
 */
KalturaBatchJobFilter.prototype.fileSizeLessThan = null;

/**
 * 
 *
 * @var int
 */
KalturaBatchJobFilter.prototype.fileSizeGreaterThan = null;


function KalturaBatchJobListResponse()
{
}
KalturaBatchJobListResponse.prototype = new KalturaObjectBase();
/**
 * 
 *
 * @var KalturaBatchJobArray
 * @readonly
 */
KalturaBatchJobListResponse.prototype.objects = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaBatchJobListResponse.prototype.totalCount = null;


function KalturaBatchJobResponse()
{
}
KalturaBatchJobResponse.prototype = new KalturaObjectBase();
/**
 * The main batch job
	 * 
 *
 * @var KalturaBatchJob
 */
KalturaBatchJobResponse.prototype.batchJob = null;

/**
 * All batch jobs that reference the main job as root
	 * 
 *
 * @var KalturaBatchJobArray
 */
KalturaBatchJobResponse.prototype.childBatchJobs = null;


function KalturaJobData()
{
}
KalturaJobData.prototype = new KalturaObjectBase();

function KalturaBulkDownloadJobData()
{
}
KalturaBulkDownloadJobData.prototype = new KalturaJobData();
/**
 * Comma separated list of entry ids
	 * 
 *
 * @var string
 */
KalturaBulkDownloadJobData.prototype.entryIds = null;

/**
 * Flavor params id to use for conversion
	 * 
 *
 * @var int
 */
KalturaBulkDownloadJobData.prototype.flavorParamsId = null;

/**
 * The id of the requesting user
	 * 
 *
 * @var string
 */
KalturaBulkDownloadJobData.prototype.puserId = null;


function KalturaBulkUpload()
{
}
KalturaBulkUpload.prototype = new KalturaObjectBase();
/**
 * 
 *
 * @var int
 */
KalturaBulkUpload.prototype.id = null;

/**
 * 
 *
 * @var string
 */
KalturaBulkUpload.prototype.uploadedBy = null;

/**
 * 
 *
 * @var int
 */
KalturaBulkUpload.prototype.uploadedOn = null;

/**
 * 
 *
 * @var int
 */
KalturaBulkUpload.prototype.numOfEntries = null;

/**
 * 
 *
 * @var KalturaBatchJobStatus
 */
KalturaBulkUpload.prototype.status = null;

/**
 * 
 *
 * @var string
 */
KalturaBulkUpload.prototype.logFileUrl = null;

/**
 * 
 *
 * @var string
 */
KalturaBulkUpload.prototype.csvFileUrl = null;

/**
 * 
 *
 * @var KalturaBulkUploadResultArray
 */
KalturaBulkUpload.prototype.results = null;


function KalturaBulkUploadJobData()
{
}
KalturaBulkUploadJobData.prototype = new KalturaJobData();
/**
 * 
 *
 * @var int
 */
KalturaBulkUploadJobData.prototype.userId = null;

/**
 * The screen name of the user
	 * 
 *
 * @var string
 */
KalturaBulkUploadJobData.prototype.uploadedBy = null;

/**
 * Selected profile id for all bulk entries
	 * 
 *
 * @var int
 */
KalturaBulkUploadJobData.prototype.conversionProfileId = null;

/**
 * Created by the API
	 * 
 *
 * @var string
 */
KalturaBulkUploadJobData.prototype.csvFilePath = null;

/**
 * Created by the API
	 * 
 *
 * @var string
 */
KalturaBulkUploadJobData.prototype.resultsFileLocalPath = null;

/**
 * Created by the API
	 * 
 *
 * @var string
 */
KalturaBulkUploadJobData.prototype.resultsFileUrl = null;

/**
 * Number of created entries
	 * 
 *
 * @var int
 */
KalturaBulkUploadJobData.prototype.numOfEntries = null;

/**
 * The version of the csv file
	 * 
 *
 * @var KalturaBulkUploadCsvVersion
 */
KalturaBulkUploadJobData.prototype.csvVersion = null;


function KalturaBulkUploadListResponse()
{
}
KalturaBulkUploadListResponse.prototype = new KalturaObjectBase();
/**
 * 
 *
 * @var KalturaBulkUploads
 * @readonly
 */
KalturaBulkUploadListResponse.prototype.objects = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaBulkUploadListResponse.prototype.totalCount = null;


function KalturaBulkUploadResult()
{
}
KalturaBulkUploadResult.prototype = new KalturaObjectBase();
/**
 * The id of the result
	 * 
 *
 * @var int
 * @readonly
 */
KalturaBulkUploadResult.prototype.id = null;

/**
 * The id of the parent job
	 * 
 *
 * @var int
 */
KalturaBulkUploadResult.prototype.bulkUploadJobId = null;

/**
 * The index of the line in the CSV
	 * 
 *
 * @var int
 */
KalturaBulkUploadResult.prototype.lineIndex = null;

/**
 * 
 *
 * @var int
 */
KalturaBulkUploadResult.prototype.partnerId = null;

/**
 * 
 *
 * @var string
 */
KalturaBulkUploadResult.prototype.entryId = null;

/**
 * 
 *
 * @var int
 */
KalturaBulkUploadResult.prototype.entryStatus = null;

/**
 * The data as recieved in the csv
	 * 
 *
 * @var string
 */
KalturaBulkUploadResult.prototype.rowData = null;

/**
 * 
 *
 * @var string
 */
KalturaBulkUploadResult.prototype.title = null;

/**
 * 
 *
 * @var string
 */
KalturaBulkUploadResult.prototype.description = null;

/**
 * 
 *
 * @var string
 */
KalturaBulkUploadResult.prototype.tags = null;

/**
 * 
 *
 * @var string
 */
KalturaBulkUploadResult.prototype.url = null;

/**
 * 
 *
 * @var string
 */
KalturaBulkUploadResult.prototype.contentType = null;

/**
 * 
 *
 * @var int
 */
KalturaBulkUploadResult.prototype.conversionProfileId = null;

/**
 * 
 *
 * @var int
 */
KalturaBulkUploadResult.prototype.accessControlProfileId = null;

/**
 * 
 *
 * @var string
 */
KalturaBulkUploadResult.prototype.category = null;

/**
 * 
 *
 * @var int
 */
KalturaBulkUploadResult.prototype.scheduleStartDate = null;

/**
 * 
 *
 * @var int
 */
KalturaBulkUploadResult.prototype.scheduleEndDate = null;

/**
 * 
 *
 * @var string
 */
KalturaBulkUploadResult.prototype.thumbnailUrl = null;

/**
 * 
 *
 * @var bool
 */
KalturaBulkUploadResult.prototype.thumbnailSaved = null;

/**
 * 
 *
 * @var string
 */
KalturaBulkUploadResult.prototype.partnerData = null;

/**
 * 
 *
 * @var string
 */
KalturaBulkUploadResult.prototype.errorDescription = null;


function KalturaCEError()
{
}
KalturaCEError.prototype = new KalturaObjectBase();
/**
 * 
 *
 * @var string
 * @readonly
 */
KalturaCEError.prototype.id = null;

/**
 * 
 *
 * @var int
 */
KalturaCEError.prototype.partnerId = null;

/**
 * 
 *
 * @var string
 */
KalturaCEError.prototype.browser = null;

/**
 * 
 *
 * @var string
 */
KalturaCEError.prototype.serverIp = null;

/**
 * 
 *
 * @var string
 */
KalturaCEError.prototype.serverOs = null;

/**
 * 
 *
 * @var string
 */
KalturaCEError.prototype.phpVersion = null;

/**
 * 
 *
 * @var string
 */
KalturaCEError.prototype.ceAdminEmail = null;

/**
 * 
 *
 * @var string
 */
KalturaCEError.prototype.type = null;

/**
 * 
 *
 * @var string
 */
KalturaCEError.prototype.description = null;

/**
 * 
 *
 * @var string
 */
KalturaCEError.prototype.data = null;


function KalturaCategory()
{
}
KalturaCategory.prototype = new KalturaObjectBase();
/**
 * The id of the Category
	 * 
 *
 * @var int
 * @readonly
 */
KalturaCategory.prototype.id = null;

/**
 * 
 *
 * @var int
 */
KalturaCategory.prototype.parentId = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaCategory.prototype.depth = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaCategory.prototype.partnerId = null;

/**
 * The name of the Category. 
	 * The following characters are not allowed: '<', '>', ','
	 * 
 *
 * @var string
 */
KalturaCategory.prototype.name = null;

/**
 * The full name of the Category
	 * 
 *
 * @var string
 * @readonly
 */
KalturaCategory.prototype.fullName = null;

/**
 * Number of entries in this Category (including child categories)
	 * 
 *
 * @var int
 * @readonly
 */
KalturaCategory.prototype.entriesCount = null;

/**
 * Creation date as Unix timestamp (In seconds)
	 * 
 *
 * @var int
 * @readonly
 */
KalturaCategory.prototype.createdAt = null;


function KalturaCategoryFilter()
{
}
KalturaCategoryFilter.prototype = new KalturaFilter();
/**
 * 
 *
 * @var int
 */
KalturaCategoryFilter.prototype.idEqual = null;

/**
 * 
 *
 * @var string
 */
KalturaCategoryFilter.prototype.idIn = null;

/**
 * 
 *
 * @var int
 */
KalturaCategoryFilter.prototype.parentIdEqual = null;

/**
 * 
 *
 * @var string
 */
KalturaCategoryFilter.prototype.parentIdIn = null;

/**
 * 
 *
 * @var int
 */
KalturaCategoryFilter.prototype.depthEqual = null;

/**
 * 
 *
 * @var string
 */
KalturaCategoryFilter.prototype.fullNameEqual = null;

/**
 * 
 *
 * @var string
 */
KalturaCategoryFilter.prototype.fullNameStartsWith = null;


function KalturaCategoryListResponse()
{
}
KalturaCategoryListResponse.prototype = new KalturaObjectBase();
/**
 * 
 *
 * @var KalturaCategoryArray
 * @readonly
 */
KalturaCategoryListResponse.prototype.objects = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaCategoryListResponse.prototype.totalCount = null;


function KalturaClientNotification()
{
}
KalturaClientNotification.prototype = new KalturaObjectBase();
/**
 * The URL where the notification should be sent to 
 *
 * @var string
 */
KalturaClientNotification.prototype.url = null;

/**
 * The serialized notification data to send
 *
 * @var string
 */
KalturaClientNotification.prototype.data = null;


function KalturaConvartableJobData()
{
}
KalturaConvartableJobData.prototype = new KalturaJobData();
/**
 * 
 *
 * @var string
 */
KalturaConvartableJobData.prototype.srcFileSyncLocalPath = null;

/**
 * 
 *
 * @var string
 */
KalturaConvartableJobData.prototype.srcFileSyncRemoteUrl = null;

/**
 * 
 *
 * @var int
 */
KalturaConvartableJobData.prototype.flavorParamsOutputId = null;

/**
 * 
 *
 * @var KalturaFlavorParamsOutput
 */
KalturaConvartableJobData.prototype.flavorParamsOutput = null;

/**
 * 
 *
 * @var int
 */
KalturaConvartableJobData.prototype.mediaInfoId = null;


function KalturaConversionProfile()
{
}
KalturaConversionProfile.prototype = new KalturaObjectBase();
/**
 * The id of the Conversion Profile
	 * 
 *
 * @var int
 * @readonly
 */
KalturaConversionProfile.prototype.id = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaConversionProfile.prototype.partnerId = null;

/**
 * The name of the Conversion Profile
	 * 
 *
 * @var string
 */
KalturaConversionProfile.prototype.name = null;

/**
 * The description of the Conversion Profile
	 * 
 *
 * @var string
 */
KalturaConversionProfile.prototype.description = null;

/**
 * Creation date as Unix timestamp (In seconds) 
	 * 
 *
 * @var int
 * @readonly
 */
KalturaConversionProfile.prototype.createdAt = null;

/**
 * List of included flavor ids (comma separated)
	 * 
 *
 * @var string
 */
KalturaConversionProfile.prototype.flavorParamsIds = null;

/**
 * True if this Conversion Profile is the default
	 * 
 *
 * @var KalturaNullableBoolean
 */
KalturaConversionProfile.prototype.isDefault = null;

/**
 * Cropping dimensions
	 * 
 *
 * @var KalturaCropDimensions
 */
KalturaConversionProfile.prototype.cropDimensions = null;

/**
 * Clipping start position (in miliseconds)
	 * 
 *
 * @var int
 */
KalturaConversionProfile.prototype.clipStart = null;

/**
 * Clipping duration (in miliseconds)
	 * 
 *
 * @var int
 */
KalturaConversionProfile.prototype.clipDuration = null;


function KalturaConversionProfileFilter()
{
}
KalturaConversionProfileFilter.prototype = new KalturaFilter();
/**
 * 
 *
 * @var int
 */
KalturaConversionProfileFilter.prototype.idEqual = null;

/**
 * 
 *
 * @var string
 */
KalturaConversionProfileFilter.prototype.idIn = null;


function KalturaConversionProfileListResponse()
{
}
KalturaConversionProfileListResponse.prototype = new KalturaObjectBase();
/**
 * 
 *
 * @var KalturaConversionProfileArray
 * @readonly
 */
KalturaConversionProfileListResponse.prototype.objects = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaConversionProfileListResponse.prototype.totalCount = null;


function KalturaConvertJobData()
{
}
KalturaConvertJobData.prototype = new KalturaConvartableJobData();
/**
 * 
 *
 * @var string
 */
KalturaConvertJobData.prototype.destFileSyncLocalPath = null;

/**
 * 
 *
 * @var string
 */
KalturaConvertJobData.prototype.destFileSyncRemoteUrl = null;

/**
 * 
 *
 * @var string
 */
KalturaConvertJobData.prototype.logFileSyncLocalPath = null;

/**
 * 
 *
 * @var string
 */
KalturaConvertJobData.prototype.flavorAssetId = null;

/**
 * 
 *
 * @var string
 */
KalturaConvertJobData.prototype.remoteMediaId = null;


function KalturaConvertProfileJobData()
{
}
KalturaConvertProfileJobData.prototype = new KalturaJobData();
/**
 * 
 *
 * @var string
 */
KalturaConvertProfileJobData.prototype.inputFileSyncLocalPath = null;

/**
 * The height of last created thumbnail, will be used to comapare if this thumbnail is the best we can have
	 * 
 *
 * @var int
 */
KalturaConvertProfileJobData.prototype.thumbHeight = null;

/**
 * The bit rate of last created thumbnail, will be used to comapare if this thumbnail is the best we can have
	 * 
 *
 * @var int
 */
KalturaConvertProfileJobData.prototype.thumbBitrate = null;


function KalturaCountryRestriction()
{
}
KalturaCountryRestriction.prototype = new KalturaBaseRestriction();
/**
 * Country restriction type (Allow or deny)
	 * 
 *
 * @var KalturaCountryRestrictionType
 */
KalturaCountryRestriction.prototype.countryRestrictionType = null;

/**
 * Comma separated list of country codes to allow to deny 
	 * 
 *
 * @var string
 */
KalturaCountryRestriction.prototype.countryList = null;


function KalturaCropDimensions()
{
}
KalturaCropDimensions.prototype = new KalturaObjectBase();
/**
 * Crop left point
	 * 
 *
 * @var int
 */
KalturaCropDimensions.prototype.left = null;

/**
 * Crop top point
	 * 
 *
 * @var int
 */
KalturaCropDimensions.prototype.top = null;

/**
 * Crop width
	 * 
 *
 * @var int
 */
KalturaCropDimensions.prototype.width = null;

/**
 * Crop height
	 * 
 *
 * @var int
 */
KalturaCropDimensions.prototype.height = null;


function KalturaDataEntry()
{
}
KalturaDataEntry.prototype = new KalturaBaseEntry();
/**
 * The data of the entry
 *
 * @var string
 */
KalturaDataEntry.prototype.dataContent = null;


function KalturaDataEntryFilter()
{
}
KalturaDataEntryFilter.prototype = new KalturaBaseEntryFilter();

function KalturaDataListResponse()
{
}
KalturaDataListResponse.prototype = new KalturaObjectBase();
/**
 * 
 *
 * @var KalturaDataEntryArray
 * @readonly
 */
KalturaDataListResponse.prototype.objects = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaDataListResponse.prototype.totalCount = null;


function KalturaDirectoryRestriction()
{
}
KalturaDirectoryRestriction.prototype = new KalturaBaseRestriction();
/**
 * Kaltura directory restriction type
	 * 
 *
 * @var KalturaDirectoryRestrictionType
 */
KalturaDirectoryRestriction.prototype.directoryRestrictionType = null;


function KalturaDocumentEntry()
{
}
KalturaDocumentEntry.prototype = new KalturaBaseEntry();
/**
 * The type of the document
 *
 * @var KalturaDocumentType
 * @insertonly
 */
KalturaDocumentEntry.prototype.documentType = null;


function KalturaDocumentEntryFilter()
{
}
KalturaDocumentEntryFilter.prototype = new KalturaBaseEntryFilter();
/**
 * 
 *
 * @var KalturaDocumentType
 */
KalturaDocumentEntryFilter.prototype.documentTypeEqual = null;

/**
 * 
 *
 * @var string
 */
KalturaDocumentEntryFilter.prototype.documentTypeIn = null;


function KalturaEntryExtraDataParams()
{
}
KalturaEntryExtraDataParams.prototype = new KalturaObjectBase();
/**
 * 
 *
 * @var string
 */
KalturaEntryExtraDataParams.prototype.referrer = null;


function KalturaEntryExtraDataResult()
{
}
KalturaEntryExtraDataResult.prototype = new KalturaObjectBase();
/**
 * 
 *
 * @var bool
 */
KalturaEntryExtraDataResult.prototype.isSiteRestricted = null;

/**
 * 
 *
 * @var bool
 */
KalturaEntryExtraDataResult.prototype.isCountryRestricted = null;

/**
 * 
 *
 * @var bool
 */
KalturaEntryExtraDataResult.prototype.isSessionRestricted = null;

/**
 * 
 *
 * @var int
 */
KalturaEntryExtraDataResult.prototype.previewLength = null;

/**
 * 
 *
 * @var bool
 */
KalturaEntryExtraDataResult.prototype.isScheduledNow = null;

/**
 * 
 *
 * @var bool
 */
KalturaEntryExtraDataResult.prototype.isAdmin = null;


function KalturaExtractMediaJobData()
{
}
KalturaExtractMediaJobData.prototype = new KalturaConvartableJobData();
/**
 * 
 *
 * @var string
 */
KalturaExtractMediaJobData.prototype.flavorAssetId = null;


function KalturaFilterPager()
{
}
KalturaFilterPager.prototype = new KalturaObjectBase();
/**
 * The number of objects to retrieve. (Default is 30, maximum page size is 500).
	 * 
 *
 * @var int
 */
KalturaFilterPager.prototype.pageSize = null;

/**
 * The page number for which {pageSize} of objects should be retrieved (Default is 1).
	 * 
 *
 * @var int
 */
KalturaFilterPager.prototype.pageIndex = null;


function KalturaFlattenJobData()
{
}
KalturaFlattenJobData.prototype = new KalturaJobData();

function KalturaFlavorAsset()
{
}
KalturaFlavorAsset.prototype = new KalturaObjectBase();
/**
 * The ID of the Flavor Asset
	 * 
 *
 * @var string
 * @readonly
 */
KalturaFlavorAsset.prototype.id = null;

/**
 * The entry ID of the Flavor Asset
	 * 
 *
 * @var string
 * @readonly
 */
KalturaFlavorAsset.prototype.entryId = null;

/**
 * 
 *
 * @var string
 * @readonly
 */
KalturaFlavorAsset.prototype.partnerId = null;

/**
 * The status of the Flavor Asset
	 * 
 *
 * @var KalturaFlavorAssetStatus
 * @readonly
 */
KalturaFlavorAsset.prototype.status = null;

/**
 * The Flavor Params used to create this Flavor Asset
	 * 
 *
 * @var int
 * @readonly
 */
KalturaFlavorAsset.prototype.flavorParamsId = null;

/**
 * The version of the Flavor Asset
	 * 
 *
 * @var int
 * @readonly
 */
KalturaFlavorAsset.prototype.version = null;

/**
 * The width of the Flavor Asset 
	 * 
 *
 * @var int
 * @readonly
 */
KalturaFlavorAsset.prototype.width = null;

/**
 * The height of the Flavor Asset
	 * 
 *
 * @var int
 * @readonly
 */
KalturaFlavorAsset.prototype.height = null;

/**
 * The overall bitrate (in KBits) of the Flavor Asset 
	 * 
 *
 * @var int
 * @readonly
 */
KalturaFlavorAsset.prototype.bitrate = null;

/**
 * The frame rate (in FPS) of the Flavor Asset
	 * 
 *
 * @var int
 * @readonly
 */
KalturaFlavorAsset.prototype.frameRate = null;

/**
 * The size (in KBytes) of the Flavor Asset
	 * 
 *
 * @var int
 * @readonly
 */
KalturaFlavorAsset.prototype.size = null;

/**
 * True if this Flavor Asset is the original source
	 * 
 *
 * @var bool
 */
KalturaFlavorAsset.prototype.isOriginal = null;

/**
 * Tags used to identify the Flavor Asset in various scenarios
	 * 
 *
 * @var string
 */
KalturaFlavorAsset.prototype.tags = null;

/**
 * True if this Flavor Asset is playable in KDP
	 * 
 *
 * @var bool
 */
KalturaFlavorAsset.prototype.isWeb = null;

/**
 * The file extension
	 * 
 *
 * @var string
 */
KalturaFlavorAsset.prototype.fileExt = null;

/**
 * The container format
	 * 
 *
 * @var string
 */
KalturaFlavorAsset.prototype.containerFormat = null;

/**
 * The video codec
	 * 
 *
 * @var string
 */
KalturaFlavorAsset.prototype.videoCodecId = null;


function KalturaFlavorAssetWithParams()
{
}
KalturaFlavorAssetWithParams.prototype = new KalturaObjectBase();
/**
 * The Flavor Asset (Can be null when there are params without asset)
	 * 
 *
 * @var KalturaFlavorAsset
 */
KalturaFlavorAssetWithParams.prototype.flavorAsset = null;

/**
 * The Flavor Params
	 * 
 *
 * @var KalturaFlavorParams
 */
KalturaFlavorAssetWithParams.prototype.flavorParams = null;

/**
 * The entry id
	 * 
 *
 * @var string
 */
KalturaFlavorAssetWithParams.prototype.entryId = null;


function KalturaFlavorParams()
{
}
KalturaFlavorParams.prototype = new KalturaObjectBase();
/**
 * The id of the Flavor Params
	 * 
 *
 * @var int
 * @readonly
 */
KalturaFlavorParams.prototype.id = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaFlavorParams.prototype.partnerId = null;

/**
 * The name of the Flavor Params
	 * 
 *
 * @var string
 */
KalturaFlavorParams.prototype.name = null;

/**
 * The description of the Flavor Params
	 * 
 *
 * @var string
 */
KalturaFlavorParams.prototype.description = null;

/**
 * Creation date as Unix timestamp (In seconds)
	 * 
 *
 * @var int
 * @readonly
 */
KalturaFlavorParams.prototype.createdAt = null;

/**
 * True if those Flavor Params are part of system defaults
	 * 
 *
 * @var KalturaNullableBoolean
 * @readonly
 */
KalturaFlavorParams.prototype.isSystemDefault = null;

/**
 * The Flavor Params tags are used to identify the flavor for different usage (e.g. web, hd, mobile)
	 * 
 *
 * @var string
 */
KalturaFlavorParams.prototype.tags = null;

/**
 * The container format of the Flavor Params
	 * 
 *
 * @var KalturaContainerFormat
 */
KalturaFlavorParams.prototype.format = null;

/**
 * The video codec of the Flavor Params
	 * 
 *
 * @var KalturaVideoCodec
 */
KalturaFlavorParams.prototype.videoCodec = null;

/**
 * The video bitrate (in KBits) of the Flavor Params
	 * 
 *
 * @var int
 */
KalturaFlavorParams.prototype.videoBitrate = null;

/**
 * The audio codec of the Flavor Params
	 * 
 *
 * @var KalturaAudioCodec
 */
KalturaFlavorParams.prototype.audioCodec = null;

/**
 * The audio bitrate (in KBits) of the Flavor Params
	 * 
 *
 * @var int
 */
KalturaFlavorParams.prototype.audioBitrate = null;

/**
 * The number of audio channels for "downmixing"
	 * 
 *
 * @var int
 */
KalturaFlavorParams.prototype.audioChannels = null;

/**
 * The audio sample rate of the Flavor Params
	 * 
 *
 * @var int
 */
KalturaFlavorParams.prototype.audioSampleRate = null;

/**
 * The desired width of the Flavor Params
	 * 
 *
 * @var int
 */
KalturaFlavorParams.prototype.width = null;

/**
 * The desired height of the Flavor Params
	 * 
 *
 * @var int
 */
KalturaFlavorParams.prototype.height = null;

/**
 * The frame rate of the Flavor Params
	 * 
 *
 * @var int
 */
KalturaFlavorParams.prototype.frameRate = null;

/**
 * The gop size of the Flavor Params
	 * 
 *
 * @var int
 */
KalturaFlavorParams.prototype.gopSize = null;

/**
 * The list of conversion engines (comma separated)
	 * 
 *
 * @var string
 */
KalturaFlavorParams.prototype.conversionEngines = null;

/**
 * The list of conversion engines extra params (separated with "|")
	 * 
 *
 * @var string
 */
KalturaFlavorParams.prototype.conversionEnginesExtraParams = null;

/**
 * 
 *
 * @var bool
 */
KalturaFlavorParams.prototype.twoPass = null;


function KalturaFlavorParamsFilter()
{
}
KalturaFlavorParamsFilter.prototype = new KalturaFilter();
/**
 * 
 *
 * @var KalturaNullableBoolean
 */
KalturaFlavorParamsFilter.prototype.isSystemDefaultEqual = null;


function KalturaFlavorParamsListResponse()
{
}
KalturaFlavorParamsListResponse.prototype = new KalturaObjectBase();
/**
 * 
 *
 * @var KalturaFlavorParamsArray
 * @readonly
 */
KalturaFlavorParamsListResponse.prototype.objects = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaFlavorParamsListResponse.prototype.totalCount = null;


function KalturaFlavorParamsOutput()
{
}
KalturaFlavorParamsOutput.prototype = new KalturaFlavorParams();
/**
 * 
 *
 * @var int
 */
KalturaFlavorParamsOutput.prototype.flavorParamsId = null;

/**
 * 
 *
 * @var string
 */
KalturaFlavorParamsOutput.prototype.commandLinesStr = null;


function KalturaFlavorParamsOutputFilter()
{
}
KalturaFlavorParamsOutputFilter.prototype = new KalturaFlavorParamsFilter();

function KalturaGoogleVideoSyndicationFeed()
{
}
KalturaGoogleVideoSyndicationFeed.prototype = new KalturaBaseSyndicationFeed();
/**
 * 
 *
 * @var KalturaGoogleSyndicationFeedAdultValues
 */
KalturaGoogleVideoSyndicationFeed.prototype.adultContent = null;


function KalturaGoogleVideoSyndicationFeedFilter()
{
}
KalturaGoogleVideoSyndicationFeedFilter.prototype = new KalturaBaseSyndicationFeedFilter();

function KalturaITunesSyndicationFeed()
{
}
KalturaITunesSyndicationFeed.prototype = new KalturaBaseSyndicationFeed();
/**
 * feed description
	 * 
 *
 * @var string
 */
KalturaITunesSyndicationFeed.prototype.feedDescription = null;

/**
 * feed language
	 * 
 *
 * @var string
 */
KalturaITunesSyndicationFeed.prototype.language = null;

/**
 * feed landing page (i.e publisher website)
	 * 
 *
 * @var string
 */
KalturaITunesSyndicationFeed.prototype.feedLandingPage = null;

/**
 * author/publisher name
	 * 
 *
 * @var string
 */
KalturaITunesSyndicationFeed.prototype.ownerName = null;

/**
 * publisher email
	 * 
 *
 * @var string
 */
KalturaITunesSyndicationFeed.prototype.ownerEmail = null;

/**
 * podcast thumbnail
	 * 
 *
 * @var string
 */
KalturaITunesSyndicationFeed.prototype.feedImageUrl = null;

/**
 * 
 *
 * @var KalturaITunesSyndicationFeedCategories
 * @readonly
 */
KalturaITunesSyndicationFeed.prototype.category = null;

/**
 * 
 *
 * @var KalturaITunesSyndicationFeedAdultValues
 */
KalturaITunesSyndicationFeed.prototype.adultContent = null;

/**
 * 
 *
 * @var string
 */
KalturaITunesSyndicationFeed.prototype.feedAuthor = null;


function KalturaITunesSyndicationFeedFilter()
{
}
KalturaITunesSyndicationFeedFilter.prototype = new KalturaBaseSyndicationFeedFilter();

function KalturaImportJobData()
{
}
KalturaImportJobData.prototype = new KalturaJobData();
/**
 * 
 *
 * @var string
 */
KalturaImportJobData.prototype.srcFileUrl = null;

/**
 * 
 *
 * @var string
 */
KalturaImportJobData.prototype.destFileLocalPath = null;

/**
 * 
 *
 * @var string
 */
KalturaImportJobData.prototype.flavorAssetId = null;


function KalturaMailJob()
{
}
KalturaMailJob.prototype = new KalturaBaseJob();
/**
 * 
 *
 * @var KalturaMailType
 */
KalturaMailJob.prototype.mailType = null;

/**
 * 
 *
 * @var int
 */
KalturaMailJob.prototype.mailPriority = null;

/**
 * 
 *
 * @var KalturaMailJobStatus
 */
KalturaMailJob.prototype.status = null;

/**
 * 
 *
 * @var string
 */
KalturaMailJob.prototype.recipientName = null;

/**
 * 
 *
 * @var string
 */
KalturaMailJob.prototype.recipientEmail = null;

/**
 * kuserId  
 *
 * @var int
 */
KalturaMailJob.prototype.recipientId = null;

/**
 * 
 *
 * @var string
 */
KalturaMailJob.prototype.fromName = null;

/**
 * 
 *
 * @var string
 */
KalturaMailJob.prototype.fromEmail = null;

/**
 * 
 *
 * @var string
 */
KalturaMailJob.prototype.bodyParams = null;

/**
 * 
 *
 * @var string
 */
KalturaMailJob.prototype.subjectParams = null;

/**
 * 
 *
 * @var string
 */
KalturaMailJob.prototype.templatePath = null;

/**
 * 
 *
 * @var int
 */
KalturaMailJob.prototype.culture = null;

/**
 * 
 *
 * @var int
 */
KalturaMailJob.prototype.campaignId = null;

/**
 * 
 *
 * @var int
 */
KalturaMailJob.prototype.minSendDate = null;


function KalturaMailJobData()
{
}
KalturaMailJobData.prototype = new KalturaJobData();
/**
 * 
 *
 * @var KalturaMailType
 */
KalturaMailJobData.prototype.mailType = null;

/**
 * 
 *
 * @var int
 */
KalturaMailJobData.prototype.mailPriority = null;

/**
 * 
 *
 * @var KalturaMailJobStatus
 */
KalturaMailJobData.prototype.status = null;

/**
 * 
 *
 * @var string
 */
KalturaMailJobData.prototype.recipientName = null;

/**
 * 
 *
 * @var string
 */
KalturaMailJobData.prototype.recipientEmail = null;

/**
 * kuserId  
 *
 * @var int
 */
KalturaMailJobData.prototype.recipientId = null;

/**
 * 
 *
 * @var string
 */
KalturaMailJobData.prototype.fromName = null;

/**
 * 
 *
 * @var string
 */
KalturaMailJobData.prototype.fromEmail = null;

/**
 * 
 *
 * @var string
 */
KalturaMailJobData.prototype.bodyParams = null;

/**
 * 
 *
 * @var string
 */
KalturaMailJobData.prototype.subjectParams = null;

/**
 * 
 *
 * @var string
 */
KalturaMailJobData.prototype.templatePath = null;

/**
 * 
 *
 * @var int
 */
KalturaMailJobData.prototype.culture = null;

/**
 * 
 *
 * @var int
 */
KalturaMailJobData.prototype.campaignId = null;

/**
 * 
 *
 * @var int
 */
KalturaMailJobData.prototype.minSendDate = null;

/**
 * 
 *
 * @var bool
 */
KalturaMailJobData.prototype.isHtml = null;


function KalturaMailJobFilter()
{
}
KalturaMailJobFilter.prototype = new KalturaBaseJobFilter();

function KalturaPlayableEntry()
{
}
KalturaPlayableEntry.prototype = new KalturaBaseEntry();
/**
 * Number of plays
	 * 
 *
 * @var int
 * @readonly
 */
KalturaPlayableEntry.prototype.plays = null;

/**
 * Number of views
	 * 
 *
 * @var int
 * @readonly
 */
KalturaPlayableEntry.prototype.views = null;

/**
 * The width in pixels
	 * 
 *
 * @var int
 * @readonly
 */
KalturaPlayableEntry.prototype.width = null;

/**
 * The height in pixels
	 * 
 *
 * @var int
 * @readonly
 */
KalturaPlayableEntry.prototype.height = null;

/**
 * The duration in seconds
	 * 
 *
 * @var int
 * @readonly
 */
KalturaPlayableEntry.prototype.duration = null;

/**
 * The duration type (short for 0-4 mins, medium for 4-20 mins, long for 20+ mins)
	 * 
 *
 * @var KalturaDurationType
 * @readonly
 */
KalturaPlayableEntry.prototype.durationType = null;


function KalturaMediaEntry()
{
}
KalturaMediaEntry.prototype = new KalturaPlayableEntry();
/**
 * The media type of the entry
	 * 
 *
 * @var KalturaMediaType
 * @insertonly
 */
KalturaMediaEntry.prototype.mediaType = null;

/**
 * Override the default conversion quality  
	 * 
 *
 * @var string
 * @insertonly
 */
KalturaMediaEntry.prototype.conversionQuality = null;

/**
 * The source type of the entry 
 *
 * @var KalturaSourceType
 * @readonly
 */
KalturaMediaEntry.prototype.sourceType = null;

/**
 * The search provider type used to import this entry
 *
 * @var KalturaSearchProviderType
 * @readonly
 */
KalturaMediaEntry.prototype.searchProviderType = null;

/**
 * The ID of the media in the importing site
 *
 * @var string
 * @readonly
 */
KalturaMediaEntry.prototype.searchProviderId = null;

/**
 * The user name used for credits
 *
 * @var string
 */
KalturaMediaEntry.prototype.creditUserName = null;

/**
 * The URL for credits
 *
 * @var string
 */
KalturaMediaEntry.prototype.creditUrl = null;

/**
 * The media date extracted from EXIF data (For images) as Unix timestamp (In seconds)
 *
 * @var int
 * @readonly
 */
KalturaMediaEntry.prototype.mediaDate = null;

/**
 * The URL used for playback. This is not the download URL.
 *
 * @var string
 * @readonly
 */
KalturaMediaEntry.prototype.dataUrl = null;

/**
 * Comma separated flavor params ids that exists for this media entry
	 * 
 *
 * @var string
 * @readonly
 */
KalturaMediaEntry.prototype.flavorParamsIds = null;


function KalturaPlayableEntryFilter()
{
}
KalturaPlayableEntryFilter.prototype = new KalturaBaseEntryFilter();
/**
 * 
 *
 * @var int
 */
KalturaPlayableEntryFilter.prototype.durationLessThan = null;

/**
 * 
 *
 * @var int
 */
KalturaPlayableEntryFilter.prototype.durationGreaterThan = null;

/**
 * 
 *
 * @var int
 */
KalturaPlayableEntryFilter.prototype.durationLessThanOrEqual = null;

/**
 * 
 *
 * @var int
 */
KalturaPlayableEntryFilter.prototype.durationGreaterThanOrEqual = null;

/**
 * 
 *
 * @var string
 */
KalturaPlayableEntryFilter.prototype.durationTypeMatchOr = null;


function KalturaMediaEntryFilter()
{
}
KalturaMediaEntryFilter.prototype = new KalturaPlayableEntryFilter();
/**
 * 
 *
 * @var KalturaMediaType
 */
KalturaMediaEntryFilter.prototype.mediaTypeEqual = null;

/**
 * 
 *
 * @var string
 */
KalturaMediaEntryFilter.prototype.mediaTypeIn = null;

/**
 * 
 *
 * @var int
 */
KalturaMediaEntryFilter.prototype.mediaDateGreaterThanOrEqual = null;

/**
 * 
 *
 * @var int
 */
KalturaMediaEntryFilter.prototype.mediaDateLessThanOrEqual = null;

/**
 * 
 *
 * @var string
 */
KalturaMediaEntryFilter.prototype.flavorParamsIdsMatchOr = null;

/**
 * 
 *
 * @var string
 */
KalturaMediaEntryFilter.prototype.flavorParamsIdsMatchAnd = null;


function KalturaMediaEntryFilterForPlaylist()
{
}
KalturaMediaEntryFilterForPlaylist.prototype = new KalturaMediaEntryFilter();
/**
 * 
 *
 * @var int
 */
KalturaMediaEntryFilterForPlaylist.prototype.limit = null;


function KalturaMediaListResponse()
{
}
KalturaMediaListResponse.prototype = new KalturaObjectBase();
/**
 * 
 *
 * @var KalturaMediaEntryArray
 * @readonly
 */
KalturaMediaListResponse.prototype.objects = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaMediaListResponse.prototype.totalCount = null;


function KalturaMixEntry()
{
}
KalturaMixEntry.prototype = new KalturaPlayableEntry();
/**
 * Indicates whether the user has submited a real thumbnail to the mix (Not the one that was generated automaticaly)
	 * 
 *
 * @var bool
 * @readonly
 */
KalturaMixEntry.prototype.hasRealThumbnail = null;

/**
 * The editor type used to edit the metadata
	 * 
 *
 * @var KalturaEditorType
 */
KalturaMixEntry.prototype.editorType = null;

/**
 * The xml data of the mix
 *
 * @var string
 */
KalturaMixEntry.prototype.dataContent = null;


function KalturaMixEntryFilter()
{
}
KalturaMixEntryFilter.prototype = new KalturaPlayableEntryFilter();

function KalturaMixListResponse()
{
}
KalturaMixListResponse.prototype = new KalturaObjectBase();
/**
 * 
 *
 * @var KalturaMixEntryArray
 * @readonly
 */
KalturaMixListResponse.prototype.objects = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaMixListResponse.prototype.totalCount = null;


function KalturaModerationFlag()
{
}
KalturaModerationFlag.prototype = new KalturaObjectBase();
/**
 * Moderation flag id
 *
 * @var int
 * @readonly
 */
KalturaModerationFlag.prototype.id = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaModerationFlag.prototype.partnerId = null;

/**
 * The user id that added the moderation flag
 *
 * @var string
 * @readonly
 */
KalturaModerationFlag.prototype.userId = null;

/**
 * The type of the moderation flag (entry or user)
 *
 * @var KalturaModerationObjectType
 * @readonly
 */
KalturaModerationFlag.prototype.moderationObjectType = null;

/**
 * If moderation flag is set for entry, this is the flagged entry id
 *
 * @var string
 */
KalturaModerationFlag.prototype.flaggedEntryId = null;

/**
 * If moderation flag is set for user, this is the flagged user id
 *
 * @var string
 */
KalturaModerationFlag.prototype.flaggedUserId = null;

/**
 * The moderation flag status
 *
 * @var KalturaModerationFlagStatus
 * @readonly
 */
KalturaModerationFlag.prototype.status = null;

/**
 * The comment that was added to the flag
 *
 * @var string
 */
KalturaModerationFlag.prototype.comments = null;

/**
 * 
 *
 * @var KalturaModerationFlagType
 */
KalturaModerationFlag.prototype.flagType = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaModerationFlag.prototype.createdAt = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaModerationFlag.prototype.updatedAt = null;


function KalturaModerationFlagListResponse()
{
}
KalturaModerationFlagListResponse.prototype = new KalturaObjectBase();
/**
 * 
 *
 * @var KalturaModerationFlagArray
 * @readonly
 */
KalturaModerationFlagListResponse.prototype.objects = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaModerationFlagListResponse.prototype.totalCount = null;


function KalturaNotification()
{
}
KalturaNotification.prototype = new KalturaBaseJob();
/**
 * 
 *
 * @var string
 */
KalturaNotification.prototype.puserId = null;

/**
 * 
 *
 * @var KalturaNotificationType
 */
KalturaNotification.prototype.type = null;

/**
 * 
 *
 * @var string
 */
KalturaNotification.prototype.objectId = null;

/**
 * 
 *
 * @var KalturaNotificationStatus
 */
KalturaNotification.prototype.status = null;

/**
 * 
 *
 * @var string
 */
KalturaNotification.prototype.notificationData = null;

/**
 * 
 *
 * @var int
 */
KalturaNotification.prototype.numberOfAttempts = null;

/**
 * 
 *
 * @var string
 */
KalturaNotification.prototype.notificationResult = null;

/**
 * 
 *
 * @var KalturaNotificationObjectType
 */
KalturaNotification.prototype.objType = null;


function KalturaNotificationFilter()
{
}
KalturaNotificationFilter.prototype = new KalturaBaseJobFilter();

function KalturaNotificationJobData()
{
}
KalturaNotificationJobData.prototype = new KalturaJobData();
/**
 * 
 *
 * @var string
 */
KalturaNotificationJobData.prototype.userId = null;

/**
 * 
 *
 * @var KalturaNotificationType
 */
KalturaNotificationJobData.prototype.type = null;

/**
 * 
 *
 * @var string
 */
KalturaNotificationJobData.prototype.typeAsString = null;

/**
 * 
 *
 * @var string
 */
KalturaNotificationJobData.prototype.objectId = null;

/**
 * 
 *
 * @var KalturaNotificationStatus
 */
KalturaNotificationJobData.prototype.status = null;

/**
 * 
 *
 * @var string
 */
KalturaNotificationJobData.prototype.data = null;

/**
 * 
 *
 * @var int
 */
KalturaNotificationJobData.prototype.numberOfAttempts = null;

/**
 * 
 *
 * @var string
 */
KalturaNotificationJobData.prototype.notificationResult = null;

/**
 * 
 *
 * @var KalturaNotificationObjectType
 */
KalturaNotificationJobData.prototype.objType = null;


function KalturaPartner()
{
}
KalturaPartner.prototype = new KalturaObjectBase();
/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaPartner.prototype.id = null;

/**
 * 
 *
 * @var string
 */
KalturaPartner.prototype.name = null;

/**
 * 
 *
 * @var string
 */
KalturaPartner.prototype.website = null;

/**
 * 
 *
 * @var string
 */
KalturaPartner.prototype.notificationUrl = null;

/**
 * 
 *
 * @var int
 */
KalturaPartner.prototype.appearInSearch = null;

/**
 * 
 *
 * @var string
 * @readonly
 */
KalturaPartner.prototype.createdAt = null;

/**
 * 
 *
 * @var string
 */
KalturaPartner.prototype.adminName = null;

/**
 * 
 *
 * @var string
 */
KalturaPartner.prototype.adminEmail = null;

/**
 * 
 *
 * @var string
 */
KalturaPartner.prototype.description = null;

/**
 * 
 *
 * @var KalturaCommercialUseType
 */
KalturaPartner.prototype.commercialUse = null;

/**
 * 
 *
 * @var string
 */
KalturaPartner.prototype.landingPage = null;

/**
 * 
 *
 * @var string
 */
KalturaPartner.prototype.userLandingPage = null;

/**
 * 
 *
 * @var string
 */
KalturaPartner.prototype.contentCategories = null;

/**
 * 
 *
 * @var KalturaPartnerType
 */
KalturaPartner.prototype.type = null;

/**
 * 
 *
 * @var string
 */
KalturaPartner.prototype.phone = null;

/**
 * 
 *
 * @var string
 */
KalturaPartner.prototype.describeYourself = null;

/**
 * 
 *
 * @var bool
 */
KalturaPartner.prototype.adultContent = null;

/**
 * 
 *
 * @var string
 */
KalturaPartner.prototype.defConversionProfileType = null;

/**
 * 
 *
 * @var int
 */
KalturaPartner.prototype.notify = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaPartner.prototype.status = null;

/**
 * 
 *
 * @var int
 */
KalturaPartner.prototype.allowQuickEdit = null;

/**
 * 
 *
 * @var int
 */
KalturaPartner.prototype.mergeEntryLists = null;

/**
 * 
 *
 * @var string
 */
KalturaPartner.prototype.notificationsConfig = null;

/**
 * 
 *
 * @var int
 */
KalturaPartner.prototype.maxUploadSize = null;

/**
 * readonly
 *
 * @var int
 */
KalturaPartner.prototype.partnerPackage = null;

/**
 * readonly
 *
 * @var string
 */
KalturaPartner.prototype.secret = null;

/**
 * readonly
 *
 * @var string
 */
KalturaPartner.prototype.adminSecret = null;

/**
 * 
 *
 * @var string
 * @readonly
 */
KalturaPartner.prototype.cmsPassword = null;

/**
 * readonly
 *
 * @var int
 */
KalturaPartner.prototype.allowMultiNotification = null;


function KalturaPartnerFilter()
{
}
KalturaPartnerFilter.prototype = new KalturaFilter();
/**
 * 
 *
 * @var string
 */
KalturaPartnerFilter.prototype.nameLike = null;

/**
 * 
 *
 * @var string
 */
KalturaPartnerFilter.prototype.nameMultiLikeOr = null;

/**
 * 
 *
 * @var string
 */
KalturaPartnerFilter.prototype.nameMultiLikeAnd = null;

/**
 * 
 *
 * @var string
 */
KalturaPartnerFilter.prototype.nameEqual = null;

/**
 * 
 *
 * @var int
 */
KalturaPartnerFilter.prototype.statusEqual = null;

/**
 * 
 *
 * @var string
 */
KalturaPartnerFilter.prototype.statusIn = null;


function KalturaPartnerUsage()
{
}
KalturaPartnerUsage.prototype = new KalturaObjectBase();
/**
 * 
 *
 * @var float
 * @readonly
 */
KalturaPartnerUsage.prototype.hostingGB = null;

/**
 * 
 *
 * @var float
 * @readonly
 */
KalturaPartnerUsage.prototype.Percent = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaPartnerUsage.prototype.packageBW = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaPartnerUsage.prototype.usageGB = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaPartnerUsage.prototype.reachedLimitDate = null;

/**
 * 
 *
 * @var string
 * @readonly
 */
KalturaPartnerUsage.prototype.usageGraph = null;


function KalturaPlaylist()
{
}
KalturaPlaylist.prototype = new KalturaBaseEntry();
/**
 * Content of the playlist - 
	 * XML if the playlistType is dynamic 
	 * text if the playlistType is static 
	 * url if the playlistType is mRss 
 *
 * @var string
 */
KalturaPlaylist.prototype.playlistContent = null;

/**
 * 
 *
 * @var KalturaMediaEntryFilterForPlaylistArray
 */
KalturaPlaylist.prototype.filters = null;

/**
 * 
 *
 * @var int
 */
KalturaPlaylist.prototype.totalResults = null;

/**
 * Type of playlist  
 *
 * @var KalturaPlaylistType
 */
KalturaPlaylist.prototype.playlistType = null;

/**
 * Number of plays
 *
 * @var int
 * @readonly
 */
KalturaPlaylist.prototype.plays = null;

/**
 * Number of views
 *
 * @var int
 * @readonly
 */
KalturaPlaylist.prototype.views = null;

/**
 * The duration in seconds
 *
 * @var int
 * @readonly
 */
KalturaPlaylist.prototype.duration = null;


function KalturaPlaylistFilter()
{
}
KalturaPlaylistFilter.prototype = new KalturaBaseEntryFilter();

function KalturaPlaylistListResponse()
{
}
KalturaPlaylistListResponse.prototype = new KalturaObjectBase();
/**
 * 
 *
 * @var KalturaPlaylistArray
 * @readonly
 */
KalturaPlaylistListResponse.prototype.objects = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaPlaylistListResponse.prototype.totalCount = null;


function KalturaPostConvertJobData()
{
}
KalturaPostConvertJobData.prototype = new KalturaJobData();
/**
 * 
 *
 * @var string
 */
KalturaPostConvertJobData.prototype.srcFileSyncLocalPath = null;

/**
 * 
 *
 * @var string
 */
KalturaPostConvertJobData.prototype.flavorAssetId = null;

/**
 * Indicates if a thumbnail should be created
	 * 
 *
 * @var bool
 */
KalturaPostConvertJobData.prototype.createThumb = null;

/**
 * The path of the created thumbnail
	 * 
 *
 * @var string
 */
KalturaPostConvertJobData.prototype.thumbPath = null;

/**
 * The position of the thumbnail in the media file
	 * 
 *
 * @var int
 */
KalturaPostConvertJobData.prototype.thumbOffset = null;

/**
 * The height of the movie, will be used to comapare if this thumbnail is the best we can have
	 * 
 *
 * @var int
 */
KalturaPostConvertJobData.prototype.thumbHeight = null;

/**
 * The bit rate of the movie, will be used to comapare if this thumbnail is the best we can have
	 * 
 *
 * @var int
 */
KalturaPostConvertJobData.prototype.thumbBitrate = null;

/**
 * 
 *
 * @var int
 */
KalturaPostConvertJobData.prototype.flavorParamsOutputId = null;


function KalturaSessionRestriction()
{
}
KalturaSessionRestriction.prototype = new KalturaBaseRestriction();

function KalturaPreviewRestriction()
{
}
KalturaPreviewRestriction.prototype = new KalturaSessionRestriction();
/**
 * The preview restriction length 
	 * 
 *
 * @var int
 */
KalturaPreviewRestriction.prototype.previewLength = null;


function KalturaPullJobData()
{
}
KalturaPullJobData.prototype = new KalturaJobData();
/**
 * 
 *
 * @var string
 */
KalturaPullJobData.prototype.srcFileUrl = null;

/**
 * 
 *
 * @var string
 */
KalturaPullJobData.prototype.destFileLocalPath = null;


function KalturaRemoteConvertJobData()
{
}
KalturaRemoteConvertJobData.prototype = new KalturaConvartableJobData();
/**
 * 
 *
 * @var string
 */
KalturaRemoteConvertJobData.prototype.srcFileUrl = null;

/**
 * Should be set by the API
	 * 
 *
 * @var string
 */
KalturaRemoteConvertJobData.prototype.destFileUrl = null;


function KalturaReportGraph()
{
}
KalturaReportGraph.prototype = new KalturaObjectBase();
/**
 * 
 *
 * @var string
 */
KalturaReportGraph.prototype.id = null;

/**
 * 
 *
 * @var string
 */
KalturaReportGraph.prototype.data = null;


function KalturaReportInputFilter()
{
}
KalturaReportInputFilter.prototype = new KalturaObjectBase();
/**
 * 
 *
 * @var int
 */
KalturaReportInputFilter.prototype.fromDate = null;

/**
 * 
 *
 * @var int
 */
KalturaReportInputFilter.prototype.toDate = null;

/**
 * 
 *
 * @var string
 */
KalturaReportInputFilter.prototype.keywords = null;

/**
 * 
 *
 * @var bool
 */
KalturaReportInputFilter.prototype.searchInTags = null;

/**
 * 
 *
 * @var bool
 */
KalturaReportInputFilter.prototype.searchInAdminTags = null;

/**
 * 
 *
 * @var string
 */
KalturaReportInputFilter.prototype.categories = null;


function KalturaReportTable()
{
}
KalturaReportTable.prototype = new KalturaObjectBase();
/**
 * 
 *
 * @var string
 * @readonly
 */
KalturaReportTable.prototype.header = null;

/**
 * 
 *
 * @var string
 * @readonly
 */
KalturaReportTable.prototype.data = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaReportTable.prototype.totalCount = null;


function KalturaReportTotal()
{
}
KalturaReportTotal.prototype = new KalturaObjectBase();
/**
 * 
 *
 * @var string
 */
KalturaReportTotal.prototype.header = null;

/**
 * 
 *
 * @var string
 */
KalturaReportTotal.prototype.data = null;


function KalturaSearch()
{
}
KalturaSearch.prototype = new KalturaObjectBase();
/**
 * 
 *
 * @var string
 */
KalturaSearch.prototype.keyWords = null;

/**
 * 
 *
 * @var KalturaSearchProviderType
 */
KalturaSearch.prototype.searchSource = null;

/**
 * 
 *
 * @var KalturaMediaType
 */
KalturaSearch.prototype.mediaType = null;

/**
 * Use this field to pass dynamic data for searching
	 * For example - if you set this field to "mymovies_$partner_id"
	 * The $partner_id will be automatically replcaed with your real partner Id
	 * 
 *
 * @var string
 */
KalturaSearch.prototype.extraData = null;

/**
 * 
 *
 * @var string
 */
KalturaSearch.prototype.authData = null;


function KalturaSearchAuthData()
{
}
KalturaSearchAuthData.prototype = new KalturaObjectBase();
/**
 * The authentication data that further should be used for search
	 * 
 *
 * @var string
 */
KalturaSearchAuthData.prototype.authData = null;

/**
 * Login URL when user need to sign-in and authorize the search
 *
 * @var string
 */
KalturaSearchAuthData.prototype.loginUrl = null;

/**
 * Information when there was an error
 *
 * @var string
 */
KalturaSearchAuthData.prototype.message = null;


function KalturaSearchResult()
{
}
KalturaSearchResult.prototype = new KalturaSearch();
/**
 * 
 *
 * @var string
 */
KalturaSearchResult.prototype.id = null;

/**
 * 
 *
 * @var string
 */
KalturaSearchResult.prototype.title = null;

/**
 * 
 *
 * @var string
 */
KalturaSearchResult.prototype.thumbUrl = null;

/**
 * 
 *
 * @var string
 */
KalturaSearchResult.prototype.description = null;

/**
 * 
 *
 * @var string
 */
KalturaSearchResult.prototype.tags = null;

/**
 * 
 *
 * @var string
 */
KalturaSearchResult.prototype.url = null;

/**
 * 
 *
 * @var string
 */
KalturaSearchResult.prototype.sourceLink = null;

/**
 * 
 *
 * @var string
 */
KalturaSearchResult.prototype.credit = null;

/**
 * 
 *
 * @var KalturaLicenseType
 */
KalturaSearchResult.prototype.licenseType = null;

/**
 * 
 *
 * @var string
 */
KalturaSearchResult.prototype.flashPlaybackType = null;


function KalturaSearchResultResponse()
{
}
KalturaSearchResultResponse.prototype = new KalturaObjectBase();
/**
 * 
 *
 * @var KalturaSearchResultArray
 * @readonly
 */
KalturaSearchResultResponse.prototype.objects = null;

/**
 * 
 *
 * @var bool
 * @readonly
 */
KalturaSearchResultResponse.prototype.needMediaInfo = null;


function KalturaSiteRestriction()
{
}
KalturaSiteRestriction.prototype = new KalturaBaseRestriction();
/**
 * The site restriction type (allow or deny)
	 * 
 *
 * @var KalturaSiteRestrictionType
 */
KalturaSiteRestriction.prototype.siteRestrictionType = null;

/**
 * Comma separated list of sites (domains) to allow or deny
	 * 
 *
 * @var string
 */
KalturaSiteRestriction.prototype.siteList = null;


function KalturaStartWidgetSessionResponse()
{
}
KalturaStartWidgetSessionResponse.prototype = new KalturaObjectBase();
/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaStartWidgetSessionResponse.prototype.partnerId = null;

/**
 * 
 *
 * @var string
 * @readonly
 */
KalturaStartWidgetSessionResponse.prototype.ks = null;

/**
 * 
 *
 * @var string
 * @readonly
 */
KalturaStartWidgetSessionResponse.prototype.userId = null;


function KalturaStatsEvent()
{
}
KalturaStatsEvent.prototype = new KalturaObjectBase();
/**
 * 
 *
 * @var string
 */
KalturaStatsEvent.prototype.clientVer = null;

/**
 * 
 *
 * @var KalturaStatsEventType
 */
KalturaStatsEvent.prototype.eventType = null;

/**
 * the client's timestamp of this event
	 * 
 *
 * @var float
 */
KalturaStatsEvent.prototype.eventTimestamp = null;

/**
 * a unique string generated by the client that will represent the client-side session: the primary component will pass it on to other components that sprout from it
 *
 * @var string
 */
KalturaStatsEvent.prototype.sessionId = null;

/**
 * 
 *
 * @var int
 */
KalturaStatsEvent.prototype.partnerId = null;

/**
 * 
 *
 * @var string
 */
KalturaStatsEvent.prototype.entryId = null;

/**
 * the UV cookie - creates in the operational system and should be passed on ofr every event 
 *
 * @var string
 */
KalturaStatsEvent.prototype.uniqueViewer = null;

/**
 * 
 *
 * @var string
 */
KalturaStatsEvent.prototype.widgetId = null;

/**
 * 
 *
 * @var int
 */
KalturaStatsEvent.prototype.uiconfId = null;

/**
 * the partner's user id 
 *
 * @var string
 */
KalturaStatsEvent.prototype.userId = null;

/**
 * the timestamp along the video when the event happend 
 *
 * @var int
 */
KalturaStatsEvent.prototype.currentPoint = null;

/**
 * the duration of the video in milliseconds - will make it much faster than quering the db for each entry 
 *
 * @var int
 */
KalturaStatsEvent.prototype.duration = null;

/**
 * will be retrieved from the request of the user 
 *
 * @var string
 * @readonly
 */
KalturaStatsEvent.prototype.userIp = null;

/**
 * the time in milliseconds the event took
 *
 * @var int
 */
KalturaStatsEvent.prototype.processDuration = null;

/**
 * the id of the GUI control - will be used in the future to better understand what the user clicked
 *
 * @var string
 */
KalturaStatsEvent.prototype.controlId = null;

/**
 * true if the user ever used seek in this session 
 *
 * @var bool
 */
KalturaStatsEvent.prototype.seek = null;

/**
 * timestamp of the new point on the timeline of the video after the user seeks 
 *
 * @var int
 */
KalturaStatsEvent.prototype.newPoint = null;

/**
 * the referrer of the client
 *
 * @var string
 */
KalturaStatsEvent.prototype.referrer = null;

/**
 * will indicate if the event is thrown for the first video in the session
 *
 * @var bool
 */
KalturaStatsEvent.prototype.isFirstInSession = null;


function KalturaStatsKmcEvent()
{
}
KalturaStatsKmcEvent.prototype = new KalturaObjectBase();
/**
 * 
 *
 * @var string
 */
KalturaStatsKmcEvent.prototype.clientVer = null;

/**
 * 
 *
 * @var string
 */
KalturaStatsKmcEvent.prototype.kmcEventActionPath = null;

/**
 * 
 *
 * @var KalturaStatsKmcEventType
 */
KalturaStatsKmcEvent.prototype.kmcEventType = null;

/**
 * the client's timestamp of this event
	 * 
 *
 * @var float
 */
KalturaStatsKmcEvent.prototype.eventTimestamp = null;

/**
 * a unique string generated by the client that will represent the client-side session: the primary component will pass it on to other components that sprout from it
 *
 * @var string
 */
KalturaStatsKmcEvent.prototype.sessionId = null;

/**
 * 
 *
 * @var int
 */
KalturaStatsKmcEvent.prototype.partnerId = null;

/**
 * 
 *
 * @var string
 */
KalturaStatsKmcEvent.prototype.entryId = null;

/**
 * 
 *
 * @var string
 */
KalturaStatsKmcEvent.prototype.widgetId = null;

/**
 * 
 *
 * @var int
 */
KalturaStatsKmcEvent.prototype.uiconfId = null;

/**
 * the partner's user id 
 *
 * @var string
 */
KalturaStatsKmcEvent.prototype.userId = null;

/**
 * will be retrieved from the request of the user 
 *
 * @var string
 * @readonly
 */
KalturaStatsKmcEvent.prototype.userIp = null;


function KalturaSyndicationFeedEntryCount()
{
}
KalturaSyndicationFeedEntryCount.prototype = new KalturaObjectBase();
/**
 * the total count of entries that should appear in the feed without flavor filtering
 *
 * @var int
 */
KalturaSyndicationFeedEntryCount.prototype.totalEntryCount = null;

/**
 * count of entries that will appear in the feed (including all relevant filters)
 *
 * @var int
 */
KalturaSyndicationFeedEntryCount.prototype.actualEntryCount = null;

/**
 * count of entries that requires transcoding in order to be included in feed
 *
 * @var int
 */
KalturaSyndicationFeedEntryCount.prototype.requireTranscodingCount = null;


function KalturaSystemUser()
{
}
KalturaSystemUser.prototype = new KalturaObjectBase();
/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaSystemUser.prototype.id = null;

/**
 * 
 *
 * @var string
 */
KalturaSystemUser.prototype.email = null;

/**
 * 
 *
 * @var string
 */
KalturaSystemUser.prototype.firstName = null;

/**
 * 
 *
 * @var string
 */
KalturaSystemUser.prototype.lastName = null;

/**
 * 
 *
 * @var string
 */
KalturaSystemUser.prototype.password = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaSystemUser.prototype.createdBy = null;

/**
 * 
 *
 * @var KalturaSystemUserStatus
 */
KalturaSystemUser.prototype.status = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaSystemUser.prototype.statusUpdatedAt = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaSystemUser.prototype.createdAt = null;


function KalturaSystemUserFilter()
{
}
KalturaSystemUserFilter.prototype = new KalturaFilter();

function KalturaSystemUserListResponse()
{
}
KalturaSystemUserListResponse.prototype = new KalturaObjectBase();
/**
 * 
 *
 * @var KalturaSystemUserArray
 * @readonly
 */
KalturaSystemUserListResponse.prototype.objects = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaSystemUserListResponse.prototype.totalCount = null;


function KalturaTubeMogulSyndicationFeed()
{
}
KalturaTubeMogulSyndicationFeed.prototype = new KalturaBaseSyndicationFeed();
/**
 * 
 *
 * @var KalturaTubeMogulSyndicationFeedCategories
 * @readonly
 */
KalturaTubeMogulSyndicationFeed.prototype.category = null;


function KalturaTubeMogulSyndicationFeedFilter()
{
}
KalturaTubeMogulSyndicationFeedFilter.prototype = new KalturaBaseSyndicationFeedFilter();

function KalturaUiConf()
{
}
KalturaUiConf.prototype = new KalturaObjectBase();
/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaUiConf.prototype.id = null;

/**
 * Name of the uiConf, this is not a primary key
 *
 * @var string
 */
KalturaUiConf.prototype.name = null;

/**
 * 
 *
 * @var string
 */
KalturaUiConf.prototype.description = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaUiConf.prototype.partnerId = null;

/**
 * 
 *
 * @var KalturaUiConfObjType
 */
KalturaUiConf.prototype.objType = null;

/**
 * 
 *
 * @var string
 * @readonly
 */
KalturaUiConf.prototype.objTypeAsString = null;

/**
 * 
 *
 * @var int
 */
KalturaUiConf.prototype.width = null;

/**
 * 
 *
 * @var int
 */
KalturaUiConf.prototype.height = null;

/**
 * 
 *
 * @var string
 */
KalturaUiConf.prototype.htmlParams = null;

/**
 * 
 *
 * @var string
 */
KalturaUiConf.prototype.swfUrl = null;

/**
 * 
 *
 * @var string
 * @readonly
 */
KalturaUiConf.prototype.confFilePath = null;

/**
 * 
 *
 * @var string
 */
KalturaUiConf.prototype.confFile = null;

/**
 * 
 *
 * @var string
 */
KalturaUiConf.prototype.confFileFeatures = null;

/**
 * 
 *
 * @var string
 */
KalturaUiConf.prototype.confVars = null;

/**
 * 
 *
 * @var bool
 */
KalturaUiConf.prototype.useCdn = null;

/**
 * 
 *
 * @var string
 */
KalturaUiConf.prototype.tags = null;

/**
 * 
 *
 * @var string
 */
KalturaUiConf.prototype.swfUrlVersion = null;

/**
 * Entry creation date as Unix timestamp (In seconds)
 *
 * @var int
 * @readonly
 */
KalturaUiConf.prototype.createdAt = null;

/**
 * Entry creation date as Unix timestamp (In seconds)
 *
 * @var int
 * @readonly
 */
KalturaUiConf.prototype.updatedAt = null;

/**
 * 
 *
 * @var KalturaUiConfCreationMode
 */
KalturaUiConf.prototype.creationMode = null;


function KalturaUiConfFilter()
{
}
KalturaUiConfFilter.prototype = new KalturaFilter();
/**
 * 
 *
 * @var int
 */
KalturaUiConfFilter.prototype.idEqual = null;

/**
 * 
 *
 * @var string
 */
KalturaUiConfFilter.prototype.idIn = null;

/**
 * 
 *
 * @var string
 */
KalturaUiConfFilter.prototype.nameLike = null;

/**
 * 
 *
 * @var KalturaUiConfObjType
 */
KalturaUiConfFilter.prototype.objTypeEqual = null;

/**
 * 
 *
 * @var string
 */
KalturaUiConfFilter.prototype.tagsMultiLikeOr = null;

/**
 * 
 *
 * @var string
 */
KalturaUiConfFilter.prototype.tagsMultiLikeAnd = null;

/**
 * 
 *
 * @var int
 */
KalturaUiConfFilter.prototype.createdAtGreaterThanOrEqual = null;

/**
 * 
 *
 * @var int
 */
KalturaUiConfFilter.prototype.createdAtLessThanOrEqual = null;

/**
 * 
 *
 * @var int
 */
KalturaUiConfFilter.prototype.updatedAtGreaterThanOrEqual = null;

/**
 * 
 *
 * @var int
 */
KalturaUiConfFilter.prototype.updatedAtLessThanOrEqual = null;


function KalturaUiConfListResponse()
{
}
KalturaUiConfListResponse.prototype = new KalturaObjectBase();
/**
 * 
 *
 * @var KalturaUiConfArray
 * @readonly
 */
KalturaUiConfListResponse.prototype.objects = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaUiConfListResponse.prototype.totalCount = null;


function KalturaUploadResponse()
{
}
KalturaUploadResponse.prototype = new KalturaObjectBase();
/**
 * 
 *
 * @var string
 */
KalturaUploadResponse.prototype.uploadTokenId = null;

/**
 * 
 *
 * @var int
 */
KalturaUploadResponse.prototype.fileSize = null;

/**
 * 
 *
 * @var KalturaUploadErrorCode
 */
KalturaUploadResponse.prototype.errorCode = null;

/**
 * 
 *
 * @var string
 */
KalturaUploadResponse.prototype.errorDescription = null;


function KalturaUser()
{
}
KalturaUser.prototype = new KalturaObjectBase();
/**
 * 
 *
 * @var string
 */
KalturaUser.prototype.id = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaUser.prototype.partnerId = null;

/**
 * 
 *
 * @var string
 */
KalturaUser.prototype.screenName = null;

/**
 * 
 *
 * @var string
 */
KalturaUser.prototype.fullName = null;

/**
 * 
 *
 * @var string
 */
KalturaUser.prototype.email = null;

/**
 * 
 *
 * @var int
 */
KalturaUser.prototype.dateOfBirth = null;

/**
 * 
 *
 * @var string
 */
KalturaUser.prototype.country = null;

/**
 * 
 *
 * @var string
 */
KalturaUser.prototype.state = null;

/**
 * 
 *
 * @var string
 */
KalturaUser.prototype.city = null;

/**
 * 
 *
 * @var string
 */
KalturaUser.prototype.zip = null;

/**
 * 
 *
 * @var string
 */
KalturaUser.prototype.thumbnailUrl = null;

/**
 * 
 *
 * @var string
 */
KalturaUser.prototype.description = null;

/**
 * 
 *
 * @var string
 */
KalturaUser.prototype.tags = null;

/**
 * Admin tags can be updated only by using an admin session
 *
 * @var string
 */
KalturaUser.prototype.adminTags = null;

/**
 * 
 *
 * @var KalturaGender
 */
KalturaUser.prototype.gender = null;

/**
 * 
 *
 * @var KalturaUserStatus
 */
KalturaUser.prototype.status = null;

/**
 * Creation date as Unix timestamp (In seconds)
 *
 * @var int
 * @readonly
 */
KalturaUser.prototype.createdAt = null;

/**
 * Last update date as Unix timestamp (In seconds)
 *
 * @var int
 * @readonly
 */
KalturaUser.prototype.updatedAt = null;

/**
 * Can be used to store various partner related data as a string 
 *
 * @var string
 */
KalturaUser.prototype.partnerData = null;

/**
 * 
 *
 * @var int
 */
KalturaUser.prototype.indexedPartnerDataInt = null;

/**
 * 
 *
 * @var string
 */
KalturaUser.prototype.indexedPartnerDataString = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaUser.prototype.storageSize = null;


function KalturaUserFilter()
{
}
KalturaUserFilter.prototype = new KalturaFilter();
/**
 * 
 *
 * @var string
 */
KalturaUserFilter.prototype.idEqual = null;

/**
 * 
 *
 * @var string
 */
KalturaUserFilter.prototype.idIn = null;

/**
 * 
 *
 * @var int
 */
KalturaUserFilter.prototype.partnerIdEqual = null;

/**
 * 
 *
 * @var string
 */
KalturaUserFilter.prototype.screenNameLike = null;

/**
 * 
 *
 * @var string
 */
KalturaUserFilter.prototype.screenNameStartsWith = null;

/**
 * 
 *
 * @var string
 */
KalturaUserFilter.prototype.emailLike = null;

/**
 * 
 *
 * @var string
 */
KalturaUserFilter.prototype.emailStartsWith = null;

/**
 * 
 *
 * @var string
 */
KalturaUserFilter.prototype.tagsMultiLikeOr = null;

/**
 * 
 *
 * @var string
 */
KalturaUserFilter.prototype.tagsMultiLikeAnd = null;

/**
 * 
 *
 * @var int
 */
KalturaUserFilter.prototype.createdAtGreaterThanOrEqual = null;

/**
 * 
 *
 * @var int
 */
KalturaUserFilter.prototype.createdAtLessThanOrEqual = null;


function KalturaUserListResponse()
{
}
KalturaUserListResponse.prototype = new KalturaObjectBase();
/**
 * 
 *
 * @var KalturaUserArray
 * @readonly
 */
KalturaUserListResponse.prototype.objects = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaUserListResponse.prototype.totalCount = null;


function KalturaWidget()
{
}
KalturaWidget.prototype = new KalturaObjectBase();
/**
 * 
 *
 * @var string
 * @readonly
 */
KalturaWidget.prototype.id = null;

/**
 * 
 *
 * @var string
 */
KalturaWidget.prototype.sourceWidgetId = null;

/**
 * 
 *
 * @var string
 * @readonly
 */
KalturaWidget.prototype.rootWidgetId = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaWidget.prototype.partnerId = null;

/**
 * 
 *
 * @var string
 */
KalturaWidget.prototype.entryId = null;

/**
 * 
 *
 * @var int
 */
KalturaWidget.prototype.uiConfId = null;

/**
 * 
 *
 * @var KalturaWidgetSecurityType
 */
KalturaWidget.prototype.securityType = null;

/**
 * 
 *
 * @var int
 */
KalturaWidget.prototype.securityPolicy = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaWidget.prototype.createdAt = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaWidget.prototype.updatedAt = null;

/**
 * Can be used to store various partner related data as a string 
 *
 * @var string
 */
KalturaWidget.prototype.partnerData = null;

/**
 * 
 *
 * @var string
 * @readonly
 */
KalturaWidget.prototype.widgetHTML = null;


function KalturaWidgetFilter()
{
}
KalturaWidgetFilter.prototype = new KalturaFilter();
/**
 * 
 *
 * @var string
 */
KalturaWidgetFilter.prototype.idEqual = null;

/**
 * 
 *
 * @var string
 */
KalturaWidgetFilter.prototype.idIn = null;

/**
 * 
 *
 * @var string
 */
KalturaWidgetFilter.prototype.sourceWidgetIdEqual = null;

/**
 * 
 *
 * @var string
 */
KalturaWidgetFilter.prototype.rootWidgetIdEqual = null;

/**
 * 
 *
 * @var int
 */
KalturaWidgetFilter.prototype.partnerIdEqual = null;

/**
 * 
 *
 * @var string
 */
KalturaWidgetFilter.prototype.entryIdEqual = null;

/**
 * 
 *
 * @var int
 */
KalturaWidgetFilter.prototype.uiConfIdEqual = null;

/**
 * 
 *
 * @var int
 */
KalturaWidgetFilter.prototype.createdAtGreaterThanOrEqual = null;

/**
 * 
 *
 * @var int
 */
KalturaWidgetFilter.prototype.createdAtLessThanOrEqual = null;

/**
 * 
 *
 * @var int
 */
KalturaWidgetFilter.prototype.updatedAtGreaterThanOrEqual = null;

/**
 * 
 *
 * @var int
 */
KalturaWidgetFilter.prototype.updatedAtLessThanOrEqual = null;

/**
 * 
 *
 * @var string
 */
KalturaWidgetFilter.prototype.partnerDataLike = null;


function KalturaWidgetListResponse()
{
}
KalturaWidgetListResponse.prototype = new KalturaObjectBase();
/**
 * 
 *
 * @var KalturaWidgetArray
 * @readonly
 */
KalturaWidgetListResponse.prototype.objects = null;

/**
 * 
 *
 * @var int
 * @readonly
 */
KalturaWidgetListResponse.prototype.totalCount = null;


function KalturaYahooSyndicationFeed()
{
}
KalturaYahooSyndicationFeed.prototype = new KalturaBaseSyndicationFeed();
/**
 * 
 *
 * @var KalturaYahooSyndicationFeedCategories
 * @readonly
 */
KalturaYahooSyndicationFeed.prototype.category = null;

/**
 * 
 *
 * @var KalturaYahooSyndicationFeedAdultValues
 */
KalturaYahooSyndicationFeed.prototype.adultContent = null;

/**
 * feed description
	 * 
 *
 * @var string
 */
KalturaYahooSyndicationFeed.prototype.feedDescription = null;

/**
 * feed landing page (i.e publisher website)
	 * 
 *
 * @var string
 */
KalturaYahooSyndicationFeed.prototype.feedLandingPage = null;


function KalturaYahooSyndicationFeedFilter()
{
}
KalturaYahooSyndicationFeedFilter.prototype = new KalturaBaseSyndicationFeedFilter();


function KalturaAccessControlService(client)
{
	this.init(client);
}

KalturaAccessControlService.prototype = new KalturaServiceBase();

KalturaAccessControlService.prototype.add = function(callback, accessControl)
{

	kparams = new Object();
	this.client.addParam(kparams, "accessControl", accessControl.toParams());
	this.client.queueServiceActionCall("accesscontrol", "add", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaAccessControlService.prototype.get = function(callback, id)
{

	kparams = new Object();
	this.client.addParam(kparams, "id", id);
	this.client.queueServiceActionCall("accesscontrol", "get", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaAccessControlService.prototype.update = function(callback, id, accessControl)
{

	kparams = new Object();
	this.client.addParam(kparams, "id", id);
	this.client.addParam(kparams, "accessControl", accessControl.toParams());
	this.client.queueServiceActionCall("accesscontrol", "update", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaAccessControlService.prototype.delete = function(callback, id)
{

	kparams = new Object();
	this.client.addParam(kparams, "id", id);
	this.client.queueServiceActionCall("accesscontrol", "delete", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaAccessControlService.prototype.listAction = function(callback, filter, pager)
{
	if(!filter)
		filter = null;
	if(!pager)
		pager = null;

	kparams = new Object();
	if (filter != null)
		this.client.addParam(kparams, "filter", filter.toParams());
	if (pager != null)
		this.client.addParam(kparams, "pager", pager.toParams());
	this.client.queueServiceActionCall("accesscontrol", "list", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

function KalturaAdminconsoleService(client)
{
	this.init(client);
}

KalturaAdminconsoleService.prototype = new KalturaServiceBase();

KalturaAdminconsoleService.prototype.listBatchJobs = function(callback, filter, pager)
{
	if(!filter)
		filter = null;
	if(!pager)
		pager = null;

	kparams = new Object();
	if (filter != null)
		this.client.addParam(kparams, "filter", toParams(filter));
	if (pager != null)
		this.client.addParam(kparams, "pager", toParams(pager));
	this.client.queueServiceActionCall("adminconsole", "listBatchJobs", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

function KalturaAdminUserService(client)
{
	this.init(client);
}

KalturaAdminUserService.prototype = new KalturaServiceBase();

KalturaAdminUserService.prototype.updatePassword = function(callback, email, password, newEmail, newPassword)
{
	if(!newEmail)
		newEmail = "";
	if(!newPassword)
		newPassword = "";

	kparams = new Object();
	this.client.addParam(kparams, "email", email);
	this.client.addParam(kparams, "password", password);
	this.client.addParam(kparams, "newEmail", newEmail);
	this.client.addParam(kparams, "newPassword", newPassword);
	this.client.queueServiceActionCall("adminuser", "updatePassword", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaAdminUserService.prototype.resetPassword = function(callback, email)
{

	kparams = new Object();
	this.client.addParam(kparams, "email", email);
	this.client.queueServiceActionCall("adminuser", "resetPassword", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaAdminUserService.prototype.login = function(callback, email, password)
{

	kparams = new Object();
	this.client.addParam(kparams, "email", email);
	this.client.addParam(kparams, "password", password);
	this.client.queueServiceActionCall("adminuser", "login", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

function KalturaBaseEntryService(client)
{
	this.init(client);
}

KalturaBaseEntryService.prototype = new KalturaServiceBase();

KalturaBaseEntryService.prototype.addFromUploadedFile = function(callback, entry, uploadTokenId, type)
{
	if(!type)
		type = -1;

	kparams = new Object();
	this.client.addParam(kparams, "entry", entry.toParams());
	this.client.addParam(kparams, "uploadTokenId", uploadTokenId);
	this.client.addParam(kparams, "type", type);
	this.client.queueServiceActionCall("baseentry", "addFromUploadedFile", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaBaseEntryService.prototype.get = function(callback, entryId, version)
{
	if(!version)
		version = -1;

	kparams = new Object();
	this.client.addParam(kparams, "entryId", entryId);
	this.client.addParam(kparams, "version", version);
	this.client.queueServiceActionCall("baseentry", "get", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaBaseEntryService.prototype.update = function(callback, entryId, baseEntry)
{

	kparams = new Object();
	this.client.addParam(kparams, "entryId", entryId);
	this.client.addParam(kparams, "baseEntry", baseEntry.toParams());
	this.client.queueServiceActionCall("baseentry", "update", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaBaseEntryService.prototype.getByIds = function(callback, entryIds)
{

	kparams = new Object();
	this.client.addParam(kparams, "entryIds", entryIds);
	this.client.queueServiceActionCall("baseentry", "getByIds", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaBaseEntryService.prototype.delete = function(callback, entryId)
{

	kparams = new Object();
	this.client.addParam(kparams, "entryId", entryId);
	this.client.queueServiceActionCall("baseentry", "delete", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaBaseEntryService.prototype.listAction = function(callback, filter, pager)
{
	if(!filter)
		filter = null;
	if(!pager)
		pager = null;

	kparams = new Object();
	if (filter != null)
		this.client.addParam(kparams, "filter", filter.toParams());
	if (pager != null)
		this.client.addParam(kparams, "pager", pager.toParams());
	this.client.queueServiceActionCall("baseentry", "list", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaBaseEntryService.prototype.count = function(callback, filter)
{
	if(!filter)
		filter = null;

	kparams = new Object();
	if (filter != null)
		this.client.addParam(kparams, "filter", filter.toParams());
	this.client.queueServiceActionCall("baseentry", "count", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaBaseEntryService.prototype.upload = function(callback, fileData)
{

	kparams = new Object();
	kfiles = new Object();
	this.client.addParam(kfiles, "fileData", fileData);
	this.client.queueServiceActionCall("baseentry", "upload", kparams, kfiles);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaBaseEntryService.prototype.updateThumbnailJpeg = function(callback, entryId, fileData)
{

	kparams = new Object();
	this.client.addParam(kparams, "entryId", entryId);
	kfiles = new Object();
	this.client.addParam(kfiles, "fileData", fileData);
	this.client.queueServiceActionCall("baseentry", "updateThumbnailJpeg", kparams, kfiles);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaBaseEntryService.prototype.updateThumbnailFromUrl = function(callback, entryId, url)
{

	kparams = new Object();
	this.client.addParam(kparams, "entryId", entryId);
	this.client.addParam(kparams, "url", url);
	this.client.queueServiceActionCall("baseentry", "updateThumbnailFromUrl", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaBaseEntryService.prototype.updateThumbnailFromSourceEntry = function(callback, entryId, sourceEntryId, timeOffset)
{

	kparams = new Object();
	this.client.addParam(kparams, "entryId", entryId);
	this.client.addParam(kparams, "sourceEntryId", sourceEntryId);
	this.client.addParam(kparams, "timeOffset", timeOffset);
	this.client.queueServiceActionCall("baseentry", "updateThumbnailFromSourceEntry", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaBaseEntryService.prototype.flag = function(callback, moderationFlag)
{

	kparams = new Object();
	this.client.addParam(kparams, "moderationFlag", moderationFlag.toParams());
	this.client.queueServiceActionCall("baseentry", "flag", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaBaseEntryService.prototype.reject = function(callback, entryId)
{

	kparams = new Object();
	this.client.addParam(kparams, "entryId", entryId);
	this.client.queueServiceActionCall("baseentry", "reject", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaBaseEntryService.prototype.approve = function(callback, entryId)
{

	kparams = new Object();
	this.client.addParam(kparams, "entryId", entryId);
	this.client.queueServiceActionCall("baseentry", "approve", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaBaseEntryService.prototype.listFlags = function(callback, entryId, pager)
{
	if(!pager)
		pager = null;

	kparams = new Object();
	this.client.addParam(kparams, "entryId", entryId);
	if (pager != null)
		this.client.addParam(kparams, "pager", pager.toParams());
	this.client.queueServiceActionCall("baseentry", "listFlags", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaBaseEntryService.prototype.anonymousRank = function(callback, entryId, rank)
{

	kparams = new Object();
	this.client.addParam(kparams, "entryId", entryId);
	this.client.addParam(kparams, "rank", rank);
	this.client.queueServiceActionCall("baseentry", "anonymousRank", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaBaseEntryService.prototype.getExtraData = function(callback, entryId, extraDataParams)
{

	kparams = new Object();
	this.client.addParam(kparams, "entryId", entryId);
	this.client.addParam(kparams, "extraDataParams", extraDataParams.toParams());
	this.client.queueServiceActionCall("baseentry", "getExtraData", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

function KalturaBulkUploadService(client)
{
	this.init(client);
}

KalturaBulkUploadService.prototype = new KalturaServiceBase();

KalturaBulkUploadService.prototype.add = function(callback, conversionProfileId, csvFileData)
{

	kparams = new Object();
	this.client.addParam(kparams, "conversionProfileId", conversionProfileId);
	kfiles = new Object();
	this.client.addParam(kfiles, "csvFileData", csvFileData);
	this.client.queueServiceActionCall("bulkupload", "add", kparams, kfiles);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaBulkUploadService.prototype.get = function(callback, id)
{

	kparams = new Object();
	this.client.addParam(kparams, "id", id);
	this.client.queueServiceActionCall("bulkupload", "get", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaBulkUploadService.prototype.listAction = function(callback, pager)
{
	if(!pager)
		pager = null;

	kparams = new Object();
	if (pager != null)
		this.client.addParam(kparams, "pager", pager.toParams());
	this.client.queueServiceActionCall("bulkupload", "list", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

function KalturaCategoryService(client)
{
	this.init(client);
}

KalturaCategoryService.prototype = new KalturaServiceBase();

KalturaCategoryService.prototype.add = function(callback, category)
{

	kparams = new Object();
	this.client.addParam(kparams, "category", category.toParams());
	this.client.queueServiceActionCall("category", "add", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaCategoryService.prototype.get = function(callback, id)
{

	kparams = new Object();
	this.client.addParam(kparams, "id", id);
	this.client.queueServiceActionCall("category", "get", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaCategoryService.prototype.update = function(callback, id, category)
{

	kparams = new Object();
	this.client.addParam(kparams, "id", id);
	this.client.addParam(kparams, "category", category.toParams());
	this.client.queueServiceActionCall("category", "update", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaCategoryService.prototype.delete = function(callback, id)
{

	kparams = new Object();
	this.client.addParam(kparams, "id", id);
	this.client.queueServiceActionCall("category", "delete", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaCategoryService.prototype.listAction = function(callback, filter)
{
	if(!filter)
		filter = null;

	kparams = new Object();
	if (filter != null)
		this.client.addParam(kparams, "filter", filter.toParams());
	this.client.queueServiceActionCall("category", "list", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

function KalturaConversionProfileService(client)
{
	this.init(client);
}

KalturaConversionProfileService.prototype = new KalturaServiceBase();

KalturaConversionProfileService.prototype.add = function(callback, conversionProfile)
{

	kparams = new Object();
	this.client.addParam(kparams, "conversionProfile", conversionProfile.toParams());
	this.client.queueServiceActionCall("conversionprofile", "add", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaConversionProfileService.prototype.get = function(callback, id)
{

	kparams = new Object();
	this.client.addParam(kparams, "id", id);
	this.client.queueServiceActionCall("conversionprofile", "get", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaConversionProfileService.prototype.update = function(callback, id, conversionProfile)
{

	kparams = new Object();
	this.client.addParam(kparams, "id", id);
	this.client.addParam(kparams, "conversionProfile", conversionProfile.toParams());
	this.client.queueServiceActionCall("conversionprofile", "update", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaConversionProfileService.prototype.delete = function(callback, id)
{

	kparams = new Object();
	this.client.addParam(kparams, "id", id);
	this.client.queueServiceActionCall("conversionprofile", "delete", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaConversionProfileService.prototype.listAction = function(callback, filter, pager)
{
	if(!filter)
		filter = null;
	if(!pager)
		pager = null;

	kparams = new Object();
	if (filter != null)
		this.client.addParam(kparams, "filter", filter.toParams());
	if (pager != null)
		this.client.addParam(kparams, "pager", pager.toParams());
	this.client.queueServiceActionCall("conversionprofile", "list", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

function KalturaDataService(client)
{
	this.init(client);
}

KalturaDataService.prototype = new KalturaServiceBase();

KalturaDataService.prototype.add = function(callback, dataEntry)
{

	kparams = new Object();
	this.client.addParam(kparams, "dataEntry", dataEntry.toParams());
	this.client.queueServiceActionCall("data", "add", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaDataService.prototype.get = function(callback, entryId, version)
{
	if(!version)
		version = -1;

	kparams = new Object();
	this.client.addParam(kparams, "entryId", entryId);
	this.client.addParam(kparams, "version", version);
	this.client.queueServiceActionCall("data", "get", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaDataService.prototype.update = function(callback, entryId, documentEntry)
{

	kparams = new Object();
	this.client.addParam(kparams, "entryId", entryId);
	this.client.addParam(kparams, "documentEntry", documentEntry.toParams());
	this.client.queueServiceActionCall("data", "update", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaDataService.prototype.delete = function(callback, entryId)
{

	kparams = new Object();
	this.client.addParam(kparams, "entryId", entryId);
	this.client.queueServiceActionCall("data", "delete", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaDataService.prototype.listAction = function(callback, filter, pager)
{
	if(!filter)
		filter = null;
	if(!pager)
		pager = null;

	kparams = new Object();
	if (filter != null)
		this.client.addParam(kparams, "filter", filter.toParams());
	if (pager != null)
		this.client.addParam(kparams, "pager", pager.toParams());
	this.client.queueServiceActionCall("data", "list", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

function KalturaFlavorAssetService(client)
{
	this.init(client);
}

KalturaFlavorAssetService.prototype = new KalturaServiceBase();

KalturaFlavorAssetService.prototype.get = function(callback, id)
{

	kparams = new Object();
	this.client.addParam(kparams, "id", id);
	this.client.queueServiceActionCall("flavorasset", "get", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaFlavorAssetService.prototype.getByEntryId = function(callback, entryId)
{

	kparams = new Object();
	this.client.addParam(kparams, "entryId", entryId);
	this.client.queueServiceActionCall("flavorasset", "getByEntryId", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaFlavorAssetService.prototype.getWebPlayableByEntryId = function(callback, entryId)
{

	kparams = new Object();
	this.client.addParam(kparams, "entryId", entryId);
	this.client.queueServiceActionCall("flavorasset", "getWebPlayableByEntryId", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaFlavorAssetService.prototype.convert = function(callback, entryId, flavorParamsId)
{

	kparams = new Object();
	this.client.addParam(kparams, "entryId", entryId);
	this.client.addParam(kparams, "flavorParamsId", flavorParamsId);
	this.client.queueServiceActionCall("flavorasset", "convert", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaFlavorAssetService.prototype.reconvert = function(callback, id)
{

	kparams = new Object();
	this.client.addParam(kparams, "id", id);
	this.client.queueServiceActionCall("flavorasset", "reconvert", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaFlavorAssetService.prototype.delete = function(callback, id)
{

	kparams = new Object();
	this.client.addParam(kparams, "id", id);
	this.client.queueServiceActionCall("flavorasset", "delete", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaFlavorAssetService.prototype.getDownloadUrl = function(callback, id)
{

	kparams = new Object();
	this.client.addParam(kparams, "id", id);
	this.client.queueServiceActionCall("flavorasset", "getDownloadUrl", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaFlavorAssetService.prototype.getFlavorAssetsWithParams = function(callback, entryId)
{

	kparams = new Object();
	this.client.addParam(kparams, "entryId", entryId);
	this.client.queueServiceActionCall("flavorasset", "getFlavorAssetsWithParams", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

function KalturaFlavorParamsService(client)
{
	this.init(client);
}

KalturaFlavorParamsService.prototype = new KalturaServiceBase();

KalturaFlavorParamsService.prototype.add = function(callback, flavorParams)
{

	kparams = new Object();
	this.client.addParam(kparams, "flavorParams", flavorParams.toParams());
	this.client.queueServiceActionCall("flavorparams", "add", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaFlavorParamsService.prototype.get = function(callback, id)
{

	kparams = new Object();
	this.client.addParam(kparams, "id", id);
	this.client.queueServiceActionCall("flavorparams", "get", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaFlavorParamsService.prototype.update = function(callback, id, flavorParams)
{

	kparams = new Object();
	this.client.addParam(kparams, "id", id);
	this.client.addParam(kparams, "flavorParams", flavorParams.toParams());
	this.client.queueServiceActionCall("flavorparams", "update", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaFlavorParamsService.prototype.delete = function(callback, id)
{

	kparams = new Object();
	this.client.addParam(kparams, "id", id);
	this.client.queueServiceActionCall("flavorparams", "delete", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaFlavorParamsService.prototype.listAction = function(callback, filter, pager)
{
	if(!filter)
		filter = null;
	if(!pager)
		pager = null;

	kparams = new Object();
	if (filter != null)
		this.client.addParam(kparams, "filter", filter.toParams());
	if (pager != null)
		this.client.addParam(kparams, "pager", pager.toParams());
	this.client.queueServiceActionCall("flavorparams", "list", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaFlavorParamsService.prototype.getByConversionProfileId = function(callback, conversionProfileId)
{

	kparams = new Object();
	this.client.addParam(kparams, "conversionProfileId", conversionProfileId);
	this.client.queueServiceActionCall("flavorparams", "getByConversionProfileId", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

function KalturaJobsService(client)
{
	this.init(client);
}

KalturaJobsService.prototype = new KalturaServiceBase();

KalturaJobsService.prototype.getImportStatus = function(callback, jobId)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.queueServiceActionCall("jobs", "getImportStatus", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.deleteImport = function(callback, jobId)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.queueServiceActionCall("jobs", "deleteImport", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.abortImport = function(callback, jobId)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.queueServiceActionCall("jobs", "abortImport", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.retryImport = function(callback, jobId)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.queueServiceActionCall("jobs", "retryImport", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.getBulkUploadStatus = function(callback, jobId)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.queueServiceActionCall("jobs", "getBulkUploadStatus", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.deleteBulkUpload = function(callback, jobId)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.queueServiceActionCall("jobs", "deleteBulkUpload", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.abortBulkUpload = function(callback, jobId)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.queueServiceActionCall("jobs", "abortBulkUpload", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.retryBulkUpload = function(callback, jobId)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.queueServiceActionCall("jobs", "retryBulkUpload", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.getConvertStatus = function(callback, jobId)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.queueServiceActionCall("jobs", "getConvertStatus", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.getConvertProfileStatus = function(callback, jobId)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.queueServiceActionCall("jobs", "getConvertProfileStatus", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.getRemoteConvertStatus = function(callback, jobId)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.queueServiceActionCall("jobs", "getRemoteConvertStatus", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.deleteConvert = function(callback, jobId)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.queueServiceActionCall("jobs", "deleteConvert", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.abortConvert = function(callback, jobId)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.queueServiceActionCall("jobs", "abortConvert", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.retryConvert = function(callback, jobId)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.queueServiceActionCall("jobs", "retryConvert", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.deleteRemoteConvert = function(callback, jobId)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.queueServiceActionCall("jobs", "deleteRemoteConvert", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.abortRemoteConvert = function(callback, jobId)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.queueServiceActionCall("jobs", "abortRemoteConvert", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.retryRemoteConvert = function(callback, jobId)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.queueServiceActionCall("jobs", "retryRemoteConvert", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.deleteConvertProfile = function(callback, jobId)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.queueServiceActionCall("jobs", "deleteConvertProfile", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.abortConvertProfile = function(callback, jobId)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.queueServiceActionCall("jobs", "abortConvertProfile", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.retryConvertProfile = function(callback, jobId)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.queueServiceActionCall("jobs", "retryConvertProfile", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.getPostConvertStatus = function(callback, jobId)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.queueServiceActionCall("jobs", "getPostConvertStatus", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.deletePostConvert = function(callback, jobId)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.queueServiceActionCall("jobs", "deletePostConvert", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.abortPostConvert = function(callback, jobId)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.queueServiceActionCall("jobs", "abortPostConvert", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.retryPostConvert = function(callback, jobId)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.queueServiceActionCall("jobs", "retryPostConvert", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.getPullStatus = function(callback, jobId)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.queueServiceActionCall("jobs", "getPullStatus", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.deletePull = function(callback, jobId)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.queueServiceActionCall("jobs", "deletePull", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.abortPull = function(callback, jobId)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.queueServiceActionCall("jobs", "abortPull", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.retryPull = function(callback, jobId)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.queueServiceActionCall("jobs", "retryPull", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.getExtractMediaStatus = function(callback, jobId)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.queueServiceActionCall("jobs", "getExtractMediaStatus", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.deleteExtractMedia = function(callback, jobId)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.queueServiceActionCall("jobs", "deleteExtractMedia", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.abortExtractMedia = function(callback, jobId)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.queueServiceActionCall("jobs", "abortExtractMedia", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.retryExtractMedia = function(callback, jobId)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.queueServiceActionCall("jobs", "retryExtractMedia", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.getNotificationStatus = function(callback, jobId)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.queueServiceActionCall("jobs", "getNotificationStatus", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.deleteNotification = function(callback, jobId)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.queueServiceActionCall("jobs", "deleteNotification", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.abortNotification = function(callback, jobId)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.queueServiceActionCall("jobs", "abortNotification", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.retryNotification = function(callback, jobId)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.queueServiceActionCall("jobs", "retryNotification", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.getMailStatus = function(callback, jobId)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.queueServiceActionCall("jobs", "getMailStatus", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.deleteMail = function(callback, jobId)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.queueServiceActionCall("jobs", "deleteMail", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.abortMail = function(callback, jobId)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.queueServiceActionCall("jobs", "abortMail", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.retryMail = function(callback, jobId)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.queueServiceActionCall("jobs", "retryMail", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.addMailJob = function(callback, mailJobData)
{

	kparams = new Object();
	this.client.addParam(kparams, "mailJobData", mailJobData.toParams());
	this.client.queueServiceActionCall("jobs", "addMailJob", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.addBatchJob = function(callback, batchJob)
{

	kparams = new Object();
	this.client.addParam(kparams, "batchJob", batchJob.toParams());
	this.client.queueServiceActionCall("jobs", "addBatchJob", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.getStatus = function(callback, jobId, jobType)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.addParam(kparams, "jobType", jobType);
	this.client.queueServiceActionCall("jobs", "getStatus", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.deleteJob = function(callback, jobId, jobType)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.addParam(kparams, "jobType", jobType);
	this.client.queueServiceActionCall("jobs", "deleteJob", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.abortJob = function(callback, jobId, jobType)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.addParam(kparams, "jobType", jobType);
	this.client.queueServiceActionCall("jobs", "abortJob", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.retryJob = function(callback, jobId, jobType)
{

	kparams = new Object();
	this.client.addParam(kparams, "jobId", jobId);
	this.client.addParam(kparams, "jobType", jobType);
	this.client.queueServiceActionCall("jobs", "retryJob", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaJobsService.prototype.listBatchJobs = function(callback, filter, pager)
{
	if(!filter)
		filter = null;
	if(!pager)
		pager = null;

	kparams = new Object();
	if (filter != null)
		this.client.addParam(kparams, "filter", toParams(filter));
	if (pager != null)
		this.client.addParam(kparams, "pager", toParams(pager));
	this.client.queueServiceActionCall("jobs", "listBatchJobs", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

function KalturaMediaService(client)
{
	this.init(client);
}

KalturaMediaService.prototype = new KalturaServiceBase();

KalturaMediaService.prototype.addFromBulk = function(callback, mediaEntry, url, bulkUploadId)
{

	kparams = new Object();
	this.client.addParam(kparams, "mediaEntry", mediaEntry.toParams());
	this.client.addParam(kparams, "url", url);
	this.client.addParam(kparams, "bulkUploadId", bulkUploadId);
	this.client.queueServiceActionCall("media", "addFromBulk", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaMediaService.prototype.addFromUrl = function(callback, mediaEntry, url)
{

	kparams = new Object();
	this.client.addParam(kparams, "mediaEntry", mediaEntry.toParams());
	this.client.addParam(kparams, "url", url);
	this.client.queueServiceActionCall("media", "addFromUrl", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaMediaService.prototype.addFromSearchResult = function(callback, mediaEntry, searchResult)
{
	if(!mediaEntry)
		mediaEntry = null;
	if(!searchResult)
		searchResult = null;

	kparams = new Object();
	if (mediaEntry != null)
		this.client.addParam(kparams, "mediaEntry", mediaEntry.toParams());
	if (searchResult != null)
		this.client.addParam(kparams, "searchResult", searchResult.toParams());
	this.client.queueServiceActionCall("media", "addFromSearchResult", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaMediaService.prototype.addFromUploadedFile = function(callback, mediaEntry, uploadTokenId)
{

	kparams = new Object();
	this.client.addParam(kparams, "mediaEntry", mediaEntry.toParams());
	this.client.addParam(kparams, "uploadTokenId", uploadTokenId);
	this.client.queueServiceActionCall("media", "addFromUploadedFile", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaMediaService.prototype.addFromRecordedWebcam = function(callback, mediaEntry, webcamTokenId)
{

	kparams = new Object();
	this.client.addParam(kparams, "mediaEntry", mediaEntry.toParams());
	this.client.addParam(kparams, "webcamTokenId", webcamTokenId);
	this.client.queueServiceActionCall("media", "addFromRecordedWebcam", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaMediaService.prototype.get = function(callback, entryId, version)
{
	if(!version)
		version = -1;

	kparams = new Object();
	this.client.addParam(kparams, "entryId", entryId);
	this.client.addParam(kparams, "version", version);
	this.client.queueServiceActionCall("media", "get", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaMediaService.prototype.update = function(callback, entryId, mediaEntry)
{

	kparams = new Object();
	this.client.addParam(kparams, "entryId", entryId);
	this.client.addParam(kparams, "mediaEntry", mediaEntry.toParams());
	this.client.queueServiceActionCall("media", "update", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaMediaService.prototype.delete = function(callback, entryId)
{

	kparams = new Object();
	this.client.addParam(kparams, "entryId", entryId);
	this.client.queueServiceActionCall("media", "delete", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaMediaService.prototype.listAction = function(callback, filter, pager)
{
	if(!filter)
		filter = null;
	if(!pager)
		pager = null;

	kparams = new Object();
	if (filter != null)
		this.client.addParam(kparams, "filter", filter.toParams());
	if (pager != null)
		this.client.addParam(kparams, "pager", pager.toParams());
	this.client.queueServiceActionCall("media", "list", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaMediaService.prototype.count = function(callback, filter)
{
	if(!filter)
		filter = null;

	kparams = new Object();
	if (filter != null)
		this.client.addParam(kparams, "filter", filter.toParams());
	this.client.queueServiceActionCall("media", "count", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaMediaService.prototype.upload = function(callback, fileData)
{

	kparams = new Object();
	kfiles = new Object();
	this.client.addParam(kfiles, "fileData", fileData);
	this.client.queueServiceActionCall("media", "upload", kparams, kfiles);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaMediaService.prototype.updateThumbnail = function(callback, entryId, timeOffset)
{

	kparams = new Object();
	this.client.addParam(kparams, "entryId", entryId);
	this.client.addParam(kparams, "timeOffset", timeOffset);
	this.client.queueServiceActionCall("media", "updateThumbnail", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaMediaService.prototype.updateThumbnailFromSourceEntry = function(callback, entryId, sourceEntryId, timeOffset)
{

	kparams = new Object();
	this.client.addParam(kparams, "entryId", entryId);
	this.client.addParam(kparams, "sourceEntryId", sourceEntryId);
	this.client.addParam(kparams, "timeOffset", timeOffset);
	this.client.queueServiceActionCall("media", "updateThumbnailFromSourceEntry", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaMediaService.prototype.updateThumbnailJpeg = function(callback, entryId, fileData)
{

	kparams = new Object();
	this.client.addParam(kparams, "entryId", entryId);
	kfiles = new Object();
	this.client.addParam(kfiles, "fileData", fileData);
	this.client.queueServiceActionCall("media", "updateThumbnailJpeg", kparams, kfiles);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaMediaService.prototype.updateThumbnailFromUrl = function(callback, entryId, url)
{

	kparams = new Object();
	this.client.addParam(kparams, "entryId", entryId);
	this.client.addParam(kparams, "url", url);
	this.client.queueServiceActionCall("media", "updateThumbnailFromUrl", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaMediaService.prototype.requestConversion = function(callback, entryId, fileFormat)
{

	kparams = new Object();
	this.client.addParam(kparams, "entryId", entryId);
	this.client.addParam(kparams, "fileFormat", fileFormat);
	this.client.queueServiceActionCall("media", "requestConversion", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaMediaService.prototype.flag = function(callback, moderationFlag)
{

	kparams = new Object();
	this.client.addParam(kparams, "moderationFlag", moderationFlag.toParams());
	this.client.queueServiceActionCall("media", "flag", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaMediaService.prototype.reject = function(callback, entryId)
{

	kparams = new Object();
	this.client.addParam(kparams, "entryId", entryId);
	this.client.queueServiceActionCall("media", "reject", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaMediaService.prototype.approve = function(callback, entryId)
{

	kparams = new Object();
	this.client.addParam(kparams, "entryId", entryId);
	this.client.queueServiceActionCall("media", "approve", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaMediaService.prototype.listFlags = function(callback, entryId, pager)
{
	if(!pager)
		pager = null;

	kparams = new Object();
	this.client.addParam(kparams, "entryId", entryId);
	if (pager != null)
		this.client.addParam(kparams, "pager", pager.toParams());
	this.client.queueServiceActionCall("media", "listFlags", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaMediaService.prototype.anonymousRank = function(callback, entryId, rank)
{

	kparams = new Object();
	this.client.addParam(kparams, "entryId", entryId);
	this.client.addParam(kparams, "rank", rank);
	this.client.queueServiceActionCall("media", "anonymousRank", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

function KalturaMixingService(client)
{
	this.init(client);
}

KalturaMixingService.prototype = new KalturaServiceBase();

KalturaMixingService.prototype.add = function(callback, mixEntry)
{

	kparams = new Object();
	this.client.addParam(kparams, "mixEntry", mixEntry.toParams());
	this.client.queueServiceActionCall("mixing", "add", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaMixingService.prototype.get = function(callback, entryId, version)
{
	if(!version)
		version = -1;

	kparams = new Object();
	this.client.addParam(kparams, "entryId", entryId);
	this.client.addParam(kparams, "version", version);
	this.client.queueServiceActionCall("mixing", "get", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaMixingService.prototype.update = function(callback, entryId, mixEntry)
{

	kparams = new Object();
	this.client.addParam(kparams, "entryId", entryId);
	this.client.addParam(kparams, "mixEntry", mixEntry.toParams());
	this.client.queueServiceActionCall("mixing", "update", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaMixingService.prototype.delete = function(callback, entryId)
{

	kparams = new Object();
	this.client.addParam(kparams, "entryId", entryId);
	this.client.queueServiceActionCall("mixing", "delete", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaMixingService.prototype.listAction = function(callback, filter, pager)
{
	if(!filter)
		filter = null;
	if(!pager)
		pager = null;

	kparams = new Object();
	if (filter != null)
		this.client.addParam(kparams, "filter", filter.toParams());
	if (pager != null)
		this.client.addParam(kparams, "pager", pager.toParams());
	this.client.queueServiceActionCall("mixing", "list", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaMixingService.prototype.count = function(callback, filter)
{
	if(!filter)
		filter = null;

	kparams = new Object();
	if (filter != null)
		this.client.addParam(kparams, "filter", filter.toParams());
	this.client.queueServiceActionCall("mixing", "count", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaMixingService.prototype.cloneAction = function(callback, entryId)
{

	kparams = new Object();
	this.client.addParam(kparams, "entryId", entryId);
	this.client.queueServiceActionCall("mixing", "clone", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaMixingService.prototype.appendMediaEntry = function(callback, mixEntryId, mediaEntryId)
{

	kparams = new Object();
	this.client.addParam(kparams, "mixEntryId", mixEntryId);
	this.client.addParam(kparams, "mediaEntryId", mediaEntryId);
	this.client.queueServiceActionCall("mixing", "appendMediaEntry", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaMixingService.prototype.requestFlattening = function(callback, entryId, fileFormat, version)
{
	if(!version)
		version = -1;

	kparams = new Object();
	this.client.addParam(kparams, "entryId", entryId);
	this.client.addParam(kparams, "fileFormat", fileFormat);
	this.client.addParam(kparams, "version", version);
	this.client.queueServiceActionCall("mixing", "requestFlattening", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaMixingService.prototype.getMixesByMediaId = function(callback, mediaEntryId)
{

	kparams = new Object();
	this.client.addParam(kparams, "mediaEntryId", mediaEntryId);
	this.client.queueServiceActionCall("mixing", "getMixesByMediaId", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaMixingService.prototype.getReadyMediaEntries = function(callback, mixId, version)
{
	if(!version)
		version = -1;

	kparams = new Object();
	this.client.addParam(kparams, "mixId", mixId);
	this.client.addParam(kparams, "version", version);
	this.client.queueServiceActionCall("mixing", "getReadyMediaEntries", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaMixingService.prototype.anonymousRank = function(callback, entryId, rank)
{

	kparams = new Object();
	this.client.addParam(kparams, "entryId", entryId);
	this.client.addParam(kparams, "rank", rank);
	this.client.queueServiceActionCall("mixing", "anonymousRank", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

function KalturaNotificationService(client)
{
	this.init(client);
}

KalturaNotificationService.prototype = new KalturaServiceBase();

KalturaNotificationService.prototype.getClientNotification = function(callback, entryId, type)
{

	kparams = new Object();
	this.client.addParam(kparams, "entryId", entryId);
	this.client.addParam(kparams, "type", type);
	this.client.queueServiceActionCall("notification", "getClientNotification", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

function KalturaPartnerService(client)
{
	this.init(client);
}

KalturaPartnerService.prototype = new KalturaServiceBase();

KalturaPartnerService.prototype.register = function(callback, partner, cmsPassword)
{
	if(!cmsPassword)
		cmsPassword = "";

	kparams = new Object();
	this.client.addParam(kparams, "partner", partner.toParams());
	this.client.addParam(kparams, "cmsPassword", cmsPassword);
	this.client.queueServiceActionCall("partner", "register", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaPartnerService.prototype.update = function(callback, partner, allowEmpty)
{
	if(!allowEmpty)
		allowEmpty = false;

	kparams = new Object();
	this.client.addParam(kparams, "partner", partner.toParams());
	this.client.addParam(kparams, "allowEmpty", allowEmpty);
	this.client.queueServiceActionCall("partner", "update", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaPartnerService.prototype.getSecrets = function(callback, partnerId, adminEmail, cmsPassword)
{

	kparams = new Object();
	this.client.addParam(kparams, "partnerId", partnerId);
	this.client.addParam(kparams, "adminEmail", adminEmail);
	this.client.addParam(kparams, "cmsPassword", cmsPassword);
	this.client.queueServiceActionCall("partner", "getSecrets", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaPartnerService.prototype.getInfo = function(callback)
{

	kparams = new Object();
	this.client.queueServiceActionCall("partner", "getInfo", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaPartnerService.prototype.get = function(callback, partnerId)
{

	kparams = new Object();
	this.client.addParam(kparams, "partnerId", partnerId);
	this.client.queueServiceActionCall("partner", "get", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaPartnerService.prototype.getUsage = function(callback, year, month, resolution)
{
	if(!year)
		year = "";
	if(!month)
		month = 1;
	if(!resolution)
		resolution = "days";

	kparams = new Object();
	this.client.addParam(kparams, "year", year);
	this.client.addParam(kparams, "month", month);
	this.client.addParam(kparams, "resolution", resolution);
	this.client.queueServiceActionCall("partner", "getUsage", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

function KalturaPlaylistService(client)
{
	this.init(client);
}

KalturaPlaylistService.prototype = new KalturaServiceBase();

KalturaPlaylistService.prototype.add = function(callback, playlist, updateStats)
{
	if(!updateStats)
		updateStats = false;

	kparams = new Object();
	this.client.addParam(kparams, "playlist", playlist.toParams());
	this.client.addParam(kparams, "updateStats", updateStats);
	this.client.queueServiceActionCall("playlist", "add", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaPlaylistService.prototype.get = function(callback, id, version)
{
	if(!version)
		version = -1;

	kparams = new Object();
	this.client.addParam(kparams, "id", id);
	this.client.addParam(kparams, "version", version);
	this.client.queueServiceActionCall("playlist", "get", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaPlaylistService.prototype.update = function(callback, id, playlist, updateStats)
{
	if(!updateStats)
		updateStats = false;

	kparams = new Object();
	this.client.addParam(kparams, "id", id);
	this.client.addParam(kparams, "playlist", playlist.toParams());
	this.client.addParam(kparams, "updateStats", updateStats);
	this.client.queueServiceActionCall("playlist", "update", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaPlaylistService.prototype.delete = function(callback, id)
{

	kparams = new Object();
	this.client.addParam(kparams, "id", id);
	this.client.queueServiceActionCall("playlist", "delete", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaPlaylistService.prototype.listAction = function(callback, filter, pager)
{
	if(!filter)
		filter = null;
	if(!pager)
		pager = null;

	kparams = new Object();
	if (filter != null)
		this.client.addParam(kparams, "filter", filter.toParams());
	if (pager != null)
		this.client.addParam(kparams, "pager", pager.toParams());
	this.client.queueServiceActionCall("playlist", "list", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaPlaylistService.prototype.execute = function(callback, id, detailed)
{
	if(!detailed)
		detailed = false;

	kparams = new Object();
	this.client.addParam(kparams, "id", id);
	this.client.addParam(kparams, "detailed", detailed);
	this.client.queueServiceActionCall("playlist", "execute", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaPlaylistService.prototype.executeFromContent = function(callback, playlistType, playlistContent, detailed)
{
	if(!detailed)
		detailed = false;

	kparams = new Object();
	this.client.addParam(kparams, "playlistType", playlistType);
	this.client.addParam(kparams, "playlistContent", playlistContent);
	this.client.addParam(kparams, "detailed", detailed);
	this.client.queueServiceActionCall("playlist", "executeFromContent", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaPlaylistService.prototype.executeFromFilters = function(callback, filters, totalResults, detailed)
{
	if(!detailed)
		detailed = false;

	kparams = new Object();
	for(var index in filters)
	{
		var obj = filters[index];
		this.client.addParam(kparams, "filters:" + index, obj.toParams());
	}
	this.client.addParam(kparams, "totalResults", totalResults);
	this.client.addParam(kparams, "detailed", detailed);
	this.client.queueServiceActionCall("playlist", "executeFromFilters", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaPlaylistService.prototype.getStatsFromContent = function(callback, playlistType, playlistContent)
{

	kparams = new Object();
	this.client.addParam(kparams, "playlistType", playlistType);
	this.client.addParam(kparams, "playlistContent", playlistContent);
	this.client.queueServiceActionCall("playlist", "getStatsFromContent", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

function KalturaReportService(client)
{
	this.init(client);
}

KalturaReportService.prototype = new KalturaServiceBase();

KalturaReportService.prototype.getGraphs = function(callback, reportType, reportInputFilter, dimension, objectIds)
{
	if(!dimension)
		dimension = null;
	if(!objectIds)
		objectIds = null;

	kparams = new Object();
	this.client.addParam(kparams, "reportType", reportType);
	this.client.addParam(kparams, "reportInputFilter", reportInputFilter.toParams());
	this.client.addParam(kparams, "dimension", dimension);
	this.client.addParam(kparams, "objectIds", objectIds);
	this.client.queueServiceActionCall("report", "getGraphs", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaReportService.prototype.getTotal = function(callback, reportType, reportInputFilter, objectIds)
{
	if(!objectIds)
		objectIds = null;

	kparams = new Object();
	this.client.addParam(kparams, "reportType", reportType);
	this.client.addParam(kparams, "reportInputFilter", reportInputFilter.toParams());
	this.client.addParam(kparams, "objectIds", objectIds);
	this.client.queueServiceActionCall("report", "getTotal", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaReportService.prototype.getTable = function(callback, reportType, reportInputFilter, pager, order, objectIds)
{
	if(!order)
		order = null;
	if(!objectIds)
		objectIds = null;

	kparams = new Object();
	this.client.addParam(kparams, "reportType", reportType);
	this.client.addParam(kparams, "reportInputFilter", reportInputFilter.toParams());
	this.client.addParam(kparams, "pager", pager.toParams());
	this.client.addParam(kparams, "order", order);
	this.client.addParam(kparams, "objectIds", objectIds);
	this.client.queueServiceActionCall("report", "getTable", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaReportService.prototype.getUrlForReportAsCsv = function(callback, reportTitle, reportText, headers, reportType, reportInputFilter, dimension, pager, order, objectIds)
{
	if(!dimension)
		dimension = null;
	if(!pager)
		pager = null;
	if(!order)
		order = null;
	if(!objectIds)
		objectIds = null;

	kparams = new Object();
	this.client.addParam(kparams, "reportTitle", reportTitle);
	this.client.addParam(kparams, "reportText", reportText);
	this.client.addParam(kparams, "headers", headers);
	this.client.addParam(kparams, "reportType", reportType);
	this.client.addParam(kparams, "reportInputFilter", reportInputFilter.toParams());
	this.client.addParam(kparams, "dimension", dimension);
	if (pager != null)
		this.client.addParam(kparams, "pager", pager.toParams());
	this.client.addParam(kparams, "order", order);
	this.client.addParam(kparams, "objectIds", objectIds);
	this.client.queueServiceActionCall("report", "getUrlForReportAsCsv", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

function KalturaSearchService(client)
{
	this.init(client);
}

KalturaSearchService.prototype = new KalturaServiceBase();

KalturaSearchService.prototype.search = function(callback, search, pager)
{
	if(!pager)
		pager = null;

	kparams = new Object();
	this.client.addParam(kparams, "search", search.toParams());
	if (pager != null)
		this.client.addParam(kparams, "pager", pager.toParams());
	this.client.queueServiceActionCall("search", "search", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaSearchService.prototype.getMediaInfo = function(callback, searchResult)
{

	kparams = new Object();
	this.client.addParam(kparams, "searchResult", searchResult.toParams());
	this.client.queueServiceActionCall("search", "getMediaInfo", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaSearchService.prototype.searchUrl = function(callback, mediaType, url)
{

	kparams = new Object();
	this.client.addParam(kparams, "mediaType", mediaType);
	this.client.addParam(kparams, "url", url);
	this.client.queueServiceActionCall("search", "searchUrl", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaSearchService.prototype.externalLogin = function(callback, searchSource, userName, password)
{

	kparams = new Object();
	this.client.addParam(kparams, "searchSource", searchSource);
	this.client.addParam(kparams, "userName", userName);
	this.client.addParam(kparams, "password", password);
	this.client.queueServiceActionCall("search", "externalLogin", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

function KalturaSessionService(client)
{
	this.init(client);
}

KalturaSessionService.prototype = new KalturaServiceBase();

KalturaSessionService.prototype.start = function(callback, secret, userId, type, partnerId, expiry, privileges)
{
	if(!userId)
		userId = "";
	if(!type)
		type = 0;
	if(!partnerId)
		partnerId = -1;
	if(!expiry)
		expiry = 86400;
	if(!privileges)
		privileges = null;

	kparams = new Object();
	this.client.addParam(kparams, "secret", secret);
	this.client.addParam(kparams, "userId", userId);
	this.client.addParam(kparams, "type", type);
	this.client.addParam(kparams, "partnerId", partnerId);
	this.client.addParam(kparams, "expiry", expiry);
	this.client.addParam(kparams, "privileges", privileges);
	this.client.queueServiceActionCall("session", "start", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaSessionService.prototype.startWidgetSession = function(callback, widgetId, expiry)
{
	if(!expiry)
		expiry = 86400;

	kparams = new Object();
	this.client.addParam(kparams, "widgetId", widgetId);
	this.client.addParam(kparams, "expiry", expiry);
	this.client.queueServiceActionCall("session", "startWidgetSession", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

function KalturaStatsService(client)
{
	this.init(client);
}

KalturaStatsService.prototype = new KalturaServiceBase();

KalturaStatsService.prototype.collect = function(callback, event)
{

	kparams = new Object();
	this.client.addParam(kparams, "event", event.toParams());
	this.client.queueServiceActionCall("stats", "collect", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaStatsService.prototype.kmcCollect = function(callback, kmcEvent)
{

	kparams = new Object();
	this.client.addParam(kparams, "kmcEvent", kmcEvent.toParams());
	this.client.queueServiceActionCall("stats", "kmcCollect", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaStatsService.prototype.reportKceError = function(callback, kalturaCEError)
{

	kparams = new Object();
	this.client.addParam(kparams, "kalturaCEError", kalturaCEError.toParams());
	this.client.queueServiceActionCall("stats", "reportKceError", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

function KalturaSyndicationFeedService(client)
{
	this.init(client);
}

KalturaSyndicationFeedService.prototype = new KalturaServiceBase();

KalturaSyndicationFeedService.prototype.add = function(callback, syndicationFeed)
{

	kparams = new Object();
	this.client.addParam(kparams, "syndicationFeed", syndicationFeed.toParams());
	this.client.queueServiceActionCall("syndicationfeed", "add", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaSyndicationFeedService.prototype.get = function(callback, id)
{

	kparams = new Object();
	this.client.addParam(kparams, "id", id);
	this.client.queueServiceActionCall("syndicationfeed", "get", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaSyndicationFeedService.prototype.update = function(callback, id, syndicationFeed)
{

	kparams = new Object();
	this.client.addParam(kparams, "id", id);
	this.client.addParam(kparams, "syndicationFeed", syndicationFeed.toParams());
	this.client.queueServiceActionCall("syndicationfeed", "update", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaSyndicationFeedService.prototype.delete = function(callback, id)
{

	kparams = new Object();
	this.client.addParam(kparams, "id", id);
	this.client.queueServiceActionCall("syndicationfeed", "delete", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaSyndicationFeedService.prototype.listAction = function(callback, filter, pager)
{
	if(!filter)
		filter = null;
	if(!pager)
		pager = null;

	kparams = new Object();
	if (filter != null)
		this.client.addParam(kparams, "filter", filter.toParams());
	if (pager != null)
		this.client.addParam(kparams, "pager", pager.toParams());
	this.client.queueServiceActionCall("syndicationfeed", "list", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaSyndicationFeedService.prototype.getEntryCount = function(callback, feedId)
{

	kparams = new Object();
	this.client.addParam(kparams, "feedId", feedId);
	this.client.queueServiceActionCall("syndicationfeed", "getEntryCount", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaSyndicationFeedService.prototype.requestConversion = function(callback, feedId)
{

	kparams = new Object();
	this.client.addParam(kparams, "feedId", feedId);
	this.client.queueServiceActionCall("syndicationfeed", "requestConversion", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

function KalturaSystemService(client)
{
	this.init(client);
}

KalturaSystemService.prototype = new KalturaServiceBase();

KalturaSystemService.prototype.ping = function(callback)
{

	kparams = new Object();
	this.client.queueServiceActionCall("system", "ping", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

function KalturaUiConfService(client)
{
	this.init(client);
}

KalturaUiConfService.prototype = new KalturaServiceBase();

KalturaUiConfService.prototype.add = function(callback, uiConf)
{

	kparams = new Object();
	this.client.addParam(kparams, "uiConf", uiConf.toParams());
	this.client.queueServiceActionCall("uiconf", "add", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaUiConfService.prototype.update = function(callback, id, uiConf)
{

	kparams = new Object();
	this.client.addParam(kparams, "id", id);
	this.client.addParam(kparams, "uiConf", uiConf.toParams());
	this.client.queueServiceActionCall("uiconf", "update", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaUiConfService.prototype.get = function(callback, id)
{

	kparams = new Object();
	this.client.addParam(kparams, "id", id);
	this.client.queueServiceActionCall("uiconf", "get", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaUiConfService.prototype.delete = function(callback, id)
{

	kparams = new Object();
	this.client.addParam(kparams, "id", id);
	this.client.queueServiceActionCall("uiconf", "delete", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaUiConfService.prototype.cloneAction = function(callback, id)
{

	kparams = new Object();
	this.client.addParam(kparams, "id", id);
	this.client.queueServiceActionCall("uiconf", "clone", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaUiConfService.prototype.listTemplates = function(callback, filter, pager)
{
	if(!filter)
		filter = null;
	if(!pager)
		pager = null;

	kparams = new Object();
	if (filter != null)
		this.client.addParam(kparams, "filter", filter.toParams());
	if (pager != null)
		this.client.addParam(kparams, "pager", pager.toParams());
	this.client.queueServiceActionCall("uiconf", "listTemplates", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaUiConfService.prototype.listAction = function(callback, filter, pager)
{
	if(!filter)
		filter = null;
	if(!pager)
		pager = null;

	kparams = new Object();
	if (filter != null)
		this.client.addParam(kparams, "filter", filter.toParams());
	if (pager != null)
		this.client.addParam(kparams, "pager", pager.toParams());
	this.client.queueServiceActionCall("uiconf", "list", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

function KalturaUploadService(client)
{
	this.init(client);
}

KalturaUploadService.prototype = new KalturaServiceBase();

KalturaUploadService.prototype.getUploadTokenId = function(callback)
{

	kparams = new Object();
	this.client.queueServiceActionCall("upload", "getUploadTokenId", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaUploadService.prototype.uploadByTokenId = function(callback, fileData, uploadTokenId)
{

	kparams = new Object();
	kfiles = new Object();
	this.client.addParam(kfiles, "fileData", fileData);
	this.client.addParam(kparams, "uploadTokenId", uploadTokenId);
	this.client.queueServiceActionCall("upload", "uploadByTokenId", kparams, kfiles);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaUploadService.prototype.getUploadedFileStatusByTokenId = function(callback, uploadTokenId)
{

	kparams = new Object();
	this.client.addParam(kparams, "uploadTokenId", uploadTokenId);
	this.client.queueServiceActionCall("upload", "getUploadedFileStatusByTokenId", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaUploadService.prototype.upload = function(callback, fileData)
{

	kparams = new Object();
	kfiles = new Object();
	this.client.addParam(kfiles, "fileData", fileData);
	this.client.queueServiceActionCall("upload", "upload", kparams, kfiles);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

function KalturaUserService(client)
{
	this.init(client);
}

KalturaUserService.prototype = new KalturaServiceBase();

KalturaUserService.prototype.add = function(callback, user)
{

	kparams = new Object();
	this.client.addParam(kparams, "user", user.toParams());
	this.client.queueServiceActionCall("user", "add", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaUserService.prototype.update = function(callback, userId, user)
{

	kparams = new Object();
	this.client.addParam(kparams, "userId", userId);
	this.client.addParam(kparams, "user", user.toParams());
	this.client.queueServiceActionCall("user", "update", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaUserService.prototype.get = function(callback, userId)
{

	kparams = new Object();
	this.client.addParam(kparams, "userId", userId);
	this.client.queueServiceActionCall("user", "get", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaUserService.prototype.delete = function(callback, userId)
{

	kparams = new Object();
	this.client.addParam(kparams, "userId", userId);
	this.client.queueServiceActionCall("user", "delete", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaUserService.prototype.listAction = function(callback, filter, pager)
{
	if(!filter)
		filter = null;
	if(!pager)
		pager = null;

	kparams = new Object();
	if (filter != null)
		this.client.addParam(kparams, "filter", filter.toParams());
	if (pager != null)
		this.client.addParam(kparams, "pager", pager.toParams());
	this.client.queueServiceActionCall("user", "list", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaUserService.prototype.notifyBan = function(callback, userId)
{

	kparams = new Object();
	this.client.addParam(kparams, "userId", userId);
	this.client.queueServiceActionCall("user", "notifyBan", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

function KalturaWidgetService(client)
{
	this.init(client);
}

KalturaWidgetService.prototype = new KalturaServiceBase();

KalturaWidgetService.prototype.add = function(callback, widget)
{

	kparams = new Object();
	this.client.addParam(kparams, "widget", widget.toParams());
	this.client.queueServiceActionCall("widget", "add", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaWidgetService.prototype.update = function(callback, id, widget)
{

	kparams = new Object();
	this.client.addParam(kparams, "id", id);
	this.client.addParam(kparams, "widget", widget.toParams());
	this.client.queueServiceActionCall("widget", "update", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaWidgetService.prototype.get = function(callback, id)
{

	kparams = new Object();
	this.client.addParam(kparams, "id", id);
	this.client.queueServiceActionCall("widget", "get", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaWidgetService.prototype.cloneAction = function(callback, widget)
{

	kparams = new Object();
	this.client.addParam(kparams, "widget", widget.toParams());
	this.client.queueServiceActionCall("widget", "clone", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaWidgetService.prototype.listAction = function(callback, filter, pager)
{
	if(!filter)
		filter = null;
	if(!pager)
		pager = null;

	kparams = new Object();
	if (filter != null)
		this.client.addParam(kparams, "filter", filter.toParams());
	if (pager != null)
		this.client.addParam(kparams, "pager", pager.toParams());
	this.client.queueServiceActionCall("widget", "list", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

function KalturaXInternalService(client)
{
	this.init(client);
}

KalturaXInternalService.prototype = new KalturaServiceBase();

KalturaXInternalService.prototype.xAddBulkDownload = function(callback, entryIds, flavorParamsId)
{
	if(!flavorParamsId)
		flavorParamsId = "";

	kparams = new Object();
	this.client.addParam(kparams, "entryIds", entryIds);
	this.client.addParam(kparams, "flavorParamsId", flavorParamsId);
	this.client.queueServiceActionCall("xinternal", "xAddBulkDownload", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

function KalturaSystemUserService(client)
{
	this.init(client);
}

KalturaSystemUserService.prototype = new KalturaServiceBase();

KalturaSystemUserService.prototype.verifyPassword = function(callback, email, password)
{

	kparams = new Object();
	this.client.addParam(kparams, "email", email);
	this.client.addParam(kparams, "password", password);
	this.client.queueServiceActionCall("systemuser", "verifyPassword", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaSystemUserService.prototype.generateNewPassword = function(callback)
{

	kparams = new Object();
	this.client.queueServiceActionCall("systemuser", "generateNewPassword", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaSystemUserService.prototype.setNewPassword = function(callback, userId, password)
{

	kparams = new Object();
	this.client.addParam(kparams, "userId", userId);
	this.client.addParam(kparams, "password", password);
	this.client.queueServiceActionCall("systemuser", "setNewPassword", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaSystemUserService.prototype.add = function(callback, systemUser)
{

	kparams = new Object();
	this.client.addParam(kparams, "systemUser", systemUser.toParams());
	this.client.queueServiceActionCall("systemuser", "add", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaSystemUserService.prototype.get = function(callback, userId)
{

	kparams = new Object();
	this.client.addParam(kparams, "userId", userId);
	this.client.queueServiceActionCall("systemuser", "get", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaSystemUserService.prototype.getByEmail = function(callback, email)
{

	kparams = new Object();
	this.client.addParam(kparams, "email", email);
	this.client.queueServiceActionCall("systemuser", "getByEmail", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaSystemUserService.prototype.update = function(callback, userId, systemUser)
{

	kparams = new Object();
	this.client.addParam(kparams, "userId", userId);
	this.client.addParam(kparams, "systemUser", systemUser.toParams());
	this.client.queueServiceActionCall("systemuser", "update", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaSystemUserService.prototype.delete = function(callback, userId)
{

	kparams = new Object();
	this.client.addParam(kparams, "userId", userId);
	this.client.queueServiceActionCall("systemuser", "delete", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

KalturaSystemUserService.prototype.listAction = function(callback, filter, pager)
{
	if(!filter)
		filter = null;
	if(!pager)
		pager = null;

	kparams = new Object();
	if (filter != null)
		this.client.addParam(kparams, "filter", filter.toParams());
	if (pager != null)
		this.client.addParam(kparams, "pager", pager.toParams());
	this.client.queueServiceActionCall("systemuser", "list", kparams);
	if (!this.client.isMultiRequest())
		this.client.doQueue(callback);
};

function KalturaClient(config)
{
	this.init(config);
}

KalturaClient.prototype = new KalturaClientBase()
/**
 * Add & Manage Access Controls
 *
 * @var KalturaAccessControlService
 */
KalturaClient.prototype.accessControl = null;

/**
 * admin console service lets you manage cross partner reports, activity, status and config. 
	 * 
 *
 * @var KalturaAdminconsoleService
 */
KalturaClient.prototype.adminconsole = null;

/**
 * Manage details for the administrative user
 *
 * @var KalturaAdminUserService
 */
KalturaClient.prototype.adminUser = null;

/**
 * Base Entry Service
 *
 * @var KalturaBaseEntryService
 */
KalturaClient.prototype.baseEntry = null;

/**
 * Bulk upload service is used to upload & manage bulk uploads using CSV files
 *
 * @var KalturaBulkUploadService
 */
KalturaClient.prototype.bulkUpload = null;

/**
 * Add & Manage Categories
 *
 * @var KalturaCategoryService
 */
KalturaClient.prototype.category = null;

/**
 * Add & Manage Conversion Profiles
 *
 * @var KalturaConversionProfileService
 */
KalturaClient.prototype.conversionProfile = null;

/**
 * Data service lets you manage data content (textual content)
 *
 * @var KalturaDataService
 */
KalturaClient.prototype.data = null;

/**
 * Retrieve information and invoke actions on Flavor Asset
 *
 * @var KalturaFlavorAssetService
 */
KalturaClient.prototype.flavorAsset = null;

/**
 * Add & Manage Flavor Params
 *
 * @var KalturaFlavorParamsService
 */
KalturaClient.prototype.flavorParams = null;

/**
 * batch service lets you handle different batch process from remote machines.
	 * As oppesed to other ojects in the system, locking mechanism is critical in this case.
	 * For this reason the GetExclusiveXX, UpdateExclusiveXX and FreeExclusiveXX actions are important for the system's intergity.
	 * In general - updating batch object should be done only using the UpdateExclusiveXX which in turn can be called only after 
	 * acuiring a batch objet properly (using  GetExclusiveXX).
	 * If an object was aquired and should be returned to the pool in it's initial state - use the FreeExclusiveXX action 
	 * 
 *
 * @var KalturaJobsService
 */
KalturaClient.prototype.jobs = null;

/**
 * Media service lets you upload and manage media files (images / videos & audio)
 *
 * @var KalturaMediaService
 */
KalturaClient.prototype.media = null;

/**
 * A Mix is an XML unique format invented by Kaltura, it allows the user to create a mix of videos and images, in and out points, transitions, text overlays, soundtrack, effects and much more...
	 * Mixing service lets you create a new mix, manage its metadata and make basic manipulations.   
 *
 * @var KalturaMixingService
 */
KalturaClient.prototype.mixing = null;

/**
 * Notification Service
 *
 * @var KalturaNotificationService
 */
KalturaClient.prototype.notification = null;

/**
 * partner service allows you to change/manage your partner personal details and settings as well
 *
 * @var KalturaPartnerService
 */
KalturaClient.prototype.partner = null;

/**
 * Playlist service lets you create,manage and play your playlists
	 * Playlists could be static (containing a fixed list of entries) or dynamic (baseed on a filter)
 *
 * @var KalturaPlaylistService
 */
KalturaClient.prototype.playlist = null;

/**
 * api for getting reports data by the report type and some inputFilter
 *
 * @var KalturaReportService
 */
KalturaClient.prototype.report = null;

/**
 * Search service allows you to search for media in various media providers
	 * This service is being used mostly by the CW component
 *
 * @var KalturaSearchService
 */
KalturaClient.prototype.search = null;

/**
 * Session service
 *
 * @var KalturaSessionService
 */
KalturaClient.prototype.session = null;

/**
 * Stats Service
 *
 * @var KalturaStatsService
 */
KalturaClient.prototype.stats = null;

/**
 * Add & Manage Syndication Feeds
 *
 * @var KalturaSyndicationFeedService
 */
KalturaClient.prototype.syndicationFeed = null;

/**
 * System service is used for internal system helpers & to retrieve system level information
 *
 * @var KalturaSystemService
 */
KalturaClient.prototype.system = null;

/**
 * UiConf service lets you create and manage your UIConfs for the various flash components
	 * This service is used by the KMC-ApplicationStudio
 *
 * @var KalturaUiConfService
 */
KalturaClient.prototype.uiConf = null;

/**
 * Upload service is used to upload files and get the token that can be later used as a reference to the uploaded file
	 * 
 *
 * @var KalturaUploadService
 */
KalturaClient.prototype.upload = null;

/**
 * Manage partner users on Kaltura's side
	 * The userId in kaltura is the unique Id in the partner's system, and the [partnerId,Id] couple are unique key in kaltura's DB
 *
 * @var KalturaUserService
 */
KalturaClient.prototype.user = null;

/**
 * widget service for full widget management
 *
 * @var KalturaWidgetService
 */
KalturaClient.prototype.widget = null;

/**
 * Internal Service is used for actions that are used internally in Kaltura applications and might be changed in the future without any notice.
 *
 * @var KalturaXInternalService
 */
KalturaClient.prototype.xInternal = null;

/**
 * System user service
 *
 * @var KalturaSystemUserService
 */
KalturaClient.prototype.systemUser = null;


KalturaClient.prototype.init = function(config)
{
	KalturaClientBase.prototype.init.apply(this, arguments);
	this.accessControl = new KalturaAccessControlService(this);
	this.adminconsole = new KalturaAdminconsoleService(this);
	this.adminUser = new KalturaAdminUserService(this);
	this.baseEntry = new KalturaBaseEntryService(this);
	this.bulkUpload = new KalturaBulkUploadService(this);
	this.category = new KalturaCategoryService(this);
	this.conversionProfile = new KalturaConversionProfileService(this);
	this.data = new KalturaDataService(this);
	this.flavorAsset = new KalturaFlavorAssetService(this);
	this.flavorParams = new KalturaFlavorParamsService(this);
	this.jobs = new KalturaJobsService(this);
	this.media = new KalturaMediaService(this);
	this.mixing = new KalturaMixingService(this);
	this.notification = new KalturaNotificationService(this);
	this.partner = new KalturaPartnerService(this);
	this.playlist = new KalturaPlaylistService(this);
	this.report = new KalturaReportService(this);
	this.search = new KalturaSearchService(this);
	this.session = new KalturaSessionService(this);
	this.stats = new KalturaStatsService(this);
	this.syndicationFeed = new KalturaSyndicationFeedService(this);
	this.system = new KalturaSystemService(this);
	this.uiConf = new KalturaUiConfService(this);
	this.upload = new KalturaUploadService(this);
	this.user = new KalturaUserService(this);
	this.widget = new KalturaWidgetService(this);
	this.xInternal = new KalturaXInternalService(this);
	this.systemUser = new KalturaSystemUserService(this);
}
