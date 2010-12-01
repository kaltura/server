<?php
require_once("KalturaClientBase.php");

class KalturaEditorType
{
	const SIMPLE = 1;
	const ADVANCED = 2;
}

class KalturaEntryStatus
{
	const ERROR_CONVERTING = -1;
	const IMPORT = 0;
	const PRECONVERT = 1;
	const READY = 2;
	const DELETED = 3;
	const PENDING = 4;
	const MODERATE = 5;
	const BLOCKED = 6;
}

class KalturaEntryType
{
	const AUTOMATIC = -1;
	const MEDIA_CLIP = 1;
	const MIX = 2;
	const PLAYLIST = 5;
	const DATA = 6;
	const DOCUMENT = 10;
}

class KalturaLicenseType
{
	const UNKNOWN = -1;
	const NONE = 0;
	const CC25 = 1;
	const CC3 = 2;
}

class KalturaMediaType
{
	const VIDEO = 1;
	const IMAGE = 2;
	const AUDIO = 5;
}

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

class KalturaPlaylistType
{
	const DYNAMIC = 10;
	const STATIC_LIST = 3;
	const EXTERNAL = 101;
}

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
}

class KalturaSessionType
{
	const USER = 0;
	const ADMIN = 2;
}

class KalturaSourceType
{
	const FILE = 1;
	const WEBCAM = 2;
	const URL = 5;
	const SEARCH_PROVIDER = 6;
}

class KalturaUiConfCreationMode
{
	const WIZARD = 2;
	const ADVANCED = 3;
}

class KalturaUiConfObjType
{
	const PLAYER = 1;
	const CONTRIBUTION_WIZARD = 2;
	const SIMPLE_EDITOR = 3;
	const ADVANCED_EDITOR = 4;
	const PLAYLIST = 5;
	const APP_STUDIO = 6;
}

class KalturaWidgetSecurityType
{
	const NONE = 1;
	const TIMEHASH = 2;
}


class KalturaAdminUser extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $password = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $email = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $screenName = null;


}

class KalturaBaseEntry extends KalturaObjectBase
{
	/**
	 * Auto generated 10 characters alphanumeric string
	 *
	 * @var string
	 * @readonly
	 */
	public $id = null;

	/**
	 * Entry name
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * Entry description
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
	 * @var string
	 */
	public $userId = null;

	/**
	 * Entry tags
	 *
	 * @var string
	 */
	public $tags = null;

	/**
	 * Entry admin tags can be updated only by administrators and are not visible to the user
	 *
	 * @var string
	 */
	public $adminTags = null;

	/**
	 * 
	 *
	 * @var KalturaEntryStatus
	 * @readonly
	 */
	public $status = null;

	/**
	 * The type of the entry, this is auto filled by the derived entry object
	 *
	 * @var KalturaEntryType
	 * @readonly
	 */
	public $type = null;

	/**
	 * Entry creation date as Unix timestamp (In seconds)
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * Calculated rank
	 *
	 * @var int
	 * @readonly
	 */
	public $rank = null;

	/**
	 * The total (sum) of all votes
	 *
	 * @var int
	 * @readonly
	 */
	public $totalRank = null;

	/**
	 * Number of votes
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
	 * @var string
	 */
	public $partnerData = null;

	/**
	 * Download URL for the entry
	 *
	 * @var string
	 * @readonly
	 */
	public $downloadUrl = null;

	/**
	 * License type used for this entry
	 *
	 * @var KalturaLicenseType
	 */
	public $licenseType = null;


}

class KalturaBaseEntryFilter extends KalturaObjectBase
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
	 * @var string
	 */
	public $typeIn = null;

	/**
	 * 
	 *
	 * @var KalturaEntryStatus
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
	 * @var string
	 */
	public $adminTagsLike = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $adminTagsMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $adminTagsMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $groupIdEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThenEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtGreaterThanEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtLessThenEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $modifiedAtGreaterThanEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $modifiedAtLessThenEqual = null;

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
	public $moderationStatusEqual = null;

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
	public $tagsAndNameMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsAndAdminTagsMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsAndAdminTagsAndNameMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsAndNameMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsAndAdminTagsMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsAndAdminTagsAndNameMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $searchTextMatchAnd = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $searchTextMatchOr = null;


}

class KalturaBaseEntryListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var KalturaBaseEntries
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

class KalturaClientNotification extends KalturaObjectBase
{
	/**
	 * The URL where the notification should be sent to 
	 *
	 * @var string
	 */
	public $url = null;

	/**
	 * The serialized notification data to send
	 *
	 * @var string
	 */
	public $data = null;


}

class KalturaFilterPager extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $pageSize = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $pageIndex = null;


}

class KalturaPlayableEntry extends KalturaBaseEntry
{
	/**
	 * Number of plays
	 *
	 * @var int
	 * @readonly
	 */
	public $plays = null;

	/**
	 * Number of views
	 *
	 * @var int
	 * @readonly
	 */
	public $views = null;

	/**
	 * The width in pixels
	 *
	 * @var int
	 * @readonly
	 */
	public $width = null;

	/**
	 * The height in pixels
	 *
	 * @var int
	 * @readonly
	 */
	public $height = null;

	/**
	 * Thumbnail URL
	 *
	 * @var string
	 * @readonly
	 */
	public $thumbnailUrl = null;

	/**
	 * The duration in seconds
	 *
	 * @var int
	 * @readonly
	 */
	public $duration = null;


}

class KalturaMediaEntry extends KalturaPlayableEntry
{
	/**
	 * The media type of the entry
	 *
	 * @var KalturaMediaType
	 * @insertonly
	 */
	public $mediaType = null;

	/**
	 * Override the default conversion quality  
	 *
	 * @var string
	 * @insertonly
	 */
	public $conversionQuality = null;

	/**
	 * The source type of the entry 
	 *
	 * @var KalturaSourceType
	 * @readonly
	 */
	public $sourceType = null;

	/**
	 * The search provider type used to import this entry
	 *
	 * @var KalturaSearchProviderType
	 * @readonly
	 */
	public $searchProviderType = null;

	/**
	 * The ID of the media in the importing site
	 *
	 * @var string
	 * @readonly
	 */
	public $searchProviderId = null;

	/**
	 * The user name used for credits
	 *
	 * @var string
	 */
	public $creditUserName = null;

	/**
	 * The URL for credits
	 *
	 * @var string
	 */
	public $creditUrl = null;

	/**
	 * The media date extracted from EXIF data (For images) as Unix timestamp (In seconds)
	 *
	 * @var int
	 * @readonly
	 */
	public $mediaDate = null;

	/**
	 * The URL used for playback. This is not the download URL.
	 *
	 * @var string
	 * @readonly
	 */
	public $dataUrl = null;


}

class KalturaMediaEntryFilter extends KalturaBaseEntryFilter
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
	public $mediaDateGreaterThanEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $mediaDateLessThanEqual = null;


}

class KalturaMediaListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var KalturaMediaEntries
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

class KalturaMixEntry extends KalturaPlayableEntry
{
	/**
	 * Indicates whether the user has submited a real thumbnail to the mix (Not the one that was generated automaticaly)
	 *
	 * @var bool
	 * @readonly
	 */
	public $hasRealThumbnail = null;

	/**
	 * The editor type used to edit the metadata
	 *
	 * @var KalturaEditorType
	 */
	public $editorType = null;

	/**
	 * The xml data of the mix
	 *
	 * @var string
	 */
	public $dataContent = null;

	/**
	 * The version of the mix
	 *
	 * @var int
	 * @readonly
	 */
	public $version = null;


}

class KalturaMixEntryFilter extends KalturaBaseEntryFilter
{

}

class KalturaMixListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var KalturaMixEntries
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
	 * @var string
	 * @readonly
	 */
	public $createdAt = null;

	/**
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
	 * @var string
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
	 * @var int
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
	 * @var int
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
	 * readonly
	 *
	 * @var int
	 */
	public $partnerPackage = null;

	/**
	 * readonly
	 *
	 * @var string
	 */
	public $secret = null;

	/**
	 * readonly
	 *
	 * @var string
	 */
	public $adminSecret = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $cmsPassword = null;


}

class KalturaPartnerUsage extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var float
	 * @readonly
	 */
	public $hostingGB = null;

	/**
	 * 
	 *
	 * @var float
	 * @readonly
	 */
	public $Percent = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $packageBW = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $usageGB = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $reachedLimitDate = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $usageGraph = null;


}

class KalturaPlaylist extends KalturaBaseEntry
{
	/**
	 * Content of the playlist - 
	 *
	 * @var string
	 */
	public $playlistContent = null;

	/**
	 * Type of playlist  
	 *
	 * @var KalturaPlaylistType
	 */
	public $playlistType = null;

	/**
	 * Number of plays
	 *
	 * @var int
	 * @readonly
	 */
	public $plays = null;

	/**
	 * Number of views
	 *
	 * @var int
	 * @readonly
	 */
	public $views = null;

	/**
	 * The duration in seconds
	 *
	 * @var int
	 * @readonly
	 */
	public $duration = null;

	/**
	 * The version of the file
	 *
	 * @var string
	 * @readonly
	 */
	public $version = null;


}

class KalturaPlaylistFilter extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $idGreaterThanEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $statusEqual = null;

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
	public $tagsMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $createdAtGreaterThanEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $createdAtLessThanEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $updatedAtGreaterThanEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $updatedAtLessThanEqual = null;


}

class KalturaSearch extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $keyWords = null;

	/**
	 * 
	 *
	 * @var KalturaSearchProviderType
	 */
	public $searchSource = null;

	/**
	 * 
	 *
	 * @var KalturaMediaType
	 */
	public $mediaType = null;

	/**
	 * Use this field to pass dynamic data for searching
	 * For example - if you set this field to "mymovies_$partner_id"
	 * The $partner_id will be automatically replcaed with your real partner Id
	 *
	 * @var string
	 */
	public $extraData = null;


}

class KalturaSearchResult extends KalturaSearch
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $id = null;

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
	public $thumbUrl = null;

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
	public $sourceLink = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $credit = null;

	/**
	 * 
	 *
	 * @var KalturaLicenseType
	 */
	public $licenseType = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $flashPlaybackType = null;


}

class KalturaUiConf extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $id = null;

	/**
	 * Name of the uiConf, this is not a primary key
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
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * 
	 *
	 * @var KalturaUiConfObjType
	 */
	public $objType = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $objTypeAsString = null;

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
	 * @var string
	 */
	public $htmlParams = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $swfUrl = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $confFilePath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $confFile = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $confFileFeatures = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $confVars = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $useCdn = null;

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
	public $swfUrlVersion = null;

	/**
	 * Entry creation date as Unix timestamp (In seconds)
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * Entry creation date as Unix timestamp (In seconds)
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;

	/**
	 * Enter description here...
	 *
	 * @var KalturaUiConfCreationMode
	 */
	public $creationMode = null;


}

class KalturaUiConfFilter extends KalturaObjectBase
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
	 * @var int
	 */
	public $idGreaterThanEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $status = null;

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
	public $tagsMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $createdAtGreaterThanEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $createdAtLessThanEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $updatedAtGreaterThanEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $updatedAtLessThanEqual = null;


}

class KalturaUser extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $id = null;

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
	 * @var string
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
	public $urlList = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $picture = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $icon = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $aboutMe = null;

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
	public $mobileNum = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $gender = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $views = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $fans = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $entries = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $producedKshows = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $status = null;

	/**
	 * Entry creation date as Unix timestamp (In seconds)
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * Entry update date as Unix timestamp (In seconds)
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
	 * @var int
	 */
	public $displayInSearch = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerData = null;


}

class KalturaUserFilter extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $status = null;

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
	public $tagsLike = null;

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
	public $countryLike = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $emailLikeRegexp = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $createdAtGreaterThanEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $createdAtLessThanEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $updatedAtGreaterThanEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $updatedAtLessThanEqual = null;


}

class KalturaWidget extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $sourceWidgetId = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $rootWidgetId = null;

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
	public $kshowId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entryId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $uiConfId = null;

	/**
	 * 
	 *
	 * @var KalturaWidgetSecurityType
	 */
	public $securityType = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $securityPolicy = null;

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
	 * @var string
	 */
	public $partnerData = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $widgetHTML = null;


}

class KalturaWidgetFilter extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $sourceWidgetId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $rootWidgetId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entryId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $uiConfId = null;

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
	public $createdAtGreaterThanEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $createdAtLessThanEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $updatedAtGreaterThanEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $updatedAtLessThanEqual = null;


}


class KalturaMediaService extends KalturaServiceBase
{
	function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	function addFromUrl(KalturaMediaEntry $mediaEntry, $url)
	{
		$kparams = array();
		$this->client->addParam($kparams, "mediaEntry", $mediaEntry->toParams());
		$this->client->addParam($kparams, "url", $url);
		$resultObject = $this->client->callService("media", "addFromUrl", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMediaEntry");
		return $resultObject;
	}

	function addFromSearchResult(KalturaMediaEntry $mediaEntry = null, KalturaSearchResult $searchResult)
	{
		$kparams = array();
		if ($mediaEntry !== null)
			$this->client->addParam($kparams, "mediaEntry", $mediaEntry->toParams());
		$this->client->addParam($kparams, "searchResult", $searchResult->toParams());
		$resultObject = $this->client->callService("media", "addFromSearchResult", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMediaEntry");
		return $resultObject;
	}

	function addFromUploadedFile(KalturaMediaEntry $mediaEntry, $uploadTokenId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "mediaEntry", $mediaEntry->toParams());
		$this->client->addParam($kparams, "uploadTokenId", $uploadTokenId);
		$resultObject = $this->client->callService("media", "addFromUploadedFile", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMediaEntry");
		return $resultObject;
	}

	function get($entryId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$resultObject = $this->client->callService("media", "get", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMediaEntry");
		return $resultObject;
	}

	function update($entryId, KalturaMediaEntry $mediaEntry)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "mediaEntry", $mediaEntry->toParams());
		$resultObject = $this->client->callService("media", "update", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMediaEntry");
		return $resultObject;
	}

	function delete($entryId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$resultObject = $this->client->callService("media", "delete", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	function listAction(KalturaMediaEntryFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$resultObject = $this->client->callService("media", "list", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMediaListResponse");
		return $resultObject;
	}

	function updateThumbnail($entryId, $timeOffset)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "timeOffset", $timeOffset);
		$resultObject = $this->client->callService("media", "updateThumbnail", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMediaEntry");
		return $resultObject;
	}

	function requestConversion($entryId, $fileFormat)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "fileFormat", $fileFormat);
		$resultObject = $this->client->callService("media", "requestConversion", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "int");
		return $resultObject;
	}
}

class KalturaMixingService extends KalturaServiceBase
{
	function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	function add(KalturaMixEntry $mixEntry)
	{
		$kparams = array();
		$this->client->addParam($kparams, "mixEntry", $mixEntry->toParams());
		$resultObject = $this->client->callService("mixing", "add", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMixEntry");
		return $resultObject;
	}

	function get($entryId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$resultObject = $this->client->callService("mixing", "get", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMixEntry");
		return $resultObject;
	}

	function update($entryId, KalturaMixEntry $mixEntry)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "mixEntry", $mixEntry->toParams());
		$resultObject = $this->client->callService("mixing", "update", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMixEntry");
		return $resultObject;
	}

	function delete($entryId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$resultObject = $this->client->callService("mixing", "delete", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	function listAction(KalturaMixEntryFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$resultObject = $this->client->callService("mixing", "list", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMixListResponse");
		return $resultObject;
	}

	function cloneAction($entryId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$resultObject = $this->client->callService("mixing", "clone", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMixEntry");
		return $resultObject;
	}

	function appendMediaEntry($mixEntryId, $mediaEntryId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "mixEntryId", $mixEntryId);
		$this->client->addParam($kparams, "mediaEntryId", $mediaEntryId);
		$resultObject = $this->client->callService("mixing", "appendMediaEntry", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMixEntry");
		return $resultObject;
	}

	function requestFlattening($entryId, $fileFormat, $version = -1)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "fileFormat", $fileFormat);
		$this->client->addParam($kparams, "version", $version);
		$resultObject = $this->client->callService("mixing", "requestFlattening", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "int");
		return $resultObject;
	}
}

class KalturaBaseEntryService extends KalturaServiceBase
{
	function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	function addFromUploadedFile(KalturaBaseEntry $entry, $uploadTokenId, $type = -1)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entry", $entry->toParams());
		$this->client->addParam($kparams, "uploadTokenId", $uploadTokenId);
		$this->client->addParam($kparams, "type", $type);
		$resultObject = $this->client->callService("baseentry", "addFromUploadedFile", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBaseEntry");
		return $resultObject;
	}

	function get($entryId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$resultObject = $this->client->callService("baseentry", "get", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBaseEntry");
		return $resultObject;
	}

	function delete($entryId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$resultObject = $this->client->callService("baseentry", "delete", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	function listAction(KalturaBaseEntryFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$resultObject = $this->client->callService("baseentry", "list", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBaseEntryListResponse");
		return $resultObject;
	}
}

class KalturaSessionService extends KalturaServiceBase
{
	function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	function start($secret, $userId, $type = 0, $partnerId = -1, $expiry = 86400, $privileges = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "secret", $secret);
		$this->client->addParam($kparams, "userId", $userId);
		$this->client->addParam($kparams, "type", $type);
		$this->client->addParam($kparams, "partnerId", $partnerId);
		$this->client->addParam($kparams, "expiry", $expiry);
		$this->client->addParam($kparams, "privileges", $privileges);
		$resultObject = $this->client->callService("session", "start", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "string");
		return $resultObject;
	}

	function startWidgetSession($widgetId, $expiry = 86400)
	{
		$kparams = array();
		$this->client->addParam($kparams, "widgetId", $widgetId);
		$this->client->addParam($kparams, "expiry", $expiry);
		$resultObject = $this->client->callService("session", "startWidgetSession", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "string");
		return $resultObject;
	}
}

class KalturaUiConfService extends KalturaServiceBase
{
	function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	function add(KalturaUiConf $uiConf)
	{
		$kparams = array();
		$this->client->addParam($kparams, "uiConf", $uiConf->toParams());
		$resultObject = $this->client->callService("uiconf", "add", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaUiConf");
		return $resultObject;
	}

	function update($id, KalturaUiConf $uiConf)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "uiConf", $uiConf->toParams());
		$resultObject = $this->client->callService("uiconf", "update", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaUiConf");
		return $resultObject;
	}

	function get($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$resultObject = $this->client->callService("uiconf", "get", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaUiConf");
		return $resultObject;
	}

	function delete($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$resultObject = $this->client->callService("uiconf", "delete", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	function cloneAction($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$resultObject = $this->client->callService("uiconf", "clone", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaUiConf");
		return $resultObject;
	}

	function listAction(KalturaUiConfFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$resultObject = $this->client->callService("uiconf", "list", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}
}

class KalturaPlaylistService extends KalturaServiceBase
{
	function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	function add(KalturaPlaylist $playlist, $updateStats = false)
	{
		$kparams = array();
		$this->client->addParam($kparams, "playlist", $playlist->toParams());
		$this->client->addParam($kparams, "updateStats", $updateStats);
		$resultObject = $this->client->callService("playlist", "add", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaPlaylist");
		return $resultObject;
	}

	function get($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$resultObject = $this->client->callService("playlist", "get", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaPlaylist");
		return $resultObject;
	}

	function update($id, KalturaPlaylist $playlist, $updateStats = false)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "playlist", $playlist->toParams());
		$this->client->addParam($kparams, "updateStats", $updateStats);
		$resultObject = $this->client->callService("playlist", "update", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaUiConf");
		return $resultObject;
	}

	function delete($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$resultObject = $this->client->callService("playlist", "delete", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaPlaylist");
		return $resultObject;
	}

	function listAction(KalturaPlaylistFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$resultObject = $this->client->callService("playlist", "list", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	function execute($id, $detailed = false)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "detailed", $detailed);
		$resultObject = $this->client->callService("playlist", "execute", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	function executeFromContent($playlistType, $playlistContent, $detailed = false)
	{
		$kparams = array();
		$this->client->addParam($kparams, "playlistType", $playlistType);
		$this->client->addParam($kparams, "playlistContent", $playlistContent);
		$this->client->addParam($kparams, "detailed", $detailed);
		$resultObject = $this->client->callService("playlist", "executeFromContent", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	function getStatsFromContent($playlistType, $playlistContent)
	{
		$kparams = array();
		$this->client->addParam($kparams, "playlistType", $playlistType);
		$this->client->addParam($kparams, "playlistContent", $playlistContent);
		$resultObject = $this->client->callService("playlist", "getStatsFromContent", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}
}

class KalturaUserService extends KalturaServiceBase
{
	function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	function add($id, KalturaUser $user)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "user", $user->toParams());
		$resultObject = $this->client->callService("user", "add", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaUser");
		return $resultObject;
	}

	function update($id, KalturaUser $user)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "user", $user->toParams());
		$resultObject = $this->client->callService("user", "update", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaUser");
		return $resultObject;
	}

	function updateid($id, $newId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "newId", $newId);
		$resultObject = $this->client->callService("user", "updateid", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaUser");
		return $resultObject;
	}

	function get($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$resultObject = $this->client->callService("user", "get", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaUser");
		return $resultObject;
	}

	function delete($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$resultObject = $this->client->callService("user", "delete", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaUser");
		return $resultObject;
	}

	function listAction(KalturaUserFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$resultObject = $this->client->callService("user", "list", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}
}

class KalturaWidgetService extends KalturaServiceBase
{
	function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	function add(KalturaWidget $widget)
	{
		$kparams = array();
		$this->client->addParam($kparams, "widget", $widget->toParams());
		$resultObject = $this->client->callService("widget", "add", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaWidget");
		return $resultObject;
	}

	function update($id, KalturaWidget $widget)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "widget", $widget->toParams());
		$resultObject = $this->client->callService("widget", "update", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaWidget");
		return $resultObject;
	}

	function get($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$resultObject = $this->client->callService("widget", "get", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaWidget");
		return $resultObject;
	}

	function cloneAction(KalturaWidget $widget)
	{
		$kparams = array();
		$this->client->addParam($kparams, "widget", $widget->toParams());
		$resultObject = $this->client->callService("widget", "clone", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaWidget");
		return $resultObject;
	}

	function listAction(KalturaWidgetFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$resultObject = $this->client->callService("widget", "list", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}
}

class KalturaSearchService extends KalturaServiceBase
{
	function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	function search(KalturaSearch $search, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "search", $search->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$resultObject = $this->client->callService("search", "search", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	function getMediaInfo(KalturaSearchResult $searchResult)
	{
		$kparams = array();
		$this->client->addParam($kparams, "searchResult", $searchResult->toParams());
		$resultObject = $this->client->callService("search", "getMediaInfo", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaSearchResult");
		return $resultObject;
	}

	function searchUrl($mediaType, $url)
	{
		$kparams = array();
		$this->client->addParam($kparams, "mediaType", $mediaType);
		$this->client->addParam($kparams, "url", $url);
		$resultObject = $this->client->callService("search", "searchUrl", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaSearchResult");
		return $resultObject;
	}
}

class KalturaPartnerService extends KalturaServiceBase
{
	function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	function register(KalturaPartner $partner, $cmsPassword = "")
	{
		$kparams = array();
		$this->client->addParam($kparams, "partner", $partner->toParams());
		$this->client->addParam($kparams, "cmsPassword", $cmsPassword);
		$resultObject = $this->client->callService("partner", "register", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaPartner");
		return $resultObject;
	}

	function update(KalturaPartner $partner, $allowEmpty = false)
	{
		$kparams = array();
		$this->client->addParam($kparams, "partner", $partner->toParams());
		$this->client->addParam($kparams, "allowEmpty", $allowEmpty);
		$resultObject = $this->client->callService("partner", "update", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaPartner");
		return $resultObject;
	}

	function getsecrets($partnerId, $adminEmail, $cmsPassword)
	{
		$kparams = array();
		$this->client->addParam($kparams, "partnerId", $partnerId);
		$this->client->addParam($kparams, "adminEmail", $adminEmail);
		$this->client->addParam($kparams, "cmsPassword", $cmsPassword);
		$resultObject = $this->client->callService("partner", "getsecrets", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaPartner");
		return $resultObject;
	}

	function getinfo()
	{
		$kparams = array();
		$resultObject = $this->client->callService("partner", "getinfo", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaPartner");
		return $resultObject;
	}

	function getusage($year, $month = 1, $resolution = "days")
	{
		$kparams = array();
		$this->client->addParam($kparams, "year", $year);
		$this->client->addParam($kparams, "month", $month);
		$this->client->addParam($kparams, "resolution", $resolution);
		$resultObject = $this->client->callService("partner", "getusage", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaPartnerUsage");
		return $resultObject;
	}
}

class KalturaAdminuserService extends KalturaServiceBase
{
	function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	function updatepassword($email, $password, $newEmail = "", $newPassword = "")
	{
		$kparams = array();
		$this->client->addParam($kparams, "email", $email);
		$this->client->addParam($kparams, "password", $password);
		$this->client->addParam($kparams, "newEmail", $newEmail);
		$this->client->addParam($kparams, "newPassword", $newPassword);
		$resultObject = $this->client->callService("adminuser", "updatepassword", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaAdminUser");
		return $resultObject;
	}

	function resetpassword($email)
	{
		$kparams = array();
		$this->client->addParam($kparams, "email", $email);
		$resultObject = $this->client->callService("adminuser", "resetpassword", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "string");
		return $resultObject;
	}

	function login($email, $password)
	{
		$kparams = array();
		$this->client->addParam($kparams, "email", $email);
		$this->client->addParam($kparams, "password", $password);
		$resultObject = $this->client->callService("adminuser", "login", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "string");
		return $resultObject;
	}
}

class KalturaSystemService extends KalturaServiceBase
{
	function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	function ping()
	{
		$kparams = array();
		$resultObject = $this->client->callService("system", "ping", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "bool");
		return $resultObject;
	}
}

class KalturaNotificationService extends KalturaServiceBase
{
	function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	function getClientNotification($entryId, $type)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "type", $type);
		$resultObject = $this->client->callService("notification", "getClientNotification", $kparams);
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaClientNotification");
		return $resultObject;
	}
}

class KalturaClient extends KalturaClientBase
{
	/**
	 * Media Service
	 *
	 * @var KalturaMediaService
	 */
	public $media = null;

	/**
	 * Mixing Service
	 *
	 * @var KalturaMixingService
	 */
	public $mixing = null;

	/**
	 * Base Entry Service
	 *
	 * @var KalturaBaseEntryService
	 */
	public $baseEntry = null;

	/**
	 * Session service
	 *
	 * @var KalturaSessionService
	 */
	public $session = null;

	/**
	 * UiConf service lets you create and manage your UIConfs for the various flash components
	 * This service is used by the KMC-ApplicationStudio
	 *
	 * @var KalturaUiConfService
	 */
	public $uiConf = null;

	/**
	 * Playlist service lets you create,manage and play your playlists
	 * Playlists could be static (containing a fixed list of entries) or dynamic (baseed on a filter)
	 *
	 * @var KalturaPlaylistService
	 */
	public $playlist = null;

	/**
	 * Manage partner users on Kaltura's side
	 * The userId in kaltura is the unique Id in the partner's system, and the [partnerId,Id] couple are unique key in kaltura's DB
	 *
	 * @var KalturaUserService
	 */
	public $user = null;

	/**
	 * widget service for full widget management
	 *
	 * @var KalturaWidgetService
	 */
	public $widget = null;

	/**
	 * Search service allows you to search for media in various media providers
	 * This service is being used mostly by the CW component
	 *
	 * @var KalturaSearchService
	 */
	public $search = null;

	/**
	 * partner service allows you to change/manage your partner personal details and settings as well
	 *
	 * @var KalturaPartnerService
	 */
	public $partner = null;

	/**
	 * adminuser service
	 *
	 * @var KalturaAdminuserService
	 */
	public $adminuser = null;

	/**
	 * System Service
	 *
	 * @var KalturaSystemService
	 */
	public $system = null;

	/**
	 * Notification Service
	 *
	 * @var KalturaNotificationService
	 */
	public $notification = null;


	public function __construct(KalturaConfiguration $config)
	{
		parent::__construct($config);
		$this->media = new KalturaMediaService($this);
		$this->mixing = new KalturaMixingService($this);
		$this->baseEntry = new KalturaBaseEntryService($this);
		$this->session = new KalturaSessionService($this);
		$this->uiConf = new KalturaUiConfService($this);
		$this->playlist = new KalturaPlaylistService($this);
		$this->user = new KalturaUserService($this);
		$this->widget = new KalturaWidgetService($this);
		$this->search = new KalturaSearchService($this);
		$this->partner = new KalturaPartnerService($this);
		$this->adminuser = new KalturaAdminuserService($this);
		$this->system = new KalturaSystemService($this);
		$this->notification = new KalturaNotificationService($this);
	}
}
