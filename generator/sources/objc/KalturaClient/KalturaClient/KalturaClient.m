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
#import "KalturaClient.h"

///////////////////////// enums /////////////////////////
@implementation KalturaAppearInListType
+ (int)PARTNER_ONLY
{
    return 1;
}
+ (int)CATEGORY_MEMBERS_ONLY
{
    return 3;
}
@end

@implementation KalturaAssetParamsOrigin
+ (int)CONVERT
{
    return 0;
}
+ (int)INGEST
{
    return 1;
}
+ (int)CONVERT_WHEN_MISSING
{
    return 2;
}
@end

@implementation KalturaAssetStatus
+ (int)ERROR
{
    return -1;
}
+ (int)QUEUED
{
    return 0;
}
+ (int)READY
{
    return 2;
}
+ (int)DELETED
{
    return 3;
}
+ (int)IMPORTING
{
    return 7;
}
+ (int)EXPORTING
{
    return 9;
}
@end

@implementation KalturaBatchJobErrorTypes
+ (int)APP
{
    return 0;
}
+ (int)RUNTIME
{
    return 1;
}
+ (int)HTTP
{
    return 2;
}
+ (int)CURL
{
    return 3;
}
+ (int)KALTURA_API
{
    return 4;
}
+ (int)KALTURA_CLIENT
{
    return 5;
}
@end

@implementation KalturaBatchJobStatus
+ (int)PENDING
{
    return 0;
}
+ (int)QUEUED
{
    return 1;
}
+ (int)PROCESSING
{
    return 2;
}
+ (int)PROCESSED
{
    return 3;
}
+ (int)MOVEFILE
{
    return 4;
}
+ (int)FINISHED
{
    return 5;
}
+ (int)FAILED
{
    return 6;
}
+ (int)ABORTED
{
    return 7;
}
+ (int)ALMOST_DONE
{
    return 8;
}
+ (int)RETRY
{
    return 9;
}
+ (int)FATAL
{
    return 10;
}
+ (int)DONT_PROCESS
{
    return 11;
}
+ (int)FINISHED_PARTIALLY
{
    return 12;
}
@end

@implementation KalturaBitRateMode
+ (int)CBR
{
    return 1;
}
+ (int)VBR
{
    return 2;
}
@end

@implementation KalturaCategoryEntryStatus
+ (int)PENDING
{
    return 1;
}
+ (int)ACTIVE
{
    return 2;
}
+ (int)DELETED
{
    return 3;
}
+ (int)REJECTED
{
    return 4;
}
@end

@implementation KalturaCategoryStatus
+ (int)UPDATING
{
    return 1;
}
+ (int)ACTIVE
{
    return 2;
}
+ (int)DELETED
{
    return 3;
}
+ (int)PURGED
{
    return 4;
}
@end

@implementation KalturaCategoryUserPermissionLevel
+ (int)MANAGER
{
    return 0;
}
+ (int)MODERATOR
{
    return 1;
}
+ (int)CONTRIBUTOR
{
    return 2;
}
+ (int)MEMBER
{
    return 3;
}
+ (int)NONE
{
    return 4;
}
@end

@implementation KalturaCategoryUserStatus
+ (int)ACTIVE
{
    return 1;
}
+ (int)PENDING
{
    return 2;
}
+ (int)NOT_ACTIVE
{
    return 3;
}
@end

@implementation KalturaCommercialUseType
+ (int)NON_COMMERCIAL_USE
{
    return 0;
}
+ (int)COMMERCIAL_USE
{
    return 1;
}
@end

@implementation KalturaContributionPolicyType
+ (int)ALL
{
    return 1;
}
+ (int)MEMBERS_WITH_CONTRIBUTION_PERMISSION
{
    return 2;
}
@end

@implementation KalturaControlPanelCommandStatus
+ (int)PENDING
{
    return 1;
}
+ (int)HANDLED
{
    return 2;
}
+ (int)DONE
{
    return 3;
}
+ (int)FAILED
{
    return 4;
}
@end

@implementation KalturaControlPanelCommandTargetType
+ (int)DATA_CENTER
{
    return 1;
}
+ (int)SCHEDULER
{
    return 2;
}
+ (int)JOB_TYPE
{
    return 3;
}
+ (int)JOB
{
    return 4;
}
+ (int)BATCH
{
    return 5;
}
@end

@implementation KalturaControlPanelCommandType
+ (int)KILL
{
    return 4;
}
@end

@implementation KalturaCountryRestrictionType
+ (int)RESTRICT_COUNTRY_LIST
{
    return 0;
}
+ (int)ALLOW_COUNTRY_LIST
{
    return 1;
}
@end

@implementation KalturaDVRStatus
+ (int)DISABLED
{
    return 0;
}
+ (int)ENABLED
{
    return 1;
}
@end

@implementation KalturaDirectoryRestrictionType
+ (int)DONT_DISPLAY
{
    return 0;
}
+ (int)DISPLAY_WITH_LINK
{
    return 1;
}
@end

@implementation KalturaEditorType
+ (int)SIMPLE
{
    return 1;
}
+ (int)ADVANCED
{
    return 2;
}
@end

@implementation KalturaEmailIngestionProfileStatus
+ (int)INACTIVE
{
    return 0;
}
+ (int)ACTIVE
{
    return 1;
}
@end

@implementation KalturaEntryModerationStatus
+ (int)PENDING_MODERATION
{
    return 1;
}
+ (int)APPROVED
{
    return 2;
}
+ (int)REJECTED
{
    return 3;
}
+ (int)FLAGGED_FOR_REVIEW
{
    return 5;
}
+ (int)AUTO_APPROVED
{
    return 6;
}
@end

@implementation KalturaFeatureStatusType
+ (int)LOCK_CATEGORY
{
    return 1;
}
+ (int)CATEGORY
{
    return 2;
}
+ (int)CATEGORY_ENTRY
{
    return 3;
}
+ (int)ENTRY
{
    return 4;
}
+ (int)CATEGORY_USER
{
    return 5;
}
+ (int)USER
{
    return 6;
}
@end

@implementation KalturaFlavorAssetStatus
+ (int)ERROR
{
    return -1;
}
+ (int)QUEUED
{
    return 0;
}
+ (int)CONVERTING
{
    return 1;
}
+ (int)READY
{
    return 2;
}
+ (int)DELETED
{
    return 3;
}
+ (int)NOT_APPLICABLE
{
    return 4;
}
+ (int)TEMP
{
    return 5;
}
+ (int)WAIT_FOR_CONVERT
{
    return 6;
}
+ (int)IMPORTING
{
    return 7;
}
+ (int)VALIDATING
{
    return 8;
}
+ (int)EXPORTING
{
    return 9;
}
@end

@implementation KalturaFlavorReadyBehaviorType
+ (int)NO_IMPACT
{
    return 0;
}
+ (int)INHERIT_FLAVOR_PARAMS
{
    return 0;
}
+ (int)REQUIRED
{
    return 1;
}
+ (int)OPTIONAL
{
    return 2;
}
@end

@implementation KalturaGender
+ (int)UNKNOWN
{
    return 0;
}
+ (int)MALE
{
    return 1;
}
+ (int)FEMALE
{
    return 2;
}
@end

@implementation KalturaInheritanceType
+ (int)INHERIT
{
    return 1;
}
+ (int)MANUAL
{
    return 2;
}
@end

@implementation KalturaIpAddressRestrictionType
+ (int)RESTRICT_LIST
{
    return 0;
}
+ (int)ALLOW_LIST
{
    return 1;
}
@end

@implementation KalturaLicenseType
+ (int)UNKNOWN
{
    return -1;
}
+ (int)NONE
{
    return 0;
}
+ (int)COPYRIGHTED
{
    return 1;
}
+ (int)PUBLIC_DOMAIN
{
    return 2;
}
+ (int)CREATIVECOMMONS_ATTRIBUTION
{
    return 3;
}
+ (int)CREATIVECOMMONS_ATTRIBUTION_SHARE_ALIKE
{
    return 4;
}
+ (int)CREATIVECOMMONS_ATTRIBUTION_NO_DERIVATIVES
{
    return 5;
}
+ (int)CREATIVECOMMONS_ATTRIBUTION_NON_COMMERCIAL
{
    return 6;
}
+ (int)CREATIVECOMMONS_ATTRIBUTION_NON_COMMERCIAL_SHARE_ALIKE
{
    return 7;
}
+ (int)CREATIVECOMMONS_ATTRIBUTION_NON_COMMERCIAL_NO_DERIVATIVES
{
    return 8;
}
+ (int)GFDL
{
    return 9;
}
+ (int)GPL
{
    return 10;
}
+ (int)AFFERO_GPL
{
    return 11;
}
+ (int)LGPL
{
    return 12;
}
+ (int)BSD
{
    return 13;
}
+ (int)APACHE
{
    return 14;
}
+ (int)MOZILLA
{
    return 15;
}
@end

@implementation KalturaLimitFlavorsRestrictionType
+ (int)RESTRICT_LIST
{
    return 0;
}
+ (int)ALLOW_LIST
{
    return 1;
}
@end

@implementation KalturaMailJobStatus
+ (int)PENDING
{
    return 1;
}
+ (int)SENT
{
    return 2;
}
+ (int)ERROR
{
    return 3;
}
+ (int)QUEUED
{
    return 4;
}
@end

@implementation KalturaMediaType
+ (int)VIDEO
{
    return 1;
}
+ (int)IMAGE
{
    return 2;
}
+ (int)AUDIO
{
    return 5;
}
+ (int)LIVE_STREAM_FLASH
{
    return 201;
}
+ (int)LIVE_STREAM_WINDOWS_MEDIA
{
    return 202;
}
+ (int)LIVE_STREAM_REAL_MEDIA
{
    return 203;
}
+ (int)LIVE_STREAM_QUICKTIME
{
    return 204;
}
@end

@implementation KalturaModerationFlagType
+ (int)SEXUAL_CONTENT
{
    return 1;
}
+ (int)VIOLENT_REPULSIVE
{
    return 2;
}
+ (int)HARMFUL_DANGEROUS
{
    return 3;
}
+ (int)SPAM_COMMERCIALS
{
    return 4;
}
+ (int)COPYRIGHT
{
    return 5;
}
+ (int)TERMS_OF_USE_VIOLATION
{
    return 6;
}
@end

@implementation KalturaMrssExtensionMode
+ (int)APPEND
{
    return 1;
}
+ (int)REPLACE
{
    return 2;
}
@end

@implementation KalturaNotificationObjectType
+ (int)ENTRY
{
    return 1;
}
+ (int)KSHOW
{
    return 2;
}
+ (int)USER
{
    return 3;
}
+ (int)BATCH_JOB
{
    return 4;
}
@end

@implementation KalturaNotificationStatus
+ (int)PENDING
{
    return 1;
}
+ (int)SENT
{
    return 2;
}
+ (int)ERROR
{
    return 3;
}
+ (int)SHOULD_RESEND
{
    return 4;
}
+ (int)ERROR_RESENDING
{
    return 5;
}
+ (int)SENT_SYNCH
{
    return 6;
}
+ (int)QUEUED
{
    return 7;
}
@end

@implementation KalturaNotificationType
+ (int)ENTRY_ADD
{
    return 1;
}
+ (int)ENTR_UPDATE_PERMISSIONS
{
    return 2;
}
+ (int)ENTRY_DELETE
{
    return 3;
}
+ (int)ENTRY_BLOCK
{
    return 4;
}
+ (int)ENTRY_UPDATE
{
    return 5;
}
+ (int)ENTRY_UPDATE_THUMBNAIL
{
    return 6;
}
+ (int)ENTRY_UPDATE_MODERATION
{
    return 7;
}
+ (int)USER_ADD
{
    return 21;
}
+ (int)USER_BANNED
{
    return 26;
}
@end

@implementation KalturaNullableBoolean
+ (int)NULL_VALUE
{
    return -1;
}
+ (int)FALSE_VALUE
{
    return 0;
}
+ (int)TRUE_VALUE
{
    return 1;
}
@end

@implementation KalturaPartnerGroupType
+ (int)PUBLISHER
{
    return 1;
}
+ (int)VAR_GROUP
{
    return 2;
}
+ (int)GROUP
{
    return 3;
}
+ (int)TEMPLATE
{
    return 4;
}
@end

@implementation KalturaPartnerStatus
+ (int)ACTIVE
{
    return 1;
}
+ (int)BLOCKED
{
    return 2;
}
+ (int)FULL_BLOCK
{
    return 3;
}
@end

@implementation KalturaPartnerType
+ (int)KMC
{
    return 1;
}
+ (int)WIKI
{
    return 100;
}
+ (int)WORDPRESS
{
    return 101;
}
+ (int)DRUPAL
{
    return 102;
}
+ (int)DEKIWIKI
{
    return 103;
}
+ (int)MOODLE
{
    return 104;
}
+ (int)COMMUNITY_EDITION
{
    return 105;
}
+ (int)JOOMLA
{
    return 106;
}
+ (int)BLACKBOARD
{
    return 107;
}
+ (int)SAKAI
{
    return 108;
}
+ (int)ADMIN_CONSOLE
{
    return 109;
}
@end

@implementation KalturaPermissionStatus
+ (int)ACTIVE
{
    return 1;
}
+ (int)BLOCKED
{
    return 2;
}
+ (int)DELETED
{
    return 3;
}
@end

@implementation KalturaPermissionType
+ (int)NORMAL
{
    return 1;
}
+ (int)SPECIAL_FEATURE
{
    return 2;
}
+ (int)PLUGIN
{
    return 3;
}
+ (int)PARTNER_GROUP
{
    return 4;
}
@end

@implementation KalturaPlaylistType
+ (int)STATIC_LIST
{
    return 3;
}
+ (int)DYNAMIC
{
    return 10;
}
+ (int)EXTERNAL
{
    return 101;
}
@end

@implementation KalturaPrivacyType
+ (int)ALL
{
    return 1;
}
+ (int)AUTHENTICATED_USERS
{
    return 2;
}
+ (int)MEMBERS_ONLY
{
    return 3;
}
@end

@implementation KalturaReportType
+ (int)TOP_CONTENT
{
    return 1;
}
+ (int)CONTENT_DROPOFF
{
    return 2;
}
+ (int)CONTENT_INTERACTIONS
{
    return 3;
}
+ (int)MAP_OVERLAY
{
    return 4;
}
+ (int)TOP_CONTRIBUTORS
{
    return 5;
}
+ (int)TOP_SYNDICATION
{
    return 6;
}
+ (int)CONTENT_CONTRIBUTIONS
{
    return 7;
}
+ (int)USER_ENGAGEMENT
{
    return 11;
}
+ (int)SPEFICIC_USER_ENGAGEMENT
{
    return 12;
}
+ (int)USER_TOP_CONTENT
{
    return 13;
}
+ (int)USER_CONTENT_DROPOFF
{
    return 14;
}
+ (int)USER_CONTENT_INTERACTIONS
{
    return 15;
}
+ (int)APPLICATIONS
{
    return 16;
}
+ (int)USER_USAGE
{
    return 17;
}
+ (int)SPECIFIC_USER_USAGE
{
    return 18;
}
+ (int)VAR_USAGE
{
    return 19;
}
+ (int)TOP_CREATORS
{
    return 20;
}
+ (int)PLATFORMS
{
    return 21;
}
+ (int)OPERATION_SYSTEM
{
    return 22;
}
+ (int)BROWSERS
{
    return 23;
}
+ (int)PARTNER_USAGE
{
    return 201;
}
@end

@implementation KalturaSearchOperatorType
+ (int)SEARCH_AND
{
    return 1;
}
+ (int)SEARCH_OR
{
    return 2;
}
@end

@implementation KalturaSearchProviderType
+ (int)FLICKR
{
    return 3;
}
+ (int)YOUTUBE
{
    return 4;
}
+ (int)MYSPACE
{
    return 7;
}
+ (int)PHOTOBUCKET
{
    return 8;
}
+ (int)JAMENDO
{
    return 9;
}
+ (int)CCMIXTER
{
    return 10;
}
+ (int)NYPL
{
    return 11;
}
+ (int)CURRENT
{
    return 12;
}
+ (int)MEDIA_COMMONS
{
    return 13;
}
+ (int)KALTURA
{
    return 20;
}
+ (int)KALTURA_USER_CLIPS
{
    return 21;
}
+ (int)ARCHIVE_ORG
{
    return 22;
}
+ (int)KALTURA_PARTNER
{
    return 23;
}
+ (int)METACAFE
{
    return 24;
}
+ (int)SEARCH_PROXY
{
    return 28;
}
+ (int)PARTNER_SPECIFIC
{
    return 100;
}
@end

@implementation KalturaSessionType
+ (int)USER
{
    return 0;
}
+ (int)ADMIN
{
    return 2;
}
@end

@implementation KalturaSiteRestrictionType
+ (int)RESTRICT_SITE_LIST
{
    return 0;
}
+ (int)ALLOW_SITE_LIST
{
    return 1;
}
@end

@implementation KalturaStatsEventType
+ (int)WIDGET_LOADED
{
    return 1;
}
+ (int)MEDIA_LOADED
{
    return 2;
}
+ (int)PLAY
{
    return 3;
}
+ (int)PLAY_REACHED_25
{
    return 4;
}
+ (int)PLAY_REACHED_50
{
    return 5;
}
+ (int)PLAY_REACHED_75
{
    return 6;
}
+ (int)PLAY_REACHED_100
{
    return 7;
}
+ (int)OPEN_EDIT
{
    return 8;
}
+ (int)OPEN_VIRAL
{
    return 9;
}
+ (int)OPEN_DOWNLOAD
{
    return 10;
}
+ (int)OPEN_REPORT
{
    return 11;
}
+ (int)BUFFER_START
{
    return 12;
}
+ (int)BUFFER_END
{
    return 13;
}
+ (int)OPEN_FULL_SCREEN
{
    return 14;
}
+ (int)CLOSE_FULL_SCREEN
{
    return 15;
}
+ (int)REPLAY
{
    return 16;
}
+ (int)SEEK
{
    return 17;
}
+ (int)OPEN_UPLOAD
{
    return 18;
}
+ (int)SAVE_PUBLISH
{
    return 19;
}
+ (int)CLOSE_EDITOR
{
    return 20;
}
+ (int)PRE_BUMPER_PLAYED
{
    return 21;
}
+ (int)POST_BUMPER_PLAYED
{
    return 22;
}
+ (int)BUMPER_CLICKED
{
    return 23;
}
+ (int)PREROLL_STARTED
{
    return 24;
}
+ (int)MIDROLL_STARTED
{
    return 25;
}
+ (int)POSTROLL_STARTED
{
    return 26;
}
+ (int)OVERLAY_STARTED
{
    return 27;
}
+ (int)PREROLL_CLICKED
{
    return 28;
}
+ (int)MIDROLL_CLICKED
{
    return 29;
}
+ (int)POSTROLL_CLICKED
{
    return 30;
}
+ (int)OVERLAY_CLICKED
{
    return 31;
}
+ (int)PREROLL_25
{
    return 32;
}
+ (int)PREROLL_50
{
    return 33;
}
+ (int)PREROLL_75
{
    return 34;
}
+ (int)MIDROLL_25
{
    return 35;
}
+ (int)MIDROLL_50
{
    return 36;
}
+ (int)MIDROLL_75
{
    return 37;
}
+ (int)POSTROLL_25
{
    return 38;
}
+ (int)POSTROLL_50
{
    return 39;
}
+ (int)POSTROLL_75
{
    return 40;
}
@end

@implementation KalturaStatsFeatureType
+ (int)NONE
{
    return 0;
}
+ (int)RELATED
{
    return 1;
}
@end

@implementation KalturaStatsKmcEventType
+ (int)CONTENT_PAGE_VIEW
{
    return 1001;
}
+ (int)CONTENT_ADD_PLAYLIST
{
    return 1010;
}
+ (int)CONTENT_EDIT_PLAYLIST
{
    return 1011;
}
+ (int)CONTENT_DELETE_PLAYLIST
{
    return 1012;
}
+ (int)CONTENT_EDIT_ENTRY
{
    return 1013;
}
+ (int)CONTENT_CHANGE_THUMBNAIL
{
    return 1014;
}
+ (int)CONTENT_ADD_TAGS
{
    return 1015;
}
+ (int)CONTENT_REMOVE_TAGS
{
    return 1016;
}
+ (int)CONTENT_ADD_ADMIN_TAGS
{
    return 1017;
}
+ (int)CONTENT_REMOVE_ADMIN_TAGS
{
    return 1018;
}
+ (int)CONTENT_DOWNLOAD
{
    return 1019;
}
+ (int)CONTENT_APPROVE_MODERATION
{
    return 1020;
}
+ (int)CONTENT_REJECT_MODERATION
{
    return 1021;
}
+ (int)CONTENT_BULK_UPLOAD
{
    return 1022;
}
+ (int)CONTENT_ADMIN_KCW_UPLOAD
{
    return 1023;
}
+ (int)ACCOUNT_CHANGE_PARTNER_INFO
{
    return 1030;
}
+ (int)ACCOUNT_CHANGE_LOGIN_INFO
{
    return 1031;
}
+ (int)ACCOUNT_CONTACT_US_USAGE
{
    return 1032;
}
+ (int)ACCOUNT_UPDATE_SERVER_SETTINGS
{
    return 1033;
}
+ (int)ACCOUNT_ACCOUNT_OVERVIEW
{
    return 1034;
}
+ (int)ACCOUNT_ACCESS_CONTROL
{
    return 1035;
}
+ (int)ACCOUNT_TRANSCODING_SETTINGS
{
    return 1036;
}
+ (int)ACCOUNT_ACCOUNT_UPGRADE
{
    return 1037;
}
+ (int)ACCOUNT_SAVE_SERVER_SETTINGS
{
    return 1038;
}
+ (int)ACCOUNT_ACCESS_CONTROL_DELETE
{
    return 1039;
}
+ (int)ACCOUNT_SAVE_TRANSCODING_SETTINGS
{
    return 1040;
}
+ (int)LOGIN
{
    return 1041;
}
+ (int)DASHBOARD_IMPORT_CONTENT
{
    return 1042;
}
+ (int)DASHBOARD_UPDATE_CONTENT
{
    return 1043;
}
+ (int)DASHBOARD_ACCOUNT_CONTACT_US
{
    return 1044;
}
+ (int)DASHBOARD_VIEW_REPORTS
{
    return 1045;
}
+ (int)DASHBOARD_EMBED_PLAYER
{
    return 1046;
}
+ (int)DASHBOARD_EMBED_PLAYLIST
{
    return 1047;
}
+ (int)DASHBOARD_CUSTOMIZE_PLAYERS
{
    return 1048;
}
+ (int)APP_STUDIO_NEW_PLAYER_SINGLE_VIDEO
{
    return 1050;
}
+ (int)APP_STUDIO_NEW_PLAYER_PLAYLIST
{
    return 1051;
}
+ (int)APP_STUDIO_NEW_PLAYER_MULTI_TAB_PLAYLIST
{
    return 1052;
}
+ (int)APP_STUDIO_EDIT_PLAYER_SINGLE_VIDEO
{
    return 1053;
}
+ (int)APP_STUDIO_EDIT_PLAYER_PLAYLIST
{
    return 1054;
}
+ (int)APP_STUDIO_EDIT_PLAYER_MULTI_TAB_PLAYLIST
{
    return 1055;
}
+ (int)APP_STUDIO_DUPLICATE_PLAYER
{
    return 1056;
}
+ (int)CONTENT_CONTENT_GO_TO_PAGE
{
    return 1057;
}
+ (int)CONTENT_DELETE_ITEM
{
    return 1058;
}
+ (int)CONTENT_DELETE_MIX
{
    return 1059;
}
+ (int)REPORTS_AND_ANALYTICS_BANDWIDTH_USAGE_TAB
{
    return 1070;
}
+ (int)REPORTS_AND_ANALYTICS_CONTENT_REPORTS_TAB
{
    return 1071;
}
+ (int)REPORTS_AND_ANALYTICS_USERS_AND_COMMUNITY_REPORTS_TAB
{
    return 1072;
}
+ (int)REPORTS_AND_ANALYTICS_TOP_CONTRIBUTORS
{
    return 1073;
}
+ (int)REPORTS_AND_ANALYTICS_MAP_OVERLAYS
{
    return 1074;
}
+ (int)REPORTS_AND_ANALYTICS_TOP_SYNDICATIONS
{
    return 1075;
}
+ (int)REPORTS_AND_ANALYTICS_TOP_CONTENT
{
    return 1076;
}
+ (int)REPORTS_AND_ANALYTICS_CONTENT_DROPOFF
{
    return 1077;
}
+ (int)REPORTS_AND_ANALYTICS_CONTENT_INTERACTIONS
{
    return 1078;
}
+ (int)REPORTS_AND_ANALYTICS_CONTENT_CONTRIBUTIONS
{
    return 1079;
}
+ (int)REPORTS_AND_ANALYTICS_VIDEO_DRILL_DOWN
{
    return 1080;
}
+ (int)REPORTS_AND_ANALYTICS_CONTENT_DRILL_DOWN_INTERACTION
{
    return 1081;
}
+ (int)REPORTS_AND_ANALYTICS_CONTENT_CONTRIBUTIONS_DRILLDOWN
{
    return 1082;
}
+ (int)REPORTS_AND_ANALYTICS_VIDEO_DRILL_DOWN_DROPOFF
{
    return 1083;
}
+ (int)REPORTS_AND_ANALYTICS_MAP_OVERLAYS_DRILLDOWN
{
    return 1084;
}
+ (int)REPORTS_AND_ANALYTICS_TOP_SYNDICATIONS_DRILL_DOWN
{
    return 1085;
}
+ (int)REPORTS_AND_ANALYTICS_BANDWIDTH_USAGE_VIEW_MONTHLY
{
    return 1086;
}
+ (int)REPORTS_AND_ANALYTICS_BANDWIDTH_USAGE_VIEW_YEARLY
{
    return 1087;
}
+ (int)CONTENT_ENTRY_DRILLDOWN
{
    return 1088;
}
+ (int)CONTENT_OPEN_PREVIEW_AND_EMBED
{
    return 1089;
}
@end

@implementation KalturaStorageProfileDeliveryStatus
+ (int)ACTIVE
{
    return 1;
}
+ (int)BLOCKED
{
    return 2;
}
@end

@implementation KalturaStorageProfileProtocol
+ (int)KALTURA_DC
{
    return 0;
}
+ (int)FTP
{
    return 1;
}
+ (int)SCP
{
    return 2;
}
+ (int)SFTP
{
    return 3;
}
+ (int)S3
{
    return 6;
}
@end

@implementation KalturaStorageProfileReadyBehavior
+ (int)NO_IMPACT
{
    return 0;
}
+ (int)REQUIRED
{
    return 1;
}
@end

@implementation KalturaStorageProfileStatus
+ (int)DISABLED
{
    return 1;
}
+ (int)AUTOMATIC
{
    return 2;
}
+ (int)MANUAL
{
    return 3;
}
@end

@implementation KalturaSyndicationFeedStatus
+ (int)DELETED
{
    return -1;
}
+ (int)ACTIVE
{
    return 1;
}
@end

@implementation KalturaSyndicationFeedType
+ (int)GOOGLE_VIDEO
{
    return 1;
}
+ (int)YAHOO
{
    return 2;
}
+ (int)ITUNES
{
    return 3;
}
+ (int)TUBE_MOGUL
{
    return 4;
}
+ (int)KALTURA
{
    return 5;
}
+ (int)KALTURA_XSLT
{
    return 6;
}
@end

@implementation KalturaThumbAssetStatus
+ (int)ERROR
{
    return -1;
}
+ (int)QUEUED
{
    return 0;
}
+ (int)CAPTURING
{
    return 1;
}
+ (int)READY
{
    return 2;
}
+ (int)DELETED
{
    return 3;
}
+ (int)IMPORTING
{
    return 7;
}
+ (int)EXPORTING
{
    return 9;
}
@end

@implementation KalturaThumbCropType
+ (int)RESIZE
{
    return 1;
}
+ (int)RESIZE_WITH_PADDING
{
    return 2;
}
+ (int)CROP
{
    return 3;
}
+ (int)CROP_FROM_TOP
{
    return 4;
}
+ (int)RESIZE_WITH_FORCE
{
    return 5;
}
@end

@implementation KalturaUiConfCreationMode
+ (int)WIZARD
{
    return 2;
}
+ (int)ADVANCED
{
    return 3;
}
@end

@implementation KalturaUiConfObjType
+ (int)PLAYER
{
    return 1;
}
+ (int)CONTRIBUTION_WIZARD
{
    return 2;
}
+ (int)SIMPLE_EDITOR
{
    return 3;
}
+ (int)ADVANCED_EDITOR
{
    return 4;
}
+ (int)PLAYLIST
{
    return 5;
}
+ (int)APP_STUDIO
{
    return 6;
}
+ (int)KRECORD
{
    return 7;
}
+ (int)PLAYER_V3
{
    return 8;
}
+ (int)KMC_ACCOUNT
{
    return 9;
}
+ (int)KMC_ANALYTICS
{
    return 10;
}
+ (int)KMC_CONTENT
{
    return 11;
}
+ (int)KMC_DASHBOARD
{
    return 12;
}
+ (int)KMC_LOGIN
{
    return 13;
}
+ (int)PLAYER_SL
{
    return 14;
}
+ (int)CLIENTSIDE_ENCODER
{
    return 15;
}
+ (int)KMC_GENERAL
{
    return 16;
}
+ (int)KMC_ROLES_AND_PERMISSIONS
{
    return 17;
}
+ (int)CLIPPER
{
    return 18;
}
+ (int)KSR
{
    return 19;
}
+ (int)KUPLOAD
{
    return 20;
}
@end

@implementation KalturaUpdateMethodType
+ (int)MANUAL
{
    return 0;
}
+ (int)AUTOMATIC
{
    return 1;
}
@end

@implementation KalturaUploadErrorCode
+ (int)NO_ERROR
{
    return 0;
}
+ (int)GENERAL_ERROR
{
    return 1;
}
+ (int)PARTIAL_UPLOAD
{
    return 2;
}
@end

@implementation KalturaUploadTokenStatus
+ (int)PENDING
{
    return 0;
}
+ (int)PARTIAL_UPLOAD
{
    return 1;
}
+ (int)FULL_UPLOAD
{
    return 2;
}
+ (int)CLOSED
{
    return 3;
}
+ (int)TIMED_OUT
{
    return 4;
}
+ (int)DELETED
{
    return 5;
}
@end

@implementation KalturaUserAgentRestrictionType
+ (int)RESTRICT_LIST
{
    return 0;
}
+ (int)ALLOW_LIST
{
    return 1;
}
@end

@implementation KalturaUserJoinPolicyType
+ (int)AUTO_JOIN
{
    return 1;
}
+ (int)REQUEST_TO_JOIN
{
    return 2;
}
+ (int)NOT_ALLOWED
{
    return 3;
}
@end

@implementation KalturaUserRoleStatus
+ (int)ACTIVE
{
    return 1;
}
+ (int)BLOCKED
{
    return 2;
}
+ (int)DELETED
{
    return 3;
}
@end

@implementation KalturaUserStatus
+ (int)BLOCKED
{
    return 0;
}
+ (int)ACTIVE
{
    return 1;
}
+ (int)DELETED
{
    return 2;
}
@end

@implementation KalturaWidgetSecurityType
+ (int)NONE
{
    return 1;
}
+ (int)TIMEHASH
{
    return 2;
}
@end

@implementation KalturaAccessControlActionType
+ (NSString*)BLOCK
{
    return @"1";
}
+ (NSString*)PREVIEW
{
    return @"2";
}
+ (NSString*)LIMIT_FLAVORS
{
    return @"3";
}
@end

@implementation KalturaAccessControlContextType
+ (NSString*)PLAY
{
    return @"1";
}
+ (NSString*)DOWNLOAD
{
    return @"2";
}
+ (NSString*)THUMBNAIL
{
    return @"3";
}
+ (NSString*)METADATA
{
    return @"4";
}
@end

@implementation KalturaAccessControlOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
@end

@implementation KalturaAccessControlProfileOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
@end

@implementation KalturaAdminUserOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)ID_ASC
{
    return @"+id";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)ID_DESC
{
    return @"-id";
}
@end

@implementation KalturaAkamaiUniversalStreamType
+ (NSString*)HD_IPHONE_IPAD_LIVE
{
    return @"HD iPhone/iPad Live";
}
+ (NSString*)UNIVERSAL_STREAMING_LIVE
{
    return @"Universal Streaming Live";
}
@end

@implementation KalturaAmazonS3StorageProfileFilesPermissionLevel
+ (NSString*)ACL_AUTHENTICATED_READ
{
    return @"authenticated-read";
}
+ (NSString*)ACL_PRIVATE
{
    return @"private";
}
+ (NSString*)ACL_PUBLIC_READ
{
    return @"public-read";
}
+ (NSString*)ACL_PUBLIC_READ_WRITE
{
    return @"public-read-write";
}
@end

@implementation KalturaAmazonS3StorageProfileOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
@end

@implementation KalturaApiActionPermissionItemOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)ID_ASC
{
    return @"+id";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)ID_DESC
{
    return @"-id";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
@end

@implementation KalturaApiParameterPermissionItemAction
+ (NSString*)USAGE
{
    return @"all";
}
+ (NSString*)INSERT
{
    return @"insert";
}
+ (NSString*)READ
{
    return @"read";
}
+ (NSString*)UPDATE
{
    return @"update";
}
@end

@implementation KalturaApiParameterPermissionItemOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)ID_ASC
{
    return @"+id";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)ID_DESC
{
    return @"-id";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
@end

@implementation KalturaAssetOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)DELETED_AT_ASC
{
    return @"+deletedAt";
}
+ (NSString*)SIZE_ASC
{
    return @"+size";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)DELETED_AT_DESC
{
    return @"-deletedAt";
}
+ (NSString*)SIZE_DESC
{
    return @"-size";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
@end

@implementation KalturaAssetParamsOrderBy
@end

@implementation KalturaAssetParamsOutputOrderBy
@end

@implementation KalturaAssetType
+ (NSString*)ATTACHMENT
{
    return @"attachment.Attachment";
}
+ (NSString*)CAPTION
{
    return @"caption.Caption";
}
+ (NSString*)DOCUMENT
{
    return @"document.Document";
}
+ (NSString*)IMAGE
{
    return @"document.Image";
}
+ (NSString*)PDF
{
    return @"document.PDF";
}
+ (NSString*)SWF
{
    return @"document.SWF";
}
+ (NSString*)WIDEVINE_FLAVOR
{
    return @"widevine.WidevineFlavor";
}
+ (NSString*)FLAVOR
{
    return @"1";
}
+ (NSString*)THUMBNAIL
{
    return @"2";
}
@end

@implementation KalturaAudioCodec
+ (NSString*)NONE
{
    return @"";
}
+ (NSString*)AAC
{
    return @"aac";
}
+ (NSString*)AACHE
{
    return @"aache";
}
+ (NSString*)AC3
{
    return @"ac3";
}
+ (NSString*)AMRNB
{
    return @"amrnb";
}
+ (NSString*)COPY
{
    return @"copy";
}
+ (NSString*)MP3
{
    return @"mp3";
}
+ (NSString*)MPEG2
{
    return @"mpeg2";
}
+ (NSString*)PCM
{
    return @"pcm";
}
+ (NSString*)VORBIS
{
    return @"vorbis";
}
+ (NSString*)WMA
{
    return @"wma";
}
+ (NSString*)WMAPRO
{
    return @"wmapro";
}
@end

@implementation KalturaBaseEntryOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)END_DATE_ASC
{
    return @"+endDate";
}
+ (NSString*)MODERATION_COUNT_ASC
{
    return @"+moderationCount";
}
+ (NSString*)NAME_ASC
{
    return @"+name";
}
+ (NSString*)PARTNER_SORT_VALUE_ASC
{
    return @"+partnerSortValue";
}
+ (NSString*)RANK_ASC
{
    return @"+rank";
}
+ (NSString*)RECENT_ASC
{
    return @"+recent";
}
+ (NSString*)START_DATE_ASC
{
    return @"+startDate";
}
+ (NSString*)TOTAL_RANK_ASC
{
    return @"+totalRank";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)WEIGHT_ASC
{
    return @"+weight";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)END_DATE_DESC
{
    return @"-endDate";
}
+ (NSString*)MODERATION_COUNT_DESC
{
    return @"-moderationCount";
}
+ (NSString*)NAME_DESC
{
    return @"-name";
}
+ (NSString*)PARTNER_SORT_VALUE_DESC
{
    return @"-partnerSortValue";
}
+ (NSString*)RANK_DESC
{
    return @"-rank";
}
+ (NSString*)RECENT_DESC
{
    return @"-recent";
}
+ (NSString*)START_DATE_DESC
{
    return @"-startDate";
}
+ (NSString*)TOTAL_RANK_DESC
{
    return @"-totalRank";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
+ (NSString*)WEIGHT_DESC
{
    return @"-weight";
}
@end

@implementation KalturaBaseSyndicationFeedOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)NAME_ASC
{
    return @"+name";
}
+ (NSString*)PLAYLIST_ID_ASC
{
    return @"+playlistId";
}
+ (NSString*)TYPE_ASC
{
    return @"+type";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)NAME_DESC
{
    return @"-name";
}
+ (NSString*)PLAYLIST_ID_DESC
{
    return @"-playlistId";
}
+ (NSString*)TYPE_DESC
{
    return @"-type";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
@end

@implementation KalturaBatchJobOrderBy
+ (NSString*)CHECK_AGAIN_TIMEOUT_ASC
{
    return @"+checkAgainTimeout";
}
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)ESTIMATED_EFFORT_ASC
{
    return @"+estimatedEffort";
}
+ (NSString*)EXECUTION_ATTEMPTS_ASC
{
    return @"+executionAttempts";
}
+ (NSString*)FINISH_TIME_ASC
{
    return @"+finishTime";
}
+ (NSString*)LOCK_EXPIRATION_ASC
{
    return @"+lockExpiration";
}
+ (NSString*)LOCK_VERSION_ASC
{
    return @"+lockVersion";
}
+ (NSString*)PRIORITY_ASC
{
    return @"+priority";
}
+ (NSString*)QUEUE_TIME_ASC
{
    return @"+queueTime";
}
+ (NSString*)STATUS_ASC
{
    return @"+status";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)CHECK_AGAIN_TIMEOUT_DESC
{
    return @"-checkAgainTimeout";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)ESTIMATED_EFFORT_DESC
{
    return @"-estimatedEffort";
}
+ (NSString*)EXECUTION_ATTEMPTS_DESC
{
    return @"-executionAttempts";
}
+ (NSString*)FINISH_TIME_DESC
{
    return @"-finishTime";
}
+ (NSString*)LOCK_EXPIRATION_DESC
{
    return @"-lockExpiration";
}
+ (NSString*)LOCK_VERSION_DESC
{
    return @"-lockVersion";
}
+ (NSString*)PRIORITY_DESC
{
    return @"-priority";
}
+ (NSString*)QUEUE_TIME_DESC
{
    return @"-queueTime";
}
+ (NSString*)STATUS_DESC
{
    return @"-status";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
@end

@implementation KalturaBatchJobType
+ (NSString*)PARSE_CAPTION_ASSET
{
    return @"captionSearch.parseCaptionAsset";
}
+ (NSString*)DISTRIBUTION_DELETE
{
    return @"contentDistribution.DistributionDelete";
}
+ (NSString*)DISTRIBUTION_DISABLE
{
    return @"contentDistribution.DistributionDisable";
}
+ (NSString*)DISTRIBUTION_ENABLE
{
    return @"contentDistribution.DistributionEnable";
}
+ (NSString*)DISTRIBUTION_FETCH_REPORT
{
    return @"contentDistribution.DistributionFetchReport";
}
+ (NSString*)DISTRIBUTION_SUBMIT
{
    return @"contentDistribution.DistributionSubmit";
}
+ (NSString*)DISTRIBUTION_SYNC
{
    return @"contentDistribution.DistributionSync";
}
+ (NSString*)DISTRIBUTION_UPDATE
{
    return @"contentDistribution.DistributionUpdate";
}
+ (NSString*)DROP_FOLDER_CONTENT_PROCESSOR
{
    return @"dropFolder.DropFolderContentProcessor";
}
+ (NSString*)CONVERT
{
    return @"0";
}
+ (NSString*)DROP_FOLDER_WATCHER
{
    return @"dropFolder.DropFolderWatcher";
}
+ (NSString*)EVENT_NOTIFICATION_HANDLER
{
    return @"eventNotification.EventNotificationHandler";
}
+ (NSString*)TAG_RESOLVE
{
    return @"tagSearch.TagResolve";
}
+ (NSString*)VIRUS_SCAN
{
    return @"virusScan.VirusScan";
}
+ (NSString*)IMPORT
{
    return @"1";
}
+ (NSString*)DELETE
{
    return @"2";
}
+ (NSString*)FLATTEN
{
    return @"3";
}
+ (NSString*)BULKUPLOAD
{
    return @"4";
}
+ (NSString*)DVDCREATOR
{
    return @"5";
}
+ (NSString*)DOWNLOAD
{
    return @"6";
}
+ (NSString*)OOCONVERT
{
    return @"7";
}
+ (NSString*)CONVERT_PROFILE
{
    return @"10";
}
+ (NSString*)POSTCONVERT
{
    return @"11";
}
+ (NSString*)EXTRACT_MEDIA
{
    return @"14";
}
+ (NSString*)MAIL
{
    return @"15";
}
+ (NSString*)NOTIFICATION
{
    return @"16";
}
+ (NSString*)CLEANUP
{
    return @"17";
}
+ (NSString*)SCHEDULER_HELPER
{
    return @"18";
}
+ (NSString*)BULKDOWNLOAD
{
    return @"19";
}
+ (NSString*)DB_CLEANUP
{
    return @"20";
}
+ (NSString*)PROVISION_PROVIDE
{
    return @"21";
}
+ (NSString*)CONVERT_COLLECTION
{
    return @"22";
}
+ (NSString*)STORAGE_EXPORT
{
    return @"23";
}
+ (NSString*)PROVISION_DELETE
{
    return @"24";
}
+ (NSString*)STORAGE_DELETE
{
    return @"25";
}
+ (NSString*)EMAIL_INGESTION
{
    return @"26";
}
+ (NSString*)METADATA_IMPORT
{
    return @"27";
}
+ (NSString*)METADATA_TRANSFORM
{
    return @"28";
}
+ (NSString*)FILESYNC_IMPORT
{
    return @"29";
}
+ (NSString*)CAPTURE_THUMB
{
    return @"30";
}
+ (NSString*)DELETE_FILE
{
    return @"31";
}
+ (NSString*)INDEX
{
    return @"32";
}
+ (NSString*)MOVE_CATEGORY_ENTRIES
{
    return @"33";
}
+ (NSString*)COPY
{
    return @"34";
}
@end

@implementation KalturaBulkUploadAction
+ (NSString*)ADD
{
    return @"1";
}
+ (NSString*)UPDATE
{
    return @"2";
}
+ (NSString*)DELETE
{
    return @"3";
}
+ (NSString*)REPLACE
{
    return @"4";
}
+ (NSString*)TRANSFORM_XSLT
{
    return @"5";
}
+ (NSString*)ADD_OR_UPDATE
{
    return @"6";
}
@end

@implementation KalturaBulkUploadObjectType
+ (NSString*)ENTRY
{
    return @"1";
}
+ (NSString*)CATEGORY
{
    return @"2";
}
+ (NSString*)USER
{
    return @"3";
}
+ (NSString*)CATEGORY_USER
{
    return @"4";
}
@end

@implementation KalturaBulkUploadOrderBy
@end

@implementation KalturaBulkUploadResultObjectType
+ (NSString*)ENTRY
{
    return @"1";
}
+ (NSString*)CATEGORY
{
    return @"2";
}
+ (NSString*)USER
{
    return @"3";
}
+ (NSString*)CATEGORY_USER
{
    return @"4";
}
@end

@implementation KalturaBulkUploadResultStatus
+ (NSString*)ERROR
{
    return @"1";
}
+ (NSString*)OK
{
    return @"2";
}
+ (NSString*)IN_PROGRESS
{
    return @"3";
}
@end

@implementation KalturaBulkUploadType
+ (NSString*)CSV
{
    return @"bulkUploadCsv.CSV";
}
+ (NSString*)XML
{
    return @"bulkUploadXml.XML";
}
+ (NSString*)DROP_FOLDER_XML
{
    return @"dropFolderXmlBulkUpload.DROP_FOLDER_XML";
}
@end

@implementation KalturaCategoryEntryOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
@end

@implementation KalturaCategoryIdentifierField
+ (NSString*)FULL_NAME
{
    return @"fullName";
}
+ (NSString*)ID
{
    return @"id";
}
+ (NSString*)REFERENCE_ID
{
    return @"referenceId";
}
@end

@implementation KalturaCategoryOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)DEPTH_ASC
{
    return @"+depth";
}
+ (NSString*)DIRECT_ENTRIES_COUNT_ASC
{
    return @"+directEntriesCount";
}
+ (NSString*)DIRECT_SUB_CATEGORIES_COUNT_ASC
{
    return @"+directSubCategoriesCount";
}
+ (NSString*)ENTRIES_COUNT_ASC
{
    return @"+entriesCount";
}
+ (NSString*)FULL_NAME_ASC
{
    return @"+fullName";
}
+ (NSString*)MEMBERS_COUNT_ASC
{
    return @"+membersCount";
}
+ (NSString*)NAME_ASC
{
    return @"+name";
}
+ (NSString*)PARTNER_SORT_VALUE_ASC
{
    return @"+partnerSortValue";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)DEPTH_DESC
{
    return @"-depth";
}
+ (NSString*)DIRECT_ENTRIES_COUNT_DESC
{
    return @"-directEntriesCount";
}
+ (NSString*)DIRECT_SUB_CATEGORIES_COUNT_DESC
{
    return @"-directSubCategoriesCount";
}
+ (NSString*)ENTRIES_COUNT_DESC
{
    return @"-entriesCount";
}
+ (NSString*)FULL_NAME_DESC
{
    return @"-fullName";
}
+ (NSString*)MEMBERS_COUNT_DESC
{
    return @"-membersCount";
}
+ (NSString*)NAME_DESC
{
    return @"-name";
}
+ (NSString*)PARTNER_SORT_VALUE_DESC
{
    return @"-partnerSortValue";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
@end

@implementation KalturaCategoryUserOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
@end

@implementation KalturaConditionType
+ (NSString*)METADATA_FIELD_COMPARE
{
    return @"metadata.FieldCompare";
}
+ (NSString*)METADATA_FIELD_MATCH
{
    return @"metadata.FieldMatch";
}
+ (NSString*)AUTHENTICATED
{
    return @"1";
}
+ (NSString*)COUNTRY
{
    return @"2";
}
+ (NSString*)IP_ADDRESS
{
    return @"3";
}
+ (NSString*)SITE
{
    return @"4";
}
+ (NSString*)USER_AGENT
{
    return @"5";
}
+ (NSString*)FIELD_MATCH
{
    return @"6";
}
+ (NSString*)FIELD_COMPARE
{
    return @"7";
}
+ (NSString*)ASSET_PROPERTIES_COMPARE
{
    return @"8";
}
@end

@implementation KalturaContainerFormat
+ (NSString*)_3GP
{
    return @"3gp";
}
+ (NSString*)APPLEHTTP
{
    return @"applehttp";
}
+ (NSString*)AVI
{
    return @"avi";
}
+ (NSString*)BMP
{
    return @"bmp";
}
+ (NSString*)COPY
{
    return @"copy";
}
+ (NSString*)FLV
{
    return @"flv";
}
+ (NSString*)ISMV
{
    return @"ismv";
}
+ (NSString*)JPG
{
    return @"jpg";
}
+ (NSString*)MKV
{
    return @"mkv";
}
+ (NSString*)MOV
{
    return @"mov";
}
+ (NSString*)MP3
{
    return @"mp3";
}
+ (NSString*)MP4
{
    return @"mp4";
}
+ (NSString*)MPEG
{
    return @"mpeg";
}
+ (NSString*)MPEGTS
{
    return @"mpegts";
}
+ (NSString*)OGG
{
    return @"ogg";
}
+ (NSString*)OGV
{
    return @"ogv";
}
+ (NSString*)PDF
{
    return @"pdf";
}
+ (NSString*)PNG
{
    return @"png";
}
+ (NSString*)SWF
{
    return @"swf";
}
+ (NSString*)WAV
{
    return @"wav";
}
+ (NSString*)WEBM
{
    return @"webm";
}
+ (NSString*)WMA
{
    return @"wma";
}
+ (NSString*)WMV
{
    return @"wmv";
}
+ (NSString*)WVM
{
    return @"wvm";
}
@end

@implementation KalturaControlPanelCommandOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
@end

@implementation KalturaConversionProfileAssetParamsOrderBy
@end

@implementation KalturaConversionProfileOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
@end

@implementation KalturaConversionProfileStatus
+ (NSString*)DISABLED
{
    return @"1";
}
+ (NSString*)ENABLED
{
    return @"2";
}
+ (NSString*)DELETED
{
    return @"3";
}
@end

@implementation KalturaDataEntryOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)END_DATE_ASC
{
    return @"+endDate";
}
+ (NSString*)MODERATION_COUNT_ASC
{
    return @"+moderationCount";
}
+ (NSString*)NAME_ASC
{
    return @"+name";
}
+ (NSString*)PARTNER_SORT_VALUE_ASC
{
    return @"+partnerSortValue";
}
+ (NSString*)RANK_ASC
{
    return @"+rank";
}
+ (NSString*)RECENT_ASC
{
    return @"+recent";
}
+ (NSString*)START_DATE_ASC
{
    return @"+startDate";
}
+ (NSString*)TOTAL_RANK_ASC
{
    return @"+totalRank";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)WEIGHT_ASC
{
    return @"+weight";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)END_DATE_DESC
{
    return @"-endDate";
}
+ (NSString*)MODERATION_COUNT_DESC
{
    return @"-moderationCount";
}
+ (NSString*)NAME_DESC
{
    return @"-name";
}
+ (NSString*)PARTNER_SORT_VALUE_DESC
{
    return @"-partnerSortValue";
}
+ (NSString*)RANK_DESC
{
    return @"-rank";
}
+ (NSString*)RECENT_DESC
{
    return @"-recent";
}
+ (NSString*)START_DATE_DESC
{
    return @"-startDate";
}
+ (NSString*)TOTAL_RANK_DESC
{
    return @"-totalRank";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
+ (NSString*)WEIGHT_DESC
{
    return @"-weight";
}
@end

@implementation KalturaDurationType
+ (NSString*)LONG
{
    return @"long";
}
+ (NSString*)MEDIUM
{
    return @"medium";
}
+ (NSString*)NOT_AVAILABLE
{
    return @"notavailable";
}
+ (NSString*)SHORT
{
    return @"short";
}
@end

@implementation KalturaDynamicEnum
@end

@implementation KalturaEntryIdentifierField
+ (NSString*)ID
{
    return @"id";
}
+ (NSString*)REFERENCE_ID
{
    return @"referenceId";
}
@end

@implementation KalturaEntryReplacementStatus
+ (NSString*)NONE
{
    return @"0";
}
+ (NSString*)APPROVED_BUT_NOT_READY
{
    return @"1";
}
+ (NSString*)READY_BUT_NOT_APPROVED
{
    return @"2";
}
+ (NSString*)NOT_READY_AND_NOT_APPROVED
{
    return @"3";
}
@end

@implementation KalturaEntryStatus
+ (NSString*)ERROR_IMPORTING
{
    return @"-2";
}
+ (NSString*)ERROR_CONVERTING
{
    return @"-1";
}
+ (NSString*)SCAN_FAILURE
{
    return @"virusScan.ScanFailure";
}
+ (NSString*)IMPORT
{
    return @"0";
}
+ (NSString*)INFECTED
{
    return @"virusScan.Infected";
}
+ (NSString*)PRECONVERT
{
    return @"1";
}
+ (NSString*)READY
{
    return @"2";
}
+ (NSString*)DELETED
{
    return @"3";
}
+ (NSString*)PENDING
{
    return @"4";
}
+ (NSString*)MODERATE
{
    return @"5";
}
+ (NSString*)BLOCKED
{
    return @"6";
}
+ (NSString*)NO_CONTENT
{
    return @"7";
}
@end

@implementation KalturaEntryType
+ (NSString*)AUTOMATIC
{
    return @"-1";
}
+ (NSString*)EXTERNAL_MEDIA
{
    return @"externalMedia.externalMedia";
}
+ (NSString*)MEDIA_CLIP
{
    return @"1";
}
+ (NSString*)MIX
{
    return @"2";
}
+ (NSString*)PLAYLIST
{
    return @"5";
}
+ (NSString*)DATA
{
    return @"6";
}
+ (NSString*)LIVE_STREAM
{
    return @"7";
}
+ (NSString*)DOCUMENT
{
    return @"10";
}
@end

@implementation KalturaFileSyncObjectType
+ (NSString*)DISTRIBUTION_PROFILE
{
    return @"contentDistribution.DistributionProfile";
}
+ (NSString*)ENTRY_DISTRIBUTION
{
    return @"contentDistribution.EntryDistribution";
}
+ (NSString*)GENERIC_DISTRIBUTION_ACTION
{
    return @"contentDistribution.GenericDistributionAction";
}
+ (NSString*)EMAIL_NOTIFICATION_TEMPLATE
{
    return @"emailNotification.EmailNotificationTemplate";
}
+ (NSString*)ENTRY
{
    return @"1";
}
+ (NSString*)UICONF
{
    return @"2";
}
+ (NSString*)BATCHJOB
{
    return @"3";
}
+ (NSString*)FLAVOR_ASSET
{
    return @"4";
}
+ (NSString*)ASSET
{
    return @"4";
}
+ (NSString*)METADATA
{
    return @"5";
}
+ (NSString*)METADATA_PROFILE
{
    return @"6";
}
+ (NSString*)SYNDICATION_FEED
{
    return @"7";
}
+ (NSString*)CONVERSION_PROFILE
{
    return @"8";
}
@end

@implementation KalturaFlavorAssetOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)DELETED_AT_ASC
{
    return @"+deletedAt";
}
+ (NSString*)SIZE_ASC
{
    return @"+size";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)DELETED_AT_DESC
{
    return @"-deletedAt";
}
+ (NSString*)SIZE_DESC
{
    return @"-size";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
@end

@implementation KalturaFlavorParamsOrderBy
@end

@implementation KalturaFlavorParamsOutputOrderBy
@end

@implementation KalturaGenericSyndicationFeedOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)NAME_ASC
{
    return @"+name";
}
+ (NSString*)PLAYLIST_ID_ASC
{
    return @"+playlistId";
}
+ (NSString*)TYPE_ASC
{
    return @"+type";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)NAME_DESC
{
    return @"-name";
}
+ (NSString*)PLAYLIST_ID_DESC
{
    return @"-playlistId";
}
+ (NSString*)TYPE_DESC
{
    return @"-type";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
@end

@implementation KalturaGenericXsltSyndicationFeedOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)NAME_ASC
{
    return @"+name";
}
+ (NSString*)PLAYLIST_ID_ASC
{
    return @"+playlistId";
}
+ (NSString*)TYPE_ASC
{
    return @"+type";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)NAME_DESC
{
    return @"-name";
}
+ (NSString*)PLAYLIST_ID_DESC
{
    return @"-playlistId";
}
+ (NSString*)TYPE_DESC
{
    return @"-type";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
@end

@implementation KalturaGeoCoderType
+ (NSString*)KALTURA
{
    return @"1";
}
@end

@implementation KalturaGoogleSyndicationFeedAdultValues
+ (NSString*)NO_
{
    return @"No";
}
+ (NSString*)YES_
{
    return @"Yes";
}
@end

@implementation KalturaGoogleVideoSyndicationFeedOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)NAME_ASC
{
    return @"+name";
}
+ (NSString*)PLAYLIST_ID_ASC
{
    return @"+playlistId";
}
+ (NSString*)TYPE_ASC
{
    return @"+type";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)NAME_DESC
{
    return @"-name";
}
+ (NSString*)PLAYLIST_ID_DESC
{
    return @"-playlistId";
}
+ (NSString*)TYPE_DESC
{
    return @"-type";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
@end

@implementation KalturaITunesSyndicationFeedAdultValues
+ (NSString*)CLEAN
{
    return @"clean";
}
+ (NSString*)NO_
{
    return @"no";
}
+ (NSString*)YES_
{
    return @"yes";
}
@end

@implementation KalturaITunesSyndicationFeedCategories
+ (NSString*)ARTS
{
    return @"Arts";
}
+ (NSString*)ARTS_DESIGN
{
    return @"Arts/Design";
}
+ (NSString*)ARTS_FASHION_BEAUTY
{
    return @"Arts/Fashion &amp; Beauty";
}
+ (NSString*)ARTS_FOOD
{
    return @"Arts/Food";
}
+ (NSString*)ARTS_LITERATURE
{
    return @"Arts/Literature";
}
+ (NSString*)ARTS_PERFORMING_ARTS
{
    return @"Arts/Performing Arts";
}
+ (NSString*)ARTS_VISUAL_ARTS
{
    return @"Arts/Visual Arts";
}
+ (NSString*)BUSINESS
{
    return @"Business";
}
+ (NSString*)BUSINESS_BUSINESS_NEWS
{
    return @"Business/Business News";
}
+ (NSString*)BUSINESS_CAREERS
{
    return @"Business/Careers";
}
+ (NSString*)BUSINESS_INVESTING
{
    return @"Business/Investing";
}
+ (NSString*)BUSINESS_MANAGEMENT_MARKETING
{
    return @"Business/Management &amp; Marketing";
}
+ (NSString*)BUSINESS_SHOPPING
{
    return @"Business/Shopping";
}
+ (NSString*)COMEDY
{
    return @"Comedy";
}
+ (NSString*)EDUCATION
{
    return @"Education";
}
+ (NSString*)EDUCATION_TECHNOLOGY
{
    return @"Education/Education Technology";
}
+ (NSString*)EDUCATION_HIGHER_EDUCATION
{
    return @"Education/Higher Education";
}
+ (NSString*)EDUCATION_K_12
{
    return @"Education/K-12";
}
+ (NSString*)EDUCATION_LANGUAGE_COURSES
{
    return @"Education/Language Courses";
}
+ (NSString*)EDUCATION_TRAINING
{
    return @"Education/Training";
}
+ (NSString*)GAMES_HOBBIES
{
    return @"Games &amp; Hobbies";
}
+ (NSString*)GAMES_HOBBIES_AUTOMOTIVE
{
    return @"Games &amp; Hobbies/Automotive";
}
+ (NSString*)GAMES_HOBBIES_AVIATION
{
    return @"Games &amp; Hobbies/Aviation";
}
+ (NSString*)GAMES_HOBBIES_HOBBIES
{
    return @"Games &amp; Hobbies/Hobbies";
}
+ (NSString*)GAMES_HOBBIES_OTHER_GAMES
{
    return @"Games &amp; Hobbies/Other Games";
}
+ (NSString*)GAMES_HOBBIES_VIDEO_GAMES
{
    return @"Games &amp; Hobbies/Video Games";
}
+ (NSString*)GOVERNMENT_ORGANIZATIONS
{
    return @"Government &amp; Organizations";
}
+ (NSString*)GOVERNMENT_ORGANIZATIONS_LOCAL
{
    return @"Government &amp; Organizations/Local";
}
+ (NSString*)GOVERNMENT_ORGANIZATIONS_NATIONAL
{
    return @"Government &amp; Organizations/National";
}
+ (NSString*)GOVERNMENT_ORGANIZATIONS_NON_PROFIT
{
    return @"Government &amp; Organizations/Non-Profit";
}
+ (NSString*)GOVERNMENT_ORGANIZATIONS_REGIONAL
{
    return @"Government &amp; Organizations/Regional";
}
+ (NSString*)HEALTH
{
    return @"Health";
}
+ (NSString*)HEALTH_ALTERNATIVE_HEALTH
{
    return @"Health/Alternative Health";
}
+ (NSString*)HEALTH_FITNESS_NUTRITION
{
    return @"Health/Fitness &amp; Nutrition";
}
+ (NSString*)HEALTH_SELF_HELP
{
    return @"Health/Self-Help";
}
+ (NSString*)HEALTH_SEXUALITY
{
    return @"Health/Sexuality";
}
+ (NSString*)KIDS_FAMILY
{
    return @"Kids &amp; Family";
}
+ (NSString*)MUSIC
{
    return @"Music";
}
+ (NSString*)NEWS_POLITICS
{
    return @"News &amp; Politics";
}
+ (NSString*)RELIGION_SPIRITUALITY
{
    return @"Religion &amp; Spirituality";
}
+ (NSString*)RELIGION_SPIRITUALITY_BUDDHISM
{
    return @"Religion &amp; Spirituality/Buddhism";
}
+ (NSString*)RELIGION_SPIRITUALITY_CHRISTIANITY
{
    return @"Religion &amp; Spirituality/Christianity";
}
+ (NSString*)RELIGION_SPIRITUALITY_HINDUISM
{
    return @"Religion &amp; Spirituality/Hinduism";
}
+ (NSString*)RELIGION_SPIRITUALITY_ISLAM
{
    return @"Religion &amp; Spirituality/Islam";
}
+ (NSString*)RELIGION_SPIRITUALITY_JUDAISM
{
    return @"Religion &amp; Spirituality/Judaism";
}
+ (NSString*)RELIGION_SPIRITUALITY_OTHER
{
    return @"Religion &amp; Spirituality/Other";
}
+ (NSString*)RELIGION_SPIRITUALITY_SPIRITUALITY
{
    return @"Religion &amp; Spirituality/Spirituality";
}
+ (NSString*)SCIENCE_MEDICINE
{
    return @"Science &amp; Medicine";
}
+ (NSString*)SCIENCE_MEDICINE_MEDICINE
{
    return @"Science &amp; Medicine/Medicine";
}
+ (NSString*)SCIENCE_MEDICINE_NATURAL_SCIENCES
{
    return @"Science &amp; Medicine/Natural Sciences";
}
+ (NSString*)SCIENCE_MEDICINE_SOCIAL_SCIENCES
{
    return @"Science &amp; Medicine/Social Sciences";
}
+ (NSString*)SOCIETY_CULTURE
{
    return @"Society &amp; Culture";
}
+ (NSString*)SOCIETY_CULTURE_HISTORY
{
    return @"Society &amp; Culture/History";
}
+ (NSString*)SOCIETY_CULTURE_PERSONAL_JOURNALS
{
    return @"Society &amp; Culture/Personal Journals";
}
+ (NSString*)SOCIETY_CULTURE_PHILOSOPHY
{
    return @"Society &amp; Culture/Philosophy";
}
+ (NSString*)SOCIETY_CULTURE_PLACES_TRAVEL
{
    return @"Society &amp; Culture/Places &amp; Travel";
}
+ (NSString*)SPORTS_RECREATION
{
    return @"Sports &amp; Recreation";
}
+ (NSString*)SPORTS_RECREATION_AMATEUR
{
    return @"Sports &amp; Recreation/Amateur";
}
+ (NSString*)SPORTS_RECREATION_COLLEGE_HIGH_SCHOOL
{
    return @"Sports &amp; Recreation/College &amp; High School";
}
+ (NSString*)SPORTS_RECREATION_OUTDOOR
{
    return @"Sports &amp; Recreation/Outdoor";
}
+ (NSString*)SPORTS_RECREATION_PROFESSIONAL
{
    return @"Sports &amp; Recreation/Professional";
}
+ (NSString*)TV_FILM
{
    return @"TV &amp; Film";
}
+ (NSString*)TECHNOLOGY
{
    return @"Technology";
}
+ (NSString*)TECHNOLOGY_GADGETS
{
    return @"Technology/Gadgets";
}
+ (NSString*)TECHNOLOGY_PODCASTING
{
    return @"Technology/Podcasting";
}
+ (NSString*)TECHNOLOGY_SOFTWARE_HOW_TO
{
    return @"Technology/Software How-To";
}
+ (NSString*)TECHNOLOGY_TECH_NEWS
{
    return @"Technology/Tech News";
}
@end

@implementation KalturaITunesSyndicationFeedOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)NAME_ASC
{
    return @"+name";
}
+ (NSString*)PLAYLIST_ID_ASC
{
    return @"+playlistId";
}
+ (NSString*)TYPE_ASC
{
    return @"+type";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)NAME_DESC
{
    return @"-name";
}
+ (NSString*)PLAYLIST_ID_DESC
{
    return @"-playlistId";
}
+ (NSString*)TYPE_DESC
{
    return @"-type";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
@end

@implementation KalturaLanguage
+ (NSString*)AB
{
    return @"Abkhazian";
}
+ (NSString*)AA
{
    return @"Afar";
}
+ (NSString*)AF
{
    return @"Afrikaans";
}
+ (NSString*)SQ
{
    return @"Albanian";
}
+ (NSString*)AM
{
    return @"Amharic";
}
+ (NSString*)AR
{
    return @"Arabic";
}
+ (NSString*)HY
{
    return @"Armenian";
}
+ (NSString*)AS_
{
    return @"Assamese";
}
+ (NSString*)AY
{
    return @"Aymara";
}
+ (NSString*)AZ
{
    return @"Azerbaijani";
}
+ (NSString*)BA
{
    return @"Bashkir";
}
+ (NSString*)EU
{
    return @"Basque";
}
+ (NSString*)BN
{
    return @"Bengali (Bangla)";
}
+ (NSString*)DZ
{
    return @"Bhutani";
}
+ (NSString*)BH
{
    return @"Bihari";
}
+ (NSString*)BI
{
    return @"Bislama";
}
+ (NSString*)BR
{
    return @"Breton";
}
+ (NSString*)BG
{
    return @"Bulgarian";
}
+ (NSString*)MY
{
    return @"Burmese";
}
+ (NSString*)BE
{
    return @"Byelorussian (Belarusian)";
}
+ (NSString*)KM
{
    return @"Cambodian";
}
+ (NSString*)CA
{
    return @"Catalan";
}
+ (NSString*)ZH
{
    return @"Chinese";
}
+ (NSString*)CO
{
    return @"Corsican";
}
+ (NSString*)HR
{
    return @"Croatian";
}
+ (NSString*)CS
{
    return @"Czech";
}
+ (NSString*)DA
{
    return @"Danish";
}
+ (NSString*)NL
{
    return @"Dutch";
}
+ (NSString*)EN
{
    return @"English";
}
+ (NSString*)EO
{
    return @"Esperanto";
}
+ (NSString*)ET
{
    return @"Estonian";
}
+ (NSString*)FO
{
    return @"Faeroese";
}
+ (NSString*)FA
{
    return @"Farsi";
}
+ (NSString*)FJ
{
    return @"Fiji";
}
+ (NSString*)FI
{
    return @"Finnish";
}
+ (NSString*)FR
{
    return @"French";
}
+ (NSString*)FY
{
    return @"Frisian";
}
+ (NSString*)GV
{
    return @"Gaelic (Manx)";
}
+ (NSString*)GD
{
    return @"Gaelic (Scottish)";
}
+ (NSString*)GL
{
    return @"Galician";
}
+ (NSString*)KA
{
    return @"Georgian";
}
+ (NSString*)DE
{
    return @"German";
}
+ (NSString*)EL
{
    return @"Greek";
}
+ (NSString*)KL
{
    return @"Greenlandic";
}
+ (NSString*)GN
{
    return @"Guarani";
}
+ (NSString*)GU
{
    return @"Gujarati";
}
+ (NSString*)HA
{
    return @"Hausa";
}
+ (NSString*)IW
{
    return @"Hebrew";
}
+ (NSString*)HE
{
    return @"Hebrew";
}
+ (NSString*)HI
{
    return @"Hindi";
}
+ (NSString*)HU
{
    return @"Hungarian";
}
+ (NSString*)IS
{
    return @"Icelandic";
}
+ (NSString*)IN
{
    return @"Indonesian";
}
+ (NSString*)ID
{
    return @"Indonesian";
}
+ (NSString*)IA
{
    return @"Interlingua";
}
+ (NSString*)IE
{
    return @"Interlingue";
}
+ (NSString*)IU
{
    return @"Inuktitut";
}
+ (NSString*)IK
{
    return @"Inupiak";
}
+ (NSString*)GA
{
    return @"Irish";
}
+ (NSString*)IT
{
    return @"Italian";
}
+ (NSString*)JA
{
    return @"Japanese";
}
+ (NSString*)JV
{
    return @"Javanese";
}
+ (NSString*)KN
{
    return @"Kannada";
}
+ (NSString*)KS
{
    return @"Kashmiri";
}
+ (NSString*)KK
{
    return @"Kazakh";
}
+ (NSString*)RW
{
    return @"Kinyarwanda (Ruanda)";
}
+ (NSString*)KY
{
    return @"Kirghiz";
}
+ (NSString*)RN
{
    return @"Kirundi (Rundi)";
}
+ (NSString*)KO
{
    return @"Korean";
}
+ (NSString*)KU
{
    return @"Kurdish";
}
+ (NSString*)LO
{
    return @"Laothian";
}
+ (NSString*)LA
{
    return @"Latin";
}
+ (NSString*)LV
{
    return @"Latvian (Lettish)";
}
+ (NSString*)LI
{
    return @"Limburgish ( Limburger)";
}
+ (NSString*)LN
{
    return @"Lingala";
}
+ (NSString*)LT
{
    return @"Lithuanian";
}
+ (NSString*)MK
{
    return @"Macedonian";
}
+ (NSString*)MG
{
    return @"Malagasy";
}
+ (NSString*)MS
{
    return @"Malay";
}
+ (NSString*)ML
{
    return @"Malayalam";
}
+ (NSString*)MT
{
    return @"Maltese";
}
+ (NSString*)MI
{
    return @"Maori";
}
+ (NSString*)MR
{
    return @"Marathi";
}
+ (NSString*)MO
{
    return @"Moldavian";
}
+ (NSString*)MN
{
    return @"Mongolian";
}
+ (NSString*)NA
{
    return @"Nauru";
}
+ (NSString*)NE
{
    return @"Nepali";
}
+ (NSString*)NO_
{
    return @"Norwegian";
}
+ (NSString*)OC
{
    return @"Occitan";
}
+ (NSString*)OR_
{
    return @"Oriya";
}
+ (NSString*)OM
{
    return @"Oromo (Afan, Galla)";
}
+ (NSString*)PS
{
    return @"Pashto (Pushto)";
}
+ (NSString*)PL
{
    return @"Polish";
}
+ (NSString*)PT
{
    return @"Portuguese";
}
+ (NSString*)PA
{
    return @"Punjabi";
}
+ (NSString*)QU
{
    return @"Quechua";
}
+ (NSString*)RM
{
    return @"Rhaeto-Romance";
}
+ (NSString*)RO
{
    return @"Romanian";
}
+ (NSString*)RU
{
    return @"Russian";
}
+ (NSString*)SM
{
    return @"Samoan";
}
+ (NSString*)SG
{
    return @"Sangro";
}
+ (NSString*)SA
{
    return @"Sanskrit";
}
+ (NSString*)SR
{
    return @"Serbian";
}
+ (NSString*)SH
{
    return @"Serbo-Croatian";
}
+ (NSString*)ST
{
    return @"Sesotho";
}
+ (NSString*)TN
{
    return @"Setswana";
}
+ (NSString*)SN
{
    return @"Shona";
}
+ (NSString*)SD
{
    return @"Sindhi";
}
+ (NSString*)SI
{
    return @"Sinhalese";
}
+ (NSString*)SS
{
    return @"Siswati";
}
+ (NSString*)SK
{
    return @"Slovak";
}
+ (NSString*)SL
{
    return @"Slovenian";
}
+ (NSString*)SO
{
    return @"Somali";
}
+ (NSString*)ES
{
    return @"Spanish";
}
+ (NSString*)SU
{
    return @"Sundanese";
}
+ (NSString*)SW
{
    return @"Swahili (Kiswahili)";
}
+ (NSString*)SV
{
    return @"Swedish";
}
+ (NSString*)TL
{
    return @"Tagalog";
}
+ (NSString*)TG
{
    return @"Tajik";
}
+ (NSString*)TA
{
    return @"Tamil";
}
+ (NSString*)TT
{
    return @"Tatar";
}
+ (NSString*)TE
{
    return @"Telugu";
}
+ (NSString*)TH
{
    return @"Thai";
}
+ (NSString*)BO
{
    return @"Tibetan";
}
+ (NSString*)TI
{
    return @"Tigrinya";
}
+ (NSString*)TO
{
    return @"Tonga";
}
+ (NSString*)TS
{
    return @"Tsonga";
}
+ (NSString*)TR
{
    return @"Turkish";
}
+ (NSString*)TK
{
    return @"Turkmen";
}
+ (NSString*)TW
{
    return @"Twi";
}
+ (NSString*)UG
{
    return @"Uighur";
}
+ (NSString*)UK
{
    return @"Ukrainian";
}
+ (NSString*)UR
{
    return @"Urdu";
}
+ (NSString*)UZ
{
    return @"Uzbek";
}
+ (NSString*)VI
{
    return @"Vietnamese";
}
+ (NSString*)VO
{
    return @"Volapuk";
}
+ (NSString*)CY
{
    return @"Welsh";
}
+ (NSString*)WO
{
    return @"Wolof";
}
+ (NSString*)XH
{
    return @"Xhosa";
}
+ (NSString*)YI
{
    return @"Yiddish";
}
+ (NSString*)JI
{
    return @"Yiddish";
}
+ (NSString*)YO
{
    return @"Yoruba";
}
+ (NSString*)ZU
{
    return @"Zulu";
}
@end

@implementation KalturaLanguageCode
+ (NSString*)AA
{
    return @"aa";
}
+ (NSString*)AB
{
    return @"ab";
}
+ (NSString*)AF
{
    return @"af";
}
+ (NSString*)AM
{
    return @"am";
}
+ (NSString*)AR
{
    return @"ar";
}
+ (NSString*)AS_
{
    return @"as";
}
+ (NSString*)AY
{
    return @"ay";
}
+ (NSString*)AZ
{
    return @"az";
}
+ (NSString*)BA
{
    return @"ba";
}
+ (NSString*)BE
{
    return @"be";
}
+ (NSString*)BG
{
    return @"bg";
}
+ (NSString*)BH
{
    return @"bh";
}
+ (NSString*)BI
{
    return @"bi";
}
+ (NSString*)BN
{
    return @"bn";
}
+ (NSString*)BO
{
    return @"bo";
}
+ (NSString*)BR
{
    return @"br";
}
+ (NSString*)CA
{
    return @"ca";
}
+ (NSString*)CO
{
    return @"co";
}
+ (NSString*)CS
{
    return @"cs";
}
+ (NSString*)CY
{
    return @"cy";
}
+ (NSString*)DA
{
    return @"da";
}
+ (NSString*)DE
{
    return @"de";
}
+ (NSString*)DZ
{
    return @"dz";
}
+ (NSString*)EL
{
    return @"el";
}
+ (NSString*)EN
{
    return @"en";
}
+ (NSString*)EO
{
    return @"eo";
}
+ (NSString*)ES
{
    return @"es";
}
+ (NSString*)ET
{
    return @"et";
}
+ (NSString*)EU
{
    return @"eu";
}
+ (NSString*)FA
{
    return @"fa";
}
+ (NSString*)FI
{
    return @"fi";
}
+ (NSString*)FJ
{
    return @"fj";
}
+ (NSString*)FO
{
    return @"fo";
}
+ (NSString*)FR
{
    return @"fr";
}
+ (NSString*)FY
{
    return @"fy";
}
+ (NSString*)GA
{
    return @"ga";
}
+ (NSString*)GD
{
    return @"gd";
}
+ (NSString*)GL
{
    return @"gl";
}
+ (NSString*)GN
{
    return @"gn";
}
+ (NSString*)GU
{
    return @"gu";
}
+ (NSString*)GV
{
    return @"gv";
}
+ (NSString*)HA
{
    return @"ha";
}
+ (NSString*)HE
{
    return @"he";
}
+ (NSString*)HI
{
    return @"hi";
}
+ (NSString*)HR
{
    return @"hr";
}
+ (NSString*)HU
{
    return @"hu";
}
+ (NSString*)HY
{
    return @"hy";
}
+ (NSString*)IA
{
    return @"ia";
}
+ (NSString*)ID
{
    return @"id";
}
+ (NSString*)IE
{
    return @"ie";
}
+ (NSString*)IK
{
    return @"ik";
}
+ (NSString*)IN
{
    return @"in";
}
+ (NSString*)IS
{
    return @"is";
}
+ (NSString*)IT
{
    return @"it";
}
+ (NSString*)IU
{
    return @"iu";
}
+ (NSString*)IW
{
    return @"iw";
}
+ (NSString*)JA
{
    return @"ja";
}
+ (NSString*)JI
{
    return @"ji";
}
+ (NSString*)JV
{
    return @"jv";
}
+ (NSString*)KA
{
    return @"ka";
}
+ (NSString*)KK
{
    return @"kk";
}
+ (NSString*)KL
{
    return @"kl";
}
+ (NSString*)KM
{
    return @"km";
}
+ (NSString*)KN
{
    return @"kn";
}
+ (NSString*)KO
{
    return @"ko";
}
+ (NSString*)KS
{
    return @"ks";
}
+ (NSString*)KU
{
    return @"ku";
}
+ (NSString*)KY
{
    return @"ky";
}
+ (NSString*)LA
{
    return @"la";
}
+ (NSString*)LI
{
    return @"li";
}
+ (NSString*)LN
{
    return @"ln";
}
+ (NSString*)LO
{
    return @"lo";
}
+ (NSString*)LT
{
    return @"lt";
}
+ (NSString*)LV
{
    return @"lv";
}
+ (NSString*)MG
{
    return @"mg";
}
+ (NSString*)MI
{
    return @"mi";
}
+ (NSString*)MK
{
    return @"mk";
}
+ (NSString*)ML
{
    return @"ml";
}
+ (NSString*)MN
{
    return @"mn";
}
+ (NSString*)MO
{
    return @"mo";
}
+ (NSString*)MR
{
    return @"mr";
}
+ (NSString*)MS
{
    return @"ms";
}
+ (NSString*)MT
{
    return @"mt";
}
+ (NSString*)MY
{
    return @"my";
}
+ (NSString*)NA
{
    return @"na";
}
+ (NSString*)NE
{
    return @"ne";
}
+ (NSString*)NL
{
    return @"nl";
}
+ (NSString*)NO_
{
    return @"no";
}
+ (NSString*)OC
{
    return @"oc";
}
+ (NSString*)OM
{
    return @"om";
}
+ (NSString*)OR_
{
    return @"or";
}
+ (NSString*)PA
{
    return @"pa";
}
+ (NSString*)PL
{
    return @"pl";
}
+ (NSString*)PS
{
    return @"ps";
}
+ (NSString*)PT
{
    return @"pt";
}
+ (NSString*)QU
{
    return @"qu";
}
+ (NSString*)RM
{
    return @"rm";
}
+ (NSString*)RN
{
    return @"rn";
}
+ (NSString*)RO
{
    return @"ro";
}
+ (NSString*)RU
{
    return @"ru";
}
+ (NSString*)RW
{
    return @"rw";
}
+ (NSString*)SA
{
    return @"sa";
}
+ (NSString*)SD
{
    return @"sd";
}
+ (NSString*)SG
{
    return @"sg";
}
+ (NSString*)SH
{
    return @"sh";
}
+ (NSString*)SI
{
    return @"si";
}
+ (NSString*)SK
{
    return @"sk";
}
+ (NSString*)SL
{
    return @"sl";
}
+ (NSString*)SM
{
    return @"sm";
}
+ (NSString*)SN
{
    return @"sn";
}
+ (NSString*)SO
{
    return @"so";
}
+ (NSString*)SQ
{
    return @"sq";
}
+ (NSString*)SR
{
    return @"sr";
}
+ (NSString*)SS
{
    return @"ss";
}
+ (NSString*)ST
{
    return @"st";
}
+ (NSString*)SU
{
    return @"su";
}
+ (NSString*)SV
{
    return @"sv";
}
+ (NSString*)SW
{
    return @"sw";
}
+ (NSString*)TA
{
    return @"ta";
}
+ (NSString*)TE
{
    return @"te";
}
+ (NSString*)TG
{
    return @"tg";
}
+ (NSString*)TH
{
    return @"th";
}
+ (NSString*)TI
{
    return @"ti";
}
+ (NSString*)TK
{
    return @"tk";
}
+ (NSString*)TL
{
    return @"tl";
}
+ (NSString*)TN
{
    return @"tn";
}
+ (NSString*)TO
{
    return @"to";
}
+ (NSString*)TR
{
    return @"tr";
}
+ (NSString*)TS
{
    return @"ts";
}
+ (NSString*)TT
{
    return @"tt";
}
+ (NSString*)TW
{
    return @"tw";
}
+ (NSString*)UG
{
    return @"ug";
}
+ (NSString*)UK
{
    return @"uk";
}
+ (NSString*)UR
{
    return @"ur";
}
+ (NSString*)UZ
{
    return @"uz";
}
+ (NSString*)VI
{
    return @"vi";
}
+ (NSString*)VO
{
    return @"vo";
}
+ (NSString*)WO
{
    return @"wo";
}
+ (NSString*)XH
{
    return @"xh";
}
+ (NSString*)YI
{
    return @"yi";
}
+ (NSString*)YO
{
    return @"yo";
}
+ (NSString*)ZH
{
    return @"zh";
}
+ (NSString*)ZU
{
    return @"zu";
}
@end

@implementation KalturaLiveStreamAdminEntryOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)DURATION_ASC
{
    return @"+duration";
}
+ (NSString*)END_DATE_ASC
{
    return @"+endDate";
}
+ (NSString*)MEDIA_TYPE_ASC
{
    return @"+mediaType";
}
+ (NSString*)MODERATION_COUNT_ASC
{
    return @"+moderationCount";
}
+ (NSString*)MS_DURATION_ASC
{
    return @"+msDuration";
}
+ (NSString*)NAME_ASC
{
    return @"+name";
}
+ (NSString*)PARTNER_SORT_VALUE_ASC
{
    return @"+partnerSortValue";
}
+ (NSString*)PLAYS_ASC
{
    return @"+plays";
}
+ (NSString*)RANK_ASC
{
    return @"+rank";
}
+ (NSString*)RECENT_ASC
{
    return @"+recent";
}
+ (NSString*)START_DATE_ASC
{
    return @"+startDate";
}
+ (NSString*)TOTAL_RANK_ASC
{
    return @"+totalRank";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)VIEWS_ASC
{
    return @"+views";
}
+ (NSString*)WEIGHT_ASC
{
    return @"+weight";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)DURATION_DESC
{
    return @"-duration";
}
+ (NSString*)END_DATE_DESC
{
    return @"-endDate";
}
+ (NSString*)MEDIA_TYPE_DESC
{
    return @"-mediaType";
}
+ (NSString*)MODERATION_COUNT_DESC
{
    return @"-moderationCount";
}
+ (NSString*)MS_DURATION_DESC
{
    return @"-msDuration";
}
+ (NSString*)NAME_DESC
{
    return @"-name";
}
+ (NSString*)PARTNER_SORT_VALUE_DESC
{
    return @"-partnerSortValue";
}
+ (NSString*)PLAYS_DESC
{
    return @"-plays";
}
+ (NSString*)RANK_DESC
{
    return @"-rank";
}
+ (NSString*)RECENT_DESC
{
    return @"-recent";
}
+ (NSString*)START_DATE_DESC
{
    return @"-startDate";
}
+ (NSString*)TOTAL_RANK_DESC
{
    return @"-totalRank";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
+ (NSString*)VIEWS_DESC
{
    return @"-views";
}
+ (NSString*)WEIGHT_DESC
{
    return @"-weight";
}
@end

@implementation KalturaLiveStreamEntryOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)DURATION_ASC
{
    return @"+duration";
}
+ (NSString*)END_DATE_ASC
{
    return @"+endDate";
}
+ (NSString*)MEDIA_TYPE_ASC
{
    return @"+mediaType";
}
+ (NSString*)MODERATION_COUNT_ASC
{
    return @"+moderationCount";
}
+ (NSString*)MS_DURATION_ASC
{
    return @"+msDuration";
}
+ (NSString*)NAME_ASC
{
    return @"+name";
}
+ (NSString*)PARTNER_SORT_VALUE_ASC
{
    return @"+partnerSortValue";
}
+ (NSString*)PLAYS_ASC
{
    return @"+plays";
}
+ (NSString*)RANK_ASC
{
    return @"+rank";
}
+ (NSString*)RECENT_ASC
{
    return @"+recent";
}
+ (NSString*)START_DATE_ASC
{
    return @"+startDate";
}
+ (NSString*)TOTAL_RANK_ASC
{
    return @"+totalRank";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)VIEWS_ASC
{
    return @"+views";
}
+ (NSString*)WEIGHT_ASC
{
    return @"+weight";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)DURATION_DESC
{
    return @"-duration";
}
+ (NSString*)END_DATE_DESC
{
    return @"-endDate";
}
+ (NSString*)MEDIA_TYPE_DESC
{
    return @"-mediaType";
}
+ (NSString*)MODERATION_COUNT_DESC
{
    return @"-moderationCount";
}
+ (NSString*)MS_DURATION_DESC
{
    return @"-msDuration";
}
+ (NSString*)NAME_DESC
{
    return @"-name";
}
+ (NSString*)PARTNER_SORT_VALUE_DESC
{
    return @"-partnerSortValue";
}
+ (NSString*)PLAYS_DESC
{
    return @"-plays";
}
+ (NSString*)RANK_DESC
{
    return @"-rank";
}
+ (NSString*)RECENT_DESC
{
    return @"-recent";
}
+ (NSString*)START_DATE_DESC
{
    return @"-startDate";
}
+ (NSString*)TOTAL_RANK_DESC
{
    return @"-totalRank";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
+ (NSString*)VIEWS_DESC
{
    return @"-views";
}
+ (NSString*)WEIGHT_DESC
{
    return @"-weight";
}
@end

@implementation KalturaMailType
+ (NSString*)MAIL_TYPE_KALTURA_NEWSLETTER
{
    return @"10";
}
+ (NSString*)MAIL_TYPE_ADDED_TO_FAVORITES
{
    return @"11";
}
+ (NSString*)MAIL_TYPE_ADDED_TO_CLIP_FAVORITES
{
    return @"12";
}
+ (NSString*)MAIL_TYPE_NEW_COMMENT_IN_PROFILE
{
    return @"13";
}
+ (NSString*)MAIL_TYPE_CLIP_ADDED_YOUR_KALTURA
{
    return @"20";
}
+ (NSString*)MAIL_TYPE_VIDEO_ADDED
{
    return @"21";
}
+ (NSString*)MAIL_TYPE_ROUGHCUT_CREATED
{
    return @"22";
}
+ (NSString*)MAIL_TYPE_ADDED_KALTURA_TO_YOUR_FAVORITES
{
    return @"23";
}
+ (NSString*)MAIL_TYPE_NEW_COMMENT_IN_KALTURA
{
    return @"24";
}
+ (NSString*)MAIL_TYPE_CLIP_ADDED
{
    return @"30";
}
+ (NSString*)MAIL_TYPE_VIDEO_CREATED
{
    return @"31";
}
+ (NSString*)MAIL_TYPE_ADDED_KALTURA_TO_HIS_FAVORITES
{
    return @"32";
}
+ (NSString*)MAIL_TYPE_NEW_COMMENT_IN_KALTURA_YOU_CONTRIBUTED
{
    return @"33";
}
+ (NSString*)MAIL_TYPE_CLIP_CONTRIBUTED
{
    return @"40";
}
+ (NSString*)MAIL_TYPE_ROUGHCUT_CREATED_SUBSCRIBED
{
    return @"41";
}
+ (NSString*)MAIL_TYPE_ADDED_KALTURA_TO_HIS_FAVORITES_SUBSCRIBED
{
    return @"42";
}
+ (NSString*)MAIL_TYPE_NEW_COMMENT_IN_KALTURA_YOU_SUBSCRIBED
{
    return @"43";
}
+ (NSString*)MAIL_TYPE_REGISTER_CONFIRM
{
    return @"50";
}
+ (NSString*)MAIL_TYPE_PASSWORD_RESET
{
    return @"51";
}
+ (NSString*)MAIL_TYPE_LOGIN_MAIL_RESET
{
    return @"52";
}
+ (NSString*)MAIL_TYPE_REGISTER_CONFIRM_VIDEO_SERVICE
{
    return @"54";
}
+ (NSString*)MAIL_TYPE_VIDEO_READY
{
    return @"60";
}
+ (NSString*)MAIL_TYPE_VIDEO_IS_READY
{
    return @"62";
}
+ (NSString*)MAIL_TYPE_BULK_DOWNLOAD_READY
{
    return @"63";
}
+ (NSString*)MAIL_TYPE_BULKUPLOAD_FINISHED
{
    return @"64";
}
+ (NSString*)MAIL_TYPE_BULKUPLOAD_FAILED
{
    return @"65";
}
+ (NSString*)MAIL_TYPE_BULKUPLOAD_ABORTED
{
    return @"66";
}
+ (NSString*)MAIL_TYPE_NOTIFY_ERR
{
    return @"70";
}
+ (NSString*)MAIL_TYPE_ACCOUNT_UPGRADE_CONFIRM
{
    return @"80";
}
+ (NSString*)MAIL_TYPE_VIDEO_SERVICE_NOTICE
{
    return @"81";
}
+ (NSString*)MAIL_TYPE_VIDEO_SERVICE_NOTICE_LIMIT_REACHED
{
    return @"82";
}
+ (NSString*)MAIL_TYPE_VIDEO_SERVICE_NOTICE_ACCOUNT_LOCKED
{
    return @"83";
}
+ (NSString*)MAIL_TYPE_VIDEO_SERVICE_NOTICE_ACCOUNT_DELETED
{
    return @"84";
}
+ (NSString*)MAIL_TYPE_VIDEO_SERVICE_NOTICE_UPGRADE_OFFER
{
    return @"85";
}
+ (NSString*)MAIL_TYPE_ACCOUNT_REACTIVE_CONFIRM
{
    return @"86";
}
+ (NSString*)MAIL_TYPE_SYSTEM_USER_RESET_PASSWORD
{
    return @"110";
}
+ (NSString*)MAIL_TYPE_SYSTEM_USER_RESET_PASSWORD_SUCCESS
{
    return @"111";
}
+ (NSString*)MAIL_TYPE_SYSTEM_USER_NEW_PASSWORD
{
    return @"112";
}
+ (NSString*)MAIL_TYPE_SYSTEM_USER_CREDENTIALS_SAVED
{
    return @"113";
}
@end

@implementation KalturaMediaEntryOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)DURATION_ASC
{
    return @"+duration";
}
+ (NSString*)END_DATE_ASC
{
    return @"+endDate";
}
+ (NSString*)MEDIA_TYPE_ASC
{
    return @"+mediaType";
}
+ (NSString*)MODERATION_COUNT_ASC
{
    return @"+moderationCount";
}
+ (NSString*)MS_DURATION_ASC
{
    return @"+msDuration";
}
+ (NSString*)NAME_ASC
{
    return @"+name";
}
+ (NSString*)PARTNER_SORT_VALUE_ASC
{
    return @"+partnerSortValue";
}
+ (NSString*)PLAYS_ASC
{
    return @"+plays";
}
+ (NSString*)RANK_ASC
{
    return @"+rank";
}
+ (NSString*)RECENT_ASC
{
    return @"+recent";
}
+ (NSString*)START_DATE_ASC
{
    return @"+startDate";
}
+ (NSString*)TOTAL_RANK_ASC
{
    return @"+totalRank";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)VIEWS_ASC
{
    return @"+views";
}
+ (NSString*)WEIGHT_ASC
{
    return @"+weight";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)DURATION_DESC
{
    return @"-duration";
}
+ (NSString*)END_DATE_DESC
{
    return @"-endDate";
}
+ (NSString*)MEDIA_TYPE_DESC
{
    return @"-mediaType";
}
+ (NSString*)MODERATION_COUNT_DESC
{
    return @"-moderationCount";
}
+ (NSString*)MS_DURATION_DESC
{
    return @"-msDuration";
}
+ (NSString*)NAME_DESC
{
    return @"-name";
}
+ (NSString*)PARTNER_SORT_VALUE_DESC
{
    return @"-partnerSortValue";
}
+ (NSString*)PLAYS_DESC
{
    return @"-plays";
}
+ (NSString*)RANK_DESC
{
    return @"-rank";
}
+ (NSString*)RECENT_DESC
{
    return @"-recent";
}
+ (NSString*)START_DATE_DESC
{
    return @"-startDate";
}
+ (NSString*)TOTAL_RANK_DESC
{
    return @"-totalRank";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
+ (NSString*)VIEWS_DESC
{
    return @"-views";
}
+ (NSString*)WEIGHT_DESC
{
    return @"-weight";
}
@end

@implementation KalturaMediaFlavorParamsOrderBy
@end

@implementation KalturaMediaFlavorParamsOutputOrderBy
@end

@implementation KalturaMediaInfoOrderBy
@end

@implementation KalturaMediaParserType
+ (NSString*)MEDIAINFO
{
    return @"0";
}
+ (NSString*)FFMPEG
{
    return @"1";
}
@end

@implementation KalturaMixEntryOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)DURATION_ASC
{
    return @"+duration";
}
+ (NSString*)END_DATE_ASC
{
    return @"+endDate";
}
+ (NSString*)MODERATION_COUNT_ASC
{
    return @"+moderationCount";
}
+ (NSString*)MS_DURATION_ASC
{
    return @"+msDuration";
}
+ (NSString*)NAME_ASC
{
    return @"+name";
}
+ (NSString*)PARTNER_SORT_VALUE_ASC
{
    return @"+partnerSortValue";
}
+ (NSString*)PLAYS_ASC
{
    return @"+plays";
}
+ (NSString*)RANK_ASC
{
    return @"+rank";
}
+ (NSString*)RECENT_ASC
{
    return @"+recent";
}
+ (NSString*)START_DATE_ASC
{
    return @"+startDate";
}
+ (NSString*)TOTAL_RANK_ASC
{
    return @"+totalRank";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)VIEWS_ASC
{
    return @"+views";
}
+ (NSString*)WEIGHT_ASC
{
    return @"+weight";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)DURATION_DESC
{
    return @"-duration";
}
+ (NSString*)END_DATE_DESC
{
    return @"-endDate";
}
+ (NSString*)MODERATION_COUNT_DESC
{
    return @"-moderationCount";
}
+ (NSString*)MS_DURATION_DESC
{
    return @"-msDuration";
}
+ (NSString*)NAME_DESC
{
    return @"-name";
}
+ (NSString*)PARTNER_SORT_VALUE_DESC
{
    return @"-partnerSortValue";
}
+ (NSString*)PLAYS_DESC
{
    return @"-plays";
}
+ (NSString*)RANK_DESC
{
    return @"-rank";
}
+ (NSString*)RECENT_DESC
{
    return @"-recent";
}
+ (NSString*)START_DATE_DESC
{
    return @"-startDate";
}
+ (NSString*)TOTAL_RANK_DESC
{
    return @"-totalRank";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
+ (NSString*)VIEWS_DESC
{
    return @"-views";
}
+ (NSString*)WEIGHT_DESC
{
    return @"-weight";
}
@end

@implementation KalturaModerationFlagStatus
+ (NSString*)PENDING
{
    return @"1";
}
+ (NSString*)MODERATED
{
    return @"2";
}
@end

@implementation KalturaModerationObjectType
+ (NSString*)ENTRY
{
    return @"2";
}
+ (NSString*)USER
{
    return @"3";
}
@end

@implementation KalturaPartnerOrderBy
+ (NSString*)ADMIN_EMAIL_ASC
{
    return @"+adminEmail";
}
+ (NSString*)ADMIN_NAME_ASC
{
    return @"+adminName";
}
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)ID_ASC
{
    return @"+id";
}
+ (NSString*)NAME_ASC
{
    return @"+name";
}
+ (NSString*)STATUS_ASC
{
    return @"+status";
}
+ (NSString*)WEBSITE_ASC
{
    return @"+website";
}
+ (NSString*)ADMIN_EMAIL_DESC
{
    return @"-adminEmail";
}
+ (NSString*)ADMIN_NAME_DESC
{
    return @"-adminName";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)ID_DESC
{
    return @"-id";
}
+ (NSString*)NAME_DESC
{
    return @"-name";
}
+ (NSString*)STATUS_DESC
{
    return @"-status";
}
+ (NSString*)WEBSITE_DESC
{
    return @"-website";
}
@end

@implementation KalturaPermissionItemOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)ID_ASC
{
    return @"+id";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)ID_DESC
{
    return @"-id";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
@end

@implementation KalturaPermissionItemType
+ (NSString*)API_ACTION_ITEM
{
    return @"kApiActionPermissionItem";
}
+ (NSString*)API_PARAMETER_ITEM
{
    return @"kApiParameterPermissionItem";
}
@end

@implementation KalturaPermissionOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)ID_ASC
{
    return @"+id";
}
+ (NSString*)NAME_ASC
{
    return @"+name";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)ID_DESC
{
    return @"-id";
}
+ (NSString*)NAME_DESC
{
    return @"-name";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
@end

@implementation KalturaPlayableEntryOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)DURATION_ASC
{
    return @"+duration";
}
+ (NSString*)END_DATE_ASC
{
    return @"+endDate";
}
+ (NSString*)MODERATION_COUNT_ASC
{
    return @"+moderationCount";
}
+ (NSString*)MS_DURATION_ASC
{
    return @"+msDuration";
}
+ (NSString*)NAME_ASC
{
    return @"+name";
}
+ (NSString*)PARTNER_SORT_VALUE_ASC
{
    return @"+partnerSortValue";
}
+ (NSString*)PLAYS_ASC
{
    return @"+plays";
}
+ (NSString*)RANK_ASC
{
    return @"+rank";
}
+ (NSString*)RECENT_ASC
{
    return @"+recent";
}
+ (NSString*)START_DATE_ASC
{
    return @"+startDate";
}
+ (NSString*)TOTAL_RANK_ASC
{
    return @"+totalRank";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)VIEWS_ASC
{
    return @"+views";
}
+ (NSString*)WEIGHT_ASC
{
    return @"+weight";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)DURATION_DESC
{
    return @"-duration";
}
+ (NSString*)END_DATE_DESC
{
    return @"-endDate";
}
+ (NSString*)MODERATION_COUNT_DESC
{
    return @"-moderationCount";
}
+ (NSString*)MS_DURATION_DESC
{
    return @"-msDuration";
}
+ (NSString*)NAME_DESC
{
    return @"-name";
}
+ (NSString*)PARTNER_SORT_VALUE_DESC
{
    return @"-partnerSortValue";
}
+ (NSString*)PLAYS_DESC
{
    return @"-plays";
}
+ (NSString*)RANK_DESC
{
    return @"-rank";
}
+ (NSString*)RECENT_DESC
{
    return @"-recent";
}
+ (NSString*)START_DATE_DESC
{
    return @"-startDate";
}
+ (NSString*)TOTAL_RANK_DESC
{
    return @"-totalRank";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
+ (NSString*)VIEWS_DESC
{
    return @"-views";
}
+ (NSString*)WEIGHT_DESC
{
    return @"-weight";
}
@end

@implementation KalturaPlaybackProtocol
+ (NSString*)APPLE_HTTP
{
    return @"applehttp";
}
+ (NSString*)AUTO
{
    return @"auto";
}
+ (NSString*)AKAMAI_HD
{
    return @"hdnetwork";
}
+ (NSString*)AKAMAI_HDS
{
    return @"hdnetworkmanifest";
}
+ (NSString*)HDS
{
    return @"hds";
}
+ (NSString*)HLS
{
    return @"hls";
}
+ (NSString*)HTTP
{
    return @"http";
}
+ (NSString*)RTMP
{
    return @"rtmp";
}
+ (NSString*)RTSP
{
    return @"rtsp";
}
+ (NSString*)SILVER_LIGHT
{
    return @"sl";
}
@end

@implementation KalturaPlaylistOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)END_DATE_ASC
{
    return @"+endDate";
}
+ (NSString*)MODERATION_COUNT_ASC
{
    return @"+moderationCount";
}
+ (NSString*)NAME_ASC
{
    return @"+name";
}
+ (NSString*)PARTNER_SORT_VALUE_ASC
{
    return @"+partnerSortValue";
}
+ (NSString*)RANK_ASC
{
    return @"+rank";
}
+ (NSString*)RECENT_ASC
{
    return @"+recent";
}
+ (NSString*)START_DATE_ASC
{
    return @"+startDate";
}
+ (NSString*)TOTAL_RANK_ASC
{
    return @"+totalRank";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)WEIGHT_ASC
{
    return @"+weight";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)END_DATE_DESC
{
    return @"-endDate";
}
+ (NSString*)MODERATION_COUNT_DESC
{
    return @"-moderationCount";
}
+ (NSString*)NAME_DESC
{
    return @"-name";
}
+ (NSString*)PARTNER_SORT_VALUE_DESC
{
    return @"-partnerSortValue";
}
+ (NSString*)RANK_DESC
{
    return @"-rank";
}
+ (NSString*)RECENT_DESC
{
    return @"-recent";
}
+ (NSString*)START_DATE_DESC
{
    return @"-startDate";
}
+ (NSString*)TOTAL_RANK_DESC
{
    return @"-totalRank";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
+ (NSString*)WEIGHT_DESC
{
    return @"-weight";
}
@end

@implementation KalturaReportInterval
+ (NSString*)DAYS
{
    return @"days";
}
+ (NSString*)MONTHS
{
    return @"months";
}
@end

@implementation KalturaReportOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
@end

@implementation KalturaSchemaType
+ (NSString*)BULK_UPLOAD_RESULT_XML
{
    return @"bulkUploadXml.bulkUploadResultXML";
}
+ (NSString*)BULK_UPLOAD_XML
{
    return @"bulkUploadXml.bulkUploadXML";
}
+ (NSString*)INGEST_API
{
    return @"cuePoint.ingestAPI";
}
+ (NSString*)SERVE_API
{
    return @"cuePoint.serveAPI";
}
+ (NSString*)DROP_FOLDER_XML
{
    return @"dropFolderXmlBulkUpload.dropFolderXml";
}
+ (NSString*)SYNDICATION
{
    return @"syndication";
}
@end

@implementation KalturaSearchConditionComparison
+ (NSString*)EQUAL
{
    return @"1";
}
+ (NSString*)EQUEL
{
    return @"1";
}
+ (NSString*)GREATER_THAN
{
    return @"2";
}
+ (NSString*)GREATER_THAN_OR_EQUEL
{
    return @"3";
}
+ (NSString*)GREATER_THAN_OR_EQUAL
{
    return @"3";
}
+ (NSString*)LESS_THAN
{
    return @"4";
}
+ (NSString*)LESS_THAN_OR_EQUEL
{
    return @"5";
}
+ (NSString*)LESS_THAN_OR_EQUAL
{
    return @"5";
}
@end

@implementation KalturaSourceType
+ (NSString*)LIMELIGHT_LIVE
{
    return @"limeLight.LIVE_STREAM";
}
+ (NSString*)FILE
{
    return @"1";
}
+ (NSString*)WEBCAM
{
    return @"2";
}
+ (NSString*)URL
{
    return @"5";
}
+ (NSString*)SEARCH_PROVIDER
{
    return @"6";
}
+ (NSString*)AKAMAI_LIVE
{
    return @"29";
}
+ (NSString*)MANUAL_LIVE_STREAM
{
    return @"30";
}
+ (NSString*)AKAMAI_UNIVERSAL_LIVE
{
    return @"31";
}
@end

@implementation KalturaStorageProfileOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
@end

@implementation KalturaSyndicationFeedEntriesOrderBy
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)RECENT
{
    return @"recent";
}
@end

@implementation KalturaTaggedObjectType
+ (NSString*)ENTRY
{
    return @"1";
}
+ (NSString*)CATEGORY
{
    return @"2";
}
@end

@implementation KalturaThumbAssetOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)DELETED_AT_ASC
{
    return @"+deletedAt";
}
+ (NSString*)SIZE_ASC
{
    return @"+size";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)DELETED_AT_DESC
{
    return @"-deletedAt";
}
+ (NSString*)SIZE_DESC
{
    return @"-size";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
@end

@implementation KalturaThumbParamsOrderBy
@end

@implementation KalturaThumbParamsOutputOrderBy
@end

@implementation KalturaTubeMogulSyndicationFeedCategories
+ (NSString*)ANIMALS_AND_PETS
{
    return @"Animals &amp; Pets";
}
+ (NSString*)ARTS_AND_ANIMATION
{
    return @"Arts &amp; Animation";
}
+ (NSString*)AUTOS
{
    return @"Autos";
}
+ (NSString*)COMEDY
{
    return @"Comedy";
}
+ (NSString*)COMMERCIALS_PROMOTIONAL
{
    return @"Commercials/Promotional";
}
+ (NSString*)ENTERTAINMENT
{
    return @"Entertainment";
}
+ (NSString*)FAMILY_AND_KIDS
{
    return @"Family &amp; Kids";
}
+ (NSString*)HOW_TO_INSTRUCTIONAL_DIY
{
    return @"How To/Instructional/DIY";
}
+ (NSString*)MUSIC
{
    return @"Music";
}
+ (NSString*)NEWS_AND_BLOGS
{
    return @"News &amp; Blogs";
}
+ (NSString*)SCIENCE_AND_TECHNOLOGY
{
    return @"Science &amp; Technology";
}
+ (NSString*)SPORTS
{
    return @"Sports";
}
+ (NSString*)TRAVEL_AND_PLACES
{
    return @"Travel &amp; Places";
}
+ (NSString*)VIDEO_GAMES
{
    return @"Video Games";
}
+ (NSString*)VLOGS_PEOPLE
{
    return @"Vlogs &amp; People";
}
@end

@implementation KalturaTubeMogulSyndicationFeedOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)NAME_ASC
{
    return @"+name";
}
+ (NSString*)PLAYLIST_ID_ASC
{
    return @"+playlistId";
}
+ (NSString*)TYPE_ASC
{
    return @"+type";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)NAME_DESC
{
    return @"-name";
}
+ (NSString*)PLAYLIST_ID_DESC
{
    return @"-playlistId";
}
+ (NSString*)TYPE_DESC
{
    return @"-type";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
@end

@implementation KalturaUiConfOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
@end

@implementation KalturaUploadTokenOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
@end

@implementation KalturaUserLoginDataOrderBy
@end

@implementation KalturaUserOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)ID_ASC
{
    return @"+id";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)ID_DESC
{
    return @"-id";
}
@end

@implementation KalturaUserRoleOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)ID_ASC
{
    return @"+id";
}
+ (NSString*)NAME_ASC
{
    return @"+name";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)ID_DESC
{
    return @"-id";
}
+ (NSString*)NAME_DESC
{
    return @"-name";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
@end

@implementation KalturaVideoCodec
+ (NSString*)NONE
{
    return @"";
}
+ (NSString*)APCH
{
    return @"apch";
}
+ (NSString*)APCN
{
    return @"apcn";
}
+ (NSString*)APCO
{
    return @"apco";
}
+ (NSString*)APCS
{
    return @"apcs";
}
+ (NSString*)COPY
{
    return @"copy";
}
+ (NSString*)DNXHD
{
    return @"dnxhd";
}
+ (NSString*)DV
{
    return @"dv";
}
+ (NSString*)FLV
{
    return @"flv";
}
+ (NSString*)H263
{
    return @"h263";
}
+ (NSString*)H264
{
    return @"h264";
}
+ (NSString*)H264B
{
    return @"h264b";
}
+ (NSString*)H264H
{
    return @"h264h";
}
+ (NSString*)H264M
{
    return @"h264m";
}
+ (NSString*)MPEG2
{
    return @"mpeg2";
}
+ (NSString*)MPEG4
{
    return @"mpeg4";
}
+ (NSString*)THEORA
{
    return @"theora";
}
+ (NSString*)VP6
{
    return @"vp6";
}
+ (NSString*)VP8
{
    return @"vp8";
}
+ (NSString*)WMV2
{
    return @"wmv2";
}
+ (NSString*)WMV3
{
    return @"wmv3";
}
+ (NSString*)WVC1A
{
    return @"wvc1a";
}
@end

@implementation KalturaWidgetOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
@end

@implementation KalturaYahooSyndicationFeedAdultValues
+ (NSString*)ADULT
{
    return @"adult";
}
+ (NSString*)NON_ADULT
{
    return @"nonadult";
}
@end

@implementation KalturaYahooSyndicationFeedCategories
+ (NSString*)ACTION
{
    return @"Action";
}
+ (NSString*)ANIMALS
{
    return @"Animals";
}
+ (NSString*)ART_AND_ANIMATION
{
    return @"Art &amp; Animation";
}
+ (NSString*)COMMERCIALS
{
    return @"Commercials";
}
+ (NSString*)ENTERTAINMENT_AND_TV
{
    return @"Entertainment &amp; TV";
}
+ (NSString*)FAMILY
{
    return @"Family";
}
+ (NSString*)FOOD
{
    return @"Food";
}
+ (NSString*)FUNNY_VIDEOS
{
    return @"Funny Videos";
}
+ (NSString*)GAMES
{
    return @"Games";
}
+ (NSString*)HEALTH_AND_BEAUTY
{
    return @"Health &amp; Beauty";
}
+ (NSString*)HOW_TO
{
    return @"How-To";
}
+ (NSString*)MOVIES_AND_SHORTS
{
    return @"Movies &amp; Shorts";
}
+ (NSString*)MUSIC
{
    return @"Music";
}
+ (NSString*)NEWS_AND_POLITICS
{
    return @"News &amp; Politics";
}
+ (NSString*)PEOPLE_AND_VLOGS
{
    return @"People &amp; Vlogs";
}
+ (NSString*)PRODUCTS_AND_TECH
{
    return @"Products &amp; Tech.";
}
+ (NSString*)SCIENCE_AND_ENVIRONMENT
{
    return @"Science &amp; Environment";
}
+ (NSString*)SPORTS
{
    return @"Sports";
}
+ (NSString*)TRANSPORTATION
{
    return @"Transportation";
}
+ (NSString*)TRAVEL
{
    return @"Travel";
}
@end

@implementation KalturaYahooSyndicationFeedOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)NAME_ASC
{
    return @"+name";
}
+ (NSString*)PLAYLIST_ID_ASC
{
    return @"+playlistId";
}
+ (NSString*)TYPE_ASC
{
    return @"+type";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)NAME_DESC
{
    return @"-name";
}
+ (NSString*)PLAYLIST_ID_DESC
{
    return @"-playlistId";
}
+ (NSString*)TYPE_DESC
{
    return @"-type";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
@end

///////////////////////// classes /////////////////////////
@implementation KalturaBaseRestriction
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaBaseRestriction"];
}

@end

@interface KalturaAccessControl()
@property (nonatomic,assign) int id;
@property (nonatomic,assign) int partnerId;
@property (nonatomic,assign) int createdAt;
@property (nonatomic,assign) BOOL containsUnsuportedRestrictions;
@end

@implementation KalturaAccessControl
@synthesize id = _id;
@synthesize partnerId = _partnerId;
@synthesize name = _name;
@synthesize systemName = _systemName;
@synthesize description = _description;
@synthesize createdAt = _createdAt;
@synthesize isDefault = _isDefault;
@synthesize restrictions = _restrictions;
@synthesize containsUnsuportedRestrictions = _containsUnsuportedRestrictions;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_id = KALTURA_UNDEF_INT;
    self->_partnerId = KALTURA_UNDEF_INT;
    self->_createdAt = KALTURA_UNDEF_INT;
    self->_isDefault = KALTURA_UNDEF_INT;
    self->_containsUnsuportedRestrictions = KALTURA_UNDEF_BOOL;
    return self;
}

- (KalturaFieldType)getTypeOfId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSystemName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDescription
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfIsDefault
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfRestrictions
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfRestrictions
{
    return @"KalturaBaseRestriction";
}

- (KalturaFieldType)getTypeOfContainsUnsuportedRestrictions
{
    return KFT_Bool;
}

- (void)setIdFromString:(NSString*)aPropVal
{
    self.id = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerIdFromString:(NSString*)aPropVal
{
    self.partnerId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtFromString:(NSString*)aPropVal
{
    self.createdAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setIsDefaultFromString:(NSString*)aPropVal
{
    self.isDefault = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setContainsUnsuportedRestrictionsFromString:(NSString*)aPropVal
{
    self.containsUnsuportedRestrictions = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAccessControl"];
    [aParams addIfDefinedKey:@"name" withString:self.name];
    [aParams addIfDefinedKey:@"systemName" withString:self.systemName];
    [aParams addIfDefinedKey:@"description" withString:self.description];
    [aParams addIfDefinedKey:@"isDefault" withInt:self.isDefault];
    [aParams addIfDefinedKey:@"restrictions" withArray:self.restrictions];
}

- (void)dealloc
{
    [self->_name release];
    [self->_systemName release];
    [self->_description release];
    [self->_restrictions release];
    [super dealloc];
}

@end

@interface KalturaAccessControlAction()
@property (nonatomic,copy) NSString* type;
@end

@implementation KalturaAccessControlAction
@synthesize type = _type;

- (KalturaFieldType)getTypeOfType
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAccessControlAction"];
}

- (void)dealloc
{
    [self->_type release];
    [super dealloc];
}

@end

@implementation KalturaAccessControlContextTypeHolder
@synthesize type = _type;

- (KalturaFieldType)getTypeOfType
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAccessControlContextTypeHolder"];
    [aParams addIfDefinedKey:@"type" withString:self.type];
}

- (void)dealloc
{
    [self->_type release];
    [super dealloc];
}

@end

@interface KalturaAccessControlListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaAccessControlListResponse
@synthesize objects = _objects;
@synthesize totalCount = _totalCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaAccessControl";
}

- (KalturaFieldType)getTypeOfTotalCount
{
    return KFT_Int;
}

- (void)setTotalCountFromString:(NSString*)aPropVal
{
    self.totalCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAccessControlListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@interface KalturaCondition()
@property (nonatomic,copy) NSString* type;
@end

@implementation KalturaCondition
@synthesize type = _type;
@synthesize not = _not;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_not = KALTURA_UNDEF_BOOL;
    return self;
}

- (KalturaFieldType)getTypeOfType
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfNot
{
    return KFT_Bool;
}

- (void)setNotFromString:(NSString*)aPropVal
{
    self.not = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaCondition"];
    [aParams addIfDefinedKey:@"not" withBool:self.not];
}

- (void)dealloc
{
    [self->_type release];
    [super dealloc];
}

@end

@implementation KalturaRule
@synthesize message = _message;
@synthesize actions = _actions;
@synthesize conditions = _conditions;
@synthesize contexts = _contexts;
@synthesize stopProcessing = _stopProcessing;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_stopProcessing = KALTURA_UNDEF_BOOL;
    return self;
}

- (KalturaFieldType)getTypeOfMessage
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfActions
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfActions
{
    return @"KalturaAccessControlAction";
}

- (KalturaFieldType)getTypeOfConditions
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfConditions
{
    return @"KalturaCondition";
}

- (KalturaFieldType)getTypeOfContexts
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfContexts
{
    return @"KalturaAccessControlContextTypeHolder";
}

- (KalturaFieldType)getTypeOfStopProcessing
{
    return KFT_Bool;
}

- (void)setStopProcessingFromString:(NSString*)aPropVal
{
    self.stopProcessing = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaRule"];
    [aParams addIfDefinedKey:@"message" withString:self.message];
    [aParams addIfDefinedKey:@"actions" withArray:self.actions];
    [aParams addIfDefinedKey:@"conditions" withArray:self.conditions];
    [aParams addIfDefinedKey:@"contexts" withArray:self.contexts];
    [aParams addIfDefinedKey:@"stopProcessing" withBool:self.stopProcessing];
}

- (void)dealloc
{
    [self->_message release];
    [self->_actions release];
    [self->_conditions release];
    [self->_contexts release];
    [super dealloc];
}

@end

@interface KalturaAccessControlProfile()
@property (nonatomic,assign) int id;
@property (nonatomic,assign) int partnerId;
@property (nonatomic,assign) int createdAt;
@property (nonatomic,assign) int updatedAt;
@end

@implementation KalturaAccessControlProfile
@synthesize id = _id;
@synthesize partnerId = _partnerId;
@synthesize name = _name;
@synthesize systemName = _systemName;
@synthesize description = _description;
@synthesize createdAt = _createdAt;
@synthesize updatedAt = _updatedAt;
@synthesize isDefault = _isDefault;
@synthesize rules = _rules;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_id = KALTURA_UNDEF_INT;
    self->_partnerId = KALTURA_UNDEF_INT;
    self->_createdAt = KALTURA_UNDEF_INT;
    self->_updatedAt = KALTURA_UNDEF_INT;
    self->_isDefault = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSystemName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDescription
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfIsDefault
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfRules
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfRules
{
    return @"KalturaRule";
}

- (void)setIdFromString:(NSString*)aPropVal
{
    self.id = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerIdFromString:(NSString*)aPropVal
{
    self.partnerId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtFromString:(NSString*)aPropVal
{
    self.createdAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtFromString:(NSString*)aPropVal
{
    self.updatedAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setIsDefaultFromString:(NSString*)aPropVal
{
    self.isDefault = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAccessControlProfile"];
    [aParams addIfDefinedKey:@"name" withString:self.name];
    [aParams addIfDefinedKey:@"systemName" withString:self.systemName];
    [aParams addIfDefinedKey:@"description" withString:self.description];
    [aParams addIfDefinedKey:@"isDefault" withInt:self.isDefault];
    [aParams addIfDefinedKey:@"rules" withArray:self.rules];
}

- (void)dealloc
{
    [self->_name release];
    [self->_systemName release];
    [self->_description release];
    [self->_rules release];
    [super dealloc];
}

@end

@interface KalturaAccessControlProfileListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaAccessControlProfileListResponse
@synthesize objects = _objects;
@synthesize totalCount = _totalCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaAccessControlProfile";
}

- (KalturaFieldType)getTypeOfTotalCount
{
    return KFT_Int;
}

- (void)setTotalCountFromString:(NSString*)aPropVal
{
    self.totalCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAccessControlProfileListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@implementation KalturaKeyValue
@synthesize key = _key;
@synthesize value = _value;

- (KalturaFieldType)getTypeOfKey
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfValue
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaKeyValue"];
    [aParams addIfDefinedKey:@"key" withString:self.key];
    [aParams addIfDefinedKey:@"value" withString:self.value];
}

- (void)dealloc
{
    [self->_key release];
    [self->_value release];
    [super dealloc];
}

@end

@implementation KalturaAccessControlScope
@synthesize referrer = _referrer;
@synthesize ip = _ip;
@synthesize ks = _ks;
@synthesize userAgent = _userAgent;
@synthesize time = _time;
@synthesize contexts = _contexts;
@synthesize hashes = _hashes;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_time = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfReferrer
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfIp
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfKs
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfUserAgent
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTime
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfContexts
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfContexts
{
    return @"KalturaAccessControlContextTypeHolder";
}

- (KalturaFieldType)getTypeOfHashes
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfHashes
{
    return @"KalturaKeyValue";
}

- (void)setTimeFromString:(NSString*)aPropVal
{
    self.time = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAccessControlScope"];
    [aParams addIfDefinedKey:@"referrer" withString:self.referrer];
    [aParams addIfDefinedKey:@"ip" withString:self.ip];
    [aParams addIfDefinedKey:@"ks" withString:self.ks];
    [aParams addIfDefinedKey:@"userAgent" withString:self.userAgent];
    [aParams addIfDefinedKey:@"time" withInt:self.time];
    [aParams addIfDefinedKey:@"contexts" withArray:self.contexts];
    [aParams addIfDefinedKey:@"hashes" withArray:self.hashes];
}

- (void)dealloc
{
    [self->_referrer release];
    [self->_ip release];
    [self->_ks release];
    [self->_userAgent release];
    [self->_contexts release];
    [self->_hashes release];
    [super dealloc];
}

@end

@interface KalturaAsset()
@property (nonatomic,copy) NSString* id;
@property (nonatomic,copy) NSString* entryId;
@property (nonatomic,assign) int partnerId;
@property (nonatomic,assign) int version;
@property (nonatomic,assign) int size;
@property (nonatomic,assign) int createdAt;
@property (nonatomic,assign) int updatedAt;
@property (nonatomic,assign) int deletedAt;
@property (nonatomic,copy) NSString* description;
@end

@implementation KalturaAsset
@synthesize id = _id;
@synthesize entryId = _entryId;
@synthesize partnerId = _partnerId;
@synthesize version = _version;
@synthesize size = _size;
@synthesize tags = _tags;
@synthesize fileExt = _fileExt;
@synthesize createdAt = _createdAt;
@synthesize updatedAt = _updatedAt;
@synthesize deletedAt = _deletedAt;
@synthesize description = _description;
@synthesize partnerData = _partnerData;
@synthesize partnerDescription = _partnerDescription;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_partnerId = KALTURA_UNDEF_INT;
    self->_version = KALTURA_UNDEF_INT;
    self->_size = KALTURA_UNDEF_INT;
    self->_createdAt = KALTURA_UNDEF_INT;
    self->_updatedAt = KALTURA_UNDEF_INT;
    self->_deletedAt = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfEntryId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPartnerId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfVersion
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfSize
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfTags
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFileExt
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDeletedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDescription
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPartnerData
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPartnerDescription
{
    return KFT_String;
}

- (void)setPartnerIdFromString:(NSString*)aPropVal
{
    self.partnerId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setVersionFromString:(NSString*)aPropVal
{
    self.version = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setSizeFromString:(NSString*)aPropVal
{
    self.size = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtFromString:(NSString*)aPropVal
{
    self.createdAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtFromString:(NSString*)aPropVal
{
    self.updatedAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDeletedAtFromString:(NSString*)aPropVal
{
    self.deletedAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAsset"];
    [aParams addIfDefinedKey:@"tags" withString:self.tags];
    [aParams addIfDefinedKey:@"fileExt" withString:self.fileExt];
    [aParams addIfDefinedKey:@"partnerData" withString:self.partnerData];
    [aParams addIfDefinedKey:@"partnerDescription" withString:self.partnerDescription];
}

- (void)dealloc
{
    [self->_id release];
    [self->_entryId release];
    [self->_tags release];
    [self->_fileExt release];
    [self->_description release];
    [self->_partnerData release];
    [self->_partnerDescription release];
    [super dealloc];
}

@end

@implementation KalturaString
@synthesize value = _value;

- (KalturaFieldType)getTypeOfValue
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaString"];
    [aParams addIfDefinedKey:@"value" withString:self.value];
}

- (void)dealloc
{
    [self->_value release];
    [super dealloc];
}

@end

@interface KalturaAssetParams()
@property (nonatomic,assign) int id;
@property (nonatomic,assign) int partnerId;
@property (nonatomic,assign) int createdAt;
@property (nonatomic,assign) int isSystemDefault;
@end

@implementation KalturaAssetParams
@synthesize id = _id;
@synthesize partnerId = _partnerId;
@synthesize name = _name;
@synthesize systemName = _systemName;
@synthesize description = _description;
@synthesize createdAt = _createdAt;
@synthesize isSystemDefault = _isSystemDefault;
@synthesize tags = _tags;
@synthesize requiredPermissions = _requiredPermissions;
@synthesize sourceRemoteStorageProfileId = _sourceRemoteStorageProfileId;
@synthesize remoteStorageProfileIds = _remoteStorageProfileIds;
@synthesize mediaParserType = _mediaParserType;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_id = KALTURA_UNDEF_INT;
    self->_partnerId = KALTURA_UNDEF_INT;
    self->_createdAt = KALTURA_UNDEF_INT;
    self->_isSystemDefault = KALTURA_UNDEF_INT;
    self->_sourceRemoteStorageProfileId = KALTURA_UNDEF_INT;
    self->_remoteStorageProfileIds = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSystemName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDescription
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfIsSystemDefault
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfTags
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfRequiredPermissions
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfRequiredPermissions
{
    return @"KalturaString";
}

- (KalturaFieldType)getTypeOfSourceRemoteStorageProfileId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfRemoteStorageProfileIds
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfMediaParserType
{
    return KFT_String;
}

- (void)setIdFromString:(NSString*)aPropVal
{
    self.id = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerIdFromString:(NSString*)aPropVal
{
    self.partnerId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtFromString:(NSString*)aPropVal
{
    self.createdAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setIsSystemDefaultFromString:(NSString*)aPropVal
{
    self.isSystemDefault = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setSourceRemoteStorageProfileIdFromString:(NSString*)aPropVal
{
    self.sourceRemoteStorageProfileId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setRemoteStorageProfileIdsFromString:(NSString*)aPropVal
{
    self.remoteStorageProfileIds = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAssetParams"];
    [aParams addIfDefinedKey:@"name" withString:self.name];
    [aParams addIfDefinedKey:@"systemName" withString:self.systemName];
    [aParams addIfDefinedKey:@"description" withString:self.description];
    [aParams addIfDefinedKey:@"tags" withString:self.tags];
    [aParams addIfDefinedKey:@"requiredPermissions" withArray:self.requiredPermissions];
    [aParams addIfDefinedKey:@"sourceRemoteStorageProfileId" withInt:self.sourceRemoteStorageProfileId];
    [aParams addIfDefinedKey:@"remoteStorageProfileIds" withInt:self.remoteStorageProfileIds];
    [aParams addIfDefinedKey:@"mediaParserType" withString:self.mediaParserType];
}

- (void)dealloc
{
    [self->_name release];
    [self->_systemName release];
    [self->_description release];
    [self->_tags release];
    [self->_requiredPermissions release];
    [self->_mediaParserType release];
    [super dealloc];
}

@end

@implementation KalturaResource
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaResource"];
}

@end

@implementation KalturaContentResource
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaContentResource"];
}

@end

@implementation KalturaAssetParamsResourceContainer
@synthesize resource = _resource;
@synthesize assetParamsId = _assetParamsId;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_assetParamsId = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfResource
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfResource
{
    return @"KalturaContentResource";
}

- (KalturaFieldType)getTypeOfAssetParamsId
{
    return KFT_Int;
}

- (void)setAssetParamsIdFromString:(NSString*)aPropVal
{
    self.assetParamsId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAssetParamsResourceContainer"];
    [aParams addIfDefinedKey:@"resource" withObject:self.resource];
    [aParams addIfDefinedKey:@"assetParamsId" withInt:self.assetParamsId];
}

- (void)dealloc
{
    [self->_resource release];
    [super dealloc];
}

@end

@implementation KalturaOperationAttributes
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaOperationAttributes"];
}

@end

@interface KalturaBaseEntry()
@property (nonatomic,copy) NSString* id;
@property (nonatomic,assign) int partnerId;
@property (nonatomic,copy) NSString* status;
@property (nonatomic,assign) int moderationStatus;
@property (nonatomic,assign) int moderationCount;
@property (nonatomic,assign) int createdAt;
@property (nonatomic,assign) int updatedAt;
@property (nonatomic,assign) double rank;
@property (nonatomic,assign) int totalRank;
@property (nonatomic,assign) int votes;
@property (nonatomic,copy) NSString* downloadUrl;
@property (nonatomic,copy) NSString* searchText;
@property (nonatomic,assign) int version;
@property (nonatomic,copy) NSString* replacingEntryId;
@property (nonatomic,copy) NSString* replacedEntryId;
@property (nonatomic,copy) NSString* replacementStatus;
@property (nonatomic,copy) NSString* rootEntryId;
@end

@implementation KalturaBaseEntry
@synthesize id = _id;
@synthesize name = _name;
@synthesize description = _description;
@synthesize partnerId = _partnerId;
@synthesize userId = _userId;
@synthesize creatorId = _creatorId;
@synthesize tags = _tags;
@synthesize adminTags = _adminTags;
@synthesize categories = _categories;
@synthesize categoriesIds = _categoriesIds;
@synthesize status = _status;
@synthesize moderationStatus = _moderationStatus;
@synthesize moderationCount = _moderationCount;
@synthesize type = _type;
@synthesize createdAt = _createdAt;
@synthesize updatedAt = _updatedAt;
@synthesize rank = _rank;
@synthesize totalRank = _totalRank;
@synthesize votes = _votes;
@synthesize groupId = _groupId;
@synthesize partnerData = _partnerData;
@synthesize downloadUrl = _downloadUrl;
@synthesize searchText = _searchText;
@synthesize licenseType = _licenseType;
@synthesize version = _version;
@synthesize thumbnailUrl = _thumbnailUrl;
@synthesize accessControlId = _accessControlId;
@synthesize startDate = _startDate;
@synthesize endDate = _endDate;
@synthesize referenceId = _referenceId;
@synthesize replacingEntryId = _replacingEntryId;
@synthesize replacedEntryId = _replacedEntryId;
@synthesize replacementStatus = _replacementStatus;
@synthesize partnerSortValue = _partnerSortValue;
@synthesize conversionProfileId = _conversionProfileId;
@synthesize rootEntryId = _rootEntryId;
@synthesize operationAttributes = _operationAttributes;
@synthesize entitledUsersEdit = _entitledUsersEdit;
@synthesize entitledUsersPublish = _entitledUsersPublish;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_partnerId = KALTURA_UNDEF_INT;
    self->_moderationStatus = KALTURA_UNDEF_INT;
    self->_moderationCount = KALTURA_UNDEF_INT;
    self->_createdAt = KALTURA_UNDEF_INT;
    self->_updatedAt = KALTURA_UNDEF_INT;
    self->_rank = KALTURA_UNDEF_FLOAT;
    self->_totalRank = KALTURA_UNDEF_INT;
    self->_votes = KALTURA_UNDEF_INT;
    self->_groupId = KALTURA_UNDEF_INT;
    self->_licenseType = KALTURA_UNDEF_INT;
    self->_version = KALTURA_UNDEF_INT;
    self->_accessControlId = KALTURA_UNDEF_INT;
    self->_startDate = KALTURA_UNDEF_INT;
    self->_endDate = KALTURA_UNDEF_INT;
    self->_partnerSortValue = KALTURA_UNDEF_INT;
    self->_conversionProfileId = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDescription
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPartnerId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUserId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreatorId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTags
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAdminTags
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCategories
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCategoriesIds
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatus
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfModerationStatus
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfModerationCount
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfType
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfRank
{
    return KFT_Float;
}

- (KalturaFieldType)getTypeOfTotalRank
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfVotes
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfGroupId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerData
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDownloadUrl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSearchText
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfLicenseType
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfVersion
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfThumbnailUrl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAccessControlId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfStartDate
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfEndDate
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfReferenceId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfReplacingEntryId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfReplacedEntryId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfReplacementStatus
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPartnerSortValue
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfConversionProfileId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfRootEntryId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfOperationAttributes
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfOperationAttributes
{
    return @"KalturaOperationAttributes";
}

- (KalturaFieldType)getTypeOfEntitledUsersEdit
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfEntitledUsersPublish
{
    return KFT_String;
}

- (void)setPartnerIdFromString:(NSString*)aPropVal
{
    self.partnerId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setModerationStatusFromString:(NSString*)aPropVal
{
    self.moderationStatus = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setModerationCountFromString:(NSString*)aPropVal
{
    self.moderationCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtFromString:(NSString*)aPropVal
{
    self.createdAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtFromString:(NSString*)aPropVal
{
    self.updatedAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setRankFromString:(NSString*)aPropVal
{
    self.rank = [KalturaSimpleTypeParser parseFloat:aPropVal];
}

- (void)setTotalRankFromString:(NSString*)aPropVal
{
    self.totalRank = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setVotesFromString:(NSString*)aPropVal
{
    self.votes = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setGroupIdFromString:(NSString*)aPropVal
{
    self.groupId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setLicenseTypeFromString:(NSString*)aPropVal
{
    self.licenseType = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setVersionFromString:(NSString*)aPropVal
{
    self.version = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setAccessControlIdFromString:(NSString*)aPropVal
{
    self.accessControlId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStartDateFromString:(NSString*)aPropVal
{
    self.startDate = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setEndDateFromString:(NSString*)aPropVal
{
    self.endDate = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerSortValueFromString:(NSString*)aPropVal
{
    self.partnerSortValue = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setConversionProfileIdFromString:(NSString*)aPropVal
{
    self.conversionProfileId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaBaseEntry"];
    [aParams addIfDefinedKey:@"name" withString:self.name];
    [aParams addIfDefinedKey:@"description" withString:self.description];
    [aParams addIfDefinedKey:@"userId" withString:self.userId];
    [aParams addIfDefinedKey:@"creatorId" withString:self.creatorId];
    [aParams addIfDefinedKey:@"tags" withString:self.tags];
    [aParams addIfDefinedKey:@"adminTags" withString:self.adminTags];
    [aParams addIfDefinedKey:@"categories" withString:self.categories];
    [aParams addIfDefinedKey:@"categoriesIds" withString:self.categoriesIds];
    [aParams addIfDefinedKey:@"type" withString:self.type];
    [aParams addIfDefinedKey:@"groupId" withInt:self.groupId];
    [aParams addIfDefinedKey:@"partnerData" withString:self.partnerData];
    [aParams addIfDefinedKey:@"licenseType" withInt:self.licenseType];
    [aParams addIfDefinedKey:@"thumbnailUrl" withString:self.thumbnailUrl];
    [aParams addIfDefinedKey:@"accessControlId" withInt:self.accessControlId];
    [aParams addIfDefinedKey:@"startDate" withInt:self.startDate];
    [aParams addIfDefinedKey:@"endDate" withInt:self.endDate];
    [aParams addIfDefinedKey:@"referenceId" withString:self.referenceId];
    [aParams addIfDefinedKey:@"partnerSortValue" withInt:self.partnerSortValue];
    [aParams addIfDefinedKey:@"conversionProfileId" withInt:self.conversionProfileId];
    [aParams addIfDefinedKey:@"operationAttributes" withArray:self.operationAttributes];
    [aParams addIfDefinedKey:@"entitledUsersEdit" withString:self.entitledUsersEdit];
    [aParams addIfDefinedKey:@"entitledUsersPublish" withString:self.entitledUsersPublish];
}

- (void)dealloc
{
    [self->_id release];
    [self->_name release];
    [self->_description release];
    [self->_userId release];
    [self->_creatorId release];
    [self->_tags release];
    [self->_adminTags release];
    [self->_categories release];
    [self->_categoriesIds release];
    [self->_status release];
    [self->_type release];
    [self->_partnerData release];
    [self->_downloadUrl release];
    [self->_searchText release];
    [self->_thumbnailUrl release];
    [self->_referenceId release];
    [self->_replacingEntryId release];
    [self->_replacedEntryId release];
    [self->_replacementStatus release];
    [self->_rootEntryId release];
    [self->_operationAttributes release];
    [self->_entitledUsersEdit release];
    [self->_entitledUsersPublish release];
    [super dealloc];
}

@end

@interface KalturaBaseEntryListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaBaseEntryListResponse
@synthesize objects = _objects;
@synthesize totalCount = _totalCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaBaseEntry";
}

- (KalturaFieldType)getTypeOfTotalCount
{
    return KFT_Int;
}

- (void)setTotalCountFromString:(NSString*)aPropVal
{
    self.totalCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaBaseEntryListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@interface KalturaBaseSyndicationFeed()
@property (nonatomic,copy) NSString* id;
@property (nonatomic,copy) NSString* feedUrl;
@property (nonatomic,assign) int partnerId;
@property (nonatomic,assign) int status;
@property (nonatomic,assign) int createdAt;
@property (nonatomic,assign) int updatedAt;
@end

@implementation KalturaBaseSyndicationFeed
@synthesize id = _id;
@synthesize feedUrl = _feedUrl;
@synthesize partnerId = _partnerId;
@synthesize playlistId = _playlistId;
@synthesize name = _name;
@synthesize status = _status;
@synthesize type = _type;
@synthesize landingPage = _landingPage;
@synthesize createdAt = _createdAt;
@synthesize allowEmbed = _allowEmbed;
@synthesize playerUiconfId = _playerUiconfId;
@synthesize flavorParamId = _flavorParamId;
@synthesize transcodeExistingContent = _transcodeExistingContent;
@synthesize addToDefaultConversionProfile = _addToDefaultConversionProfile;
@synthesize categories = _categories;
@synthesize storageId = _storageId;
@synthesize entriesOrderBy = _entriesOrderBy;
@synthesize enforceEntitlement = _enforceEntitlement;
@synthesize privacyContext = _privacyContext;
@synthesize updatedAt = _updatedAt;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_partnerId = KALTURA_UNDEF_INT;
    self->_status = KALTURA_UNDEF_INT;
    self->_type = KALTURA_UNDEF_INT;
    self->_createdAt = KALTURA_UNDEF_INT;
    self->_allowEmbed = KALTURA_UNDEF_BOOL;
    self->_playerUiconfId = KALTURA_UNDEF_INT;
    self->_flavorParamId = KALTURA_UNDEF_INT;
    self->_transcodeExistingContent = KALTURA_UNDEF_BOOL;
    self->_addToDefaultConversionProfile = KALTURA_UNDEF_BOOL;
    self->_storageId = KALTURA_UNDEF_INT;
    self->_enforceEntitlement = KALTURA_UNDEF_BOOL;
    self->_updatedAt = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFeedUrl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPartnerId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPlaylistId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatus
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfType
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfLandingPage
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfAllowEmbed
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfPlayerUiconfId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfFlavorParamId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfTranscodeExistingContent
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfAddToDefaultConversionProfile
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfCategories
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStorageId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfEntriesOrderBy
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfEnforceEntitlement
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfPrivacyContext
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfUpdatedAt
{
    return KFT_Int;
}

- (void)setPartnerIdFromString:(NSString*)aPropVal
{
    self.partnerId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusFromString:(NSString*)aPropVal
{
    self.status = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setTypeFromString:(NSString*)aPropVal
{
    self.type = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtFromString:(NSString*)aPropVal
{
    self.createdAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setAllowEmbedFromString:(NSString*)aPropVal
{
    self.allowEmbed = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)setPlayerUiconfIdFromString:(NSString*)aPropVal
{
    self.playerUiconfId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setFlavorParamIdFromString:(NSString*)aPropVal
{
    self.flavorParamId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setTranscodeExistingContentFromString:(NSString*)aPropVal
{
    self.transcodeExistingContent = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)setAddToDefaultConversionProfileFromString:(NSString*)aPropVal
{
    self.addToDefaultConversionProfile = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)setStorageIdFromString:(NSString*)aPropVal
{
    self.storageId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setEnforceEntitlementFromString:(NSString*)aPropVal
{
    self.enforceEntitlement = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)setUpdatedAtFromString:(NSString*)aPropVal
{
    self.updatedAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaBaseSyndicationFeed"];
    [aParams addIfDefinedKey:@"playlistId" withString:self.playlistId];
    [aParams addIfDefinedKey:@"name" withString:self.name];
    [aParams addIfDefinedKey:@"type" withInt:self.type];
    [aParams addIfDefinedKey:@"landingPage" withString:self.landingPage];
    [aParams addIfDefinedKey:@"allowEmbed" withBool:self.allowEmbed];
    [aParams addIfDefinedKey:@"playerUiconfId" withInt:self.playerUiconfId];
    [aParams addIfDefinedKey:@"flavorParamId" withInt:self.flavorParamId];
    [aParams addIfDefinedKey:@"transcodeExistingContent" withBool:self.transcodeExistingContent];
    [aParams addIfDefinedKey:@"addToDefaultConversionProfile" withBool:self.addToDefaultConversionProfile];
    [aParams addIfDefinedKey:@"categories" withString:self.categories];
    [aParams addIfDefinedKey:@"storageId" withInt:self.storageId];
    [aParams addIfDefinedKey:@"entriesOrderBy" withString:self.entriesOrderBy];
    [aParams addIfDefinedKey:@"enforceEntitlement" withBool:self.enforceEntitlement];
    [aParams addIfDefinedKey:@"privacyContext" withString:self.privacyContext];
}

- (void)dealloc
{
    [self->_id release];
    [self->_feedUrl release];
    [self->_playlistId release];
    [self->_name release];
    [self->_landingPage release];
    [self->_categories release];
    [self->_entriesOrderBy release];
    [self->_privacyContext release];
    [super dealloc];
}

@end

@interface KalturaBaseSyndicationFeedListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaBaseSyndicationFeedListResponse
@synthesize objects = _objects;
@synthesize totalCount = _totalCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaBaseSyndicationFeed";
}

- (KalturaFieldType)getTypeOfTotalCount
{
    return KFT_Int;
}

- (void)setTotalCountFromString:(NSString*)aPropVal
{
    self.totalCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaBaseSyndicationFeedListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@implementation KalturaBulkUploadPluginData
@synthesize field = _field;
@synthesize value = _value;

- (KalturaFieldType)getTypeOfField
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfValue
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaBulkUploadPluginData"];
    [aParams addIfDefinedKey:@"field" withString:self.field];
    [aParams addIfDefinedKey:@"value" withString:self.value];
}

- (void)dealloc
{
    [self->_field release];
    [self->_value release];
    [super dealloc];
}

@end

@interface KalturaBulkUploadResult()
@property (nonatomic,assign) int id;
@end

@implementation KalturaBulkUploadResult
@synthesize id = _id;
@synthesize bulkUploadJobId = _bulkUploadJobId;
@synthesize lineIndex = _lineIndex;
@synthesize partnerId = _partnerId;
@synthesize status = _status;
@synthesize action = _action;
@synthesize objectId = _objectId;
@synthesize objectStatus = _objectStatus;
@synthesize bulkUploadResultObjectType = _bulkUploadResultObjectType;
@synthesize rowData = _rowData;
@synthesize partnerData = _partnerData;
@synthesize objectErrorDescription = _objectErrorDescription;
@synthesize pluginsData = _pluginsData;
@synthesize errorDescription = _errorDescription;
@synthesize errorCode = _errorCode;
@synthesize errorType = _errorType;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_id = KALTURA_UNDEF_INT;
    self->_bulkUploadJobId = KALTURA_UNDEF_INT;
    self->_lineIndex = KALTURA_UNDEF_INT;
    self->_partnerId = KALTURA_UNDEF_INT;
    self->_objectStatus = KALTURA_UNDEF_INT;
    self->_errorType = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfBulkUploadJobId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfLineIndex
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfStatus
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAction
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfObjectId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfObjectStatus
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfBulkUploadResultObjectType
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfRowData
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPartnerData
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfObjectErrorDescription
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPluginsData
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfPluginsData
{
    return @"KalturaBulkUploadPluginData";
}

- (KalturaFieldType)getTypeOfErrorDescription
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfErrorCode
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfErrorType
{
    return KFT_Int;
}

- (void)setIdFromString:(NSString*)aPropVal
{
    self.id = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setBulkUploadJobIdFromString:(NSString*)aPropVal
{
    self.bulkUploadJobId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setLineIndexFromString:(NSString*)aPropVal
{
    self.lineIndex = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerIdFromString:(NSString*)aPropVal
{
    self.partnerId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setObjectStatusFromString:(NSString*)aPropVal
{
    self.objectStatus = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setErrorTypeFromString:(NSString*)aPropVal
{
    self.errorType = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaBulkUploadResult"];
    [aParams addIfDefinedKey:@"bulkUploadJobId" withInt:self.bulkUploadJobId];
    [aParams addIfDefinedKey:@"lineIndex" withInt:self.lineIndex];
    [aParams addIfDefinedKey:@"partnerId" withInt:self.partnerId];
    [aParams addIfDefinedKey:@"status" withString:self.status];
    [aParams addIfDefinedKey:@"action" withString:self.action];
    [aParams addIfDefinedKey:@"objectId" withString:self.objectId];
    [aParams addIfDefinedKey:@"objectStatus" withInt:self.objectStatus];
    [aParams addIfDefinedKey:@"bulkUploadResultObjectType" withString:self.bulkUploadResultObjectType];
    [aParams addIfDefinedKey:@"rowData" withString:self.rowData];
    [aParams addIfDefinedKey:@"partnerData" withString:self.partnerData];
    [aParams addIfDefinedKey:@"objectErrorDescription" withString:self.objectErrorDescription];
    [aParams addIfDefinedKey:@"pluginsData" withArray:self.pluginsData];
    [aParams addIfDefinedKey:@"errorDescription" withString:self.errorDescription];
    [aParams addIfDefinedKey:@"errorCode" withString:self.errorCode];
    [aParams addIfDefinedKey:@"errorType" withInt:self.errorType];
}

- (void)dealloc
{
    [self->_status release];
    [self->_action release];
    [self->_objectId release];
    [self->_bulkUploadResultObjectType release];
    [self->_rowData release];
    [self->_partnerData release];
    [self->_objectErrorDescription release];
    [self->_pluginsData release];
    [self->_errorDescription release];
    [self->_errorCode release];
    [super dealloc];
}

@end

@implementation KalturaBulkUpload
@synthesize id = _id;
@synthesize uploadedBy = _uploadedBy;
@synthesize uploadedByUserId = _uploadedByUserId;
@synthesize uploadedOn = _uploadedOn;
@synthesize numOfEntries = _numOfEntries;
@synthesize status = _status;
@synthesize logFileUrl = _logFileUrl;
@synthesize csvFileUrl = _csvFileUrl;
@synthesize bulkFileUrl = _bulkFileUrl;
@synthesize bulkUploadType = _bulkUploadType;
@synthesize results = _results;
@synthesize error = _error;
@synthesize errorType = _errorType;
@synthesize errorNumber = _errorNumber;
@synthesize fileName = _fileName;
@synthesize description = _description;
@synthesize numOfObjects = _numOfObjects;
@synthesize bulkUploadObjectType = _bulkUploadObjectType;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_id = KALTURA_UNDEF_INT;
    self->_uploadedOn = KALTURA_UNDEF_INT;
    self->_numOfEntries = KALTURA_UNDEF_INT;
    self->_status = KALTURA_UNDEF_INT;
    self->_errorType = KALTURA_UNDEF_INT;
    self->_errorNumber = KALTURA_UNDEF_INT;
    self->_numOfObjects = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUploadedBy
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfUploadedByUserId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfUploadedOn
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfNumOfEntries
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfStatus
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfLogFileUrl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCsvFileUrl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfBulkFileUrl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfBulkUploadType
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfResults
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfResults
{
    return @"KalturaBulkUploadResult";
}

- (KalturaFieldType)getTypeOfError
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfErrorType
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfErrorNumber
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfFileName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDescription
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfNumOfObjects
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfBulkUploadObjectType
{
    return KFT_String;
}

- (void)setIdFromString:(NSString*)aPropVal
{
    self.id = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUploadedOnFromString:(NSString*)aPropVal
{
    self.uploadedOn = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setNumOfEntriesFromString:(NSString*)aPropVal
{
    self.numOfEntries = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusFromString:(NSString*)aPropVal
{
    self.status = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setErrorTypeFromString:(NSString*)aPropVal
{
    self.errorType = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setErrorNumberFromString:(NSString*)aPropVal
{
    self.errorNumber = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setNumOfObjectsFromString:(NSString*)aPropVal
{
    self.numOfObjects = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaBulkUpload"];
    [aParams addIfDefinedKey:@"id" withInt:self.id];
    [aParams addIfDefinedKey:@"uploadedBy" withString:self.uploadedBy];
    [aParams addIfDefinedKey:@"uploadedByUserId" withString:self.uploadedByUserId];
    [aParams addIfDefinedKey:@"uploadedOn" withInt:self.uploadedOn];
    [aParams addIfDefinedKey:@"numOfEntries" withInt:self.numOfEntries];
    [aParams addIfDefinedKey:@"status" withInt:self.status];
    [aParams addIfDefinedKey:@"logFileUrl" withString:self.logFileUrl];
    [aParams addIfDefinedKey:@"csvFileUrl" withString:self.csvFileUrl];
    [aParams addIfDefinedKey:@"bulkFileUrl" withString:self.bulkFileUrl];
    [aParams addIfDefinedKey:@"bulkUploadType" withString:self.bulkUploadType];
    [aParams addIfDefinedKey:@"results" withArray:self.results];
    [aParams addIfDefinedKey:@"error" withString:self.error];
    [aParams addIfDefinedKey:@"errorType" withInt:self.errorType];
    [aParams addIfDefinedKey:@"errorNumber" withInt:self.errorNumber];
    [aParams addIfDefinedKey:@"fileName" withString:self.fileName];
    [aParams addIfDefinedKey:@"description" withString:self.description];
    [aParams addIfDefinedKey:@"numOfObjects" withInt:self.numOfObjects];
    [aParams addIfDefinedKey:@"bulkUploadObjectType" withString:self.bulkUploadObjectType];
}

- (void)dealloc
{
    [self->_uploadedBy release];
    [self->_uploadedByUserId release];
    [self->_logFileUrl release];
    [self->_csvFileUrl release];
    [self->_bulkFileUrl release];
    [self->_bulkUploadType release];
    [self->_results release];
    [self->_error release];
    [self->_fileName release];
    [self->_description release];
    [self->_bulkUploadObjectType release];
    [super dealloc];
}

@end

@interface KalturaBulkUploadListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaBulkUploadListResponse
@synthesize objects = _objects;
@synthesize totalCount = _totalCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaBulkUpload";
}

- (KalturaFieldType)getTypeOfTotalCount
{
    return KFT_Int;
}

- (void)setTotalCountFromString:(NSString*)aPropVal
{
    self.totalCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaBulkUploadListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@implementation KalturaBulkUploadObjectData
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaBulkUploadObjectData"];
}

@end

@interface KalturaCEError()
@property (nonatomic,copy) NSString* id;
@end

@implementation KalturaCEError
@synthesize id = _id;
@synthesize partnerId = _partnerId;
@synthesize browser = _browser;
@synthesize serverIp = _serverIp;
@synthesize serverOs = _serverOs;
@synthesize phpVersion = _phpVersion;
@synthesize ceAdminEmail = _ceAdminEmail;
@synthesize type = _type;
@synthesize description = _description;
@synthesize data = _data;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_partnerId = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPartnerId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfBrowser
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfServerIp
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfServerOs
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPhpVersion
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCeAdminEmail
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfType
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDescription
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfData
{
    return KFT_String;
}

- (void)setPartnerIdFromString:(NSString*)aPropVal
{
    self.partnerId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaCEError"];
    [aParams addIfDefinedKey:@"partnerId" withInt:self.partnerId];
    [aParams addIfDefinedKey:@"browser" withString:self.browser];
    [aParams addIfDefinedKey:@"serverIp" withString:self.serverIp];
    [aParams addIfDefinedKey:@"serverOs" withString:self.serverOs];
    [aParams addIfDefinedKey:@"phpVersion" withString:self.phpVersion];
    [aParams addIfDefinedKey:@"ceAdminEmail" withString:self.ceAdminEmail];
    [aParams addIfDefinedKey:@"type" withString:self.type];
    [aParams addIfDefinedKey:@"description" withString:self.description];
    [aParams addIfDefinedKey:@"data" withString:self.data];
}

- (void)dealloc
{
    [self->_id release];
    [self->_browser release];
    [self->_serverIp release];
    [self->_serverOs release];
    [self->_phpVersion release];
    [self->_ceAdminEmail release];
    [self->_type release];
    [self->_description release];
    [self->_data release];
    [super dealloc];
}

@end

@interface KalturaCategory()
@property (nonatomic,assign) int id;
@property (nonatomic,assign) int depth;
@property (nonatomic,assign) int partnerId;
@property (nonatomic,copy) NSString* fullName;
@property (nonatomic,copy) NSString* fullIds;
@property (nonatomic,assign) int entriesCount;
@property (nonatomic,assign) int createdAt;
@property (nonatomic,assign) int updatedAt;
@property (nonatomic,assign) int userJoinPolicy;
@property (nonatomic,assign) int directEntriesCount;
@property (nonatomic,assign) int membersCount;
@property (nonatomic,assign) int pendingMembersCount;
@property (nonatomic,copy) NSString* privacyContexts;
@property (nonatomic,assign) int status;
@property (nonatomic,assign) int inheritedParentId;
@property (nonatomic,assign) int directSubCategoriesCount;
@property (nonatomic,assign) int pendingEntriesCount;
@end

@implementation KalturaCategory
@synthesize id = _id;
@synthesize parentId = _parentId;
@synthesize depth = _depth;
@synthesize partnerId = _partnerId;
@synthesize name = _name;
@synthesize fullName = _fullName;
@synthesize fullIds = _fullIds;
@synthesize entriesCount = _entriesCount;
@synthesize createdAt = _createdAt;
@synthesize updatedAt = _updatedAt;
@synthesize description = _description;
@synthesize tags = _tags;
@synthesize appearInList = _appearInList;
@synthesize privacy = _privacy;
@synthesize inheritanceType = _inheritanceType;
@synthesize userJoinPolicy = _userJoinPolicy;
@synthesize defaultPermissionLevel = _defaultPermissionLevel;
@synthesize owner = _owner;
@synthesize directEntriesCount = _directEntriesCount;
@synthesize referenceId = _referenceId;
@synthesize contributionPolicy = _contributionPolicy;
@synthesize membersCount = _membersCount;
@synthesize pendingMembersCount = _pendingMembersCount;
@synthesize privacyContext = _privacyContext;
@synthesize privacyContexts = _privacyContexts;
@synthesize status = _status;
@synthesize inheritedParentId = _inheritedParentId;
@synthesize partnerSortValue = _partnerSortValue;
@synthesize partnerData = _partnerData;
@synthesize defaultOrderBy = _defaultOrderBy;
@synthesize directSubCategoriesCount = _directSubCategoriesCount;
@synthesize moderation = _moderation;
@synthesize pendingEntriesCount = _pendingEntriesCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_id = KALTURA_UNDEF_INT;
    self->_parentId = KALTURA_UNDEF_INT;
    self->_depth = KALTURA_UNDEF_INT;
    self->_partnerId = KALTURA_UNDEF_INT;
    self->_entriesCount = KALTURA_UNDEF_INT;
    self->_createdAt = KALTURA_UNDEF_INT;
    self->_updatedAt = KALTURA_UNDEF_INT;
    self->_appearInList = KALTURA_UNDEF_INT;
    self->_privacy = KALTURA_UNDEF_INT;
    self->_inheritanceType = KALTURA_UNDEF_INT;
    self->_userJoinPolicy = KALTURA_UNDEF_INT;
    self->_defaultPermissionLevel = KALTURA_UNDEF_INT;
    self->_directEntriesCount = KALTURA_UNDEF_INT;
    self->_contributionPolicy = KALTURA_UNDEF_INT;
    self->_membersCount = KALTURA_UNDEF_INT;
    self->_pendingMembersCount = KALTURA_UNDEF_INT;
    self->_status = KALTURA_UNDEF_INT;
    self->_inheritedParentId = KALTURA_UNDEF_INT;
    self->_partnerSortValue = KALTURA_UNDEF_INT;
    self->_directSubCategoriesCount = KALTURA_UNDEF_INT;
    self->_moderation = KALTURA_UNDEF_INT;
    self->_pendingEntriesCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfParentId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDepth
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFullName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFullIds
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfEntriesCount
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDescription
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTags
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAppearInList
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPrivacy
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfInheritanceType
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUserJoinPolicy
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDefaultPermissionLevel
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfOwner
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDirectEntriesCount
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfReferenceId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfContributionPolicy
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfMembersCount
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPendingMembersCount
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPrivacyContext
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPrivacyContexts
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatus
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfInheritedParentId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerSortValue
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerData
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDefaultOrderBy
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDirectSubCategoriesCount
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfModeration
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPendingEntriesCount
{
    return KFT_Int;
}

- (void)setIdFromString:(NSString*)aPropVal
{
    self.id = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setParentIdFromString:(NSString*)aPropVal
{
    self.parentId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDepthFromString:(NSString*)aPropVal
{
    self.depth = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerIdFromString:(NSString*)aPropVal
{
    self.partnerId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setEntriesCountFromString:(NSString*)aPropVal
{
    self.entriesCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtFromString:(NSString*)aPropVal
{
    self.createdAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtFromString:(NSString*)aPropVal
{
    self.updatedAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setAppearInListFromString:(NSString*)aPropVal
{
    self.appearInList = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPrivacyFromString:(NSString*)aPropVal
{
    self.privacy = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setInheritanceTypeFromString:(NSString*)aPropVal
{
    self.inheritanceType = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUserJoinPolicyFromString:(NSString*)aPropVal
{
    self.userJoinPolicy = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDefaultPermissionLevelFromString:(NSString*)aPropVal
{
    self.defaultPermissionLevel = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDirectEntriesCountFromString:(NSString*)aPropVal
{
    self.directEntriesCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setContributionPolicyFromString:(NSString*)aPropVal
{
    self.contributionPolicy = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setMembersCountFromString:(NSString*)aPropVal
{
    self.membersCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPendingMembersCountFromString:(NSString*)aPropVal
{
    self.pendingMembersCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusFromString:(NSString*)aPropVal
{
    self.status = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setInheritedParentIdFromString:(NSString*)aPropVal
{
    self.inheritedParentId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerSortValueFromString:(NSString*)aPropVal
{
    self.partnerSortValue = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDirectSubCategoriesCountFromString:(NSString*)aPropVal
{
    self.directSubCategoriesCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setModerationFromString:(NSString*)aPropVal
{
    self.moderation = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPendingEntriesCountFromString:(NSString*)aPropVal
{
    self.pendingEntriesCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaCategory"];
    [aParams addIfDefinedKey:@"parentId" withInt:self.parentId];
    [aParams addIfDefinedKey:@"name" withString:self.name];
    [aParams addIfDefinedKey:@"description" withString:self.description];
    [aParams addIfDefinedKey:@"tags" withString:self.tags];
    [aParams addIfDefinedKey:@"appearInList" withInt:self.appearInList];
    [aParams addIfDefinedKey:@"privacy" withInt:self.privacy];
    [aParams addIfDefinedKey:@"inheritanceType" withInt:self.inheritanceType];
    [aParams addIfDefinedKey:@"defaultPermissionLevel" withInt:self.defaultPermissionLevel];
    [aParams addIfDefinedKey:@"owner" withString:self.owner];
    [aParams addIfDefinedKey:@"referenceId" withString:self.referenceId];
    [aParams addIfDefinedKey:@"contributionPolicy" withInt:self.contributionPolicy];
    [aParams addIfDefinedKey:@"privacyContext" withString:self.privacyContext];
    [aParams addIfDefinedKey:@"partnerSortValue" withInt:self.partnerSortValue];
    [aParams addIfDefinedKey:@"partnerData" withString:self.partnerData];
    [aParams addIfDefinedKey:@"defaultOrderBy" withString:self.defaultOrderBy];
    [aParams addIfDefinedKey:@"moderation" withInt:self.moderation];
}

- (void)dealloc
{
    [self->_name release];
    [self->_fullName release];
    [self->_fullIds release];
    [self->_description release];
    [self->_tags release];
    [self->_owner release];
    [self->_referenceId release];
    [self->_privacyContext release];
    [self->_privacyContexts release];
    [self->_partnerData release];
    [self->_defaultOrderBy release];
    [super dealloc];
}

@end

@interface KalturaCategoryEntry()
@property (nonatomic,assign) int createdAt;
@property (nonatomic,copy) NSString* categoryFullIds;
@property (nonatomic,assign) int status;
@end

@implementation KalturaCategoryEntry
@synthesize categoryId = _categoryId;
@synthesize entryId = _entryId;
@synthesize createdAt = _createdAt;
@synthesize categoryFullIds = _categoryFullIds;
@synthesize status = _status;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_categoryId = KALTURA_UNDEF_INT;
    self->_createdAt = KALTURA_UNDEF_INT;
    self->_status = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfCategoryId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfEntryId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCategoryFullIds
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatus
{
    return KFT_Int;
}

- (void)setCategoryIdFromString:(NSString*)aPropVal
{
    self.categoryId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtFromString:(NSString*)aPropVal
{
    self.createdAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusFromString:(NSString*)aPropVal
{
    self.status = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaCategoryEntry"];
    [aParams addIfDefinedKey:@"categoryId" withInt:self.categoryId];
    [aParams addIfDefinedKey:@"entryId" withString:self.entryId];
}

- (void)dealloc
{
    [self->_entryId release];
    [self->_categoryFullIds release];
    [super dealloc];
}

@end

@interface KalturaCategoryEntryListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaCategoryEntryListResponse
@synthesize objects = _objects;
@synthesize totalCount = _totalCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaCategoryEntry";
}

- (KalturaFieldType)getTypeOfTotalCount
{
    return KFT_Int;
}

- (void)setTotalCountFromString:(NSString*)aPropVal
{
    self.totalCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaCategoryEntryListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@interface KalturaCategoryListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaCategoryListResponse
@synthesize objects = _objects;
@synthesize totalCount = _totalCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaCategory";
}

- (KalturaFieldType)getTypeOfTotalCount
{
    return KFT_Int;
}

- (void)setTotalCountFromString:(NSString*)aPropVal
{
    self.totalCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaCategoryListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@interface KalturaCategoryUser()
@property (nonatomic,assign) int partnerId;
@property (nonatomic,assign) int status;
@property (nonatomic,assign) int createdAt;
@property (nonatomic,assign) int updatedAt;
@property (nonatomic,copy) NSString* categoryFullIds;
@end

@implementation KalturaCategoryUser
@synthesize categoryId = _categoryId;
@synthesize userId = _userId;
@synthesize partnerId = _partnerId;
@synthesize permissionLevel = _permissionLevel;
@synthesize status = _status;
@synthesize createdAt = _createdAt;
@synthesize updatedAt = _updatedAt;
@synthesize updateMethod = _updateMethod;
@synthesize categoryFullIds = _categoryFullIds;
@synthesize permissionNames = _permissionNames;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_categoryId = KALTURA_UNDEF_INT;
    self->_partnerId = KALTURA_UNDEF_INT;
    self->_permissionLevel = KALTURA_UNDEF_INT;
    self->_status = KALTURA_UNDEF_INT;
    self->_createdAt = KALTURA_UNDEF_INT;
    self->_updatedAt = KALTURA_UNDEF_INT;
    self->_updateMethod = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfCategoryId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUserId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPartnerId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPermissionLevel
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfStatus
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdateMethod
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCategoryFullIds
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPermissionNames
{
    return KFT_String;
}

- (void)setCategoryIdFromString:(NSString*)aPropVal
{
    self.categoryId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerIdFromString:(NSString*)aPropVal
{
    self.partnerId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPermissionLevelFromString:(NSString*)aPropVal
{
    self.permissionLevel = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusFromString:(NSString*)aPropVal
{
    self.status = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtFromString:(NSString*)aPropVal
{
    self.createdAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtFromString:(NSString*)aPropVal
{
    self.updatedAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdateMethodFromString:(NSString*)aPropVal
{
    self.updateMethod = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaCategoryUser"];
    [aParams addIfDefinedKey:@"categoryId" withInt:self.categoryId];
    [aParams addIfDefinedKey:@"userId" withString:self.userId];
    [aParams addIfDefinedKey:@"permissionLevel" withInt:self.permissionLevel];
    [aParams addIfDefinedKey:@"updateMethod" withInt:self.updateMethod];
    [aParams addIfDefinedKey:@"permissionNames" withString:self.permissionNames];
}

- (void)dealloc
{
    [self->_userId release];
    [self->_categoryFullIds release];
    [self->_permissionNames release];
    [super dealloc];
}

@end

@interface KalturaCategoryUserListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaCategoryUserListResponse
@synthesize objects = _objects;
@synthesize totalCount = _totalCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaCategoryUser";
}

- (KalturaFieldType)getTypeOfTotalCount
{
    return KFT_Int;
}

- (void)setTotalCountFromString:(NSString*)aPropVal
{
    self.totalCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaCategoryUserListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@implementation KalturaClientNotification
@synthesize url = _url;
@synthesize data = _data;

- (KalturaFieldType)getTypeOfUrl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfData
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaClientNotification"];
    [aParams addIfDefinedKey:@"url" withString:self.url];
    [aParams addIfDefinedKey:@"data" withString:self.data];
}

- (void)dealloc
{
    [self->_url release];
    [self->_data release];
    [super dealloc];
}

@end

@implementation KalturaContext
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaContext"];
}

@end

@implementation KalturaConversionAttribute
@synthesize flavorParamsId = _flavorParamsId;
@synthesize name = _name;
@synthesize value = _value;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_flavorParamsId = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfFlavorParamsId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfValue
{
    return KFT_String;
}

- (void)setFlavorParamsIdFromString:(NSString*)aPropVal
{
    self.flavorParamsId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaConversionAttribute"];
    [aParams addIfDefinedKey:@"flavorParamsId" withInt:self.flavorParamsId];
    [aParams addIfDefinedKey:@"name" withString:self.name];
    [aParams addIfDefinedKey:@"value" withString:self.value];
}

- (void)dealloc
{
    [self->_name release];
    [self->_value release];
    [super dealloc];
}

@end

@implementation KalturaCropDimensions
@synthesize left = _left;
@synthesize top = _top;
@synthesize width = _width;
@synthesize height = _height;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_left = KALTURA_UNDEF_INT;
    self->_top = KALTURA_UNDEF_INT;
    self->_width = KALTURA_UNDEF_INT;
    self->_height = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfLeft
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfTop
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfWidth
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfHeight
{
    return KFT_Int;
}

- (void)setLeftFromString:(NSString*)aPropVal
{
    self.left = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setTopFromString:(NSString*)aPropVal
{
    self.top = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setWidthFromString:(NSString*)aPropVal
{
    self.width = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setHeightFromString:(NSString*)aPropVal
{
    self.height = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaCropDimensions"];
    [aParams addIfDefinedKey:@"left" withInt:self.left];
    [aParams addIfDefinedKey:@"top" withInt:self.top];
    [aParams addIfDefinedKey:@"width" withInt:self.width];
    [aParams addIfDefinedKey:@"height" withInt:self.height];
}

@end

@interface KalturaConversionProfile()
@property (nonatomic,assign) int id;
@property (nonatomic,assign) int partnerId;
@property (nonatomic,assign) int createdAt;
@property (nonatomic,assign) BOOL isPartnerDefault;
@end

@implementation KalturaConversionProfile
@synthesize id = _id;
@synthesize partnerId = _partnerId;
@synthesize status = _status;
@synthesize name = _name;
@synthesize systemName = _systemName;
@synthesize tags = _tags;
@synthesize description = _description;
@synthesize defaultEntryId = _defaultEntryId;
@synthesize createdAt = _createdAt;
@synthesize flavorParamsIds = _flavorParamsIds;
@synthesize isDefault = _isDefault;
@synthesize isPartnerDefault = _isPartnerDefault;
@synthesize cropDimensions = _cropDimensions;
@synthesize clipStart = _clipStart;
@synthesize clipDuration = _clipDuration;
@synthesize xslTransformation = _xslTransformation;
@synthesize storageProfileId = _storageProfileId;
@synthesize mediaParserType = _mediaParserType;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_id = KALTURA_UNDEF_INT;
    self->_partnerId = KALTURA_UNDEF_INT;
    self->_createdAt = KALTURA_UNDEF_INT;
    self->_isDefault = KALTURA_UNDEF_INT;
    self->_isPartnerDefault = KALTURA_UNDEF_BOOL;
    self->_clipStart = KALTURA_UNDEF_INT;
    self->_clipDuration = KALTURA_UNDEF_INT;
    self->_storageProfileId = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfStatus
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSystemName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTags
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDescription
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDefaultEntryId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfFlavorParamsIds
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfIsDefault
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfIsPartnerDefault
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfCropDimensions
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfCropDimensions
{
    return @"KalturaCropDimensions";
}

- (KalturaFieldType)getTypeOfClipStart
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfClipDuration
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfXslTransformation
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStorageProfileId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfMediaParserType
{
    return KFT_String;
}

- (void)setIdFromString:(NSString*)aPropVal
{
    self.id = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerIdFromString:(NSString*)aPropVal
{
    self.partnerId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtFromString:(NSString*)aPropVal
{
    self.createdAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setIsDefaultFromString:(NSString*)aPropVal
{
    self.isDefault = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setIsPartnerDefaultFromString:(NSString*)aPropVal
{
    self.isPartnerDefault = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)setClipStartFromString:(NSString*)aPropVal
{
    self.clipStart = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setClipDurationFromString:(NSString*)aPropVal
{
    self.clipDuration = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStorageProfileIdFromString:(NSString*)aPropVal
{
    self.storageProfileId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaConversionProfile"];
    [aParams addIfDefinedKey:@"status" withString:self.status];
    [aParams addIfDefinedKey:@"name" withString:self.name];
    [aParams addIfDefinedKey:@"systemName" withString:self.systemName];
    [aParams addIfDefinedKey:@"tags" withString:self.tags];
    [aParams addIfDefinedKey:@"description" withString:self.description];
    [aParams addIfDefinedKey:@"defaultEntryId" withString:self.defaultEntryId];
    [aParams addIfDefinedKey:@"flavorParamsIds" withString:self.flavorParamsIds];
    [aParams addIfDefinedKey:@"isDefault" withInt:self.isDefault];
    [aParams addIfDefinedKey:@"cropDimensions" withObject:self.cropDimensions];
    [aParams addIfDefinedKey:@"clipStart" withInt:self.clipStart];
    [aParams addIfDefinedKey:@"clipDuration" withInt:self.clipDuration];
    [aParams addIfDefinedKey:@"xslTransformation" withString:self.xslTransformation];
    [aParams addIfDefinedKey:@"storageProfileId" withInt:self.storageProfileId];
    [aParams addIfDefinedKey:@"mediaParserType" withString:self.mediaParserType];
}

- (void)dealloc
{
    [self->_status release];
    [self->_name release];
    [self->_systemName release];
    [self->_tags release];
    [self->_description release];
    [self->_defaultEntryId release];
    [self->_flavorParamsIds release];
    [self->_cropDimensions release];
    [self->_xslTransformation release];
    [self->_mediaParserType release];
    [super dealloc];
}

@end

@interface KalturaConversionProfileAssetParams()
@property (nonatomic,assign) int conversionProfileId;
@property (nonatomic,assign) int assetParamsId;
@end

@implementation KalturaConversionProfileAssetParams
@synthesize conversionProfileId = _conversionProfileId;
@synthesize assetParamsId = _assetParamsId;
@synthesize readyBehavior = _readyBehavior;
@synthesize origin = _origin;
@synthesize systemName = _systemName;
@synthesize forceNoneComplied = _forceNoneComplied;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_conversionProfileId = KALTURA_UNDEF_INT;
    self->_assetParamsId = KALTURA_UNDEF_INT;
    self->_readyBehavior = KALTURA_UNDEF_INT;
    self->_origin = KALTURA_UNDEF_INT;
    self->_forceNoneComplied = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfConversionProfileId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfAssetParamsId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfReadyBehavior
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfOrigin
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfSystemName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfForceNoneComplied
{
    return KFT_Int;
}

- (void)setConversionProfileIdFromString:(NSString*)aPropVal
{
    self.conversionProfileId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setAssetParamsIdFromString:(NSString*)aPropVal
{
    self.assetParamsId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setReadyBehaviorFromString:(NSString*)aPropVal
{
    self.readyBehavior = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setOriginFromString:(NSString*)aPropVal
{
    self.origin = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setForceNoneCompliedFromString:(NSString*)aPropVal
{
    self.forceNoneComplied = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaConversionProfileAssetParams"];
    [aParams addIfDefinedKey:@"readyBehavior" withInt:self.readyBehavior];
    [aParams addIfDefinedKey:@"origin" withInt:self.origin];
    [aParams addIfDefinedKey:@"systemName" withString:self.systemName];
    [aParams addIfDefinedKey:@"forceNoneComplied" withInt:self.forceNoneComplied];
}

- (void)dealloc
{
    [self->_systemName release];
    [super dealloc];
}

@end

@interface KalturaConversionProfileAssetParamsListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaConversionProfileAssetParamsListResponse
@synthesize objects = _objects;
@synthesize totalCount = _totalCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaConversionProfileAssetParams";
}

- (KalturaFieldType)getTypeOfTotalCount
{
    return KFT_Int;
}

- (void)setTotalCountFromString:(NSString*)aPropVal
{
    self.totalCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaConversionProfileAssetParamsListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@interface KalturaConversionProfileListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaConversionProfileListResponse
@synthesize objects = _objects;
@synthesize totalCount = _totalCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaConversionProfile";
}

- (KalturaFieldType)getTypeOfTotalCount
{
    return KFT_Int;
}

- (void)setTotalCountFromString:(NSString*)aPropVal
{
    self.totalCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaConversionProfileListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@implementation KalturaConvertCollectionFlavorData
@synthesize flavorAssetId = _flavorAssetId;
@synthesize flavorParamsOutputId = _flavorParamsOutputId;
@synthesize readyBehavior = _readyBehavior;
@synthesize videoBitrate = _videoBitrate;
@synthesize audioBitrate = _audioBitrate;
@synthesize destFileSyncLocalPath = _destFileSyncLocalPath;
@synthesize destFileSyncRemoteUrl = _destFileSyncRemoteUrl;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_flavorParamsOutputId = KALTURA_UNDEF_INT;
    self->_readyBehavior = KALTURA_UNDEF_INT;
    self->_videoBitrate = KALTURA_UNDEF_INT;
    self->_audioBitrate = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfFlavorAssetId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFlavorParamsOutputId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfReadyBehavior
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfVideoBitrate
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfAudioBitrate
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDestFileSyncLocalPath
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDestFileSyncRemoteUrl
{
    return KFT_String;
}

- (void)setFlavorParamsOutputIdFromString:(NSString*)aPropVal
{
    self.flavorParamsOutputId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setReadyBehaviorFromString:(NSString*)aPropVal
{
    self.readyBehavior = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setVideoBitrateFromString:(NSString*)aPropVal
{
    self.videoBitrate = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setAudioBitrateFromString:(NSString*)aPropVal
{
    self.audioBitrate = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaConvertCollectionFlavorData"];
    [aParams addIfDefinedKey:@"flavorAssetId" withString:self.flavorAssetId];
    [aParams addIfDefinedKey:@"flavorParamsOutputId" withInt:self.flavorParamsOutputId];
    [aParams addIfDefinedKey:@"readyBehavior" withInt:self.readyBehavior];
    [aParams addIfDefinedKey:@"videoBitrate" withInt:self.videoBitrate];
    [aParams addIfDefinedKey:@"audioBitrate" withInt:self.audioBitrate];
    [aParams addIfDefinedKey:@"destFileSyncLocalPath" withString:self.destFileSyncLocalPath];
    [aParams addIfDefinedKey:@"destFileSyncRemoteUrl" withString:self.destFileSyncRemoteUrl];
}

- (void)dealloc
{
    [self->_flavorAssetId release];
    [self->_destFileSyncLocalPath release];
    [self->_destFileSyncRemoteUrl release];
    [super dealloc];
}

@end

@implementation KalturaDataEntry
@synthesize dataContent = _dataContent;
@synthesize retrieveDataContentByGet = _retrieveDataContentByGet;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_retrieveDataContentByGet = KALTURA_UNDEF_BOOL;
    return self;
}

- (KalturaFieldType)getTypeOfDataContent
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfRetrieveDataContentByGet
{
    return KFT_Bool;
}

- (void)setRetrieveDataContentByGetFromString:(NSString*)aPropVal
{
    self.retrieveDataContentByGet = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDataEntry"];
    [aParams addIfDefinedKey:@"dataContent" withString:self.dataContent];
    [aParams addIfDefinedKey:@"retrieveDataContentByGet" withBool:self.retrieveDataContentByGet];
}

- (void)dealloc
{
    [self->_dataContent release];
    [super dealloc];
}

@end

@interface KalturaDataListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaDataListResponse
@synthesize objects = _objects;
@synthesize totalCount = _totalCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaDataEntry";
}

- (KalturaFieldType)getTypeOfTotalCount
{
    return KFT_Int;
}

- (void)setTotalCountFromString:(NSString*)aPropVal
{
    self.totalCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDataListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@interface KalturaEmailIngestionProfile()
@property (nonatomic,assign) int id;
@property (nonatomic,assign) int partnerId;
@property (nonatomic,assign) int status;
@property (nonatomic,copy) NSString* createdAt;
@end

@implementation KalturaEmailIngestionProfile
@synthesize id = _id;
@synthesize name = _name;
@synthesize description = _description;
@synthesize emailAddress = _emailAddress;
@synthesize mailboxId = _mailboxId;
@synthesize partnerId = _partnerId;
@synthesize conversionProfile2Id = _conversionProfile2Id;
@synthesize moderationStatus = _moderationStatus;
@synthesize status = _status;
@synthesize createdAt = _createdAt;
@synthesize defaultCategory = _defaultCategory;
@synthesize defaultUserId = _defaultUserId;
@synthesize defaultTags = _defaultTags;
@synthesize defaultAdminTags = _defaultAdminTags;
@synthesize maxAttachmentSizeKbytes = _maxAttachmentSizeKbytes;
@synthesize maxAttachmentsPerMail = _maxAttachmentsPerMail;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_id = KALTURA_UNDEF_INT;
    self->_partnerId = KALTURA_UNDEF_INT;
    self->_conversionProfile2Id = KALTURA_UNDEF_INT;
    self->_moderationStatus = KALTURA_UNDEF_INT;
    self->_status = KALTURA_UNDEF_INT;
    self->_maxAttachmentSizeKbytes = KALTURA_UNDEF_INT;
    self->_maxAttachmentsPerMail = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDescription
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfEmailAddress
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfMailboxId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPartnerId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfConversionProfile2Id
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfModerationStatus
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfStatus
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreatedAt
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDefaultCategory
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDefaultUserId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDefaultTags
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDefaultAdminTags
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfMaxAttachmentSizeKbytes
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfMaxAttachmentsPerMail
{
    return KFT_Int;
}

- (void)setIdFromString:(NSString*)aPropVal
{
    self.id = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerIdFromString:(NSString*)aPropVal
{
    self.partnerId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setConversionProfile2IdFromString:(NSString*)aPropVal
{
    self.conversionProfile2Id = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setModerationStatusFromString:(NSString*)aPropVal
{
    self.moderationStatus = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusFromString:(NSString*)aPropVal
{
    self.status = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setMaxAttachmentSizeKbytesFromString:(NSString*)aPropVal
{
    self.maxAttachmentSizeKbytes = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setMaxAttachmentsPerMailFromString:(NSString*)aPropVal
{
    self.maxAttachmentsPerMail = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaEmailIngestionProfile"];
    [aParams addIfDefinedKey:@"name" withString:self.name];
    [aParams addIfDefinedKey:@"description" withString:self.description];
    [aParams addIfDefinedKey:@"emailAddress" withString:self.emailAddress];
    [aParams addIfDefinedKey:@"mailboxId" withString:self.mailboxId];
    [aParams addIfDefinedKey:@"conversionProfile2Id" withInt:self.conversionProfile2Id];
    [aParams addIfDefinedKey:@"moderationStatus" withInt:self.moderationStatus];
    [aParams addIfDefinedKey:@"defaultCategory" withString:self.defaultCategory];
    [aParams addIfDefinedKey:@"defaultUserId" withString:self.defaultUserId];
    [aParams addIfDefinedKey:@"defaultTags" withString:self.defaultTags];
    [aParams addIfDefinedKey:@"defaultAdminTags" withString:self.defaultAdminTags];
    [aParams addIfDefinedKey:@"maxAttachmentSizeKbytes" withInt:self.maxAttachmentSizeKbytes];
    [aParams addIfDefinedKey:@"maxAttachmentsPerMail" withInt:self.maxAttachmentsPerMail];
}

- (void)dealloc
{
    [self->_name release];
    [self->_description release];
    [self->_emailAddress release];
    [self->_mailboxId release];
    [self->_createdAt release];
    [self->_defaultCategory release];
    [self->_defaultUserId release];
    [self->_defaultTags release];
    [self->_defaultAdminTags release];
    [super dealloc];
}

@end

@implementation KalturaValue
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaValue"];
}

@end

@implementation KalturaStringValue
@synthesize value = _value;

- (KalturaFieldType)getTypeOfValue
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaStringValue"];
    [aParams addIfDefinedKey:@"value" withString:self.value];
}

- (void)dealloc
{
    [self->_value release];
    [super dealloc];
}

@end

@interface KalturaFlavorAsset()
@property (nonatomic,assign) int width;
@property (nonatomic,assign) int height;
@property (nonatomic,assign) int bitrate;
@property (nonatomic,assign) double frameRate;
@property (nonatomic,assign) BOOL isOriginal;
@property (nonatomic,assign) BOOL isWeb;
@property (nonatomic,copy) NSString* containerFormat;
@property (nonatomic,copy) NSString* videoCodecId;
@property (nonatomic,assign) int status;
@end

@implementation KalturaFlavorAsset
@synthesize flavorParamsId = _flavorParamsId;
@synthesize width = _width;
@synthesize height = _height;
@synthesize bitrate = _bitrate;
@synthesize frameRate = _frameRate;
@synthesize isOriginal = _isOriginal;
@synthesize isWeb = _isWeb;
@synthesize containerFormat = _containerFormat;
@synthesize videoCodecId = _videoCodecId;
@synthesize status = _status;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_flavorParamsId = KALTURA_UNDEF_INT;
    self->_width = KALTURA_UNDEF_INT;
    self->_height = KALTURA_UNDEF_INT;
    self->_bitrate = KALTURA_UNDEF_INT;
    self->_frameRate = KALTURA_UNDEF_FLOAT;
    self->_isOriginal = KALTURA_UNDEF_BOOL;
    self->_isWeb = KALTURA_UNDEF_BOOL;
    self->_status = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfFlavorParamsId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfWidth
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfHeight
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfBitrate
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfFrameRate
{
    return KFT_Float;
}

- (KalturaFieldType)getTypeOfIsOriginal
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfIsWeb
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfContainerFormat
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfVideoCodecId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatus
{
    return KFT_Int;
}

- (void)setFlavorParamsIdFromString:(NSString*)aPropVal
{
    self.flavorParamsId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setWidthFromString:(NSString*)aPropVal
{
    self.width = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setHeightFromString:(NSString*)aPropVal
{
    self.height = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setBitrateFromString:(NSString*)aPropVal
{
    self.bitrate = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setFrameRateFromString:(NSString*)aPropVal
{
    self.frameRate = [KalturaSimpleTypeParser parseFloat:aPropVal];
}

- (void)setIsOriginalFromString:(NSString*)aPropVal
{
    self.isOriginal = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)setIsWebFromString:(NSString*)aPropVal
{
    self.isWeb = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)setStatusFromString:(NSString*)aPropVal
{
    self.status = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaFlavorAsset"];
    [aParams addIfDefinedKey:@"flavorParamsId" withInt:self.flavorParamsId];
}

- (void)dealloc
{
    [self->_containerFormat release];
    [self->_videoCodecId release];
    [super dealloc];
}

@end

@implementation KalturaEntryContextDataResult
@synthesize isSiteRestricted = _isSiteRestricted;
@synthesize isCountryRestricted = _isCountryRestricted;
@synthesize isSessionRestricted = _isSessionRestricted;
@synthesize isIpAddressRestricted = _isIpAddressRestricted;
@synthesize isUserAgentRestricted = _isUserAgentRestricted;
@synthesize previewLength = _previewLength;
@synthesize isScheduledNow = _isScheduledNow;
@synthesize isAdmin = _isAdmin;
@synthesize streamerType = _streamerType;
@synthesize mediaProtocol = _mediaProtocol;
@synthesize storageProfilesXML = _storageProfilesXML;
@synthesize accessControlMessages = _accessControlMessages;
@synthesize accessControlActions = _accessControlActions;
@synthesize flavorAssets = _flavorAssets;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_isSiteRestricted = KALTURA_UNDEF_BOOL;
    self->_isCountryRestricted = KALTURA_UNDEF_BOOL;
    self->_isSessionRestricted = KALTURA_UNDEF_BOOL;
    self->_isIpAddressRestricted = KALTURA_UNDEF_BOOL;
    self->_isUserAgentRestricted = KALTURA_UNDEF_BOOL;
    self->_previewLength = KALTURA_UNDEF_INT;
    self->_isScheduledNow = KALTURA_UNDEF_BOOL;
    self->_isAdmin = KALTURA_UNDEF_BOOL;
    return self;
}

- (KalturaFieldType)getTypeOfIsSiteRestricted
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfIsCountryRestricted
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfIsSessionRestricted
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfIsIpAddressRestricted
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfIsUserAgentRestricted
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfPreviewLength
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfIsScheduledNow
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfIsAdmin
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfStreamerType
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfMediaProtocol
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStorageProfilesXML
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAccessControlMessages
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfAccessControlMessages
{
    return @"KalturaString";
}

- (KalturaFieldType)getTypeOfAccessControlActions
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfAccessControlActions
{
    return @"KalturaAccessControlAction";
}

- (KalturaFieldType)getTypeOfFlavorAssets
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfFlavorAssets
{
    return @"KalturaFlavorAsset";
}

- (void)setIsSiteRestrictedFromString:(NSString*)aPropVal
{
    self.isSiteRestricted = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)setIsCountryRestrictedFromString:(NSString*)aPropVal
{
    self.isCountryRestricted = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)setIsSessionRestrictedFromString:(NSString*)aPropVal
{
    self.isSessionRestricted = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)setIsIpAddressRestrictedFromString:(NSString*)aPropVal
{
    self.isIpAddressRestricted = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)setIsUserAgentRestrictedFromString:(NSString*)aPropVal
{
    self.isUserAgentRestricted = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)setPreviewLengthFromString:(NSString*)aPropVal
{
    self.previewLength = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setIsScheduledNowFromString:(NSString*)aPropVal
{
    self.isScheduledNow = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)setIsAdminFromString:(NSString*)aPropVal
{
    self.isAdmin = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaEntryContextDataResult"];
    [aParams addIfDefinedKey:@"isSiteRestricted" withBool:self.isSiteRestricted];
    [aParams addIfDefinedKey:@"isCountryRestricted" withBool:self.isCountryRestricted];
    [aParams addIfDefinedKey:@"isSessionRestricted" withBool:self.isSessionRestricted];
    [aParams addIfDefinedKey:@"isIpAddressRestricted" withBool:self.isIpAddressRestricted];
    [aParams addIfDefinedKey:@"isUserAgentRestricted" withBool:self.isUserAgentRestricted];
    [aParams addIfDefinedKey:@"previewLength" withInt:self.previewLength];
    [aParams addIfDefinedKey:@"isScheduledNow" withBool:self.isScheduledNow];
    [aParams addIfDefinedKey:@"isAdmin" withBool:self.isAdmin];
    [aParams addIfDefinedKey:@"streamerType" withString:self.streamerType];
    [aParams addIfDefinedKey:@"mediaProtocol" withString:self.mediaProtocol];
    [aParams addIfDefinedKey:@"storageProfilesXML" withString:self.storageProfilesXML];
    [aParams addIfDefinedKey:@"accessControlMessages" withArray:self.accessControlMessages];
    [aParams addIfDefinedKey:@"accessControlActions" withArray:self.accessControlActions];
    [aParams addIfDefinedKey:@"flavorAssets" withArray:self.flavorAssets];
}

- (void)dealloc
{
    [self->_streamerType release];
    [self->_mediaProtocol release];
    [self->_storageProfilesXML release];
    [self->_accessControlMessages release];
    [self->_accessControlActions release];
    [self->_flavorAssets release];
    [super dealloc];
}

@end

@implementation KalturaObjectIdentifier
@synthesize extendedFeatures = _extendedFeatures;

- (KalturaFieldType)getTypeOfExtendedFeatures
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaObjectIdentifier"];
    [aParams addIfDefinedKey:@"extendedFeatures" withString:self.extendedFeatures];
}

- (void)dealloc
{
    [self->_extendedFeatures release];
    [super dealloc];
}

@end

@implementation KalturaExtendingItemMrssParameter
@synthesize xpath = _xpath;
@synthesize identifier = _identifier;
@synthesize extensionMode = _extensionMode;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_extensionMode = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfXpath
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfIdentifier
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfIdentifier
{
    return @"KalturaObjectIdentifier";
}

- (KalturaFieldType)getTypeOfExtensionMode
{
    return KFT_Int;
}

- (void)setExtensionModeFromString:(NSString*)aPropVal
{
    self.extensionMode = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaExtendingItemMrssParameter"];
    [aParams addIfDefinedKey:@"xpath" withString:self.xpath];
    [aParams addIfDefinedKey:@"identifier" withObject:self.identifier];
    [aParams addIfDefinedKey:@"extensionMode" withInt:self.extensionMode];
}

- (void)dealloc
{
    [self->_xpath release];
    [self->_identifier release];
    [super dealloc];
}

@end

@interface KalturaPlayableEntry()
@property (nonatomic,assign) int plays;
@property (nonatomic,assign) int views;
@property (nonatomic,assign) int width;
@property (nonatomic,assign) int height;
@property (nonatomic,assign) int duration;
@property (nonatomic,copy) NSString* durationType;
@end

@implementation KalturaPlayableEntry
@synthesize plays = _plays;
@synthesize views = _views;
@synthesize width = _width;
@synthesize height = _height;
@synthesize duration = _duration;
@synthesize msDuration = _msDuration;
@synthesize durationType = _durationType;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_plays = KALTURA_UNDEF_INT;
    self->_views = KALTURA_UNDEF_INT;
    self->_width = KALTURA_UNDEF_INT;
    self->_height = KALTURA_UNDEF_INT;
    self->_duration = KALTURA_UNDEF_INT;
    self->_msDuration = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfPlays
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfViews
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfWidth
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfHeight
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDuration
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfMsDuration
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDurationType
{
    return KFT_String;
}

- (void)setPlaysFromString:(NSString*)aPropVal
{
    self.plays = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setViewsFromString:(NSString*)aPropVal
{
    self.views = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setWidthFromString:(NSString*)aPropVal
{
    self.width = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setHeightFromString:(NSString*)aPropVal
{
    self.height = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDurationFromString:(NSString*)aPropVal
{
    self.duration = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setMsDurationFromString:(NSString*)aPropVal
{
    self.msDuration = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaPlayableEntry"];
    [aParams addIfDefinedKey:@"msDuration" withInt:self.msDuration];
}

- (void)dealloc
{
    [self->_durationType release];
    [super dealloc];
}

@end

@interface KalturaMediaEntry()
@property (nonatomic,assign) int mediaDate;
@property (nonatomic,copy) NSString* dataUrl;
@property (nonatomic,copy) NSString* flavorParamsIds;
@end

@implementation KalturaMediaEntry
@synthesize mediaType = _mediaType;
@synthesize conversionQuality = _conversionQuality;
@synthesize sourceType = _sourceType;
@synthesize searchProviderType = _searchProviderType;
@synthesize searchProviderId = _searchProviderId;
@synthesize creditUserName = _creditUserName;
@synthesize creditUrl = _creditUrl;
@synthesize mediaDate = _mediaDate;
@synthesize dataUrl = _dataUrl;
@synthesize flavorParamsIds = _flavorParamsIds;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_mediaType = KALTURA_UNDEF_INT;
    self->_searchProviderType = KALTURA_UNDEF_INT;
    self->_mediaDate = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfMediaType
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfConversionQuality
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSourceType
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSearchProviderType
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfSearchProviderId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreditUserName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreditUrl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfMediaDate
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDataUrl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFlavorParamsIds
{
    return KFT_String;
}

- (void)setMediaTypeFromString:(NSString*)aPropVal
{
    self.mediaType = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setSearchProviderTypeFromString:(NSString*)aPropVal
{
    self.searchProviderType = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setMediaDateFromString:(NSString*)aPropVal
{
    self.mediaDate = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaMediaEntry"];
    [aParams addIfDefinedKey:@"mediaType" withInt:self.mediaType];
    [aParams addIfDefinedKey:@"conversionQuality" withString:self.conversionQuality];
    [aParams addIfDefinedKey:@"sourceType" withString:self.sourceType];
    [aParams addIfDefinedKey:@"searchProviderType" withInt:self.searchProviderType];
    [aParams addIfDefinedKey:@"searchProviderId" withString:self.searchProviderId];
    [aParams addIfDefinedKey:@"creditUserName" withString:self.creditUserName];
    [aParams addIfDefinedKey:@"creditUrl" withString:self.creditUrl];
}

- (void)dealloc
{
    [self->_conversionQuality release];
    [self->_sourceType release];
    [self->_searchProviderId release];
    [self->_creditUserName release];
    [self->_creditUrl release];
    [self->_dataUrl release];
    [self->_flavorParamsIds release];
    [super dealloc];
}

@end

@implementation KalturaFeatureStatus
@synthesize type = _type;
@synthesize value = _value;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_type = KALTURA_UNDEF_INT;
    self->_value = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfType
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfValue
{
    return KFT_Int;
}

- (void)setTypeFromString:(NSString*)aPropVal
{
    self.type = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setValueFromString:(NSString*)aPropVal
{
    self.value = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaFeatureStatus"];
    [aParams addIfDefinedKey:@"type" withInt:self.type];
    [aParams addIfDefinedKey:@"value" withInt:self.value];
}

@end

@interface KalturaFeatureStatusListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaFeatureStatusListResponse
@synthesize objects = _objects;
@synthesize totalCount = _totalCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaFeatureStatus";
}

- (KalturaFieldType)getTypeOfTotalCount
{
    return KFT_Int;
}

- (void)setTotalCountFromString:(NSString*)aPropVal
{
    self.totalCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaFeatureStatusListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@implementation KalturaSearchItem
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaSearchItem"];
}

@end

@implementation KalturaFilter
@synthesize orderBy = _orderBy;
@synthesize advancedSearch = _advancedSearch;

- (KalturaFieldType)getTypeOfOrderBy
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAdvancedSearch
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfAdvancedSearch
{
    return @"KalturaSearchItem";
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaFilter"];
    [aParams addIfDefinedKey:@"orderBy" withString:self.orderBy];
    [aParams addIfDefinedKey:@"advancedSearch" withObject:self.advancedSearch];
}

- (void)dealloc
{
    [self->_orderBy release];
    [self->_advancedSearch release];
    [super dealloc];
}

@end

@implementation KalturaFilterPager
@synthesize pageSize = _pageSize;
@synthesize pageIndex = _pageIndex;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_pageSize = KALTURA_UNDEF_INT;
    self->_pageIndex = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfPageSize
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPageIndex
{
    return KFT_Int;
}

- (void)setPageSizeFromString:(NSString*)aPropVal
{
    self.pageSize = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPageIndexFromString:(NSString*)aPropVal
{
    self.pageIndex = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaFilterPager"];
    [aParams addIfDefinedKey:@"pageSize" withInt:self.pageSize];
    [aParams addIfDefinedKey:@"pageIndex" withInt:self.pageIndex];
}

@end

@interface KalturaFlavorAssetListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaFlavorAssetListResponse
@synthesize objects = _objects;
@synthesize totalCount = _totalCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaFlavorAsset";
}

- (KalturaFieldType)getTypeOfTotalCount
{
    return KFT_Int;
}

- (void)setTotalCountFromString:(NSString*)aPropVal
{
    self.totalCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaFlavorAssetListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@implementation KalturaFlavorParams
@synthesize videoCodec = _videoCodec;
@synthesize videoBitrate = _videoBitrate;
@synthesize audioCodec = _audioCodec;
@synthesize audioBitrate = _audioBitrate;
@synthesize audioChannels = _audioChannels;
@synthesize audioSampleRate = _audioSampleRate;
@synthesize width = _width;
@synthesize height = _height;
@synthesize frameRate = _frameRate;
@synthesize gopSize = _gopSize;
@synthesize conversionEngines = _conversionEngines;
@synthesize conversionEnginesExtraParams = _conversionEnginesExtraParams;
@synthesize twoPass = _twoPass;
@synthesize deinterlice = _deinterlice;
@synthesize rotate = _rotate;
@synthesize operators = _operators;
@synthesize engineVersion = _engineVersion;
@synthesize format = _format;
@synthesize aspectRatioProcessingMode = _aspectRatioProcessingMode;
@synthesize forceFrameToMultiplication16 = _forceFrameToMultiplication16;
@synthesize isGopInSec = _isGopInSec;
@synthesize isAvoidVideoShrinkFramesizeToSource = _isAvoidVideoShrinkFramesizeToSource;
@synthesize isAvoidVideoShrinkBitrateToSource = _isAvoidVideoShrinkBitrateToSource;
@synthesize isVideoFrameRateForLowBrAppleHls = _isVideoFrameRateForLowBrAppleHls;
@synthesize anamorphicPixels = _anamorphicPixels;
@synthesize isAvoidForcedKeyFrames = _isAvoidForcedKeyFrames;
@synthesize maxFrameRate = _maxFrameRate;
@synthesize videoConstantBitrate = _videoConstantBitrate;
@synthesize videoBitrateTolerance = _videoBitrateTolerance;
@synthesize clipOffset = _clipOffset;
@synthesize clipDuration = _clipDuration;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_videoBitrate = KALTURA_UNDEF_INT;
    self->_audioBitrate = KALTURA_UNDEF_INT;
    self->_audioChannels = KALTURA_UNDEF_INT;
    self->_audioSampleRate = KALTURA_UNDEF_INT;
    self->_width = KALTURA_UNDEF_INT;
    self->_height = KALTURA_UNDEF_INT;
    self->_frameRate = KALTURA_UNDEF_INT;
    self->_gopSize = KALTURA_UNDEF_INT;
    self->_twoPass = KALTURA_UNDEF_BOOL;
    self->_deinterlice = KALTURA_UNDEF_INT;
    self->_rotate = KALTURA_UNDEF_INT;
    self->_engineVersion = KALTURA_UNDEF_INT;
    self->_aspectRatioProcessingMode = KALTURA_UNDEF_INT;
    self->_forceFrameToMultiplication16 = KALTURA_UNDEF_INT;
    self->_isGopInSec = KALTURA_UNDEF_INT;
    self->_isAvoidVideoShrinkFramesizeToSource = KALTURA_UNDEF_INT;
    self->_isAvoidVideoShrinkBitrateToSource = KALTURA_UNDEF_INT;
    self->_isVideoFrameRateForLowBrAppleHls = KALTURA_UNDEF_INT;
    self->_anamorphicPixels = KALTURA_UNDEF_FLOAT;
    self->_isAvoidForcedKeyFrames = KALTURA_UNDEF_INT;
    self->_maxFrameRate = KALTURA_UNDEF_INT;
    self->_videoConstantBitrate = KALTURA_UNDEF_INT;
    self->_videoBitrateTolerance = KALTURA_UNDEF_INT;
    self->_clipOffset = KALTURA_UNDEF_INT;
    self->_clipDuration = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfVideoCodec
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfVideoBitrate
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfAudioCodec
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAudioBitrate
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfAudioChannels
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfAudioSampleRate
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfWidth
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfHeight
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfFrameRate
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfGopSize
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfConversionEngines
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfConversionEnginesExtraParams
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTwoPass
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfDeinterlice
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfRotate
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfOperators
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfEngineVersion
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfFormat
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAspectRatioProcessingMode
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfForceFrameToMultiplication16
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfIsGopInSec
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfIsAvoidVideoShrinkFramesizeToSource
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfIsAvoidVideoShrinkBitrateToSource
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfIsVideoFrameRateForLowBrAppleHls
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfAnamorphicPixels
{
    return KFT_Float;
}

- (KalturaFieldType)getTypeOfIsAvoidForcedKeyFrames
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfMaxFrameRate
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfVideoConstantBitrate
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfVideoBitrateTolerance
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfClipOffset
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfClipDuration
{
    return KFT_Int;
}

- (void)setVideoBitrateFromString:(NSString*)aPropVal
{
    self.videoBitrate = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setAudioBitrateFromString:(NSString*)aPropVal
{
    self.audioBitrate = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setAudioChannelsFromString:(NSString*)aPropVal
{
    self.audioChannels = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setAudioSampleRateFromString:(NSString*)aPropVal
{
    self.audioSampleRate = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setWidthFromString:(NSString*)aPropVal
{
    self.width = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setHeightFromString:(NSString*)aPropVal
{
    self.height = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setFrameRateFromString:(NSString*)aPropVal
{
    self.frameRate = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setGopSizeFromString:(NSString*)aPropVal
{
    self.gopSize = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setTwoPassFromString:(NSString*)aPropVal
{
    self.twoPass = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)setDeinterliceFromString:(NSString*)aPropVal
{
    self.deinterlice = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setRotateFromString:(NSString*)aPropVal
{
    self.rotate = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setEngineVersionFromString:(NSString*)aPropVal
{
    self.engineVersion = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setAspectRatioProcessingModeFromString:(NSString*)aPropVal
{
    self.aspectRatioProcessingMode = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setForceFrameToMultiplication16FromString:(NSString*)aPropVal
{
    self.forceFrameToMultiplication16 = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setIsGopInSecFromString:(NSString*)aPropVal
{
    self.isGopInSec = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setIsAvoidVideoShrinkFramesizeToSourceFromString:(NSString*)aPropVal
{
    self.isAvoidVideoShrinkFramesizeToSource = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setIsAvoidVideoShrinkBitrateToSourceFromString:(NSString*)aPropVal
{
    self.isAvoidVideoShrinkBitrateToSource = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setIsVideoFrameRateForLowBrAppleHlsFromString:(NSString*)aPropVal
{
    self.isVideoFrameRateForLowBrAppleHls = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setAnamorphicPixelsFromString:(NSString*)aPropVal
{
    self.anamorphicPixels = [KalturaSimpleTypeParser parseFloat:aPropVal];
}

- (void)setIsAvoidForcedKeyFramesFromString:(NSString*)aPropVal
{
    self.isAvoidForcedKeyFrames = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setMaxFrameRateFromString:(NSString*)aPropVal
{
    self.maxFrameRate = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setVideoConstantBitrateFromString:(NSString*)aPropVal
{
    self.videoConstantBitrate = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setVideoBitrateToleranceFromString:(NSString*)aPropVal
{
    self.videoBitrateTolerance = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setClipOffsetFromString:(NSString*)aPropVal
{
    self.clipOffset = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setClipDurationFromString:(NSString*)aPropVal
{
    self.clipDuration = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaFlavorParams"];
    [aParams addIfDefinedKey:@"videoCodec" withString:self.videoCodec];
    [aParams addIfDefinedKey:@"videoBitrate" withInt:self.videoBitrate];
    [aParams addIfDefinedKey:@"audioCodec" withString:self.audioCodec];
    [aParams addIfDefinedKey:@"audioBitrate" withInt:self.audioBitrate];
    [aParams addIfDefinedKey:@"audioChannels" withInt:self.audioChannels];
    [aParams addIfDefinedKey:@"audioSampleRate" withInt:self.audioSampleRate];
    [aParams addIfDefinedKey:@"width" withInt:self.width];
    [aParams addIfDefinedKey:@"height" withInt:self.height];
    [aParams addIfDefinedKey:@"frameRate" withInt:self.frameRate];
    [aParams addIfDefinedKey:@"gopSize" withInt:self.gopSize];
    [aParams addIfDefinedKey:@"conversionEngines" withString:self.conversionEngines];
    [aParams addIfDefinedKey:@"conversionEnginesExtraParams" withString:self.conversionEnginesExtraParams];
    [aParams addIfDefinedKey:@"twoPass" withBool:self.twoPass];
    [aParams addIfDefinedKey:@"deinterlice" withInt:self.deinterlice];
    [aParams addIfDefinedKey:@"rotate" withInt:self.rotate];
    [aParams addIfDefinedKey:@"operators" withString:self.operators];
    [aParams addIfDefinedKey:@"engineVersion" withInt:self.engineVersion];
    [aParams addIfDefinedKey:@"format" withString:self.format];
    [aParams addIfDefinedKey:@"aspectRatioProcessingMode" withInt:self.aspectRatioProcessingMode];
    [aParams addIfDefinedKey:@"forceFrameToMultiplication16" withInt:self.forceFrameToMultiplication16];
    [aParams addIfDefinedKey:@"isGopInSec" withInt:self.isGopInSec];
    [aParams addIfDefinedKey:@"isAvoidVideoShrinkFramesizeToSource" withInt:self.isAvoidVideoShrinkFramesizeToSource];
    [aParams addIfDefinedKey:@"isAvoidVideoShrinkBitrateToSource" withInt:self.isAvoidVideoShrinkBitrateToSource];
    [aParams addIfDefinedKey:@"isVideoFrameRateForLowBrAppleHls" withInt:self.isVideoFrameRateForLowBrAppleHls];
    [aParams addIfDefinedKey:@"anamorphicPixels" withFloat:self.anamorphicPixels];
    [aParams addIfDefinedKey:@"isAvoidForcedKeyFrames" withInt:self.isAvoidForcedKeyFrames];
    [aParams addIfDefinedKey:@"maxFrameRate" withInt:self.maxFrameRate];
    [aParams addIfDefinedKey:@"videoConstantBitrate" withInt:self.videoConstantBitrate];
    [aParams addIfDefinedKey:@"videoBitrateTolerance" withInt:self.videoBitrateTolerance];
    [aParams addIfDefinedKey:@"clipOffset" withInt:self.clipOffset];
    [aParams addIfDefinedKey:@"clipDuration" withInt:self.clipDuration];
}

- (void)dealloc
{
    [self->_videoCodec release];
    [self->_audioCodec release];
    [self->_conversionEngines release];
    [self->_conversionEnginesExtraParams release];
    [self->_operators release];
    [self->_format release];
    [super dealloc];
}

@end

@implementation KalturaFlavorAssetWithParams
@synthesize flavorAsset = _flavorAsset;
@synthesize flavorParams = _flavorParams;
@synthesize entryId = _entryId;

- (KalturaFieldType)getTypeOfFlavorAsset
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfFlavorAsset
{
    return @"KalturaFlavorAsset";
}

- (KalturaFieldType)getTypeOfFlavorParams
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfFlavorParams
{
    return @"KalturaFlavorParams";
}

- (KalturaFieldType)getTypeOfEntryId
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaFlavorAssetWithParams"];
    [aParams addIfDefinedKey:@"flavorAsset" withObject:self.flavorAsset];
    [aParams addIfDefinedKey:@"flavorParams" withObject:self.flavorParams];
    [aParams addIfDefinedKey:@"entryId" withString:self.entryId];
}

- (void)dealloc
{
    [self->_flavorAsset release];
    [self->_flavorParams release];
    [self->_entryId release];
    [super dealloc];
}

@end

@interface KalturaFlavorParamsListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaFlavorParamsListResponse
@synthesize objects = _objects;
@synthesize totalCount = _totalCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaFlavorParams";
}

- (KalturaFieldType)getTypeOfTotalCount
{
    return KFT_Int;
}

- (void)setTotalCountFromString:(NSString*)aPropVal
{
    self.totalCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaFlavorParamsListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@implementation KalturaFlavorParamsOutput
@synthesize flavorParamsId = _flavorParamsId;
@synthesize commandLinesStr = _commandLinesStr;
@synthesize flavorParamsVersion = _flavorParamsVersion;
@synthesize flavorAssetId = _flavorAssetId;
@synthesize flavorAssetVersion = _flavorAssetVersion;
@synthesize readyBehavior = _readyBehavior;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_flavorParamsId = KALTURA_UNDEF_INT;
    self->_readyBehavior = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfFlavorParamsId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCommandLinesStr
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFlavorParamsVersion
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFlavorAssetId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFlavorAssetVersion
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfReadyBehavior
{
    return KFT_Int;
}

- (void)setFlavorParamsIdFromString:(NSString*)aPropVal
{
    self.flavorParamsId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setReadyBehaviorFromString:(NSString*)aPropVal
{
    self.readyBehavior = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaFlavorParamsOutput"];
    [aParams addIfDefinedKey:@"flavorParamsId" withInt:self.flavorParamsId];
    [aParams addIfDefinedKey:@"commandLinesStr" withString:self.commandLinesStr];
    [aParams addIfDefinedKey:@"flavorParamsVersion" withString:self.flavorParamsVersion];
    [aParams addIfDefinedKey:@"flavorAssetId" withString:self.flavorAssetId];
    [aParams addIfDefinedKey:@"flavorAssetVersion" withString:self.flavorAssetVersion];
    [aParams addIfDefinedKey:@"readyBehavior" withInt:self.readyBehavior];
}

- (void)dealloc
{
    [self->_commandLinesStr release];
    [self->_flavorParamsVersion release];
    [self->_flavorAssetId release];
    [self->_flavorAssetVersion release];
    [super dealloc];
}

@end

@interface KalturaFlavorParamsOutputListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaFlavorParamsOutputListResponse
@synthesize objects = _objects;
@synthesize totalCount = _totalCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaFlavorParamsOutput";
}

- (KalturaFieldType)getTypeOfTotalCount
{
    return KFT_Int;
}

- (void)setTotalCountFromString:(NSString*)aPropVal
{
    self.totalCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaFlavorParamsOutputListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@implementation KalturaJobData
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaJobData"];
}

@end

@implementation KalturaLiveStreamBitrate
@synthesize bitrate = _bitrate;
@synthesize width = _width;
@synthesize height = _height;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_bitrate = KALTURA_UNDEF_INT;
    self->_width = KALTURA_UNDEF_INT;
    self->_height = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfBitrate
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfWidth
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfHeight
{
    return KFT_Int;
}

- (void)setBitrateFromString:(NSString*)aPropVal
{
    self.bitrate = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setWidthFromString:(NSString*)aPropVal
{
    self.width = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setHeightFromString:(NSString*)aPropVal
{
    self.height = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaLiveStreamBitrate"];
    [aParams addIfDefinedKey:@"bitrate" withInt:self.bitrate];
    [aParams addIfDefinedKey:@"width" withInt:self.width];
    [aParams addIfDefinedKey:@"height" withInt:self.height];
}

@end

@implementation KalturaLiveStreamConfiguration
@synthesize protocol = _protocol;
@synthesize url = _url;

- (KalturaFieldType)getTypeOfProtocol
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfUrl
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaLiveStreamConfiguration"];
    [aParams addIfDefinedKey:@"protocol" withString:self.protocol];
    [aParams addIfDefinedKey:@"url" withString:self.url];
}

- (void)dealloc
{
    [self->_protocol release];
    [self->_url release];
    [super dealloc];
}

@end

@interface KalturaLiveStreamEntry()
@property (nonatomic,copy) NSString* streamRemoteId;
@property (nonatomic,copy) NSString* streamRemoteBackupId;
@end

@implementation KalturaLiveStreamEntry
@synthesize offlineMessage = _offlineMessage;
@synthesize streamRemoteId = _streamRemoteId;
@synthesize streamRemoteBackupId = _streamRemoteBackupId;
@synthesize bitrates = _bitrates;
@synthesize primaryBroadcastingUrl = _primaryBroadcastingUrl;
@synthesize secondaryBroadcastingUrl = _secondaryBroadcastingUrl;
@synthesize streamName = _streamName;
@synthesize streamUrl = _streamUrl;
@synthesize hlsStreamUrl = _hlsStreamUrl;
@synthesize dvrStatus = _dvrStatus;
@synthesize dvrWindow = _dvrWindow;
@synthesize urlManager = _urlManager;
@synthesize liveStreamConfigurations = _liveStreamConfigurations;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_dvrStatus = KALTURA_UNDEF_INT;
    self->_dvrWindow = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfOfflineMessage
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStreamRemoteId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStreamRemoteBackupId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfBitrates
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfBitrates
{
    return @"KalturaLiveStreamBitrate";
}

- (KalturaFieldType)getTypeOfPrimaryBroadcastingUrl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSecondaryBroadcastingUrl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStreamName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStreamUrl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfHlsStreamUrl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDvrStatus
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDvrWindow
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUrlManager
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfLiveStreamConfigurations
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfLiveStreamConfigurations
{
    return @"KalturaLiveStreamConfiguration";
}

- (void)setDvrStatusFromString:(NSString*)aPropVal
{
    self.dvrStatus = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDvrWindowFromString:(NSString*)aPropVal
{
    self.dvrWindow = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaLiveStreamEntry"];
    [aParams addIfDefinedKey:@"offlineMessage" withString:self.offlineMessage];
    [aParams addIfDefinedKey:@"bitrates" withArray:self.bitrates];
    [aParams addIfDefinedKey:@"primaryBroadcastingUrl" withString:self.primaryBroadcastingUrl];
    [aParams addIfDefinedKey:@"secondaryBroadcastingUrl" withString:self.secondaryBroadcastingUrl];
    [aParams addIfDefinedKey:@"streamName" withString:self.streamName];
    [aParams addIfDefinedKey:@"streamUrl" withString:self.streamUrl];
    [aParams addIfDefinedKey:@"hlsStreamUrl" withString:self.hlsStreamUrl];
    [aParams addIfDefinedKey:@"dvrStatus" withInt:self.dvrStatus];
    [aParams addIfDefinedKey:@"dvrWindow" withInt:self.dvrWindow];
    [aParams addIfDefinedKey:@"urlManager" withString:self.urlManager];
    [aParams addIfDefinedKey:@"liveStreamConfigurations" withArray:self.liveStreamConfigurations];
}

- (void)dealloc
{
    [self->_offlineMessage release];
    [self->_streamRemoteId release];
    [self->_streamRemoteBackupId release];
    [self->_bitrates release];
    [self->_primaryBroadcastingUrl release];
    [self->_secondaryBroadcastingUrl release];
    [self->_streamName release];
    [self->_streamUrl release];
    [self->_hlsStreamUrl release];
    [self->_urlManager release];
    [self->_liveStreamConfigurations release];
    [super dealloc];
}

@end

@interface KalturaLiveStreamListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaLiveStreamListResponse
@synthesize objects = _objects;
@synthesize totalCount = _totalCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaLiveStreamEntry";
}

- (KalturaFieldType)getTypeOfTotalCount
{
    return KFT_Int;
}

- (void)setTotalCountFromString:(NSString*)aPropVal
{
    self.totalCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaLiveStreamListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@implementation KalturaBaseEntryBaseFilter
@synthesize idEqual = _idEqual;
@synthesize idIn = _idIn;
@synthesize idNotIn = _idNotIn;
@synthesize nameLike = _nameLike;
@synthesize nameMultiLikeOr = _nameMultiLikeOr;
@synthesize nameMultiLikeAnd = _nameMultiLikeAnd;
@synthesize nameEqual = _nameEqual;
@synthesize partnerIdEqual = _partnerIdEqual;
@synthesize partnerIdIn = _partnerIdIn;
@synthesize userIdEqual = _userIdEqual;
@synthesize creatorIdEqual = _creatorIdEqual;
@synthesize tagsLike = _tagsLike;
@synthesize tagsMultiLikeOr = _tagsMultiLikeOr;
@synthesize tagsMultiLikeAnd = _tagsMultiLikeAnd;
@synthesize adminTagsLike = _adminTagsLike;
@synthesize adminTagsMultiLikeOr = _adminTagsMultiLikeOr;
@synthesize adminTagsMultiLikeAnd = _adminTagsMultiLikeAnd;
@synthesize categoriesMatchAnd = _categoriesMatchAnd;
@synthesize categoriesMatchOr = _categoriesMatchOr;
@synthesize categoriesIdsMatchAnd = _categoriesIdsMatchAnd;
@synthesize categoriesIdsMatchOr = _categoriesIdsMatchOr;
@synthesize statusEqual = _statusEqual;
@synthesize statusNotEqual = _statusNotEqual;
@synthesize statusIn = _statusIn;
@synthesize statusNotIn = _statusNotIn;
@synthesize moderationStatusEqual = _moderationStatusEqual;
@synthesize moderationStatusNotEqual = _moderationStatusNotEqual;
@synthesize moderationStatusIn = _moderationStatusIn;
@synthesize moderationStatusNotIn = _moderationStatusNotIn;
@synthesize typeEqual = _typeEqual;
@synthesize typeIn = _typeIn;
@synthesize createdAtGreaterThanOrEqual = _createdAtGreaterThanOrEqual;
@synthesize createdAtLessThanOrEqual = _createdAtLessThanOrEqual;
@synthesize updatedAtGreaterThanOrEqual = _updatedAtGreaterThanOrEqual;
@synthesize updatedAtLessThanOrEqual = _updatedAtLessThanOrEqual;
@synthesize totalRankLessThanOrEqual = _totalRankLessThanOrEqual;
@synthesize totalRankGreaterThanOrEqual = _totalRankGreaterThanOrEqual;
@synthesize groupIdEqual = _groupIdEqual;
@synthesize searchTextMatchAnd = _searchTextMatchAnd;
@synthesize searchTextMatchOr = _searchTextMatchOr;
@synthesize accessControlIdEqual = _accessControlIdEqual;
@synthesize accessControlIdIn = _accessControlIdIn;
@synthesize startDateGreaterThanOrEqual = _startDateGreaterThanOrEqual;
@synthesize startDateLessThanOrEqual = _startDateLessThanOrEqual;
@synthesize startDateGreaterThanOrEqualOrNull = _startDateGreaterThanOrEqualOrNull;
@synthesize startDateLessThanOrEqualOrNull = _startDateLessThanOrEqualOrNull;
@synthesize endDateGreaterThanOrEqual = _endDateGreaterThanOrEqual;
@synthesize endDateLessThanOrEqual = _endDateLessThanOrEqual;
@synthesize endDateGreaterThanOrEqualOrNull = _endDateGreaterThanOrEqualOrNull;
@synthesize endDateLessThanOrEqualOrNull = _endDateLessThanOrEqualOrNull;
@synthesize referenceIdEqual = _referenceIdEqual;
@synthesize referenceIdIn = _referenceIdIn;
@synthesize replacingEntryIdEqual = _replacingEntryIdEqual;
@synthesize replacingEntryIdIn = _replacingEntryIdIn;
@synthesize replacedEntryIdEqual = _replacedEntryIdEqual;
@synthesize replacedEntryIdIn = _replacedEntryIdIn;
@synthesize replacementStatusEqual = _replacementStatusEqual;
@synthesize replacementStatusIn = _replacementStatusIn;
@synthesize partnerSortValueGreaterThanOrEqual = _partnerSortValueGreaterThanOrEqual;
@synthesize partnerSortValueLessThanOrEqual = _partnerSortValueLessThanOrEqual;
@synthesize rootEntryIdEqual = _rootEntryIdEqual;
@synthesize rootEntryIdIn = _rootEntryIdIn;
@synthesize tagsNameMultiLikeOr = _tagsNameMultiLikeOr;
@synthesize tagsAdminTagsMultiLikeOr = _tagsAdminTagsMultiLikeOr;
@synthesize tagsAdminTagsNameMultiLikeOr = _tagsAdminTagsNameMultiLikeOr;
@synthesize tagsNameMultiLikeAnd = _tagsNameMultiLikeAnd;
@synthesize tagsAdminTagsMultiLikeAnd = _tagsAdminTagsMultiLikeAnd;
@synthesize tagsAdminTagsNameMultiLikeAnd = _tagsAdminTagsNameMultiLikeAnd;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_partnerIdEqual = KALTURA_UNDEF_INT;
    self->_moderationStatusEqual = KALTURA_UNDEF_INT;
    self->_moderationStatusNotEqual = KALTURA_UNDEF_INT;
    self->_createdAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_createdAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_updatedAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_updatedAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_totalRankLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_totalRankGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_groupIdEqual = KALTURA_UNDEF_INT;
    self->_accessControlIdEqual = KALTURA_UNDEF_INT;
    self->_startDateGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_startDateLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_startDateGreaterThanOrEqualOrNull = KALTURA_UNDEF_INT;
    self->_startDateLessThanOrEqualOrNull = KALTURA_UNDEF_INT;
    self->_endDateGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_endDateLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_endDateGreaterThanOrEqualOrNull = KALTURA_UNDEF_INT;
    self->_endDateLessThanOrEqualOrNull = KALTURA_UNDEF_INT;
    self->_partnerSortValueGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_partnerSortValueLessThanOrEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfIdEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfIdNotIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfNameLike
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfNameMultiLikeOr
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfNameMultiLikeAnd
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfNameEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPartnerIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfUserIdEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreatorIdEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTagsLike
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTagsMultiLikeOr
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTagsMultiLikeAnd
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAdminTagsLike
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAdminTagsMultiLikeOr
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAdminTagsMultiLikeAnd
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCategoriesMatchAnd
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCategoriesMatchOr
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCategoriesIdsMatchAnd
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCategoriesIdsMatchOr
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatusEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatusNotEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatusIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatusNotIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfModerationStatusEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfModerationStatusNotEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfModerationStatusIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfModerationStatusNotIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTypeEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTypeIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfTotalRankLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfTotalRankGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfGroupIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfSearchTextMatchAnd
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSearchTextMatchOr
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAccessControlIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfAccessControlIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStartDateGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfStartDateLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfStartDateGreaterThanOrEqualOrNull
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfStartDateLessThanOrEqualOrNull
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfEndDateGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfEndDateLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfEndDateGreaterThanOrEqualOrNull
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfEndDateLessThanOrEqualOrNull
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfReferenceIdEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfReferenceIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfReplacingEntryIdEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfReplacingEntryIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfReplacedEntryIdEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfReplacedEntryIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfReplacementStatusEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfReplacementStatusIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPartnerSortValueGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerSortValueLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfRootEntryIdEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfRootEntryIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTagsNameMultiLikeOr
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTagsAdminTagsMultiLikeOr
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTagsAdminTagsNameMultiLikeOr
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTagsNameMultiLikeAnd
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTagsAdminTagsMultiLikeAnd
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTagsAdminTagsNameMultiLikeAnd
{
    return KFT_String;
}

- (void)setPartnerIdEqualFromString:(NSString*)aPropVal
{
    self.partnerIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setModerationStatusEqualFromString:(NSString*)aPropVal
{
    self.moderationStatusEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setModerationStatusNotEqualFromString:(NSString*)aPropVal
{
    self.moderationStatusNotEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.updatedAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.updatedAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setTotalRankLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.totalRankLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setTotalRankGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.totalRankGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setGroupIdEqualFromString:(NSString*)aPropVal
{
    self.groupIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setAccessControlIdEqualFromString:(NSString*)aPropVal
{
    self.accessControlIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStartDateGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.startDateGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStartDateLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.startDateLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStartDateGreaterThanOrEqualOrNullFromString:(NSString*)aPropVal
{
    self.startDateGreaterThanOrEqualOrNull = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStartDateLessThanOrEqualOrNullFromString:(NSString*)aPropVal
{
    self.startDateLessThanOrEqualOrNull = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setEndDateGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.endDateGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setEndDateLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.endDateLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setEndDateGreaterThanOrEqualOrNullFromString:(NSString*)aPropVal
{
    self.endDateGreaterThanOrEqualOrNull = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setEndDateLessThanOrEqualOrNullFromString:(NSString*)aPropVal
{
    self.endDateLessThanOrEqualOrNull = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerSortValueGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.partnerSortValueGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerSortValueLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.partnerSortValueLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaBaseEntryBaseFilter"];
    [aParams addIfDefinedKey:@"idEqual" withString:self.idEqual];
    [aParams addIfDefinedKey:@"idIn" withString:self.idIn];
    [aParams addIfDefinedKey:@"idNotIn" withString:self.idNotIn];
    [aParams addIfDefinedKey:@"nameLike" withString:self.nameLike];
    [aParams addIfDefinedKey:@"nameMultiLikeOr" withString:self.nameMultiLikeOr];
    [aParams addIfDefinedKey:@"nameMultiLikeAnd" withString:self.nameMultiLikeAnd];
    [aParams addIfDefinedKey:@"nameEqual" withString:self.nameEqual];
    [aParams addIfDefinedKey:@"partnerIdEqual" withInt:self.partnerIdEqual];
    [aParams addIfDefinedKey:@"partnerIdIn" withString:self.partnerIdIn];
    [aParams addIfDefinedKey:@"userIdEqual" withString:self.userIdEqual];
    [aParams addIfDefinedKey:@"creatorIdEqual" withString:self.creatorIdEqual];
    [aParams addIfDefinedKey:@"tagsLike" withString:self.tagsLike];
    [aParams addIfDefinedKey:@"tagsMultiLikeOr" withString:self.tagsMultiLikeOr];
    [aParams addIfDefinedKey:@"tagsMultiLikeAnd" withString:self.tagsMultiLikeAnd];
    [aParams addIfDefinedKey:@"adminTagsLike" withString:self.adminTagsLike];
    [aParams addIfDefinedKey:@"adminTagsMultiLikeOr" withString:self.adminTagsMultiLikeOr];
    [aParams addIfDefinedKey:@"adminTagsMultiLikeAnd" withString:self.adminTagsMultiLikeAnd];
    [aParams addIfDefinedKey:@"categoriesMatchAnd" withString:self.categoriesMatchAnd];
    [aParams addIfDefinedKey:@"categoriesMatchOr" withString:self.categoriesMatchOr];
    [aParams addIfDefinedKey:@"categoriesIdsMatchAnd" withString:self.categoriesIdsMatchAnd];
    [aParams addIfDefinedKey:@"categoriesIdsMatchOr" withString:self.categoriesIdsMatchOr];
    [aParams addIfDefinedKey:@"statusEqual" withString:self.statusEqual];
    [aParams addIfDefinedKey:@"statusNotEqual" withString:self.statusNotEqual];
    [aParams addIfDefinedKey:@"statusIn" withString:self.statusIn];
    [aParams addIfDefinedKey:@"statusNotIn" withString:self.statusNotIn];
    [aParams addIfDefinedKey:@"moderationStatusEqual" withInt:self.moderationStatusEqual];
    [aParams addIfDefinedKey:@"moderationStatusNotEqual" withInt:self.moderationStatusNotEqual];
    [aParams addIfDefinedKey:@"moderationStatusIn" withString:self.moderationStatusIn];
    [aParams addIfDefinedKey:@"moderationStatusNotIn" withString:self.moderationStatusNotIn];
    [aParams addIfDefinedKey:@"typeEqual" withString:self.typeEqual];
    [aParams addIfDefinedKey:@"typeIn" withString:self.typeIn];
    [aParams addIfDefinedKey:@"createdAtGreaterThanOrEqual" withInt:self.createdAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"createdAtLessThanOrEqual" withInt:self.createdAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"updatedAtGreaterThanOrEqual" withInt:self.updatedAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"updatedAtLessThanOrEqual" withInt:self.updatedAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"totalRankLessThanOrEqual" withInt:self.totalRankLessThanOrEqual];
    [aParams addIfDefinedKey:@"totalRankGreaterThanOrEqual" withInt:self.totalRankGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"groupIdEqual" withInt:self.groupIdEqual];
    [aParams addIfDefinedKey:@"searchTextMatchAnd" withString:self.searchTextMatchAnd];
    [aParams addIfDefinedKey:@"searchTextMatchOr" withString:self.searchTextMatchOr];
    [aParams addIfDefinedKey:@"accessControlIdEqual" withInt:self.accessControlIdEqual];
    [aParams addIfDefinedKey:@"accessControlIdIn" withString:self.accessControlIdIn];
    [aParams addIfDefinedKey:@"startDateGreaterThanOrEqual" withInt:self.startDateGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"startDateLessThanOrEqual" withInt:self.startDateLessThanOrEqual];
    [aParams addIfDefinedKey:@"startDateGreaterThanOrEqualOrNull" withInt:self.startDateGreaterThanOrEqualOrNull];
    [aParams addIfDefinedKey:@"startDateLessThanOrEqualOrNull" withInt:self.startDateLessThanOrEqualOrNull];
    [aParams addIfDefinedKey:@"endDateGreaterThanOrEqual" withInt:self.endDateGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"endDateLessThanOrEqual" withInt:self.endDateLessThanOrEqual];
    [aParams addIfDefinedKey:@"endDateGreaterThanOrEqualOrNull" withInt:self.endDateGreaterThanOrEqualOrNull];
    [aParams addIfDefinedKey:@"endDateLessThanOrEqualOrNull" withInt:self.endDateLessThanOrEqualOrNull];
    [aParams addIfDefinedKey:@"referenceIdEqual" withString:self.referenceIdEqual];
    [aParams addIfDefinedKey:@"referenceIdIn" withString:self.referenceIdIn];
    [aParams addIfDefinedKey:@"replacingEntryIdEqual" withString:self.replacingEntryIdEqual];
    [aParams addIfDefinedKey:@"replacingEntryIdIn" withString:self.replacingEntryIdIn];
    [aParams addIfDefinedKey:@"replacedEntryIdEqual" withString:self.replacedEntryIdEqual];
    [aParams addIfDefinedKey:@"replacedEntryIdIn" withString:self.replacedEntryIdIn];
    [aParams addIfDefinedKey:@"replacementStatusEqual" withString:self.replacementStatusEqual];
    [aParams addIfDefinedKey:@"replacementStatusIn" withString:self.replacementStatusIn];
    [aParams addIfDefinedKey:@"partnerSortValueGreaterThanOrEqual" withInt:self.partnerSortValueGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"partnerSortValueLessThanOrEqual" withInt:self.partnerSortValueLessThanOrEqual];
    [aParams addIfDefinedKey:@"rootEntryIdEqual" withString:self.rootEntryIdEqual];
    [aParams addIfDefinedKey:@"rootEntryIdIn" withString:self.rootEntryIdIn];
    [aParams addIfDefinedKey:@"tagsNameMultiLikeOr" withString:self.tagsNameMultiLikeOr];
    [aParams addIfDefinedKey:@"tagsAdminTagsMultiLikeOr" withString:self.tagsAdminTagsMultiLikeOr];
    [aParams addIfDefinedKey:@"tagsAdminTagsNameMultiLikeOr" withString:self.tagsAdminTagsNameMultiLikeOr];
    [aParams addIfDefinedKey:@"tagsNameMultiLikeAnd" withString:self.tagsNameMultiLikeAnd];
    [aParams addIfDefinedKey:@"tagsAdminTagsMultiLikeAnd" withString:self.tagsAdminTagsMultiLikeAnd];
    [aParams addIfDefinedKey:@"tagsAdminTagsNameMultiLikeAnd" withString:self.tagsAdminTagsNameMultiLikeAnd];
}

- (void)dealloc
{
    [self->_idEqual release];
    [self->_idIn release];
    [self->_idNotIn release];
    [self->_nameLike release];
    [self->_nameMultiLikeOr release];
    [self->_nameMultiLikeAnd release];
    [self->_nameEqual release];
    [self->_partnerIdIn release];
    [self->_userIdEqual release];
    [self->_creatorIdEqual release];
    [self->_tagsLike release];
    [self->_tagsMultiLikeOr release];
    [self->_tagsMultiLikeAnd release];
    [self->_adminTagsLike release];
    [self->_adminTagsMultiLikeOr release];
    [self->_adminTagsMultiLikeAnd release];
    [self->_categoriesMatchAnd release];
    [self->_categoriesMatchOr release];
    [self->_categoriesIdsMatchAnd release];
    [self->_categoriesIdsMatchOr release];
    [self->_statusEqual release];
    [self->_statusNotEqual release];
    [self->_statusIn release];
    [self->_statusNotIn release];
    [self->_moderationStatusIn release];
    [self->_moderationStatusNotIn release];
    [self->_typeEqual release];
    [self->_typeIn release];
    [self->_searchTextMatchAnd release];
    [self->_searchTextMatchOr release];
    [self->_accessControlIdIn release];
    [self->_referenceIdEqual release];
    [self->_referenceIdIn release];
    [self->_replacingEntryIdEqual release];
    [self->_replacingEntryIdIn release];
    [self->_replacedEntryIdEqual release];
    [self->_replacedEntryIdIn release];
    [self->_replacementStatusEqual release];
    [self->_replacementStatusIn release];
    [self->_rootEntryIdEqual release];
    [self->_rootEntryIdIn release];
    [self->_tagsNameMultiLikeOr release];
    [self->_tagsAdminTagsMultiLikeOr release];
    [self->_tagsAdminTagsNameMultiLikeOr release];
    [self->_tagsNameMultiLikeAnd release];
    [self->_tagsAdminTagsMultiLikeAnd release];
    [self->_tagsAdminTagsNameMultiLikeAnd release];
    [super dealloc];
}

@end

@implementation KalturaBaseEntryFilter
@synthesize freeText = _freeText;
@synthesize isRoot = _isRoot;
@synthesize categoriesFullNameIn = _categoriesFullNameIn;
@synthesize categoryAncestorIdIn = _categoryAncestorIdIn;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_isRoot = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfFreeText
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfIsRoot
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCategoriesFullNameIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCategoryAncestorIdIn
{
    return KFT_String;
}

- (void)setIsRootFromString:(NSString*)aPropVal
{
    self.isRoot = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaBaseEntryFilter"];
    [aParams addIfDefinedKey:@"freeText" withString:self.freeText];
    [aParams addIfDefinedKey:@"isRoot" withInt:self.isRoot];
    [aParams addIfDefinedKey:@"categoriesFullNameIn" withString:self.categoriesFullNameIn];
    [aParams addIfDefinedKey:@"categoryAncestorIdIn" withString:self.categoryAncestorIdIn];
}

- (void)dealloc
{
    [self->_freeText release];
    [self->_categoriesFullNameIn release];
    [self->_categoryAncestorIdIn release];
    [super dealloc];
}

@end

@implementation KalturaPlayableEntryBaseFilter
@synthesize durationLessThan = _durationLessThan;
@synthesize durationGreaterThan = _durationGreaterThan;
@synthesize durationLessThanOrEqual = _durationLessThanOrEqual;
@synthesize durationGreaterThanOrEqual = _durationGreaterThanOrEqual;
@synthesize msDurationLessThan = _msDurationLessThan;
@synthesize msDurationGreaterThan = _msDurationGreaterThan;
@synthesize msDurationLessThanOrEqual = _msDurationLessThanOrEqual;
@synthesize msDurationGreaterThanOrEqual = _msDurationGreaterThanOrEqual;
@synthesize durationTypeMatchOr = _durationTypeMatchOr;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_durationLessThan = KALTURA_UNDEF_INT;
    self->_durationGreaterThan = KALTURA_UNDEF_INT;
    self->_durationLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_durationGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_msDurationLessThan = KALTURA_UNDEF_INT;
    self->_msDurationGreaterThan = KALTURA_UNDEF_INT;
    self->_msDurationLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_msDurationGreaterThanOrEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfDurationLessThan
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDurationGreaterThan
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDurationLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDurationGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfMsDurationLessThan
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfMsDurationGreaterThan
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfMsDurationLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfMsDurationGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDurationTypeMatchOr
{
    return KFT_String;
}

- (void)setDurationLessThanFromString:(NSString*)aPropVal
{
    self.durationLessThan = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDurationGreaterThanFromString:(NSString*)aPropVal
{
    self.durationGreaterThan = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDurationLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.durationLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDurationGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.durationGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setMsDurationLessThanFromString:(NSString*)aPropVal
{
    self.msDurationLessThan = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setMsDurationGreaterThanFromString:(NSString*)aPropVal
{
    self.msDurationGreaterThan = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setMsDurationLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.msDurationLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setMsDurationGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.msDurationGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaPlayableEntryBaseFilter"];
    [aParams addIfDefinedKey:@"durationLessThan" withInt:self.durationLessThan];
    [aParams addIfDefinedKey:@"durationGreaterThan" withInt:self.durationGreaterThan];
    [aParams addIfDefinedKey:@"durationLessThanOrEqual" withInt:self.durationLessThanOrEqual];
    [aParams addIfDefinedKey:@"durationGreaterThanOrEqual" withInt:self.durationGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"msDurationLessThan" withInt:self.msDurationLessThan];
    [aParams addIfDefinedKey:@"msDurationGreaterThan" withInt:self.msDurationGreaterThan];
    [aParams addIfDefinedKey:@"msDurationLessThanOrEqual" withInt:self.msDurationLessThanOrEqual];
    [aParams addIfDefinedKey:@"msDurationGreaterThanOrEqual" withInt:self.msDurationGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"durationTypeMatchOr" withString:self.durationTypeMatchOr];
}

- (void)dealloc
{
    [self->_durationTypeMatchOr release];
    [super dealloc];
}

@end

@implementation KalturaPlayableEntryFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaPlayableEntryFilter"];
}

@end

@implementation KalturaMediaEntryBaseFilter
@synthesize mediaTypeEqual = _mediaTypeEqual;
@synthesize mediaTypeIn = _mediaTypeIn;
@synthesize mediaDateGreaterThanOrEqual = _mediaDateGreaterThanOrEqual;
@synthesize mediaDateLessThanOrEqual = _mediaDateLessThanOrEqual;
@synthesize flavorParamsIdsMatchOr = _flavorParamsIdsMatchOr;
@synthesize flavorParamsIdsMatchAnd = _flavorParamsIdsMatchAnd;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_mediaTypeEqual = KALTURA_UNDEF_INT;
    self->_mediaDateGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_mediaDateLessThanOrEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfMediaTypeEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfMediaTypeIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfMediaDateGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfMediaDateLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfFlavorParamsIdsMatchOr
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFlavorParamsIdsMatchAnd
{
    return KFT_String;
}

- (void)setMediaTypeEqualFromString:(NSString*)aPropVal
{
    self.mediaTypeEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setMediaDateGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.mediaDateGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setMediaDateLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.mediaDateLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaMediaEntryBaseFilter"];
    [aParams addIfDefinedKey:@"mediaTypeEqual" withInt:self.mediaTypeEqual];
    [aParams addIfDefinedKey:@"mediaTypeIn" withString:self.mediaTypeIn];
    [aParams addIfDefinedKey:@"mediaDateGreaterThanOrEqual" withInt:self.mediaDateGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"mediaDateLessThanOrEqual" withInt:self.mediaDateLessThanOrEqual];
    [aParams addIfDefinedKey:@"flavorParamsIdsMatchOr" withString:self.flavorParamsIdsMatchOr];
    [aParams addIfDefinedKey:@"flavorParamsIdsMatchAnd" withString:self.flavorParamsIdsMatchAnd];
}

- (void)dealloc
{
    [self->_mediaTypeIn release];
    [self->_flavorParamsIdsMatchOr release];
    [self->_flavorParamsIdsMatchAnd release];
    [super dealloc];
}

@end

@implementation KalturaMediaEntryFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaMediaEntryFilter"];
}

@end

@implementation KalturaMediaEntryFilterForPlaylist
@synthesize limit = _limit;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_limit = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfLimit
{
    return KFT_Int;
}

- (void)setLimitFromString:(NSString*)aPropVal
{
    self.limit = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaMediaEntryFilterForPlaylist"];
    [aParams addIfDefinedKey:@"limit" withInt:self.limit];
}

@end

@interface KalturaMediaInfo()
@property (nonatomic,assign) int id;
@end

@implementation KalturaMediaInfo
@synthesize id = _id;
@synthesize flavorAssetId = _flavorAssetId;
@synthesize fileSize = _fileSize;
@synthesize containerFormat = _containerFormat;
@synthesize containerId = _containerId;
@synthesize containerProfile = _containerProfile;
@synthesize containerDuration = _containerDuration;
@synthesize containerBitRate = _containerBitRate;
@synthesize videoFormat = _videoFormat;
@synthesize videoCodecId = _videoCodecId;
@synthesize videoDuration = _videoDuration;
@synthesize videoBitRate = _videoBitRate;
@synthesize videoBitRateMode = _videoBitRateMode;
@synthesize videoWidth = _videoWidth;
@synthesize videoHeight = _videoHeight;
@synthesize videoFrameRate = _videoFrameRate;
@synthesize videoDar = _videoDar;
@synthesize videoRotation = _videoRotation;
@synthesize audioFormat = _audioFormat;
@synthesize audioCodecId = _audioCodecId;
@synthesize audioDuration = _audioDuration;
@synthesize audioBitRate = _audioBitRate;
@synthesize audioBitRateMode = _audioBitRateMode;
@synthesize audioChannels = _audioChannels;
@synthesize audioSamplingRate = _audioSamplingRate;
@synthesize audioResolution = _audioResolution;
@synthesize writingLib = _writingLib;
@synthesize rawData = _rawData;
@synthesize multiStreamInfo = _multiStreamInfo;
@synthesize scanType = _scanType;
@synthesize multiStream = _multiStream;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_id = KALTURA_UNDEF_INT;
    self->_fileSize = KALTURA_UNDEF_INT;
    self->_containerDuration = KALTURA_UNDEF_INT;
    self->_containerBitRate = KALTURA_UNDEF_INT;
    self->_videoDuration = KALTURA_UNDEF_INT;
    self->_videoBitRate = KALTURA_UNDEF_INT;
    self->_videoBitRateMode = KALTURA_UNDEF_INT;
    self->_videoWidth = KALTURA_UNDEF_INT;
    self->_videoHeight = KALTURA_UNDEF_INT;
    self->_videoFrameRate = KALTURA_UNDEF_FLOAT;
    self->_videoDar = KALTURA_UNDEF_FLOAT;
    self->_videoRotation = KALTURA_UNDEF_INT;
    self->_audioDuration = KALTURA_UNDEF_INT;
    self->_audioBitRate = KALTURA_UNDEF_INT;
    self->_audioBitRateMode = KALTURA_UNDEF_INT;
    self->_audioChannels = KALTURA_UNDEF_INT;
    self->_audioSamplingRate = KALTURA_UNDEF_INT;
    self->_audioResolution = KALTURA_UNDEF_INT;
    self->_scanType = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfFlavorAssetId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFileSize
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfContainerFormat
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfContainerId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfContainerProfile
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfContainerDuration
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfContainerBitRate
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfVideoFormat
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfVideoCodecId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfVideoDuration
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfVideoBitRate
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfVideoBitRateMode
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfVideoWidth
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfVideoHeight
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfVideoFrameRate
{
    return KFT_Float;
}

- (KalturaFieldType)getTypeOfVideoDar
{
    return KFT_Float;
}

- (KalturaFieldType)getTypeOfVideoRotation
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfAudioFormat
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAudioCodecId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAudioDuration
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfAudioBitRate
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfAudioBitRateMode
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfAudioChannels
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfAudioSamplingRate
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfAudioResolution
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfWritingLib
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfRawData
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfMultiStreamInfo
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfScanType
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfMultiStream
{
    return KFT_String;
}

- (void)setIdFromString:(NSString*)aPropVal
{
    self.id = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setFileSizeFromString:(NSString*)aPropVal
{
    self.fileSize = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setContainerDurationFromString:(NSString*)aPropVal
{
    self.containerDuration = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setContainerBitRateFromString:(NSString*)aPropVal
{
    self.containerBitRate = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setVideoDurationFromString:(NSString*)aPropVal
{
    self.videoDuration = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setVideoBitRateFromString:(NSString*)aPropVal
{
    self.videoBitRate = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setVideoBitRateModeFromString:(NSString*)aPropVal
{
    self.videoBitRateMode = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setVideoWidthFromString:(NSString*)aPropVal
{
    self.videoWidth = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setVideoHeightFromString:(NSString*)aPropVal
{
    self.videoHeight = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setVideoFrameRateFromString:(NSString*)aPropVal
{
    self.videoFrameRate = [KalturaSimpleTypeParser parseFloat:aPropVal];
}

- (void)setVideoDarFromString:(NSString*)aPropVal
{
    self.videoDar = [KalturaSimpleTypeParser parseFloat:aPropVal];
}

- (void)setVideoRotationFromString:(NSString*)aPropVal
{
    self.videoRotation = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setAudioDurationFromString:(NSString*)aPropVal
{
    self.audioDuration = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setAudioBitRateFromString:(NSString*)aPropVal
{
    self.audioBitRate = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setAudioBitRateModeFromString:(NSString*)aPropVal
{
    self.audioBitRateMode = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setAudioChannelsFromString:(NSString*)aPropVal
{
    self.audioChannels = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setAudioSamplingRateFromString:(NSString*)aPropVal
{
    self.audioSamplingRate = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setAudioResolutionFromString:(NSString*)aPropVal
{
    self.audioResolution = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setScanTypeFromString:(NSString*)aPropVal
{
    self.scanType = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaMediaInfo"];
    [aParams addIfDefinedKey:@"flavorAssetId" withString:self.flavorAssetId];
    [aParams addIfDefinedKey:@"fileSize" withInt:self.fileSize];
    [aParams addIfDefinedKey:@"containerFormat" withString:self.containerFormat];
    [aParams addIfDefinedKey:@"containerId" withString:self.containerId];
    [aParams addIfDefinedKey:@"containerProfile" withString:self.containerProfile];
    [aParams addIfDefinedKey:@"containerDuration" withInt:self.containerDuration];
    [aParams addIfDefinedKey:@"containerBitRate" withInt:self.containerBitRate];
    [aParams addIfDefinedKey:@"videoFormat" withString:self.videoFormat];
    [aParams addIfDefinedKey:@"videoCodecId" withString:self.videoCodecId];
    [aParams addIfDefinedKey:@"videoDuration" withInt:self.videoDuration];
    [aParams addIfDefinedKey:@"videoBitRate" withInt:self.videoBitRate];
    [aParams addIfDefinedKey:@"videoBitRateMode" withInt:self.videoBitRateMode];
    [aParams addIfDefinedKey:@"videoWidth" withInt:self.videoWidth];
    [aParams addIfDefinedKey:@"videoHeight" withInt:self.videoHeight];
    [aParams addIfDefinedKey:@"videoFrameRate" withFloat:self.videoFrameRate];
    [aParams addIfDefinedKey:@"videoDar" withFloat:self.videoDar];
    [aParams addIfDefinedKey:@"videoRotation" withInt:self.videoRotation];
    [aParams addIfDefinedKey:@"audioFormat" withString:self.audioFormat];
    [aParams addIfDefinedKey:@"audioCodecId" withString:self.audioCodecId];
    [aParams addIfDefinedKey:@"audioDuration" withInt:self.audioDuration];
    [aParams addIfDefinedKey:@"audioBitRate" withInt:self.audioBitRate];
    [aParams addIfDefinedKey:@"audioBitRateMode" withInt:self.audioBitRateMode];
    [aParams addIfDefinedKey:@"audioChannels" withInt:self.audioChannels];
    [aParams addIfDefinedKey:@"audioSamplingRate" withInt:self.audioSamplingRate];
    [aParams addIfDefinedKey:@"audioResolution" withInt:self.audioResolution];
    [aParams addIfDefinedKey:@"writingLib" withString:self.writingLib];
    [aParams addIfDefinedKey:@"rawData" withString:self.rawData];
    [aParams addIfDefinedKey:@"multiStreamInfo" withString:self.multiStreamInfo];
    [aParams addIfDefinedKey:@"scanType" withInt:self.scanType];
    [aParams addIfDefinedKey:@"multiStream" withString:self.multiStream];
}

- (void)dealloc
{
    [self->_flavorAssetId release];
    [self->_containerFormat release];
    [self->_containerId release];
    [self->_containerProfile release];
    [self->_videoFormat release];
    [self->_videoCodecId release];
    [self->_audioFormat release];
    [self->_audioCodecId release];
    [self->_writingLib release];
    [self->_rawData release];
    [self->_multiStreamInfo release];
    [self->_multiStream release];
    [super dealloc];
}

@end

@interface KalturaMediaInfoListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaMediaInfoListResponse
@synthesize objects = _objects;
@synthesize totalCount = _totalCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaMediaInfo";
}

- (KalturaFieldType)getTypeOfTotalCount
{
    return KFT_Int;
}

- (void)setTotalCountFromString:(NSString*)aPropVal
{
    self.totalCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaMediaInfoListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@interface KalturaMediaListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaMediaListResponse
@synthesize objects = _objects;
@synthesize totalCount = _totalCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaMediaEntry";
}

- (KalturaFieldType)getTypeOfTotalCount
{
    return KFT_Int;
}

- (void)setTotalCountFromString:(NSString*)aPropVal
{
    self.totalCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaMediaListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@interface KalturaMixEntry()
@property (nonatomic,assign) BOOL hasRealThumbnail;
@end

@implementation KalturaMixEntry
@synthesize hasRealThumbnail = _hasRealThumbnail;
@synthesize editorType = _editorType;
@synthesize dataContent = _dataContent;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_hasRealThumbnail = KALTURA_UNDEF_BOOL;
    self->_editorType = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfHasRealThumbnail
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfEditorType
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDataContent
{
    return KFT_String;
}

- (void)setHasRealThumbnailFromString:(NSString*)aPropVal
{
    self.hasRealThumbnail = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)setEditorTypeFromString:(NSString*)aPropVal
{
    self.editorType = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaMixEntry"];
    [aParams addIfDefinedKey:@"editorType" withInt:self.editorType];
    [aParams addIfDefinedKey:@"dataContent" withString:self.dataContent];
}

- (void)dealloc
{
    [self->_dataContent release];
    [super dealloc];
}

@end

@interface KalturaMixListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaMixListResponse
@synthesize objects = _objects;
@synthesize totalCount = _totalCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaMixEntry";
}

- (KalturaFieldType)getTypeOfTotalCount
{
    return KFT_Int;
}

- (void)setTotalCountFromString:(NSString*)aPropVal
{
    self.totalCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaMixListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@interface KalturaModerationFlag()
@property (nonatomic,assign) int id;
@property (nonatomic,assign) int partnerId;
@property (nonatomic,copy) NSString* userId;
@property (nonatomic,copy) NSString* moderationObjectType;
@property (nonatomic,copy) NSString* status;
@property (nonatomic,assign) int createdAt;
@property (nonatomic,assign) int updatedAt;
@end

@implementation KalturaModerationFlag
@synthesize id = _id;
@synthesize partnerId = _partnerId;
@synthesize userId = _userId;
@synthesize moderationObjectType = _moderationObjectType;
@synthesize flaggedEntryId = _flaggedEntryId;
@synthesize flaggedUserId = _flaggedUserId;
@synthesize status = _status;
@synthesize comments = _comments;
@synthesize flagType = _flagType;
@synthesize createdAt = _createdAt;
@synthesize updatedAt = _updatedAt;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_id = KALTURA_UNDEF_INT;
    self->_partnerId = KALTURA_UNDEF_INT;
    self->_flagType = KALTURA_UNDEF_INT;
    self->_createdAt = KALTURA_UNDEF_INT;
    self->_updatedAt = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUserId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfModerationObjectType
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFlaggedEntryId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFlaggedUserId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatus
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfComments
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFlagType
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAt
{
    return KFT_Int;
}

- (void)setIdFromString:(NSString*)aPropVal
{
    self.id = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerIdFromString:(NSString*)aPropVal
{
    self.partnerId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setFlagTypeFromString:(NSString*)aPropVal
{
    self.flagType = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtFromString:(NSString*)aPropVal
{
    self.createdAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtFromString:(NSString*)aPropVal
{
    self.updatedAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaModerationFlag"];
    [aParams addIfDefinedKey:@"flaggedEntryId" withString:self.flaggedEntryId];
    [aParams addIfDefinedKey:@"flaggedUserId" withString:self.flaggedUserId];
    [aParams addIfDefinedKey:@"comments" withString:self.comments];
    [aParams addIfDefinedKey:@"flagType" withInt:self.flagType];
}

- (void)dealloc
{
    [self->_userId release];
    [self->_moderationObjectType release];
    [self->_flaggedEntryId release];
    [self->_flaggedUserId release];
    [self->_status release];
    [self->_comments release];
    [super dealloc];
}

@end

@interface KalturaModerationFlagListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaModerationFlagListResponse
@synthesize objects = _objects;
@synthesize totalCount = _totalCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaModerationFlag";
}

- (KalturaFieldType)getTypeOfTotalCount
{
    return KFT_Int;
}

- (void)setTotalCountFromString:(NSString*)aPropVal
{
    self.totalCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaModerationFlagListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@implementation KalturaPlayerDeliveryType
@synthesize id = _id;
@synthesize label = _label;
@synthesize flashvars = _flashvars;
@synthesize minVersion = _minVersion;

- (KalturaFieldType)getTypeOfId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfLabel
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFlashvars
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfFlashvars
{
    return @"KalturaKeyValue";
}

- (KalturaFieldType)getTypeOfMinVersion
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaPlayerDeliveryType"];
    [aParams addIfDefinedKey:@"id" withString:self.id];
    [aParams addIfDefinedKey:@"label" withString:self.label];
    [aParams addIfDefinedKey:@"flashvars" withArray:self.flashvars];
    [aParams addIfDefinedKey:@"minVersion" withString:self.minVersion];
}

- (void)dealloc
{
    [self->_id release];
    [self->_label release];
    [self->_flashvars release];
    [self->_minVersion release];
    [super dealloc];
}

@end

@implementation KalturaPlayerEmbedCodeType
@synthesize id = _id;
@synthesize label = _label;
@synthesize entryOnly = _entryOnly;
@synthesize minVersion = _minVersion;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_entryOnly = KALTURA_UNDEF_BOOL;
    return self;
}

- (KalturaFieldType)getTypeOfId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfLabel
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfEntryOnly
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfMinVersion
{
    return KFT_String;
}

- (void)setEntryOnlyFromString:(NSString*)aPropVal
{
    self.entryOnly = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaPlayerEmbedCodeType"];
    [aParams addIfDefinedKey:@"id" withString:self.id];
    [aParams addIfDefinedKey:@"label" withString:self.label];
    [aParams addIfDefinedKey:@"entryOnly" withBool:self.entryOnly];
    [aParams addIfDefinedKey:@"minVersion" withString:self.minVersion];
}

- (void)dealloc
{
    [self->_id release];
    [self->_label release];
    [self->_minVersion release];
    [super dealloc];
}

@end

@interface KalturaPartner()
@property (nonatomic,assign) int id;
@property (nonatomic,assign) int createdAt;
@property (nonatomic,assign) int status;
@property (nonatomic,assign) int partnerPackage;
@property (nonatomic,copy) NSString* secret;
@property (nonatomic,copy) NSString* adminSecret;
@property (nonatomic,copy) NSString* cmsPassword;
@property (nonatomic,assign) int adminLoginUsersQuota;
@property (nonatomic,assign) int publishersQuota;
@property (nonatomic,assign) int partnerGroupType;
@property (nonatomic,assign) BOOL defaultEntitlementEnforcement;
@property (nonatomic,copy) NSString* defaultDeliveryType;
@property (nonatomic,copy) NSString* defaultEmbedCodeType;
@property (nonatomic,retain) NSMutableArray* deliveryTypes;
@property (nonatomic,retain) NSMutableArray* embedCodeTypes;
@property (nonatomic,assign) int templatePartnerId;
@property (nonatomic,assign) BOOL ignoreSeoLinks;
@property (nonatomic,copy) NSString* host;
@property (nonatomic,copy) NSString* cdnHost;
@property (nonatomic,copy) NSString* rtmpUrl;
@property (nonatomic,copy) NSString* language;
@property (nonatomic,assign) BOOL isFirstLogin;
@property (nonatomic,copy) NSString* logoutUrl;
@end

@implementation KalturaPartner
@synthesize id = _id;
@synthesize name = _name;
@synthesize website = _website;
@synthesize notificationUrl = _notificationUrl;
@synthesize appearInSearch = _appearInSearch;
@synthesize createdAt = _createdAt;
@synthesize adminName = _adminName;
@synthesize adminEmail = _adminEmail;
@synthesize description = _description;
@synthesize commercialUse = _commercialUse;
@synthesize landingPage = _landingPage;
@synthesize userLandingPage = _userLandingPage;
@synthesize contentCategories = _contentCategories;
@synthesize type = _type;
@synthesize phone = _phone;
@synthesize describeYourself = _describeYourself;
@synthesize adultContent = _adultContent;
@synthesize defConversionProfileType = _defConversionProfileType;
@synthesize notify = _notify;
@synthesize status = _status;
@synthesize allowQuickEdit = _allowQuickEdit;
@synthesize mergeEntryLists = _mergeEntryLists;
@synthesize notificationsConfig = _notificationsConfig;
@synthesize maxUploadSize = _maxUploadSize;
@synthesize partnerPackage = _partnerPackage;
@synthesize secret = _secret;
@synthesize adminSecret = _adminSecret;
@synthesize cmsPassword = _cmsPassword;
@synthesize allowMultiNotification = _allowMultiNotification;
@synthesize adminLoginUsersQuota = _adminLoginUsersQuota;
@synthesize adminUserId = _adminUserId;
@synthesize firstName = _firstName;
@synthesize lastName = _lastName;
@synthesize country = _country;
@synthesize state = _state;
@synthesize additionalParams = _additionalParams;
@synthesize publishersQuota = _publishersQuota;
@synthesize partnerGroupType = _partnerGroupType;
@synthesize defaultEntitlementEnforcement = _defaultEntitlementEnforcement;
@synthesize defaultDeliveryType = _defaultDeliveryType;
@synthesize defaultEmbedCodeType = _defaultEmbedCodeType;
@synthesize deliveryTypes = _deliveryTypes;
@synthesize embedCodeTypes = _embedCodeTypes;
@synthesize templatePartnerId = _templatePartnerId;
@synthesize ignoreSeoLinks = _ignoreSeoLinks;
@synthesize host = _host;
@synthesize cdnHost = _cdnHost;
@synthesize rtmpUrl = _rtmpUrl;
@synthesize language = _language;
@synthesize isFirstLogin = _isFirstLogin;
@synthesize logoutUrl = _logoutUrl;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_id = KALTURA_UNDEF_INT;
    self->_appearInSearch = KALTURA_UNDEF_INT;
    self->_createdAt = KALTURA_UNDEF_INT;
    self->_commercialUse = KALTURA_UNDEF_INT;
    self->_type = KALTURA_UNDEF_INT;
    self->_adultContent = KALTURA_UNDEF_BOOL;
    self->_notify = KALTURA_UNDEF_INT;
    self->_status = KALTURA_UNDEF_INT;
    self->_allowQuickEdit = KALTURA_UNDEF_INT;
    self->_mergeEntryLists = KALTURA_UNDEF_INT;
    self->_maxUploadSize = KALTURA_UNDEF_INT;
    self->_partnerPackage = KALTURA_UNDEF_INT;
    self->_allowMultiNotification = KALTURA_UNDEF_INT;
    self->_adminLoginUsersQuota = KALTURA_UNDEF_INT;
    self->_publishersQuota = KALTURA_UNDEF_INT;
    self->_partnerGroupType = KALTURA_UNDEF_INT;
    self->_defaultEntitlementEnforcement = KALTURA_UNDEF_BOOL;
    self->_templatePartnerId = KALTURA_UNDEF_INT;
    self->_ignoreSeoLinks = KALTURA_UNDEF_BOOL;
    self->_isFirstLogin = KALTURA_UNDEF_BOOL;
    return self;
}

- (KalturaFieldType)getTypeOfId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfWebsite
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfNotificationUrl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAppearInSearch
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfAdminName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAdminEmail
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDescription
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCommercialUse
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfLandingPage
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfUserLandingPage
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfContentCategories
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfType
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPhone
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDescribeYourself
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAdultContent
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfDefConversionProfileType
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfNotify
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfStatus
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfAllowQuickEdit
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfMergeEntryLists
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfNotificationsConfig
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfMaxUploadSize
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerPackage
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfSecret
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAdminSecret
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCmsPassword
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAllowMultiNotification
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfAdminLoginUsersQuota
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfAdminUserId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFirstName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfLastName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCountry
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfState
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAdditionalParams
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfAdditionalParams
{
    return @"KalturaKeyValue";
}

- (KalturaFieldType)getTypeOfPublishersQuota
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerGroupType
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDefaultEntitlementEnforcement
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfDefaultDeliveryType
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDefaultEmbedCodeType
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDeliveryTypes
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfDeliveryTypes
{
    return @"KalturaPlayerDeliveryType";
}

- (KalturaFieldType)getTypeOfEmbedCodeTypes
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfEmbedCodeTypes
{
    return @"KalturaPlayerEmbedCodeType";
}

- (KalturaFieldType)getTypeOfTemplatePartnerId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfIgnoreSeoLinks
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfHost
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCdnHost
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfRtmpUrl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfLanguage
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfIsFirstLogin
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfLogoutUrl
{
    return KFT_String;
}

- (void)setIdFromString:(NSString*)aPropVal
{
    self.id = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setAppearInSearchFromString:(NSString*)aPropVal
{
    self.appearInSearch = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtFromString:(NSString*)aPropVal
{
    self.createdAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCommercialUseFromString:(NSString*)aPropVal
{
    self.commercialUse = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setTypeFromString:(NSString*)aPropVal
{
    self.type = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setAdultContentFromString:(NSString*)aPropVal
{
    self.adultContent = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)setNotifyFromString:(NSString*)aPropVal
{
    self.notify = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusFromString:(NSString*)aPropVal
{
    self.status = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setAllowQuickEditFromString:(NSString*)aPropVal
{
    self.allowQuickEdit = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setMergeEntryListsFromString:(NSString*)aPropVal
{
    self.mergeEntryLists = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setMaxUploadSizeFromString:(NSString*)aPropVal
{
    self.maxUploadSize = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerPackageFromString:(NSString*)aPropVal
{
    self.partnerPackage = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setAllowMultiNotificationFromString:(NSString*)aPropVal
{
    self.allowMultiNotification = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setAdminLoginUsersQuotaFromString:(NSString*)aPropVal
{
    self.adminLoginUsersQuota = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPublishersQuotaFromString:(NSString*)aPropVal
{
    self.publishersQuota = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerGroupTypeFromString:(NSString*)aPropVal
{
    self.partnerGroupType = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDefaultEntitlementEnforcementFromString:(NSString*)aPropVal
{
    self.defaultEntitlementEnforcement = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)setTemplatePartnerIdFromString:(NSString*)aPropVal
{
    self.templatePartnerId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setIgnoreSeoLinksFromString:(NSString*)aPropVal
{
    self.ignoreSeoLinks = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)setIsFirstLoginFromString:(NSString*)aPropVal
{
    self.isFirstLogin = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaPartner"];
    [aParams addIfDefinedKey:@"name" withString:self.name];
    [aParams addIfDefinedKey:@"website" withString:self.website];
    [aParams addIfDefinedKey:@"notificationUrl" withString:self.notificationUrl];
    [aParams addIfDefinedKey:@"appearInSearch" withInt:self.appearInSearch];
    [aParams addIfDefinedKey:@"adminName" withString:self.adminName];
    [aParams addIfDefinedKey:@"adminEmail" withString:self.adminEmail];
    [aParams addIfDefinedKey:@"description" withString:self.description];
    [aParams addIfDefinedKey:@"commercialUse" withInt:self.commercialUse];
    [aParams addIfDefinedKey:@"landingPage" withString:self.landingPage];
    [aParams addIfDefinedKey:@"userLandingPage" withString:self.userLandingPage];
    [aParams addIfDefinedKey:@"contentCategories" withString:self.contentCategories];
    [aParams addIfDefinedKey:@"type" withInt:self.type];
    [aParams addIfDefinedKey:@"phone" withString:self.phone];
    [aParams addIfDefinedKey:@"describeYourself" withString:self.describeYourself];
    [aParams addIfDefinedKey:@"adultContent" withBool:self.adultContent];
    [aParams addIfDefinedKey:@"defConversionProfileType" withString:self.defConversionProfileType];
    [aParams addIfDefinedKey:@"notify" withInt:self.notify];
    [aParams addIfDefinedKey:@"allowQuickEdit" withInt:self.allowQuickEdit];
    [aParams addIfDefinedKey:@"mergeEntryLists" withInt:self.mergeEntryLists];
    [aParams addIfDefinedKey:@"notificationsConfig" withString:self.notificationsConfig];
    [aParams addIfDefinedKey:@"maxUploadSize" withInt:self.maxUploadSize];
    [aParams addIfDefinedKey:@"allowMultiNotification" withInt:self.allowMultiNotification];
    [aParams addIfDefinedKey:@"adminUserId" withString:self.adminUserId];
    [aParams addIfDefinedKey:@"firstName" withString:self.firstName];
    [aParams addIfDefinedKey:@"lastName" withString:self.lastName];
    [aParams addIfDefinedKey:@"country" withString:self.country];
    [aParams addIfDefinedKey:@"state" withString:self.state];
    [aParams addIfDefinedKey:@"additionalParams" withArray:self.additionalParams];
}

- (void)dealloc
{
    [self->_name release];
    [self->_website release];
    [self->_notificationUrl release];
    [self->_adminName release];
    [self->_adminEmail release];
    [self->_description release];
    [self->_landingPage release];
    [self->_userLandingPage release];
    [self->_contentCategories release];
    [self->_phone release];
    [self->_describeYourself release];
    [self->_defConversionProfileType release];
    [self->_notificationsConfig release];
    [self->_secret release];
    [self->_adminSecret release];
    [self->_cmsPassword release];
    [self->_adminUserId release];
    [self->_firstName release];
    [self->_lastName release];
    [self->_country release];
    [self->_state release];
    [self->_additionalParams release];
    [self->_defaultDeliveryType release];
    [self->_defaultEmbedCodeType release];
    [self->_deliveryTypes release];
    [self->_embedCodeTypes release];
    [self->_host release];
    [self->_cdnHost release];
    [self->_rtmpUrl release];
    [self->_language release];
    [self->_logoutUrl release];
    [super dealloc];
}

@end

@interface KalturaPartnerListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaPartnerListResponse
@synthesize objects = _objects;
@synthesize totalCount = _totalCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaPartner";
}

- (KalturaFieldType)getTypeOfTotalCount
{
    return KFT_Int;
}

- (void)setTotalCountFromString:(NSString*)aPropVal
{
    self.totalCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaPartnerListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@interface KalturaPartnerStatistics()
@property (nonatomic,assign) int packageBandwidthAndStorage;
@property (nonatomic,assign) double hosting;
@property (nonatomic,assign) double bandwidth;
@property (nonatomic,assign) int usage;
@property (nonatomic,assign) double usagePercent;
@property (nonatomic,assign) int reachedLimitDate;
@end

@implementation KalturaPartnerStatistics
@synthesize packageBandwidthAndStorage = _packageBandwidthAndStorage;
@synthesize hosting = _hosting;
@synthesize bandwidth = _bandwidth;
@synthesize usage = _usage;
@synthesize usagePercent = _usagePercent;
@synthesize reachedLimitDate = _reachedLimitDate;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_packageBandwidthAndStorage = KALTURA_UNDEF_INT;
    self->_hosting = KALTURA_UNDEF_FLOAT;
    self->_bandwidth = KALTURA_UNDEF_FLOAT;
    self->_usage = KALTURA_UNDEF_INT;
    self->_usagePercent = KALTURA_UNDEF_FLOAT;
    self->_reachedLimitDate = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfPackageBandwidthAndStorage
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfHosting
{
    return KFT_Float;
}

- (KalturaFieldType)getTypeOfBandwidth
{
    return KFT_Float;
}

- (KalturaFieldType)getTypeOfUsage
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUsagePercent
{
    return KFT_Float;
}

- (KalturaFieldType)getTypeOfReachedLimitDate
{
    return KFT_Int;
}

- (void)setPackageBandwidthAndStorageFromString:(NSString*)aPropVal
{
    self.packageBandwidthAndStorage = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setHostingFromString:(NSString*)aPropVal
{
    self.hosting = [KalturaSimpleTypeParser parseFloat:aPropVal];
}

- (void)setBandwidthFromString:(NSString*)aPropVal
{
    self.bandwidth = [KalturaSimpleTypeParser parseFloat:aPropVal];
}

- (void)setUsageFromString:(NSString*)aPropVal
{
    self.usage = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUsagePercentFromString:(NSString*)aPropVal
{
    self.usagePercent = [KalturaSimpleTypeParser parseFloat:aPropVal];
}

- (void)setReachedLimitDateFromString:(NSString*)aPropVal
{
    self.reachedLimitDate = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaPartnerStatistics"];
}

@end

@interface KalturaPartnerUsage()
@property (nonatomic,assign) double hostingGB;
@property (nonatomic,assign) double Percent;
@property (nonatomic,assign) int packageBW;
@property (nonatomic,assign) double usageGB;
@property (nonatomic,assign) int reachedLimitDate;
@property (nonatomic,copy) NSString* usageGraph;
@end

@implementation KalturaPartnerUsage
@synthesize hostingGB = _hostingGB;
@synthesize Percent = _Percent;
@synthesize packageBW = _packageBW;
@synthesize usageGB = _usageGB;
@synthesize reachedLimitDate = _reachedLimitDate;
@synthesize usageGraph = _usageGraph;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_hostingGB = KALTURA_UNDEF_FLOAT;
    self->_Percent = KALTURA_UNDEF_FLOAT;
    self->_packageBW = KALTURA_UNDEF_INT;
    self->_usageGB = KALTURA_UNDEF_FLOAT;
    self->_reachedLimitDate = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfHostingGB
{
    return KFT_Float;
}

- (KalturaFieldType)getTypeOfPercent
{
    return KFT_Float;
}

- (KalturaFieldType)getTypeOfPackageBW
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUsageGB
{
    return KFT_Float;
}

- (KalturaFieldType)getTypeOfReachedLimitDate
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUsageGraph
{
    return KFT_String;
}

- (void)setHostingGBFromString:(NSString*)aPropVal
{
    self.hostingGB = [KalturaSimpleTypeParser parseFloat:aPropVal];
}

- (void)setPercentFromString:(NSString*)aPropVal
{
    self.Percent = [KalturaSimpleTypeParser parseFloat:aPropVal];
}

- (void)setPackageBWFromString:(NSString*)aPropVal
{
    self.packageBW = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUsageGBFromString:(NSString*)aPropVal
{
    self.usageGB = [KalturaSimpleTypeParser parseFloat:aPropVal];
}

- (void)setReachedLimitDateFromString:(NSString*)aPropVal
{
    self.reachedLimitDate = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaPartnerUsage"];
}

- (void)dealloc
{
    [self->_usageGraph release];
    [super dealloc];
}

@end

@interface KalturaPermission()
@property (nonatomic,assign) int id;
@property (nonatomic,assign) int type;
@property (nonatomic,assign) int partnerId;
@property (nonatomic,assign) int createdAt;
@property (nonatomic,assign) int updatedAt;
@end

@implementation KalturaPermission
@synthesize id = _id;
@synthesize type = _type;
@synthesize name = _name;
@synthesize friendlyName = _friendlyName;
@synthesize description = _description;
@synthesize status = _status;
@synthesize partnerId = _partnerId;
@synthesize dependsOnPermissionNames = _dependsOnPermissionNames;
@synthesize tags = _tags;
@synthesize permissionItemsIds = _permissionItemsIds;
@synthesize createdAt = _createdAt;
@synthesize updatedAt = _updatedAt;
@synthesize partnerGroup = _partnerGroup;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_id = KALTURA_UNDEF_INT;
    self->_type = KALTURA_UNDEF_INT;
    self->_status = KALTURA_UNDEF_INT;
    self->_partnerId = KALTURA_UNDEF_INT;
    self->_createdAt = KALTURA_UNDEF_INT;
    self->_updatedAt = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfType
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFriendlyName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDescription
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatus
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDependsOnPermissionNames
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTags
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPermissionItemsIds
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerGroup
{
    return KFT_String;
}

- (void)setIdFromString:(NSString*)aPropVal
{
    self.id = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setTypeFromString:(NSString*)aPropVal
{
    self.type = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusFromString:(NSString*)aPropVal
{
    self.status = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerIdFromString:(NSString*)aPropVal
{
    self.partnerId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtFromString:(NSString*)aPropVal
{
    self.createdAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtFromString:(NSString*)aPropVal
{
    self.updatedAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaPermission"];
    [aParams addIfDefinedKey:@"name" withString:self.name];
    [aParams addIfDefinedKey:@"friendlyName" withString:self.friendlyName];
    [aParams addIfDefinedKey:@"description" withString:self.description];
    [aParams addIfDefinedKey:@"status" withInt:self.status];
    [aParams addIfDefinedKey:@"dependsOnPermissionNames" withString:self.dependsOnPermissionNames];
    [aParams addIfDefinedKey:@"tags" withString:self.tags];
    [aParams addIfDefinedKey:@"permissionItemsIds" withString:self.permissionItemsIds];
    [aParams addIfDefinedKey:@"partnerGroup" withString:self.partnerGroup];
}

- (void)dealloc
{
    [self->_name release];
    [self->_friendlyName release];
    [self->_description release];
    [self->_dependsOnPermissionNames release];
    [self->_tags release];
    [self->_permissionItemsIds release];
    [self->_partnerGroup release];
    [super dealloc];
}

@end

@interface KalturaPermissionItem()
@property (nonatomic,assign) int id;
@property (nonatomic,copy) NSString* type;
@property (nonatomic,assign) int partnerId;
@property (nonatomic,assign) int createdAt;
@property (nonatomic,assign) int updatedAt;
@end

@implementation KalturaPermissionItem
@synthesize id = _id;
@synthesize type = _type;
@synthesize partnerId = _partnerId;
@synthesize tags = _tags;
@synthesize createdAt = _createdAt;
@synthesize updatedAt = _updatedAt;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_id = KALTURA_UNDEF_INT;
    self->_partnerId = KALTURA_UNDEF_INT;
    self->_createdAt = KALTURA_UNDEF_INT;
    self->_updatedAt = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfType
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPartnerId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfTags
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAt
{
    return KFT_Int;
}

- (void)setIdFromString:(NSString*)aPropVal
{
    self.id = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerIdFromString:(NSString*)aPropVal
{
    self.partnerId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtFromString:(NSString*)aPropVal
{
    self.createdAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtFromString:(NSString*)aPropVal
{
    self.updatedAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaPermissionItem"];
    [aParams addIfDefinedKey:@"tags" withString:self.tags];
}

- (void)dealloc
{
    [self->_type release];
    [self->_tags release];
    [super dealloc];
}

@end

@interface KalturaPermissionItemListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaPermissionItemListResponse
@synthesize objects = _objects;
@synthesize totalCount = _totalCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaPermissionItem";
}

- (KalturaFieldType)getTypeOfTotalCount
{
    return KFT_Int;
}

- (void)setTotalCountFromString:(NSString*)aPropVal
{
    self.totalCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaPermissionItemListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@interface KalturaPermissionListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaPermissionListResponse
@synthesize objects = _objects;
@synthesize totalCount = _totalCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaPermission";
}

- (KalturaFieldType)getTypeOfTotalCount
{
    return KFT_Int;
}

- (void)setTotalCountFromString:(NSString*)aPropVal
{
    self.totalCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaPermissionListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@interface KalturaPlaylist()
@property (nonatomic,assign) int plays;
@property (nonatomic,assign) int views;
@property (nonatomic,assign) int duration;
@end

@implementation KalturaPlaylist
@synthesize playlistContent = _playlistContent;
@synthesize filters = _filters;
@synthesize totalResults = _totalResults;
@synthesize playlistType = _playlistType;
@synthesize plays = _plays;
@synthesize views = _views;
@synthesize duration = _duration;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalResults = KALTURA_UNDEF_INT;
    self->_playlistType = KALTURA_UNDEF_INT;
    self->_plays = KALTURA_UNDEF_INT;
    self->_views = KALTURA_UNDEF_INT;
    self->_duration = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfPlaylistContent
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFilters
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfFilters
{
    return @"KalturaMediaEntryFilterForPlaylist";
}

- (KalturaFieldType)getTypeOfTotalResults
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPlaylistType
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPlays
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfViews
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDuration
{
    return KFT_Int;
}

- (void)setTotalResultsFromString:(NSString*)aPropVal
{
    self.totalResults = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPlaylistTypeFromString:(NSString*)aPropVal
{
    self.playlistType = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPlaysFromString:(NSString*)aPropVal
{
    self.plays = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setViewsFromString:(NSString*)aPropVal
{
    self.views = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDurationFromString:(NSString*)aPropVal
{
    self.duration = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaPlaylist"];
    [aParams addIfDefinedKey:@"playlistContent" withString:self.playlistContent];
    [aParams addIfDefinedKey:@"filters" withArray:self.filters];
    [aParams addIfDefinedKey:@"totalResults" withInt:self.totalResults];
    [aParams addIfDefinedKey:@"playlistType" withInt:self.playlistType];
}

- (void)dealloc
{
    [self->_playlistContent release];
    [self->_filters release];
    [super dealloc];
}

@end

@interface KalturaPlaylistListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaPlaylistListResponse
@synthesize objects = _objects;
@synthesize totalCount = _totalCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaPlaylist";
}

- (KalturaFieldType)getTypeOfTotalCount
{
    return KFT_Int;
}

- (void)setTotalCountFromString:(NSString*)aPropVal
{
    self.totalCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaPlaylistListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@interface KalturaRemotePath()
@property (nonatomic,assign) int storageProfileId;
@property (nonatomic,copy) NSString* uri;
@end

@implementation KalturaRemotePath
@synthesize storageProfileId = _storageProfileId;
@synthesize uri = _uri;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_storageProfileId = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfStorageProfileId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUri
{
    return KFT_String;
}

- (void)setStorageProfileIdFromString:(NSString*)aPropVal
{
    self.storageProfileId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaRemotePath"];
}

- (void)dealloc
{
    [self->_uri release];
    [super dealloc];
}

@end

@interface KalturaRemotePathListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaRemotePathListResponse
@synthesize objects = _objects;
@synthesize totalCount = _totalCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaRemotePath";
}

- (KalturaFieldType)getTypeOfTotalCount
{
    return KFT_Int;
}

- (void)setTotalCountFromString:(NSString*)aPropVal
{
    self.totalCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaRemotePathListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@implementation KalturaUrlResource
@synthesize url = _url;

- (KalturaFieldType)getTypeOfUrl
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaUrlResource"];
    [aParams addIfDefinedKey:@"url" withString:self.url];
}

- (void)dealloc
{
    [self->_url release];
    [super dealloc];
}

@end

@implementation KalturaRemoteStorageResource
@synthesize storageProfileId = _storageProfileId;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_storageProfileId = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfStorageProfileId
{
    return KFT_Int;
}

- (void)setStorageProfileIdFromString:(NSString*)aPropVal
{
    self.storageProfileId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaRemoteStorageResource"];
    [aParams addIfDefinedKey:@"storageProfileId" withInt:self.storageProfileId];
}

@end

@implementation KalturaReportBaseTotal
@synthesize id = _id;
@synthesize data = _data;

- (KalturaFieldType)getTypeOfId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfData
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaReportBaseTotal"];
    [aParams addIfDefinedKey:@"id" withString:self.id];
    [aParams addIfDefinedKey:@"data" withString:self.data];
}

- (void)dealloc
{
    [self->_id release];
    [self->_data release];
    [super dealloc];
}

@end

@implementation KalturaReportGraph
@synthesize id = _id;
@synthesize data = _data;

- (KalturaFieldType)getTypeOfId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfData
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaReportGraph"];
    [aParams addIfDefinedKey:@"id" withString:self.id];
    [aParams addIfDefinedKey:@"data" withString:self.data];
}

- (void)dealloc
{
    [self->_id release];
    [self->_data release];
    [super dealloc];
}

@end

@implementation KalturaReportInputBaseFilter
@synthesize fromDate = _fromDate;
@synthesize toDate = _toDate;
@synthesize fromDay = _fromDay;
@synthesize toDay = _toDay;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_fromDate = KALTURA_UNDEF_INT;
    self->_toDate = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfFromDate
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfToDate
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfFromDay
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfToDay
{
    return KFT_String;
}

- (void)setFromDateFromString:(NSString*)aPropVal
{
    self.fromDate = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setToDateFromString:(NSString*)aPropVal
{
    self.toDate = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaReportInputBaseFilter"];
    [aParams addIfDefinedKey:@"fromDate" withInt:self.fromDate];
    [aParams addIfDefinedKey:@"toDate" withInt:self.toDate];
    [aParams addIfDefinedKey:@"fromDay" withString:self.fromDay];
    [aParams addIfDefinedKey:@"toDay" withString:self.toDay];
}

- (void)dealloc
{
    [self->_fromDay release];
    [self->_toDay release];
    [super dealloc];
}

@end

@implementation KalturaReportResponse
@synthesize columns = _columns;
@synthesize results = _results;

- (KalturaFieldType)getTypeOfColumns
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfResults
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfResults
{
    return @"KalturaString";
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaReportResponse"];
    [aParams addIfDefinedKey:@"columns" withString:self.columns];
    [aParams addIfDefinedKey:@"results" withArray:self.results];
}

- (void)dealloc
{
    [self->_columns release];
    [self->_results release];
    [super dealloc];
}

@end

@interface KalturaReportTable()
@property (nonatomic,copy) NSString* header;
@property (nonatomic,copy) NSString* data;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaReportTable
@synthesize header = _header;
@synthesize data = _data;
@synthesize totalCount = _totalCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfHeader
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfData
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTotalCount
{
    return KFT_Int;
}

- (void)setTotalCountFromString:(NSString*)aPropVal
{
    self.totalCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaReportTable"];
}

- (void)dealloc
{
    [self->_header release];
    [self->_data release];
    [super dealloc];
}

@end

@implementation KalturaReportTotal
@synthesize header = _header;
@synthesize data = _data;

- (KalturaFieldType)getTypeOfHeader
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfData
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaReportTotal"];
    [aParams addIfDefinedKey:@"header" withString:self.header];
    [aParams addIfDefinedKey:@"data" withString:self.data];
}

- (void)dealloc
{
    [self->_header release];
    [self->_data release];
    [super dealloc];
}

@end

@implementation KalturaSearch
@synthesize keyWords = _keyWords;
@synthesize searchSource = _searchSource;
@synthesize mediaType = _mediaType;
@synthesize extraData = _extraData;
@synthesize authData = _authData;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_searchSource = KALTURA_UNDEF_INT;
    self->_mediaType = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfKeyWords
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSearchSource
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfMediaType
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfExtraData
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAuthData
{
    return KFT_String;
}

- (void)setSearchSourceFromString:(NSString*)aPropVal
{
    self.searchSource = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setMediaTypeFromString:(NSString*)aPropVal
{
    self.mediaType = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaSearch"];
    [aParams addIfDefinedKey:@"keyWords" withString:self.keyWords];
    [aParams addIfDefinedKey:@"searchSource" withInt:self.searchSource];
    [aParams addIfDefinedKey:@"mediaType" withInt:self.mediaType];
    [aParams addIfDefinedKey:@"extraData" withString:self.extraData];
    [aParams addIfDefinedKey:@"authData" withString:self.authData];
}

- (void)dealloc
{
    [self->_keyWords release];
    [self->_extraData release];
    [self->_authData release];
    [super dealloc];
}

@end

@implementation KalturaSearchAuthData
@synthesize authData = _authData;
@synthesize loginUrl = _loginUrl;
@synthesize message = _message;

- (KalturaFieldType)getTypeOfAuthData
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfLoginUrl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfMessage
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaSearchAuthData"];
    [aParams addIfDefinedKey:@"authData" withString:self.authData];
    [aParams addIfDefinedKey:@"loginUrl" withString:self.loginUrl];
    [aParams addIfDefinedKey:@"message" withString:self.message];
}

- (void)dealloc
{
    [self->_authData release];
    [self->_loginUrl release];
    [self->_message release];
    [super dealloc];
}

@end

@implementation KalturaSearchResult
@synthesize id = _id;
@synthesize title = _title;
@synthesize thumbUrl = _thumbUrl;
@synthesize description = _description;
@synthesize tags = _tags;
@synthesize url = _url;
@synthesize sourceLink = _sourceLink;
@synthesize credit = _credit;
@synthesize licenseType = _licenseType;
@synthesize flashPlaybackType = _flashPlaybackType;
@synthesize fileExt = _fileExt;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_licenseType = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTitle
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfThumbUrl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDescription
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTags
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfUrl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSourceLink
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCredit
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfLicenseType
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfFlashPlaybackType
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFileExt
{
    return KFT_String;
}

- (void)setLicenseTypeFromString:(NSString*)aPropVal
{
    self.licenseType = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaSearchResult"];
    [aParams addIfDefinedKey:@"id" withString:self.id];
    [aParams addIfDefinedKey:@"title" withString:self.title];
    [aParams addIfDefinedKey:@"thumbUrl" withString:self.thumbUrl];
    [aParams addIfDefinedKey:@"description" withString:self.description];
    [aParams addIfDefinedKey:@"tags" withString:self.tags];
    [aParams addIfDefinedKey:@"url" withString:self.url];
    [aParams addIfDefinedKey:@"sourceLink" withString:self.sourceLink];
    [aParams addIfDefinedKey:@"credit" withString:self.credit];
    [aParams addIfDefinedKey:@"licenseType" withInt:self.licenseType];
    [aParams addIfDefinedKey:@"flashPlaybackType" withString:self.flashPlaybackType];
    [aParams addIfDefinedKey:@"fileExt" withString:self.fileExt];
}

- (void)dealloc
{
    [self->_id release];
    [self->_title release];
    [self->_thumbUrl release];
    [self->_description release];
    [self->_tags release];
    [self->_url release];
    [self->_sourceLink release];
    [self->_credit release];
    [self->_flashPlaybackType release];
    [self->_fileExt release];
    [super dealloc];
}

@end

@interface KalturaSearchResultResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) BOOL needMediaInfo;
@end

@implementation KalturaSearchResultResponse
@synthesize objects = _objects;
@synthesize needMediaInfo = _needMediaInfo;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_needMediaInfo = KALTURA_UNDEF_BOOL;
    return self;
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaSearchResult";
}

- (KalturaFieldType)getTypeOfNeedMediaInfo
{
    return KFT_Bool;
}

- (void)setNeedMediaInfoFromString:(NSString*)aPropVal
{
    self.needMediaInfo = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaSearchResultResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@interface KalturaSessionInfo()
@property (nonatomic,copy) NSString* ks;
@property (nonatomic,assign) int sessionType;
@property (nonatomic,assign) int partnerId;
@property (nonatomic,copy) NSString* userId;
@property (nonatomic,assign) int expiry;
@property (nonatomic,copy) NSString* privileges;
@end

@implementation KalturaSessionInfo
@synthesize ks = _ks;
@synthesize sessionType = _sessionType;
@synthesize partnerId = _partnerId;
@synthesize userId = _userId;
@synthesize expiry = _expiry;
@synthesize privileges = _privileges;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_sessionType = KALTURA_UNDEF_INT;
    self->_partnerId = KALTURA_UNDEF_INT;
    self->_expiry = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfKs
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSessionType
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUserId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfExpiry
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPrivileges
{
    return KFT_String;
}

- (void)setSessionTypeFromString:(NSString*)aPropVal
{
    self.sessionType = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerIdFromString:(NSString*)aPropVal
{
    self.partnerId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setExpiryFromString:(NSString*)aPropVal
{
    self.expiry = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaSessionInfo"];
}

- (void)dealloc
{
    [self->_ks release];
    [self->_userId release];
    [self->_privileges release];
    [super dealloc];
}

@end

@interface KalturaStartWidgetSessionResponse()
@property (nonatomic,assign) int partnerId;
@property (nonatomic,copy) NSString* ks;
@property (nonatomic,copy) NSString* userId;
@end

@implementation KalturaStartWidgetSessionResponse
@synthesize partnerId = _partnerId;
@synthesize ks = _ks;
@synthesize userId = _userId;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_partnerId = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfPartnerId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfKs
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfUserId
{
    return KFT_String;
}

- (void)setPartnerIdFromString:(NSString*)aPropVal
{
    self.partnerId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaStartWidgetSessionResponse"];
}

- (void)dealloc
{
    [self->_ks release];
    [self->_userId release];
    [super dealloc];
}

@end

@interface KalturaStatsEvent()
@property (nonatomic,copy) NSString* userIp;
@end

@implementation KalturaStatsEvent
@synthesize clientVer = _clientVer;
@synthesize eventType = _eventType;
@synthesize eventTimestamp = _eventTimestamp;
@synthesize sessionId = _sessionId;
@synthesize partnerId = _partnerId;
@synthesize entryId = _entryId;
@synthesize uniqueViewer = _uniqueViewer;
@synthesize widgetId = _widgetId;
@synthesize uiconfId = _uiconfId;
@synthesize userId = _userId;
@synthesize currentPoint = _currentPoint;
@synthesize duration = _duration;
@synthesize userIp = _userIp;
@synthesize processDuration = _processDuration;
@synthesize controlId = _controlId;
@synthesize seek = _seek;
@synthesize newPoint = _newPoint;
@synthesize referrer = _referrer;
@synthesize isFirstInSession = _isFirstInSession;
@synthesize applicationId = _applicationId;
@synthesize contextId = _contextId;
@synthesize featureType = _featureType;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_eventType = KALTURA_UNDEF_INT;
    self->_eventTimestamp = KALTURA_UNDEF_FLOAT;
    self->_partnerId = KALTURA_UNDEF_INT;
    self->_uiconfId = KALTURA_UNDEF_INT;
    self->_currentPoint = KALTURA_UNDEF_INT;
    self->_duration = KALTURA_UNDEF_INT;
    self->_processDuration = KALTURA_UNDEF_INT;
    self->_seek = KALTURA_UNDEF_BOOL;
    self->_newPoint = KALTURA_UNDEF_INT;
    self->_isFirstInSession = KALTURA_UNDEF_BOOL;
    self->_contextId = KALTURA_UNDEF_INT;
    self->_featureType = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfClientVer
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfEventType
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfEventTimestamp
{
    return KFT_Float;
}

- (KalturaFieldType)getTypeOfSessionId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPartnerId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfEntryId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfUniqueViewer
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfWidgetId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfUiconfId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUserId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCurrentPoint
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDuration
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUserIp
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfProcessDuration
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfControlId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSeek
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfNewPoint
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfReferrer
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfIsFirstInSession
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfApplicationId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfContextId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfFeatureType
{
    return KFT_Int;
}

- (void)setEventTypeFromString:(NSString*)aPropVal
{
    self.eventType = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setEventTimestampFromString:(NSString*)aPropVal
{
    self.eventTimestamp = [KalturaSimpleTypeParser parseFloat:aPropVal];
}

- (void)setPartnerIdFromString:(NSString*)aPropVal
{
    self.partnerId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUiconfIdFromString:(NSString*)aPropVal
{
    self.uiconfId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCurrentPointFromString:(NSString*)aPropVal
{
    self.currentPoint = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDurationFromString:(NSString*)aPropVal
{
    self.duration = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setProcessDurationFromString:(NSString*)aPropVal
{
    self.processDuration = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setSeekFromString:(NSString*)aPropVal
{
    self.seek = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)setNewPointFromString:(NSString*)aPropVal
{
    self.newPoint = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setIsFirstInSessionFromString:(NSString*)aPropVal
{
    self.isFirstInSession = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)setContextIdFromString:(NSString*)aPropVal
{
    self.contextId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setFeatureTypeFromString:(NSString*)aPropVal
{
    self.featureType = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaStatsEvent"];
    [aParams addIfDefinedKey:@"clientVer" withString:self.clientVer];
    [aParams addIfDefinedKey:@"eventType" withInt:self.eventType];
    [aParams addIfDefinedKey:@"eventTimestamp" withFloat:self.eventTimestamp];
    [aParams addIfDefinedKey:@"sessionId" withString:self.sessionId];
    [aParams addIfDefinedKey:@"partnerId" withInt:self.partnerId];
    [aParams addIfDefinedKey:@"entryId" withString:self.entryId];
    [aParams addIfDefinedKey:@"uniqueViewer" withString:self.uniqueViewer];
    [aParams addIfDefinedKey:@"widgetId" withString:self.widgetId];
    [aParams addIfDefinedKey:@"uiconfId" withInt:self.uiconfId];
    [aParams addIfDefinedKey:@"userId" withString:self.userId];
    [aParams addIfDefinedKey:@"currentPoint" withInt:self.currentPoint];
    [aParams addIfDefinedKey:@"duration" withInt:self.duration];
    [aParams addIfDefinedKey:@"processDuration" withInt:self.processDuration];
    [aParams addIfDefinedKey:@"controlId" withString:self.controlId];
    [aParams addIfDefinedKey:@"seek" withBool:self.seek];
    [aParams addIfDefinedKey:@"newPoint" withInt:self.newPoint];
    [aParams addIfDefinedKey:@"referrer" withString:self.referrer];
    [aParams addIfDefinedKey:@"isFirstInSession" withBool:self.isFirstInSession];
    [aParams addIfDefinedKey:@"applicationId" withString:self.applicationId];
    [aParams addIfDefinedKey:@"contextId" withInt:self.contextId];
    [aParams addIfDefinedKey:@"featureType" withInt:self.featureType];
}

- (void)dealloc
{
    [self->_clientVer release];
    [self->_sessionId release];
    [self->_entryId release];
    [self->_uniqueViewer release];
    [self->_widgetId release];
    [self->_userId release];
    [self->_userIp release];
    [self->_controlId release];
    [self->_referrer release];
    [self->_applicationId release];
    [super dealloc];
}

@end

@interface KalturaStatsKmcEvent()
@property (nonatomic,copy) NSString* userIp;
@end

@implementation KalturaStatsKmcEvent
@synthesize clientVer = _clientVer;
@synthesize kmcEventActionPath = _kmcEventActionPath;
@synthesize kmcEventType = _kmcEventType;
@synthesize eventTimestamp = _eventTimestamp;
@synthesize sessionId = _sessionId;
@synthesize partnerId = _partnerId;
@synthesize entryId = _entryId;
@synthesize widgetId = _widgetId;
@synthesize uiconfId = _uiconfId;
@synthesize userId = _userId;
@synthesize userIp = _userIp;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_kmcEventType = KALTURA_UNDEF_INT;
    self->_eventTimestamp = KALTURA_UNDEF_FLOAT;
    self->_partnerId = KALTURA_UNDEF_INT;
    self->_uiconfId = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfClientVer
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfKmcEventActionPath
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfKmcEventType
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfEventTimestamp
{
    return KFT_Float;
}

- (KalturaFieldType)getTypeOfSessionId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPartnerId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfEntryId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfWidgetId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfUiconfId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUserId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfUserIp
{
    return KFT_String;
}

- (void)setKmcEventTypeFromString:(NSString*)aPropVal
{
    self.kmcEventType = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setEventTimestampFromString:(NSString*)aPropVal
{
    self.eventTimestamp = [KalturaSimpleTypeParser parseFloat:aPropVal];
}

- (void)setPartnerIdFromString:(NSString*)aPropVal
{
    self.partnerId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUiconfIdFromString:(NSString*)aPropVal
{
    self.uiconfId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaStatsKmcEvent"];
    [aParams addIfDefinedKey:@"clientVer" withString:self.clientVer];
    [aParams addIfDefinedKey:@"kmcEventActionPath" withString:self.kmcEventActionPath];
    [aParams addIfDefinedKey:@"kmcEventType" withInt:self.kmcEventType];
    [aParams addIfDefinedKey:@"eventTimestamp" withFloat:self.eventTimestamp];
    [aParams addIfDefinedKey:@"sessionId" withString:self.sessionId];
    [aParams addIfDefinedKey:@"partnerId" withInt:self.partnerId];
    [aParams addIfDefinedKey:@"entryId" withString:self.entryId];
    [aParams addIfDefinedKey:@"widgetId" withString:self.widgetId];
    [aParams addIfDefinedKey:@"uiconfId" withInt:self.uiconfId];
    [aParams addIfDefinedKey:@"userId" withString:self.userId];
}

- (void)dealloc
{
    [self->_clientVer release];
    [self->_kmcEventActionPath release];
    [self->_sessionId release];
    [self->_entryId release];
    [self->_widgetId release];
    [self->_userId release];
    [self->_userIp release];
    [super dealloc];
}

@end

@interface KalturaStorageProfile()
@property (nonatomic,assign) int id;
@property (nonatomic,assign) int createdAt;
@property (nonatomic,assign) int updatedAt;
@property (nonatomic,assign) int partnerId;
@end

@implementation KalturaStorageProfile
@synthesize id = _id;
@synthesize createdAt = _createdAt;
@synthesize updatedAt = _updatedAt;
@synthesize partnerId = _partnerId;
@synthesize name = _name;
@synthesize systemName = _systemName;
@synthesize desciption = _desciption;
@synthesize status = _status;
@synthesize protocol = _protocol;
@synthesize storageUrl = _storageUrl;
@synthesize storageBaseDir = _storageBaseDir;
@synthesize storageUsername = _storageUsername;
@synthesize storagePassword = _storagePassword;
@synthesize storageFtpPassiveMode = _storageFtpPassiveMode;
@synthesize deliveryHttpBaseUrl = _deliveryHttpBaseUrl;
@synthesize deliveryRmpBaseUrl = _deliveryRmpBaseUrl;
@synthesize deliveryIisBaseUrl = _deliveryIisBaseUrl;
@synthesize minFileSize = _minFileSize;
@synthesize maxFileSize = _maxFileSize;
@synthesize flavorParamsIds = _flavorParamsIds;
@synthesize maxConcurrentConnections = _maxConcurrentConnections;
@synthesize pathManagerClass = _pathManagerClass;
@synthesize pathManagerParams = _pathManagerParams;
@synthesize urlManagerClass = _urlManagerClass;
@synthesize urlManagerParams = _urlManagerParams;
@synthesize trigger = _trigger;
@synthesize deliveryPriority = _deliveryPriority;
@synthesize deliveryStatus = _deliveryStatus;
@synthesize rtmpPrefix = _rtmpPrefix;
@synthesize readyBehavior = _readyBehavior;
@synthesize allowAutoDelete = _allowAutoDelete;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_id = KALTURA_UNDEF_INT;
    self->_createdAt = KALTURA_UNDEF_INT;
    self->_updatedAt = KALTURA_UNDEF_INT;
    self->_partnerId = KALTURA_UNDEF_INT;
    self->_status = KALTURA_UNDEF_INT;
    self->_protocol = KALTURA_UNDEF_INT;
    self->_storageFtpPassiveMode = KALTURA_UNDEF_BOOL;
    self->_minFileSize = KALTURA_UNDEF_INT;
    self->_maxFileSize = KALTURA_UNDEF_INT;
    self->_maxConcurrentConnections = KALTURA_UNDEF_INT;
    self->_trigger = KALTURA_UNDEF_INT;
    self->_deliveryPriority = KALTURA_UNDEF_INT;
    self->_deliveryStatus = KALTURA_UNDEF_INT;
    self->_readyBehavior = KALTURA_UNDEF_INT;
    self->_allowAutoDelete = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSystemName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDesciption
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatus
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfProtocol
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfStorageUrl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStorageBaseDir
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStorageUsername
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStoragePassword
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStorageFtpPassiveMode
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfDeliveryHttpBaseUrl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDeliveryRmpBaseUrl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDeliveryIisBaseUrl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfMinFileSize
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfMaxFileSize
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfFlavorParamsIds
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfMaxConcurrentConnections
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPathManagerClass
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPathManagerParams
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfPathManagerParams
{
    return @"KalturaKeyValue";
}

- (KalturaFieldType)getTypeOfUrlManagerClass
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfUrlManagerParams
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfUrlManagerParams
{
    return @"KalturaKeyValue";
}

- (KalturaFieldType)getTypeOfTrigger
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDeliveryPriority
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDeliveryStatus
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfRtmpPrefix
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfReadyBehavior
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfAllowAutoDelete
{
    return KFT_Int;
}

- (void)setIdFromString:(NSString*)aPropVal
{
    self.id = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtFromString:(NSString*)aPropVal
{
    self.createdAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtFromString:(NSString*)aPropVal
{
    self.updatedAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerIdFromString:(NSString*)aPropVal
{
    self.partnerId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusFromString:(NSString*)aPropVal
{
    self.status = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setProtocolFromString:(NSString*)aPropVal
{
    self.protocol = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStorageFtpPassiveModeFromString:(NSString*)aPropVal
{
    self.storageFtpPassiveMode = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)setMinFileSizeFromString:(NSString*)aPropVal
{
    self.minFileSize = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setMaxFileSizeFromString:(NSString*)aPropVal
{
    self.maxFileSize = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setMaxConcurrentConnectionsFromString:(NSString*)aPropVal
{
    self.maxConcurrentConnections = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setTriggerFromString:(NSString*)aPropVal
{
    self.trigger = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDeliveryPriorityFromString:(NSString*)aPropVal
{
    self.deliveryPriority = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDeliveryStatusFromString:(NSString*)aPropVal
{
    self.deliveryStatus = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setReadyBehaviorFromString:(NSString*)aPropVal
{
    self.readyBehavior = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setAllowAutoDeleteFromString:(NSString*)aPropVal
{
    self.allowAutoDelete = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaStorageProfile"];
    [aParams addIfDefinedKey:@"name" withString:self.name];
    [aParams addIfDefinedKey:@"systemName" withString:self.systemName];
    [aParams addIfDefinedKey:@"desciption" withString:self.desciption];
    [aParams addIfDefinedKey:@"status" withInt:self.status];
    [aParams addIfDefinedKey:@"protocol" withInt:self.protocol];
    [aParams addIfDefinedKey:@"storageUrl" withString:self.storageUrl];
    [aParams addIfDefinedKey:@"storageBaseDir" withString:self.storageBaseDir];
    [aParams addIfDefinedKey:@"storageUsername" withString:self.storageUsername];
    [aParams addIfDefinedKey:@"storagePassword" withString:self.storagePassword];
    [aParams addIfDefinedKey:@"storageFtpPassiveMode" withBool:self.storageFtpPassiveMode];
    [aParams addIfDefinedKey:@"deliveryHttpBaseUrl" withString:self.deliveryHttpBaseUrl];
    [aParams addIfDefinedKey:@"deliveryRmpBaseUrl" withString:self.deliveryRmpBaseUrl];
    [aParams addIfDefinedKey:@"deliveryIisBaseUrl" withString:self.deliveryIisBaseUrl];
    [aParams addIfDefinedKey:@"minFileSize" withInt:self.minFileSize];
    [aParams addIfDefinedKey:@"maxFileSize" withInt:self.maxFileSize];
    [aParams addIfDefinedKey:@"flavorParamsIds" withString:self.flavorParamsIds];
    [aParams addIfDefinedKey:@"maxConcurrentConnections" withInt:self.maxConcurrentConnections];
    [aParams addIfDefinedKey:@"pathManagerClass" withString:self.pathManagerClass];
    [aParams addIfDefinedKey:@"pathManagerParams" withArray:self.pathManagerParams];
    [aParams addIfDefinedKey:@"urlManagerClass" withString:self.urlManagerClass];
    [aParams addIfDefinedKey:@"urlManagerParams" withArray:self.urlManagerParams];
    [aParams addIfDefinedKey:@"trigger" withInt:self.trigger];
    [aParams addIfDefinedKey:@"deliveryPriority" withInt:self.deliveryPriority];
    [aParams addIfDefinedKey:@"deliveryStatus" withInt:self.deliveryStatus];
    [aParams addIfDefinedKey:@"rtmpPrefix" withString:self.rtmpPrefix];
    [aParams addIfDefinedKey:@"readyBehavior" withInt:self.readyBehavior];
    [aParams addIfDefinedKey:@"allowAutoDelete" withInt:self.allowAutoDelete];
}

- (void)dealloc
{
    [self->_name release];
    [self->_systemName release];
    [self->_desciption release];
    [self->_storageUrl release];
    [self->_storageBaseDir release];
    [self->_storageUsername release];
    [self->_storagePassword release];
    [self->_deliveryHttpBaseUrl release];
    [self->_deliveryRmpBaseUrl release];
    [self->_deliveryIisBaseUrl release];
    [self->_flavorParamsIds release];
    [self->_pathManagerClass release];
    [self->_pathManagerParams release];
    [self->_urlManagerClass release];
    [self->_urlManagerParams release];
    [self->_rtmpPrefix release];
    [super dealloc];
}

@end

@interface KalturaStorageProfileListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaStorageProfileListResponse
@synthesize objects = _objects;
@synthesize totalCount = _totalCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaStorageProfile";
}

- (KalturaFieldType)getTypeOfTotalCount
{
    return KFT_Int;
}

- (void)setTotalCountFromString:(NSString*)aPropVal
{
    self.totalCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaStorageProfileListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@implementation KalturaSyndicationFeedEntryCount
@synthesize totalEntryCount = _totalEntryCount;
@synthesize actualEntryCount = _actualEntryCount;
@synthesize requireTranscodingCount = _requireTranscodingCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalEntryCount = KALTURA_UNDEF_INT;
    self->_actualEntryCount = KALTURA_UNDEF_INT;
    self->_requireTranscodingCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfTotalEntryCount
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfActualEntryCount
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfRequireTranscodingCount
{
    return KFT_Int;
}

- (void)setTotalEntryCountFromString:(NSString*)aPropVal
{
    self.totalEntryCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setActualEntryCountFromString:(NSString*)aPropVal
{
    self.actualEntryCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setRequireTranscodingCountFromString:(NSString*)aPropVal
{
    self.requireTranscodingCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaSyndicationFeedEntryCount"];
    [aParams addIfDefinedKey:@"totalEntryCount" withInt:self.totalEntryCount];
    [aParams addIfDefinedKey:@"actualEntryCount" withInt:self.actualEntryCount];
    [aParams addIfDefinedKey:@"requireTranscodingCount" withInt:self.requireTranscodingCount];
}

@end

@interface KalturaThumbAsset()
@property (nonatomic,assign) int width;
@property (nonatomic,assign) int height;
@property (nonatomic,assign) int status;
@end

@implementation KalturaThumbAsset
@synthesize thumbParamsId = _thumbParamsId;
@synthesize width = _width;
@synthesize height = _height;
@synthesize status = _status;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_thumbParamsId = KALTURA_UNDEF_INT;
    self->_width = KALTURA_UNDEF_INT;
    self->_height = KALTURA_UNDEF_INT;
    self->_status = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfThumbParamsId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfWidth
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfHeight
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfStatus
{
    return KFT_Int;
}

- (void)setThumbParamsIdFromString:(NSString*)aPropVal
{
    self.thumbParamsId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setWidthFromString:(NSString*)aPropVal
{
    self.width = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setHeightFromString:(NSString*)aPropVal
{
    self.height = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusFromString:(NSString*)aPropVal
{
    self.status = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaThumbAsset"];
    [aParams addIfDefinedKey:@"thumbParamsId" withInt:self.thumbParamsId];
}

@end

@interface KalturaThumbAssetListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaThumbAssetListResponse
@synthesize objects = _objects;
@synthesize totalCount = _totalCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaThumbAsset";
}

- (KalturaFieldType)getTypeOfTotalCount
{
    return KFT_Int;
}

- (void)setTotalCountFromString:(NSString*)aPropVal
{
    self.totalCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaThumbAssetListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@implementation KalturaThumbParams
@synthesize cropType = _cropType;
@synthesize quality = _quality;
@synthesize cropX = _cropX;
@synthesize cropY = _cropY;
@synthesize cropWidth = _cropWidth;
@synthesize cropHeight = _cropHeight;
@synthesize videoOffset = _videoOffset;
@synthesize width = _width;
@synthesize height = _height;
@synthesize scaleWidth = _scaleWidth;
@synthesize scaleHeight = _scaleHeight;
@synthesize backgroundColor = _backgroundColor;
@synthesize sourceParamsId = _sourceParamsId;
@synthesize format = _format;
@synthesize density = _density;
@synthesize stripProfiles = _stripProfiles;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_cropType = KALTURA_UNDEF_INT;
    self->_quality = KALTURA_UNDEF_INT;
    self->_cropX = KALTURA_UNDEF_INT;
    self->_cropY = KALTURA_UNDEF_INT;
    self->_cropWidth = KALTURA_UNDEF_INT;
    self->_cropHeight = KALTURA_UNDEF_INT;
    self->_videoOffset = KALTURA_UNDEF_FLOAT;
    self->_width = KALTURA_UNDEF_INT;
    self->_height = KALTURA_UNDEF_INT;
    self->_scaleWidth = KALTURA_UNDEF_FLOAT;
    self->_scaleHeight = KALTURA_UNDEF_FLOAT;
    self->_sourceParamsId = KALTURA_UNDEF_INT;
    self->_density = KALTURA_UNDEF_INT;
    self->_stripProfiles = KALTURA_UNDEF_BOOL;
    return self;
}

- (KalturaFieldType)getTypeOfCropType
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfQuality
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCropX
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCropY
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCropWidth
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCropHeight
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfVideoOffset
{
    return KFT_Float;
}

- (KalturaFieldType)getTypeOfWidth
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfHeight
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfScaleWidth
{
    return KFT_Float;
}

- (KalturaFieldType)getTypeOfScaleHeight
{
    return KFT_Float;
}

- (KalturaFieldType)getTypeOfBackgroundColor
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSourceParamsId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfFormat
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDensity
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfStripProfiles
{
    return KFT_Bool;
}

- (void)setCropTypeFromString:(NSString*)aPropVal
{
    self.cropType = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setQualityFromString:(NSString*)aPropVal
{
    self.quality = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCropXFromString:(NSString*)aPropVal
{
    self.cropX = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCropYFromString:(NSString*)aPropVal
{
    self.cropY = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCropWidthFromString:(NSString*)aPropVal
{
    self.cropWidth = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCropHeightFromString:(NSString*)aPropVal
{
    self.cropHeight = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setVideoOffsetFromString:(NSString*)aPropVal
{
    self.videoOffset = [KalturaSimpleTypeParser parseFloat:aPropVal];
}

- (void)setWidthFromString:(NSString*)aPropVal
{
    self.width = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setHeightFromString:(NSString*)aPropVal
{
    self.height = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setScaleWidthFromString:(NSString*)aPropVal
{
    self.scaleWidth = [KalturaSimpleTypeParser parseFloat:aPropVal];
}

- (void)setScaleHeightFromString:(NSString*)aPropVal
{
    self.scaleHeight = [KalturaSimpleTypeParser parseFloat:aPropVal];
}

- (void)setSourceParamsIdFromString:(NSString*)aPropVal
{
    self.sourceParamsId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDensityFromString:(NSString*)aPropVal
{
    self.density = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStripProfilesFromString:(NSString*)aPropVal
{
    self.stripProfiles = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaThumbParams"];
    [aParams addIfDefinedKey:@"cropType" withInt:self.cropType];
    [aParams addIfDefinedKey:@"quality" withInt:self.quality];
    [aParams addIfDefinedKey:@"cropX" withInt:self.cropX];
    [aParams addIfDefinedKey:@"cropY" withInt:self.cropY];
    [aParams addIfDefinedKey:@"cropWidth" withInt:self.cropWidth];
    [aParams addIfDefinedKey:@"cropHeight" withInt:self.cropHeight];
    [aParams addIfDefinedKey:@"videoOffset" withFloat:self.videoOffset];
    [aParams addIfDefinedKey:@"width" withInt:self.width];
    [aParams addIfDefinedKey:@"height" withInt:self.height];
    [aParams addIfDefinedKey:@"scaleWidth" withFloat:self.scaleWidth];
    [aParams addIfDefinedKey:@"scaleHeight" withFloat:self.scaleHeight];
    [aParams addIfDefinedKey:@"backgroundColor" withString:self.backgroundColor];
    [aParams addIfDefinedKey:@"sourceParamsId" withInt:self.sourceParamsId];
    [aParams addIfDefinedKey:@"format" withString:self.format];
    [aParams addIfDefinedKey:@"density" withInt:self.density];
    [aParams addIfDefinedKey:@"stripProfiles" withBool:self.stripProfiles];
}

- (void)dealloc
{
    [self->_backgroundColor release];
    [self->_format release];
    [super dealloc];
}

@end

@interface KalturaThumbParamsListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaThumbParamsListResponse
@synthesize objects = _objects;
@synthesize totalCount = _totalCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaThumbParams";
}

- (KalturaFieldType)getTypeOfTotalCount
{
    return KFT_Int;
}

- (void)setTotalCountFromString:(NSString*)aPropVal
{
    self.totalCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaThumbParamsListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@implementation KalturaThumbParamsOutput
@synthesize thumbParamsId = _thumbParamsId;
@synthesize thumbParamsVersion = _thumbParamsVersion;
@synthesize thumbAssetId = _thumbAssetId;
@synthesize thumbAssetVersion = _thumbAssetVersion;
@synthesize rotate = _rotate;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_thumbParamsId = KALTURA_UNDEF_INT;
    self->_rotate = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfThumbParamsId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfThumbParamsVersion
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfThumbAssetId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfThumbAssetVersion
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfRotate
{
    return KFT_Int;
}

- (void)setThumbParamsIdFromString:(NSString*)aPropVal
{
    self.thumbParamsId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setRotateFromString:(NSString*)aPropVal
{
    self.rotate = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaThumbParamsOutput"];
    [aParams addIfDefinedKey:@"thumbParamsId" withInt:self.thumbParamsId];
    [aParams addIfDefinedKey:@"thumbParamsVersion" withString:self.thumbParamsVersion];
    [aParams addIfDefinedKey:@"thumbAssetId" withString:self.thumbAssetId];
    [aParams addIfDefinedKey:@"thumbAssetVersion" withString:self.thumbAssetVersion];
    [aParams addIfDefinedKey:@"rotate" withInt:self.rotate];
}

- (void)dealloc
{
    [self->_thumbParamsVersion release];
    [self->_thumbAssetId release];
    [self->_thumbAssetVersion release];
    [super dealloc];
}

@end

@interface KalturaThumbParamsOutputListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaThumbParamsOutputListResponse
@synthesize objects = _objects;
@synthesize totalCount = _totalCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaThumbParamsOutput";
}

- (KalturaFieldType)getTypeOfTotalCount
{
    return KFT_Int;
}

- (void)setTotalCountFromString:(NSString*)aPropVal
{
    self.totalCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaThumbParamsOutputListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@interface KalturaUiConf()
@property (nonatomic,assign) int id;
@property (nonatomic,assign) int partnerId;
@property (nonatomic,copy) NSString* objTypeAsString;
@property (nonatomic,copy) NSString* confFilePath;
@property (nonatomic,assign) int createdAt;
@property (nonatomic,assign) int updatedAt;
@end

@implementation KalturaUiConf
@synthesize id = _id;
@synthesize name = _name;
@synthesize description = _description;
@synthesize partnerId = _partnerId;
@synthesize objType = _objType;
@synthesize objTypeAsString = _objTypeAsString;
@synthesize width = _width;
@synthesize height = _height;
@synthesize htmlParams = _htmlParams;
@synthesize swfUrl = _swfUrl;
@synthesize confFilePath = _confFilePath;
@synthesize confFile = _confFile;
@synthesize confFileFeatures = _confFileFeatures;
@synthesize confVars = _confVars;
@synthesize useCdn = _useCdn;
@synthesize tags = _tags;
@synthesize swfUrlVersion = _swfUrlVersion;
@synthesize createdAt = _createdAt;
@synthesize updatedAt = _updatedAt;
@synthesize creationMode = _creationMode;
@synthesize html5Url = _html5Url;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_id = KALTURA_UNDEF_INT;
    self->_partnerId = KALTURA_UNDEF_INT;
    self->_objType = KALTURA_UNDEF_INT;
    self->_width = KALTURA_UNDEF_INT;
    self->_height = KALTURA_UNDEF_INT;
    self->_useCdn = KALTURA_UNDEF_BOOL;
    self->_createdAt = KALTURA_UNDEF_INT;
    self->_updatedAt = KALTURA_UNDEF_INT;
    self->_creationMode = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDescription
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPartnerId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfObjType
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfObjTypeAsString
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfWidth
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfHeight
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfHtmlParams
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSwfUrl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfConfFilePath
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfConfFile
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfConfFileFeatures
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfConfVars
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfUseCdn
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfTags
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSwfUrlVersion
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreationMode
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfHtml5Url
{
    return KFT_String;
}

- (void)setIdFromString:(NSString*)aPropVal
{
    self.id = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerIdFromString:(NSString*)aPropVal
{
    self.partnerId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setObjTypeFromString:(NSString*)aPropVal
{
    self.objType = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setWidthFromString:(NSString*)aPropVal
{
    self.width = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setHeightFromString:(NSString*)aPropVal
{
    self.height = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUseCdnFromString:(NSString*)aPropVal
{
    self.useCdn = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)setCreatedAtFromString:(NSString*)aPropVal
{
    self.createdAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtFromString:(NSString*)aPropVal
{
    self.updatedAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreationModeFromString:(NSString*)aPropVal
{
    self.creationMode = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaUiConf"];
    [aParams addIfDefinedKey:@"name" withString:self.name];
    [aParams addIfDefinedKey:@"description" withString:self.description];
    [aParams addIfDefinedKey:@"objType" withInt:self.objType];
    [aParams addIfDefinedKey:@"width" withInt:self.width];
    [aParams addIfDefinedKey:@"height" withInt:self.height];
    [aParams addIfDefinedKey:@"htmlParams" withString:self.htmlParams];
    [aParams addIfDefinedKey:@"swfUrl" withString:self.swfUrl];
    [aParams addIfDefinedKey:@"confFile" withString:self.confFile];
    [aParams addIfDefinedKey:@"confFileFeatures" withString:self.confFileFeatures];
    [aParams addIfDefinedKey:@"confVars" withString:self.confVars];
    [aParams addIfDefinedKey:@"useCdn" withBool:self.useCdn];
    [aParams addIfDefinedKey:@"tags" withString:self.tags];
    [aParams addIfDefinedKey:@"swfUrlVersion" withString:self.swfUrlVersion];
    [aParams addIfDefinedKey:@"creationMode" withInt:self.creationMode];
    [aParams addIfDefinedKey:@"html5Url" withString:self.html5Url];
}

- (void)dealloc
{
    [self->_name release];
    [self->_description release];
    [self->_objTypeAsString release];
    [self->_htmlParams release];
    [self->_swfUrl release];
    [self->_confFilePath release];
    [self->_confFile release];
    [self->_confFileFeatures release];
    [self->_confVars release];
    [self->_tags release];
    [self->_swfUrlVersion release];
    [self->_html5Url release];
    [super dealloc];
}

@end

@interface KalturaUiConfListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaUiConfListResponse
@synthesize objects = _objects;
@synthesize totalCount = _totalCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaUiConf";
}

- (KalturaFieldType)getTypeOfTotalCount
{
    return KFT_Int;
}

- (void)setTotalCountFromString:(NSString*)aPropVal
{
    self.totalCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaUiConfListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@implementation KalturaUiConfTypeInfo
@synthesize type = _type;
@synthesize versions = _versions;
@synthesize directory = _directory;
@synthesize filename = _filename;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_type = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfType
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfVersions
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfVersions
{
    return @"KalturaString";
}

- (KalturaFieldType)getTypeOfDirectory
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFilename
{
    return KFT_String;
}

- (void)setTypeFromString:(NSString*)aPropVal
{
    self.type = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaUiConfTypeInfo"];
    [aParams addIfDefinedKey:@"type" withInt:self.type];
    [aParams addIfDefinedKey:@"versions" withArray:self.versions];
    [aParams addIfDefinedKey:@"directory" withString:self.directory];
    [aParams addIfDefinedKey:@"filename" withString:self.filename];
}

- (void)dealloc
{
    [self->_versions release];
    [self->_directory release];
    [self->_filename release];
    [super dealloc];
}

@end

@implementation KalturaUploadResponse
@synthesize uploadTokenId = _uploadTokenId;
@synthesize fileSize = _fileSize;
@synthesize errorCode = _errorCode;
@synthesize errorDescription = _errorDescription;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_fileSize = KALTURA_UNDEF_INT;
    self->_errorCode = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfUploadTokenId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFileSize
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfErrorCode
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfErrorDescription
{
    return KFT_String;
}

- (void)setFileSizeFromString:(NSString*)aPropVal
{
    self.fileSize = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setErrorCodeFromString:(NSString*)aPropVal
{
    self.errorCode = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaUploadResponse"];
    [aParams addIfDefinedKey:@"uploadTokenId" withString:self.uploadTokenId];
    [aParams addIfDefinedKey:@"fileSize" withInt:self.fileSize];
    [aParams addIfDefinedKey:@"errorCode" withInt:self.errorCode];
    [aParams addIfDefinedKey:@"errorDescription" withString:self.errorDescription];
}

- (void)dealloc
{
    [self->_uploadTokenId release];
    [self->_errorDescription release];
    [super dealloc];
}

@end

@interface KalturaUploadToken()
@property (nonatomic,copy) NSString* id;
@property (nonatomic,assign) int partnerId;
@property (nonatomic,copy) NSString* userId;
@property (nonatomic,assign) int status;
@property (nonatomic,assign) double uploadedFileSize;
@property (nonatomic,assign) int createdAt;
@property (nonatomic,assign) int updatedAt;
@end

@implementation KalturaUploadToken
@synthesize id = _id;
@synthesize partnerId = _partnerId;
@synthesize userId = _userId;
@synthesize status = _status;
@synthesize fileName = _fileName;
@synthesize fileSize = _fileSize;
@synthesize uploadedFileSize = _uploadedFileSize;
@synthesize createdAt = _createdAt;
@synthesize updatedAt = _updatedAt;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_partnerId = KALTURA_UNDEF_INT;
    self->_status = KALTURA_UNDEF_INT;
    self->_fileSize = KALTURA_UNDEF_FLOAT;
    self->_uploadedFileSize = KALTURA_UNDEF_FLOAT;
    self->_createdAt = KALTURA_UNDEF_INT;
    self->_updatedAt = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPartnerId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUserId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatus
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfFileName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFileSize
{
    return KFT_Float;
}

- (KalturaFieldType)getTypeOfUploadedFileSize
{
    return KFT_Float;
}

- (KalturaFieldType)getTypeOfCreatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAt
{
    return KFT_Int;
}

- (void)setPartnerIdFromString:(NSString*)aPropVal
{
    self.partnerId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusFromString:(NSString*)aPropVal
{
    self.status = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setFileSizeFromString:(NSString*)aPropVal
{
    self.fileSize = [KalturaSimpleTypeParser parseFloat:aPropVal];
}

- (void)setUploadedFileSizeFromString:(NSString*)aPropVal
{
    self.uploadedFileSize = [KalturaSimpleTypeParser parseFloat:aPropVal];
}

- (void)setCreatedAtFromString:(NSString*)aPropVal
{
    self.createdAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtFromString:(NSString*)aPropVal
{
    self.updatedAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaUploadToken"];
    [aParams addIfDefinedKey:@"fileName" withString:self.fileName];
    [aParams addIfDefinedKey:@"fileSize" withFloat:self.fileSize];
}

- (void)dealloc
{
    [self->_id release];
    [self->_userId release];
    [self->_fileName release];
    [super dealloc];
}

@end

@interface KalturaUploadTokenListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaUploadTokenListResponse
@synthesize objects = _objects;
@synthesize totalCount = _totalCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaUploadToken";
}

- (KalturaFieldType)getTypeOfTotalCount
{
    return KFT_Int;
}

- (void)setTotalCountFromString:(NSString*)aPropVal
{
    self.totalCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaUploadTokenListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@interface KalturaUser()
@property (nonatomic,assign) int partnerId;
@property (nonatomic,assign) int createdAt;
@property (nonatomic,assign) int updatedAt;
@property (nonatomic,assign) int storageSize;
@property (nonatomic,assign) int lastLoginTime;
@property (nonatomic,assign) int statusUpdatedAt;
@property (nonatomic,assign) int deletedAt;
@property (nonatomic,assign) BOOL loginEnabled;
@property (nonatomic,copy) NSString* roleNames;
@property (nonatomic,assign) BOOL isAccountOwner;
@end

@implementation KalturaUser
@synthesize id = _id;
@synthesize partnerId = _partnerId;
@synthesize screenName = _screenName;
@synthesize fullName = _fullName;
@synthesize email = _email;
@synthesize dateOfBirth = _dateOfBirth;
@synthesize country = _country;
@synthesize state = _state;
@synthesize city = _city;
@synthesize zip = _zip;
@synthesize thumbnailUrl = _thumbnailUrl;
@synthesize description = _description;
@synthesize tags = _tags;
@synthesize adminTags = _adminTags;
@synthesize gender = _gender;
@synthesize status = _status;
@synthesize createdAt = _createdAt;
@synthesize updatedAt = _updatedAt;
@synthesize partnerData = _partnerData;
@synthesize indexedPartnerDataInt = _indexedPartnerDataInt;
@synthesize indexedPartnerDataString = _indexedPartnerDataString;
@synthesize storageSize = _storageSize;
@synthesize password = _password;
@synthesize firstName = _firstName;
@synthesize lastName = _lastName;
@synthesize isAdmin = _isAdmin;
@synthesize lastLoginTime = _lastLoginTime;
@synthesize statusUpdatedAt = _statusUpdatedAt;
@synthesize deletedAt = _deletedAt;
@synthesize loginEnabled = _loginEnabled;
@synthesize roleIds = _roleIds;
@synthesize roleNames = _roleNames;
@synthesize isAccountOwner = _isAccountOwner;
@synthesize allowedPartnerIds = _allowedPartnerIds;
@synthesize allowedPartnerPackages = _allowedPartnerPackages;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_partnerId = KALTURA_UNDEF_INT;
    self->_dateOfBirth = KALTURA_UNDEF_INT;
    self->_gender = KALTURA_UNDEF_INT;
    self->_status = KALTURA_UNDEF_INT;
    self->_createdAt = KALTURA_UNDEF_INT;
    self->_updatedAt = KALTURA_UNDEF_INT;
    self->_indexedPartnerDataInt = KALTURA_UNDEF_INT;
    self->_storageSize = KALTURA_UNDEF_INT;
    self->_isAdmin = KALTURA_UNDEF_BOOL;
    self->_lastLoginTime = KALTURA_UNDEF_INT;
    self->_statusUpdatedAt = KALTURA_UNDEF_INT;
    self->_deletedAt = KALTURA_UNDEF_INT;
    self->_loginEnabled = KALTURA_UNDEF_BOOL;
    self->_isAccountOwner = KALTURA_UNDEF_BOOL;
    return self;
}

- (KalturaFieldType)getTypeOfId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPartnerId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfScreenName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFullName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfEmail
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDateOfBirth
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCountry
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfState
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCity
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfZip
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfThumbnailUrl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDescription
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTags
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAdminTags
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfGender
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfStatus
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerData
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfIndexedPartnerDataInt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfIndexedPartnerDataString
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStorageSize
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPassword
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFirstName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfLastName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfIsAdmin
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfLastLoginTime
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfStatusUpdatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDeletedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfLoginEnabled
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfRoleIds
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfRoleNames
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfIsAccountOwner
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfAllowedPartnerIds
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAllowedPartnerPackages
{
    return KFT_String;
}

- (void)setPartnerIdFromString:(NSString*)aPropVal
{
    self.partnerId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDateOfBirthFromString:(NSString*)aPropVal
{
    self.dateOfBirth = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setGenderFromString:(NSString*)aPropVal
{
    self.gender = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusFromString:(NSString*)aPropVal
{
    self.status = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtFromString:(NSString*)aPropVal
{
    self.createdAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtFromString:(NSString*)aPropVal
{
    self.updatedAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setIndexedPartnerDataIntFromString:(NSString*)aPropVal
{
    self.indexedPartnerDataInt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStorageSizeFromString:(NSString*)aPropVal
{
    self.storageSize = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setIsAdminFromString:(NSString*)aPropVal
{
    self.isAdmin = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)setLastLoginTimeFromString:(NSString*)aPropVal
{
    self.lastLoginTime = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusUpdatedAtFromString:(NSString*)aPropVal
{
    self.statusUpdatedAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDeletedAtFromString:(NSString*)aPropVal
{
    self.deletedAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setLoginEnabledFromString:(NSString*)aPropVal
{
    self.loginEnabled = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)setIsAccountOwnerFromString:(NSString*)aPropVal
{
    self.isAccountOwner = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaUser"];
    [aParams addIfDefinedKey:@"id" withString:self.id];
    [aParams addIfDefinedKey:@"screenName" withString:self.screenName];
    [aParams addIfDefinedKey:@"fullName" withString:self.fullName];
    [aParams addIfDefinedKey:@"email" withString:self.email];
    [aParams addIfDefinedKey:@"dateOfBirth" withInt:self.dateOfBirth];
    [aParams addIfDefinedKey:@"country" withString:self.country];
    [aParams addIfDefinedKey:@"state" withString:self.state];
    [aParams addIfDefinedKey:@"city" withString:self.city];
    [aParams addIfDefinedKey:@"zip" withString:self.zip];
    [aParams addIfDefinedKey:@"thumbnailUrl" withString:self.thumbnailUrl];
    [aParams addIfDefinedKey:@"description" withString:self.description];
    [aParams addIfDefinedKey:@"tags" withString:self.tags];
    [aParams addIfDefinedKey:@"adminTags" withString:self.adminTags];
    [aParams addIfDefinedKey:@"gender" withInt:self.gender];
    [aParams addIfDefinedKey:@"status" withInt:self.status];
    [aParams addIfDefinedKey:@"partnerData" withString:self.partnerData];
    [aParams addIfDefinedKey:@"indexedPartnerDataInt" withInt:self.indexedPartnerDataInt];
    [aParams addIfDefinedKey:@"indexedPartnerDataString" withString:self.indexedPartnerDataString];
    [aParams addIfDefinedKey:@"password" withString:self.password];
    [aParams addIfDefinedKey:@"firstName" withString:self.firstName];
    [aParams addIfDefinedKey:@"lastName" withString:self.lastName];
    [aParams addIfDefinedKey:@"isAdmin" withBool:self.isAdmin];
    [aParams addIfDefinedKey:@"roleIds" withString:self.roleIds];
    [aParams addIfDefinedKey:@"allowedPartnerIds" withString:self.allowedPartnerIds];
    [aParams addIfDefinedKey:@"allowedPartnerPackages" withString:self.allowedPartnerPackages];
}

- (void)dealloc
{
    [self->_id release];
    [self->_screenName release];
    [self->_fullName release];
    [self->_email release];
    [self->_country release];
    [self->_state release];
    [self->_city release];
    [self->_zip release];
    [self->_thumbnailUrl release];
    [self->_description release];
    [self->_tags release];
    [self->_adminTags release];
    [self->_partnerData release];
    [self->_indexedPartnerDataString release];
    [self->_password release];
    [self->_firstName release];
    [self->_lastName release];
    [self->_roleIds release];
    [self->_roleNames release];
    [self->_allowedPartnerIds release];
    [self->_allowedPartnerPackages release];
    [super dealloc];
}

@end

@interface KalturaUserListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaUserListResponse
@synthesize objects = _objects;
@synthesize totalCount = _totalCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaUser";
}

- (KalturaFieldType)getTypeOfTotalCount
{
    return KFT_Int;
}

- (void)setTotalCountFromString:(NSString*)aPropVal
{
    self.totalCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaUserListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@interface KalturaUserRole()
@property (nonatomic,assign) int id;
@property (nonatomic,assign) int partnerId;
@property (nonatomic,assign) int createdAt;
@property (nonatomic,assign) int updatedAt;
@end

@implementation KalturaUserRole
@synthesize id = _id;
@synthesize name = _name;
@synthesize systemName = _systemName;
@synthesize description = _description;
@synthesize status = _status;
@synthesize partnerId = _partnerId;
@synthesize permissionNames = _permissionNames;
@synthesize tags = _tags;
@synthesize createdAt = _createdAt;
@synthesize updatedAt = _updatedAt;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_id = KALTURA_UNDEF_INT;
    self->_status = KALTURA_UNDEF_INT;
    self->_partnerId = KALTURA_UNDEF_INT;
    self->_createdAt = KALTURA_UNDEF_INT;
    self->_updatedAt = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSystemName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDescription
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatus
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPermissionNames
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTags
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAt
{
    return KFT_Int;
}

- (void)setIdFromString:(NSString*)aPropVal
{
    self.id = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusFromString:(NSString*)aPropVal
{
    self.status = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerIdFromString:(NSString*)aPropVal
{
    self.partnerId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtFromString:(NSString*)aPropVal
{
    self.createdAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtFromString:(NSString*)aPropVal
{
    self.updatedAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaUserRole"];
    [aParams addIfDefinedKey:@"name" withString:self.name];
    [aParams addIfDefinedKey:@"systemName" withString:self.systemName];
    [aParams addIfDefinedKey:@"description" withString:self.description];
    [aParams addIfDefinedKey:@"status" withInt:self.status];
    [aParams addIfDefinedKey:@"permissionNames" withString:self.permissionNames];
    [aParams addIfDefinedKey:@"tags" withString:self.tags];
}

- (void)dealloc
{
    [self->_name release];
    [self->_systemName release];
    [self->_description release];
    [self->_permissionNames release];
    [self->_tags release];
    [super dealloc];
}

@end

@interface KalturaUserRoleListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaUserRoleListResponse
@synthesize objects = _objects;
@synthesize totalCount = _totalCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaUserRole";
}

- (KalturaFieldType)getTypeOfTotalCount
{
    return KFT_Int;
}

- (void)setTotalCountFromString:(NSString*)aPropVal
{
    self.totalCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaUserRoleListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@interface KalturaWidget()
@property (nonatomic,copy) NSString* id;
@property (nonatomic,copy) NSString* rootWidgetId;
@property (nonatomic,assign) int partnerId;
@property (nonatomic,assign) int createdAt;
@property (nonatomic,assign) int updatedAt;
@property (nonatomic,copy) NSString* widgetHTML;
@end

@implementation KalturaWidget
@synthesize id = _id;
@synthesize sourceWidgetId = _sourceWidgetId;
@synthesize rootWidgetId = _rootWidgetId;
@synthesize partnerId = _partnerId;
@synthesize entryId = _entryId;
@synthesize uiConfId = _uiConfId;
@synthesize securityType = _securityType;
@synthesize securityPolicy = _securityPolicy;
@synthesize createdAt = _createdAt;
@synthesize updatedAt = _updatedAt;
@synthesize partnerData = _partnerData;
@synthesize widgetHTML = _widgetHTML;
@synthesize enforceEntitlement = _enforceEntitlement;
@synthesize privacyContext = _privacyContext;
@synthesize addEmbedHtml5Support = _addEmbedHtml5Support;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_partnerId = KALTURA_UNDEF_INT;
    self->_uiConfId = KALTURA_UNDEF_INT;
    self->_securityType = KALTURA_UNDEF_INT;
    self->_securityPolicy = KALTURA_UNDEF_INT;
    self->_createdAt = KALTURA_UNDEF_INT;
    self->_updatedAt = KALTURA_UNDEF_INT;
    self->_enforceEntitlement = KALTURA_UNDEF_BOOL;
    self->_addEmbedHtml5Support = KALTURA_UNDEF_BOOL;
    return self;
}

- (KalturaFieldType)getTypeOfId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSourceWidgetId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfRootWidgetId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPartnerId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfEntryId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfUiConfId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfSecurityType
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfSecurityPolicy
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerData
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfWidgetHTML
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfEnforceEntitlement
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfPrivacyContext
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAddEmbedHtml5Support
{
    return KFT_Bool;
}

- (void)setPartnerIdFromString:(NSString*)aPropVal
{
    self.partnerId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUiConfIdFromString:(NSString*)aPropVal
{
    self.uiConfId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setSecurityTypeFromString:(NSString*)aPropVal
{
    self.securityType = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setSecurityPolicyFromString:(NSString*)aPropVal
{
    self.securityPolicy = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtFromString:(NSString*)aPropVal
{
    self.createdAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtFromString:(NSString*)aPropVal
{
    self.updatedAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setEnforceEntitlementFromString:(NSString*)aPropVal
{
    self.enforceEntitlement = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)setAddEmbedHtml5SupportFromString:(NSString*)aPropVal
{
    self.addEmbedHtml5Support = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaWidget"];
    [aParams addIfDefinedKey:@"sourceWidgetId" withString:self.sourceWidgetId];
    [aParams addIfDefinedKey:@"entryId" withString:self.entryId];
    [aParams addIfDefinedKey:@"uiConfId" withInt:self.uiConfId];
    [aParams addIfDefinedKey:@"securityType" withInt:self.securityType];
    [aParams addIfDefinedKey:@"securityPolicy" withInt:self.securityPolicy];
    [aParams addIfDefinedKey:@"partnerData" withString:self.partnerData];
    [aParams addIfDefinedKey:@"enforceEntitlement" withBool:self.enforceEntitlement];
    [aParams addIfDefinedKey:@"privacyContext" withString:self.privacyContext];
    [aParams addIfDefinedKey:@"addEmbedHtml5Support" withBool:self.addEmbedHtml5Support];
}

- (void)dealloc
{
    [self->_id release];
    [self->_sourceWidgetId release];
    [self->_rootWidgetId release];
    [self->_entryId release];
    [self->_partnerData release];
    [self->_widgetHTML release];
    [self->_privacyContext release];
    [super dealloc];
}

@end

@interface KalturaWidgetListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaWidgetListResponse
@synthesize objects = _objects;
@synthesize totalCount = _totalCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaWidget";
}

- (KalturaFieldType)getTypeOfTotalCount
{
    return KFT_Int;
}

- (void)setTotalCountFromString:(NSString*)aPropVal
{
    self.totalCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaWidgetListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@implementation KalturaAccessControlBaseFilter
@synthesize idEqual = _idEqual;
@synthesize idIn = _idIn;
@synthesize systemNameEqual = _systemNameEqual;
@synthesize systemNameIn = _systemNameIn;
@synthesize createdAtGreaterThanOrEqual = _createdAtGreaterThanOrEqual;
@synthesize createdAtLessThanOrEqual = _createdAtLessThanOrEqual;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_idEqual = KALTURA_UNDEF_INT;
    self->_createdAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_createdAtLessThanOrEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSystemNameEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSystemNameIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (void)setIdEqualFromString:(NSString*)aPropVal
{
    self.idEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAccessControlBaseFilter"];
    [aParams addIfDefinedKey:@"idEqual" withInt:self.idEqual];
    [aParams addIfDefinedKey:@"idIn" withString:self.idIn];
    [aParams addIfDefinedKey:@"systemNameEqual" withString:self.systemNameEqual];
    [aParams addIfDefinedKey:@"systemNameIn" withString:self.systemNameIn];
    [aParams addIfDefinedKey:@"createdAtGreaterThanOrEqual" withInt:self.createdAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"createdAtLessThanOrEqual" withInt:self.createdAtLessThanOrEqual];
}

- (void)dealloc
{
    [self->_idIn release];
    [self->_systemNameEqual release];
    [self->_systemNameIn release];
    [super dealloc];
}

@end

@implementation KalturaAccessControlBlockAction
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAccessControlBlockAction"];
}

@end

@implementation KalturaAccessControlLimitFlavorsAction
@synthesize flavorParamsIds = _flavorParamsIds;
@synthesize isBlockedList = _isBlockedList;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_isBlockedList = KALTURA_UNDEF_BOOL;
    return self;
}

- (KalturaFieldType)getTypeOfFlavorParamsIds
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfIsBlockedList
{
    return KFT_Bool;
}

- (void)setIsBlockedListFromString:(NSString*)aPropVal
{
    self.isBlockedList = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAccessControlLimitFlavorsAction"];
    [aParams addIfDefinedKey:@"flavorParamsIds" withString:self.flavorParamsIds];
    [aParams addIfDefinedKey:@"isBlockedList" withBool:self.isBlockedList];
}

- (void)dealloc
{
    [self->_flavorParamsIds release];
    [super dealloc];
}

@end

@implementation KalturaAccessControlPreviewAction
@synthesize limit = _limit;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_limit = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfLimit
{
    return KFT_Int;
}

- (void)setLimitFromString:(NSString*)aPropVal
{
    self.limit = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAccessControlPreviewAction"];
    [aParams addIfDefinedKey:@"limit" withInt:self.limit];
}

@end

@implementation KalturaAccessControlProfileBaseFilter
@synthesize idEqual = _idEqual;
@synthesize idIn = _idIn;
@synthesize systemNameEqual = _systemNameEqual;
@synthesize systemNameIn = _systemNameIn;
@synthesize createdAtGreaterThanOrEqual = _createdAtGreaterThanOrEqual;
@synthesize createdAtLessThanOrEqual = _createdAtLessThanOrEqual;
@synthesize updatedAtGreaterThanOrEqual = _updatedAtGreaterThanOrEqual;
@synthesize updatedAtLessThanOrEqual = _updatedAtLessThanOrEqual;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_idEqual = KALTURA_UNDEF_INT;
    self->_createdAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_createdAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_updatedAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_updatedAtLessThanOrEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSystemNameEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSystemNameIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (void)setIdEqualFromString:(NSString*)aPropVal
{
    self.idEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.updatedAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.updatedAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAccessControlProfileBaseFilter"];
    [aParams addIfDefinedKey:@"idEqual" withInt:self.idEqual];
    [aParams addIfDefinedKey:@"idIn" withString:self.idIn];
    [aParams addIfDefinedKey:@"systemNameEqual" withString:self.systemNameEqual];
    [aParams addIfDefinedKey:@"systemNameIn" withString:self.systemNameIn];
    [aParams addIfDefinedKey:@"createdAtGreaterThanOrEqual" withInt:self.createdAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"createdAtLessThanOrEqual" withInt:self.createdAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"updatedAtGreaterThanOrEqual" withInt:self.updatedAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"updatedAtLessThanOrEqual" withInt:self.updatedAtLessThanOrEqual];
}

- (void)dealloc
{
    [self->_idIn release];
    [self->_systemNameEqual release];
    [self->_systemNameIn release];
    [super dealloc];
}

@end

@implementation KalturaAdminUser
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAdminUser"];
}

@end

@implementation KalturaAmazonS3StorageProfile
@synthesize filesPermissionInS3 = _filesPermissionInS3;

- (KalturaFieldType)getTypeOfFilesPermissionInS3
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAmazonS3StorageProfile"];
    [aParams addIfDefinedKey:@"filesPermissionInS3" withString:self.filesPermissionInS3];
}

- (void)dealloc
{
    [self->_filesPermissionInS3 release];
    [super dealloc];
}

@end

@implementation KalturaApiActionPermissionItem
@synthesize service = _service;
@synthesize action = _action;

- (KalturaFieldType)getTypeOfService
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAction
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaApiActionPermissionItem"];
    [aParams addIfDefinedKey:@"service" withString:self.service];
    [aParams addIfDefinedKey:@"action" withString:self.action];
}

- (void)dealloc
{
    [self->_service release];
    [self->_action release];
    [super dealloc];
}

@end

@implementation KalturaApiParameterPermissionItem
@synthesize object = _object;
@synthesize parameter = _parameter;
@synthesize action = _action;

- (KalturaFieldType)getTypeOfObject
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfParameter
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAction
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaApiParameterPermissionItem"];
    [aParams addIfDefinedKey:@"object" withString:self.object];
    [aParams addIfDefinedKey:@"parameter" withString:self.parameter];
    [aParams addIfDefinedKey:@"action" withString:self.action];
}

- (void)dealloc
{
    [self->_object release];
    [self->_parameter release];
    [self->_action release];
    [super dealloc];
}

@end

@implementation KalturaAssetBaseFilter
@synthesize idEqual = _idEqual;
@synthesize idIn = _idIn;
@synthesize entryIdEqual = _entryIdEqual;
@synthesize entryIdIn = _entryIdIn;
@synthesize partnerIdEqual = _partnerIdEqual;
@synthesize partnerIdIn = _partnerIdIn;
@synthesize sizeGreaterThanOrEqual = _sizeGreaterThanOrEqual;
@synthesize sizeLessThanOrEqual = _sizeLessThanOrEqual;
@synthesize tagsLike = _tagsLike;
@synthesize tagsMultiLikeOr = _tagsMultiLikeOr;
@synthesize tagsMultiLikeAnd = _tagsMultiLikeAnd;
@synthesize createdAtGreaterThanOrEqual = _createdAtGreaterThanOrEqual;
@synthesize createdAtLessThanOrEqual = _createdAtLessThanOrEqual;
@synthesize updatedAtGreaterThanOrEqual = _updatedAtGreaterThanOrEqual;
@synthesize updatedAtLessThanOrEqual = _updatedAtLessThanOrEqual;
@synthesize deletedAtGreaterThanOrEqual = _deletedAtGreaterThanOrEqual;
@synthesize deletedAtLessThanOrEqual = _deletedAtLessThanOrEqual;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_partnerIdEqual = KALTURA_UNDEF_INT;
    self->_sizeGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_sizeLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_createdAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_createdAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_updatedAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_updatedAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_deletedAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_deletedAtLessThanOrEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfIdEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfEntryIdEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfEntryIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPartnerIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSizeGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfSizeLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfTagsLike
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTagsMultiLikeOr
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTagsMultiLikeAnd
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDeletedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDeletedAtLessThanOrEqual
{
    return KFT_Int;
}

- (void)setPartnerIdEqualFromString:(NSString*)aPropVal
{
    self.partnerIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setSizeGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.sizeGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setSizeLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.sizeLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.updatedAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.updatedAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDeletedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.deletedAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDeletedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.deletedAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAssetBaseFilter"];
    [aParams addIfDefinedKey:@"idEqual" withString:self.idEqual];
    [aParams addIfDefinedKey:@"idIn" withString:self.idIn];
    [aParams addIfDefinedKey:@"entryIdEqual" withString:self.entryIdEqual];
    [aParams addIfDefinedKey:@"entryIdIn" withString:self.entryIdIn];
    [aParams addIfDefinedKey:@"partnerIdEqual" withInt:self.partnerIdEqual];
    [aParams addIfDefinedKey:@"partnerIdIn" withString:self.partnerIdIn];
    [aParams addIfDefinedKey:@"sizeGreaterThanOrEqual" withInt:self.sizeGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"sizeLessThanOrEqual" withInt:self.sizeLessThanOrEqual];
    [aParams addIfDefinedKey:@"tagsLike" withString:self.tagsLike];
    [aParams addIfDefinedKey:@"tagsMultiLikeOr" withString:self.tagsMultiLikeOr];
    [aParams addIfDefinedKey:@"tagsMultiLikeAnd" withString:self.tagsMultiLikeAnd];
    [aParams addIfDefinedKey:@"createdAtGreaterThanOrEqual" withInt:self.createdAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"createdAtLessThanOrEqual" withInt:self.createdAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"updatedAtGreaterThanOrEqual" withInt:self.updatedAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"updatedAtLessThanOrEqual" withInt:self.updatedAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"deletedAtGreaterThanOrEqual" withInt:self.deletedAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"deletedAtLessThanOrEqual" withInt:self.deletedAtLessThanOrEqual];
}

- (void)dealloc
{
    [self->_idEqual release];
    [self->_idIn release];
    [self->_entryIdEqual release];
    [self->_entryIdIn release];
    [self->_partnerIdIn release];
    [self->_tagsLike release];
    [self->_tagsMultiLikeOr release];
    [self->_tagsMultiLikeAnd release];
    [super dealloc];
}

@end

@implementation KalturaAssetParamsBaseFilter
@synthesize systemNameEqual = _systemNameEqual;
@synthesize systemNameIn = _systemNameIn;
@synthesize isSystemDefaultEqual = _isSystemDefaultEqual;
@synthesize tagsEqual = _tagsEqual;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_isSystemDefaultEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfSystemNameEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSystemNameIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfIsSystemDefaultEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfTagsEqual
{
    return KFT_String;
}

- (void)setIsSystemDefaultEqualFromString:(NSString*)aPropVal
{
    self.isSystemDefaultEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAssetParamsBaseFilter"];
    [aParams addIfDefinedKey:@"systemNameEqual" withString:self.systemNameEqual];
    [aParams addIfDefinedKey:@"systemNameIn" withString:self.systemNameIn];
    [aParams addIfDefinedKey:@"isSystemDefaultEqual" withInt:self.isSystemDefaultEqual];
    [aParams addIfDefinedKey:@"tagsEqual" withString:self.tagsEqual];
}

- (void)dealloc
{
    [self->_systemNameEqual release];
    [self->_systemNameIn release];
    [self->_tagsEqual release];
    [super dealloc];
}

@end

@implementation KalturaAssetParamsOutput
@synthesize assetParamsId = _assetParamsId;
@synthesize assetParamsVersion = _assetParamsVersion;
@synthesize assetId = _assetId;
@synthesize assetVersion = _assetVersion;
@synthesize readyBehavior = _readyBehavior;
@synthesize format = _format;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_assetParamsId = KALTURA_UNDEF_INT;
    self->_readyBehavior = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfAssetParamsId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfAssetParamsVersion
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAssetId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAssetVersion
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfReadyBehavior
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfFormat
{
    return KFT_String;
}

- (void)setAssetParamsIdFromString:(NSString*)aPropVal
{
    self.assetParamsId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setReadyBehaviorFromString:(NSString*)aPropVal
{
    self.readyBehavior = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAssetParamsOutput"];
    [aParams addIfDefinedKey:@"assetParamsId" withInt:self.assetParamsId];
    [aParams addIfDefinedKey:@"assetParamsVersion" withString:self.assetParamsVersion];
    [aParams addIfDefinedKey:@"assetId" withString:self.assetId];
    [aParams addIfDefinedKey:@"assetVersion" withString:self.assetVersion];
    [aParams addIfDefinedKey:@"readyBehavior" withInt:self.readyBehavior];
    [aParams addIfDefinedKey:@"format" withString:self.format];
}

- (void)dealloc
{
    [self->_assetParamsVersion release];
    [self->_assetId release];
    [self->_assetVersion release];
    [self->_format release];
    [super dealloc];
}

@end

@implementation KalturaAssetPropertiesCompareCondition
@synthesize properties = _properties;

- (KalturaFieldType)getTypeOfProperties
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfProperties
{
    return @"KalturaKeyValue";
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAssetPropertiesCompareCondition"];
    [aParams addIfDefinedKey:@"properties" withArray:self.properties];
}

- (void)dealloc
{
    [self->_properties release];
    [super dealloc];
}

@end

@implementation KalturaAssetsParamsResourceContainers
@synthesize resources = _resources;

- (KalturaFieldType)getTypeOfResources
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfResources
{
    return @"KalturaAssetParamsResourceContainer";
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAssetsParamsResourceContainers"];
    [aParams addIfDefinedKey:@"resources" withArray:self.resources];
}

- (void)dealloc
{
    [self->_resources release];
    [super dealloc];
}

@end

@implementation KalturaAuthenticatedCondition
@synthesize privileges = _privileges;

- (KalturaFieldType)getTypeOfPrivileges
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfPrivileges
{
    return @"KalturaStringValue";
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAuthenticatedCondition"];
    [aParams addIfDefinedKey:@"privileges" withArray:self.privileges];
}

- (void)dealloc
{
    [self->_privileges release];
    [super dealloc];
}

@end

@implementation KalturaBaseSyndicationFeedBaseFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaBaseSyndicationFeedBaseFilter"];
}

@end

@implementation KalturaBatchJobBaseFilter
@synthesize idEqual = _idEqual;
@synthesize idGreaterThanOrEqual = _idGreaterThanOrEqual;
@synthesize partnerIdEqual = _partnerIdEqual;
@synthesize partnerIdIn = _partnerIdIn;
@synthesize partnerIdNotIn = _partnerIdNotIn;
@synthesize createdAtGreaterThanOrEqual = _createdAtGreaterThanOrEqual;
@synthesize createdAtLessThanOrEqual = _createdAtLessThanOrEqual;
@synthesize updatedAtGreaterThanOrEqual = _updatedAtGreaterThanOrEqual;
@synthesize updatedAtLessThanOrEqual = _updatedAtLessThanOrEqual;
@synthesize lockExpirationGreaterThanOrEqual = _lockExpirationGreaterThanOrEqual;
@synthesize lockExpirationLessThanOrEqual = _lockExpirationLessThanOrEqual;
@synthesize executionAttemptsGreaterThanOrEqual = _executionAttemptsGreaterThanOrEqual;
@synthesize executionAttemptsLessThanOrEqual = _executionAttemptsLessThanOrEqual;
@synthesize lockVersionGreaterThanOrEqual = _lockVersionGreaterThanOrEqual;
@synthesize lockVersionLessThanOrEqual = _lockVersionLessThanOrEqual;
@synthesize entryIdEqual = _entryIdEqual;
@synthesize jobTypeEqual = _jobTypeEqual;
@synthesize jobTypeIn = _jobTypeIn;
@synthesize jobTypeNotIn = _jobTypeNotIn;
@synthesize jobSubTypeEqual = _jobSubTypeEqual;
@synthesize jobSubTypeIn = _jobSubTypeIn;
@synthesize jobSubTypeNotIn = _jobSubTypeNotIn;
@synthesize statusEqual = _statusEqual;
@synthesize statusIn = _statusIn;
@synthesize statusNotIn = _statusNotIn;
@synthesize abortEqual = _abortEqual;
@synthesize checkAgainTimeoutGreaterThanOrEqual = _checkAgainTimeoutGreaterThanOrEqual;
@synthesize checkAgainTimeoutLessThanOrEqual = _checkAgainTimeoutLessThanOrEqual;
@synthesize priorityGreaterThanOrEqual = _priorityGreaterThanOrEqual;
@synthesize priorityLessThanOrEqual = _priorityLessThanOrEqual;
@synthesize priorityEqual = _priorityEqual;
@synthesize priorityIn = _priorityIn;
@synthesize priorityNotIn = _priorityNotIn;
@synthesize bulkJobIdEqual = _bulkJobIdEqual;
@synthesize bulkJobIdIn = _bulkJobIdIn;
@synthesize bulkJobIdNotIn = _bulkJobIdNotIn;
@synthesize parentJobIdEqual = _parentJobIdEqual;
@synthesize parentJobIdIn = _parentJobIdIn;
@synthesize parentJobIdNotIn = _parentJobIdNotIn;
@synthesize rootJobIdEqual = _rootJobIdEqual;
@synthesize rootJobIdIn = _rootJobIdIn;
@synthesize rootJobIdNotIn = _rootJobIdNotIn;
@synthesize queueTimeGreaterThanOrEqual = _queueTimeGreaterThanOrEqual;
@synthesize queueTimeLessThanOrEqual = _queueTimeLessThanOrEqual;
@synthesize finishTimeGreaterThanOrEqual = _finishTimeGreaterThanOrEqual;
@synthesize finishTimeLessThanOrEqual = _finishTimeLessThanOrEqual;
@synthesize errTypeEqual = _errTypeEqual;
@synthesize errTypeIn = _errTypeIn;
@synthesize errTypeNotIn = _errTypeNotIn;
@synthesize errNumberEqual = _errNumberEqual;
@synthesize errNumberIn = _errNumberIn;
@synthesize errNumberNotIn = _errNumberNotIn;
@synthesize estimatedEffortLessThan = _estimatedEffortLessThan;
@synthesize estimatedEffortGreaterThan = _estimatedEffortGreaterThan;
@synthesize schedulerIdEqual = _schedulerIdEqual;
@synthesize schedulerIdIn = _schedulerIdIn;
@synthesize schedulerIdNotIn = _schedulerIdNotIn;
@synthesize workerIdEqual = _workerIdEqual;
@synthesize workerIdIn = _workerIdIn;
@synthesize workerIdNotIn = _workerIdNotIn;
@synthesize batchIndexEqual = _batchIndexEqual;
@synthesize batchIndexIn = _batchIndexIn;
@synthesize batchIndexNotIn = _batchIndexNotIn;
@synthesize lastSchedulerIdEqual = _lastSchedulerIdEqual;
@synthesize lastSchedulerIdIn = _lastSchedulerIdIn;
@synthesize lastSchedulerIdNotIn = _lastSchedulerIdNotIn;
@synthesize lastWorkerIdEqual = _lastWorkerIdEqual;
@synthesize lastWorkerIdIn = _lastWorkerIdIn;
@synthesize lastWorkerIdNotIn = _lastWorkerIdNotIn;
@synthesize dcEqual = _dcEqual;
@synthesize dcIn = _dcIn;
@synthesize dcNotIn = _dcNotIn;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_idEqual = KALTURA_UNDEF_INT;
    self->_idGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_partnerIdEqual = KALTURA_UNDEF_INT;
    self->_createdAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_createdAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_updatedAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_updatedAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_lockExpirationGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_lockExpirationLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_executionAttemptsGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_executionAttemptsLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_lockVersionGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_lockVersionLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_jobSubTypeEqual = KALTURA_UNDEF_INT;
    self->_statusEqual = KALTURA_UNDEF_INT;
    self->_abortEqual = KALTURA_UNDEF_INT;
    self->_checkAgainTimeoutGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_checkAgainTimeoutLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_priorityGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_priorityLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_priorityEqual = KALTURA_UNDEF_INT;
    self->_bulkJobIdEqual = KALTURA_UNDEF_INT;
    self->_parentJobIdEqual = KALTURA_UNDEF_INT;
    self->_rootJobIdEqual = KALTURA_UNDEF_INT;
    self->_queueTimeGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_queueTimeLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_finishTimeGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_finishTimeLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_errTypeEqual = KALTURA_UNDEF_INT;
    self->_errNumberEqual = KALTURA_UNDEF_INT;
    self->_estimatedEffortLessThan = KALTURA_UNDEF_INT;
    self->_estimatedEffortGreaterThan = KALTURA_UNDEF_INT;
    self->_schedulerIdEqual = KALTURA_UNDEF_INT;
    self->_workerIdEqual = KALTURA_UNDEF_INT;
    self->_batchIndexEqual = KALTURA_UNDEF_INT;
    self->_lastSchedulerIdEqual = KALTURA_UNDEF_INT;
    self->_lastWorkerIdEqual = KALTURA_UNDEF_INT;
    self->_dcEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfIdGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPartnerIdNotIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfLockExpirationGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfLockExpirationLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfExecutionAttemptsGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfExecutionAttemptsLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfLockVersionGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfLockVersionLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfEntryIdEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfJobTypeEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfJobTypeIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfJobTypeNotIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfJobSubTypeEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfJobSubTypeIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfJobSubTypeNotIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatusEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfStatusIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatusNotIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAbortEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCheckAgainTimeoutGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCheckAgainTimeoutLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPriorityGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPriorityLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPriorityEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPriorityIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPriorityNotIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfBulkJobIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfBulkJobIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfBulkJobIdNotIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfParentJobIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfParentJobIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfParentJobIdNotIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfRootJobIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfRootJobIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfRootJobIdNotIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfQueueTimeGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfQueueTimeLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfFinishTimeGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfFinishTimeLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfErrTypeEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfErrTypeIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfErrTypeNotIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfErrNumberEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfErrNumberIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfErrNumberNotIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfEstimatedEffortLessThan
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfEstimatedEffortGreaterThan
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfSchedulerIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfSchedulerIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSchedulerIdNotIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfWorkerIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfWorkerIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfWorkerIdNotIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfBatchIndexEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfBatchIndexIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfBatchIndexNotIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfLastSchedulerIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfLastSchedulerIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfLastSchedulerIdNotIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfLastWorkerIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfLastWorkerIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfLastWorkerIdNotIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDcEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDcIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDcNotIn
{
    return KFT_String;
}

- (void)setIdEqualFromString:(NSString*)aPropVal
{
    self.idEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setIdGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.idGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerIdEqualFromString:(NSString*)aPropVal
{
    self.partnerIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.updatedAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.updatedAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setLockExpirationGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.lockExpirationGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setLockExpirationLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.lockExpirationLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setExecutionAttemptsGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.executionAttemptsGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setExecutionAttemptsLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.executionAttemptsLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setLockVersionGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.lockVersionGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setLockVersionLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.lockVersionLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setJobSubTypeEqualFromString:(NSString*)aPropVal
{
    self.jobSubTypeEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusEqualFromString:(NSString*)aPropVal
{
    self.statusEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setAbortEqualFromString:(NSString*)aPropVal
{
    self.abortEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCheckAgainTimeoutGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.checkAgainTimeoutGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCheckAgainTimeoutLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.checkAgainTimeoutLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPriorityGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.priorityGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPriorityLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.priorityLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPriorityEqualFromString:(NSString*)aPropVal
{
    self.priorityEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setBulkJobIdEqualFromString:(NSString*)aPropVal
{
    self.bulkJobIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setParentJobIdEqualFromString:(NSString*)aPropVal
{
    self.parentJobIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setRootJobIdEqualFromString:(NSString*)aPropVal
{
    self.rootJobIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setQueueTimeGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.queueTimeGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setQueueTimeLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.queueTimeLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setFinishTimeGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.finishTimeGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setFinishTimeLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.finishTimeLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setErrTypeEqualFromString:(NSString*)aPropVal
{
    self.errTypeEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setErrNumberEqualFromString:(NSString*)aPropVal
{
    self.errNumberEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setEstimatedEffortLessThanFromString:(NSString*)aPropVal
{
    self.estimatedEffortLessThan = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setEstimatedEffortGreaterThanFromString:(NSString*)aPropVal
{
    self.estimatedEffortGreaterThan = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setSchedulerIdEqualFromString:(NSString*)aPropVal
{
    self.schedulerIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setWorkerIdEqualFromString:(NSString*)aPropVal
{
    self.workerIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setBatchIndexEqualFromString:(NSString*)aPropVal
{
    self.batchIndexEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setLastSchedulerIdEqualFromString:(NSString*)aPropVal
{
    self.lastSchedulerIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setLastWorkerIdEqualFromString:(NSString*)aPropVal
{
    self.lastWorkerIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDcEqualFromString:(NSString*)aPropVal
{
    self.dcEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaBatchJobBaseFilter"];
    [aParams addIfDefinedKey:@"idEqual" withInt:self.idEqual];
    [aParams addIfDefinedKey:@"idGreaterThanOrEqual" withInt:self.idGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"partnerIdEqual" withInt:self.partnerIdEqual];
    [aParams addIfDefinedKey:@"partnerIdIn" withString:self.partnerIdIn];
    [aParams addIfDefinedKey:@"partnerIdNotIn" withString:self.partnerIdNotIn];
    [aParams addIfDefinedKey:@"createdAtGreaterThanOrEqual" withInt:self.createdAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"createdAtLessThanOrEqual" withInt:self.createdAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"updatedAtGreaterThanOrEqual" withInt:self.updatedAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"updatedAtLessThanOrEqual" withInt:self.updatedAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"lockExpirationGreaterThanOrEqual" withInt:self.lockExpirationGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"lockExpirationLessThanOrEqual" withInt:self.lockExpirationLessThanOrEqual];
    [aParams addIfDefinedKey:@"executionAttemptsGreaterThanOrEqual" withInt:self.executionAttemptsGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"executionAttemptsLessThanOrEqual" withInt:self.executionAttemptsLessThanOrEqual];
    [aParams addIfDefinedKey:@"lockVersionGreaterThanOrEqual" withInt:self.lockVersionGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"lockVersionLessThanOrEqual" withInt:self.lockVersionLessThanOrEqual];
    [aParams addIfDefinedKey:@"entryIdEqual" withString:self.entryIdEqual];
    [aParams addIfDefinedKey:@"jobTypeEqual" withString:self.jobTypeEqual];
    [aParams addIfDefinedKey:@"jobTypeIn" withString:self.jobTypeIn];
    [aParams addIfDefinedKey:@"jobTypeNotIn" withString:self.jobTypeNotIn];
    [aParams addIfDefinedKey:@"jobSubTypeEqual" withInt:self.jobSubTypeEqual];
    [aParams addIfDefinedKey:@"jobSubTypeIn" withString:self.jobSubTypeIn];
    [aParams addIfDefinedKey:@"jobSubTypeNotIn" withString:self.jobSubTypeNotIn];
    [aParams addIfDefinedKey:@"statusEqual" withInt:self.statusEqual];
    [aParams addIfDefinedKey:@"statusIn" withString:self.statusIn];
    [aParams addIfDefinedKey:@"statusNotIn" withString:self.statusNotIn];
    [aParams addIfDefinedKey:@"abortEqual" withInt:self.abortEqual];
    [aParams addIfDefinedKey:@"checkAgainTimeoutGreaterThanOrEqual" withInt:self.checkAgainTimeoutGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"checkAgainTimeoutLessThanOrEqual" withInt:self.checkAgainTimeoutLessThanOrEqual];
    [aParams addIfDefinedKey:@"priorityGreaterThanOrEqual" withInt:self.priorityGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"priorityLessThanOrEqual" withInt:self.priorityLessThanOrEqual];
    [aParams addIfDefinedKey:@"priorityEqual" withInt:self.priorityEqual];
    [aParams addIfDefinedKey:@"priorityIn" withString:self.priorityIn];
    [aParams addIfDefinedKey:@"priorityNotIn" withString:self.priorityNotIn];
    [aParams addIfDefinedKey:@"bulkJobIdEqual" withInt:self.bulkJobIdEqual];
    [aParams addIfDefinedKey:@"bulkJobIdIn" withString:self.bulkJobIdIn];
    [aParams addIfDefinedKey:@"bulkJobIdNotIn" withString:self.bulkJobIdNotIn];
    [aParams addIfDefinedKey:@"parentJobIdEqual" withInt:self.parentJobIdEqual];
    [aParams addIfDefinedKey:@"parentJobIdIn" withString:self.parentJobIdIn];
    [aParams addIfDefinedKey:@"parentJobIdNotIn" withString:self.parentJobIdNotIn];
    [aParams addIfDefinedKey:@"rootJobIdEqual" withInt:self.rootJobIdEqual];
    [aParams addIfDefinedKey:@"rootJobIdIn" withString:self.rootJobIdIn];
    [aParams addIfDefinedKey:@"rootJobIdNotIn" withString:self.rootJobIdNotIn];
    [aParams addIfDefinedKey:@"queueTimeGreaterThanOrEqual" withInt:self.queueTimeGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"queueTimeLessThanOrEqual" withInt:self.queueTimeLessThanOrEqual];
    [aParams addIfDefinedKey:@"finishTimeGreaterThanOrEqual" withInt:self.finishTimeGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"finishTimeLessThanOrEqual" withInt:self.finishTimeLessThanOrEqual];
    [aParams addIfDefinedKey:@"errTypeEqual" withInt:self.errTypeEqual];
    [aParams addIfDefinedKey:@"errTypeIn" withString:self.errTypeIn];
    [aParams addIfDefinedKey:@"errTypeNotIn" withString:self.errTypeNotIn];
    [aParams addIfDefinedKey:@"errNumberEqual" withInt:self.errNumberEqual];
    [aParams addIfDefinedKey:@"errNumberIn" withString:self.errNumberIn];
    [aParams addIfDefinedKey:@"errNumberNotIn" withString:self.errNumberNotIn];
    [aParams addIfDefinedKey:@"estimatedEffortLessThan" withInt:self.estimatedEffortLessThan];
    [aParams addIfDefinedKey:@"estimatedEffortGreaterThan" withInt:self.estimatedEffortGreaterThan];
    [aParams addIfDefinedKey:@"schedulerIdEqual" withInt:self.schedulerIdEqual];
    [aParams addIfDefinedKey:@"schedulerIdIn" withString:self.schedulerIdIn];
    [aParams addIfDefinedKey:@"schedulerIdNotIn" withString:self.schedulerIdNotIn];
    [aParams addIfDefinedKey:@"workerIdEqual" withInt:self.workerIdEqual];
    [aParams addIfDefinedKey:@"workerIdIn" withString:self.workerIdIn];
    [aParams addIfDefinedKey:@"workerIdNotIn" withString:self.workerIdNotIn];
    [aParams addIfDefinedKey:@"batchIndexEqual" withInt:self.batchIndexEqual];
    [aParams addIfDefinedKey:@"batchIndexIn" withString:self.batchIndexIn];
    [aParams addIfDefinedKey:@"batchIndexNotIn" withString:self.batchIndexNotIn];
    [aParams addIfDefinedKey:@"lastSchedulerIdEqual" withInt:self.lastSchedulerIdEqual];
    [aParams addIfDefinedKey:@"lastSchedulerIdIn" withString:self.lastSchedulerIdIn];
    [aParams addIfDefinedKey:@"lastSchedulerIdNotIn" withString:self.lastSchedulerIdNotIn];
    [aParams addIfDefinedKey:@"lastWorkerIdEqual" withInt:self.lastWorkerIdEqual];
    [aParams addIfDefinedKey:@"lastWorkerIdIn" withString:self.lastWorkerIdIn];
    [aParams addIfDefinedKey:@"lastWorkerIdNotIn" withString:self.lastWorkerIdNotIn];
    [aParams addIfDefinedKey:@"dcEqual" withInt:self.dcEqual];
    [aParams addIfDefinedKey:@"dcIn" withString:self.dcIn];
    [aParams addIfDefinedKey:@"dcNotIn" withString:self.dcNotIn];
}

- (void)dealloc
{
    [self->_partnerIdIn release];
    [self->_partnerIdNotIn release];
    [self->_entryIdEqual release];
    [self->_jobTypeEqual release];
    [self->_jobTypeIn release];
    [self->_jobTypeNotIn release];
    [self->_jobSubTypeIn release];
    [self->_jobSubTypeNotIn release];
    [self->_statusIn release];
    [self->_statusNotIn release];
    [self->_priorityIn release];
    [self->_priorityNotIn release];
    [self->_bulkJobIdIn release];
    [self->_bulkJobIdNotIn release];
    [self->_parentJobIdIn release];
    [self->_parentJobIdNotIn release];
    [self->_rootJobIdIn release];
    [self->_rootJobIdNotIn release];
    [self->_errTypeIn release];
    [self->_errTypeNotIn release];
    [self->_errNumberIn release];
    [self->_errNumberNotIn release];
    [self->_schedulerIdIn release];
    [self->_schedulerIdNotIn release];
    [self->_workerIdIn release];
    [self->_workerIdNotIn release];
    [self->_batchIndexIn release];
    [self->_batchIndexNotIn release];
    [self->_lastSchedulerIdIn release];
    [self->_lastSchedulerIdNotIn release];
    [self->_lastWorkerIdIn release];
    [self->_lastWorkerIdNotIn release];
    [self->_dcIn release];
    [self->_dcNotIn release];
    [super dealloc];
}

@end

@implementation KalturaBooleanValue
@synthesize value = _value;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_value = KALTURA_UNDEF_BOOL;
    return self;
}

- (KalturaFieldType)getTypeOfValue
{
    return KFT_Bool;
}

- (void)setValueFromString:(NSString*)aPropVal
{
    self.value = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaBooleanValue"];
    [aParams addIfDefinedKey:@"value" withBool:self.value];
}

@end

@implementation KalturaBulkDownloadJobData
@synthesize entryIds = _entryIds;
@synthesize flavorParamsId = _flavorParamsId;
@synthesize puserId = _puserId;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_flavorParamsId = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfEntryIds
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFlavorParamsId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPuserId
{
    return KFT_String;
}

- (void)setFlavorParamsIdFromString:(NSString*)aPropVal
{
    self.flavorParamsId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaBulkDownloadJobData"];
    [aParams addIfDefinedKey:@"entryIds" withString:self.entryIds];
    [aParams addIfDefinedKey:@"flavorParamsId" withInt:self.flavorParamsId];
    [aParams addIfDefinedKey:@"puserId" withString:self.puserId];
}

- (void)dealloc
{
    [self->_entryIds release];
    [self->_puserId release];
    [super dealloc];
}

@end

@implementation KalturaBulkUploadBaseFilter
@synthesize uploadedOnGreaterThanOrEqual = _uploadedOnGreaterThanOrEqual;
@synthesize uploadedOnLessThanOrEqual = _uploadedOnLessThanOrEqual;
@synthesize uploadedOnEqual = _uploadedOnEqual;
@synthesize statusIn = _statusIn;
@synthesize statusEqual = _statusEqual;
@synthesize bulkUploadObjectTypeEqual = _bulkUploadObjectTypeEqual;
@synthesize bulkUploadObjectTypeIn = _bulkUploadObjectTypeIn;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_uploadedOnGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_uploadedOnLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_uploadedOnEqual = KALTURA_UNDEF_INT;
    self->_statusEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfUploadedOnGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUploadedOnLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUploadedOnEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfStatusIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatusEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfBulkUploadObjectTypeEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfBulkUploadObjectTypeIn
{
    return KFT_String;
}

- (void)setUploadedOnGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.uploadedOnGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUploadedOnLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.uploadedOnLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUploadedOnEqualFromString:(NSString*)aPropVal
{
    self.uploadedOnEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusEqualFromString:(NSString*)aPropVal
{
    self.statusEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaBulkUploadBaseFilter"];
    [aParams addIfDefinedKey:@"uploadedOnGreaterThanOrEqual" withInt:self.uploadedOnGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"uploadedOnLessThanOrEqual" withInt:self.uploadedOnLessThanOrEqual];
    [aParams addIfDefinedKey:@"uploadedOnEqual" withInt:self.uploadedOnEqual];
    [aParams addIfDefinedKey:@"statusIn" withString:self.statusIn];
    [aParams addIfDefinedKey:@"statusEqual" withInt:self.statusEqual];
    [aParams addIfDefinedKey:@"bulkUploadObjectTypeEqual" withString:self.bulkUploadObjectTypeEqual];
    [aParams addIfDefinedKey:@"bulkUploadObjectTypeIn" withString:self.bulkUploadObjectTypeIn];
}

- (void)dealloc
{
    [self->_statusIn release];
    [self->_bulkUploadObjectTypeEqual release];
    [self->_bulkUploadObjectTypeIn release];
    [super dealloc];
}

@end

@implementation KalturaBulkUploadCategoryData
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaBulkUploadCategoryData"];
}

@end

@implementation KalturaBulkUploadCategoryUserData
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaBulkUploadCategoryUserData"];
}

@end

@implementation KalturaBulkUploadEntryData
@synthesize conversionProfileId = _conversionProfileId;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_conversionProfileId = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfConversionProfileId
{
    return KFT_Int;
}

- (void)setConversionProfileIdFromString:(NSString*)aPropVal
{
    self.conversionProfileId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaBulkUploadEntryData"];
    [aParams addIfDefinedKey:@"conversionProfileId" withInt:self.conversionProfileId];
}

@end

@interface KalturaBulkUploadJobData()
@property (nonatomic,copy) NSString* userId;
@property (nonatomic,copy) NSString* uploadedBy;
@property (nonatomic,assign) int conversionProfileId;
@property (nonatomic,copy) NSString* resultsFileLocalPath;
@property (nonatomic,copy) NSString* resultsFileUrl;
@property (nonatomic,assign) int numOfEntries;
@property (nonatomic,assign) int numOfObjects;
@property (nonatomic,copy) NSString* filePath;
@property (nonatomic,copy) NSString* bulkUploadObjectType;
@property (nonatomic,retain) KalturaBulkUploadObjectData* objectData;
@property (nonatomic,copy) NSString* type;
@end

@implementation KalturaBulkUploadJobData
@synthesize userId = _userId;
@synthesize uploadedBy = _uploadedBy;
@synthesize conversionProfileId = _conversionProfileId;
@synthesize resultsFileLocalPath = _resultsFileLocalPath;
@synthesize resultsFileUrl = _resultsFileUrl;
@synthesize numOfEntries = _numOfEntries;
@synthesize numOfObjects = _numOfObjects;
@synthesize filePath = _filePath;
@synthesize bulkUploadObjectType = _bulkUploadObjectType;
@synthesize fileName = _fileName;
@synthesize objectData = _objectData;
@synthesize type = _type;
@synthesize emailRecipients = _emailRecipients;
@synthesize numOfErrorObjects = _numOfErrorObjects;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_conversionProfileId = KALTURA_UNDEF_INT;
    self->_numOfEntries = KALTURA_UNDEF_INT;
    self->_numOfObjects = KALTURA_UNDEF_INT;
    self->_numOfErrorObjects = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfUserId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfUploadedBy
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfConversionProfileId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfResultsFileLocalPath
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfResultsFileUrl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfNumOfEntries
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfNumOfObjects
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfFilePath
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfBulkUploadObjectType
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFileName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfObjectData
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfObjectData
{
    return @"KalturaBulkUploadObjectData";
}

- (KalturaFieldType)getTypeOfType
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfEmailRecipients
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfNumOfErrorObjects
{
    return KFT_Int;
}

- (void)setConversionProfileIdFromString:(NSString*)aPropVal
{
    self.conversionProfileId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setNumOfEntriesFromString:(NSString*)aPropVal
{
    self.numOfEntries = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setNumOfObjectsFromString:(NSString*)aPropVal
{
    self.numOfObjects = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setNumOfErrorObjectsFromString:(NSString*)aPropVal
{
    self.numOfErrorObjects = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaBulkUploadJobData"];
    [aParams addIfDefinedKey:@"fileName" withString:self.fileName];
    [aParams addIfDefinedKey:@"emailRecipients" withString:self.emailRecipients];
    [aParams addIfDefinedKey:@"numOfErrorObjects" withInt:self.numOfErrorObjects];
}

- (void)dealloc
{
    [self->_userId release];
    [self->_uploadedBy release];
    [self->_resultsFileLocalPath release];
    [self->_resultsFileUrl release];
    [self->_filePath release];
    [self->_bulkUploadObjectType release];
    [self->_fileName release];
    [self->_objectData release];
    [self->_type release];
    [self->_emailRecipients release];
    [super dealloc];
}

@end

@implementation KalturaBulkUploadResultCategory
@synthesize relativePath = _relativePath;
@synthesize name = _name;
@synthesize referenceId = _referenceId;
@synthesize description = _description;
@synthesize tags = _tags;
@synthesize appearInList = _appearInList;
@synthesize privacy = _privacy;
@synthesize inheritanceType = _inheritanceType;
@synthesize userJoinPolicy = _userJoinPolicy;
@synthesize defaultPermissionLevel = _defaultPermissionLevel;
@synthesize owner = _owner;
@synthesize contributionPolicy = _contributionPolicy;
@synthesize partnerSortValue = _partnerSortValue;
@synthesize moderation = _moderation;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_appearInList = KALTURA_UNDEF_INT;
    self->_privacy = KALTURA_UNDEF_INT;
    self->_inheritanceType = KALTURA_UNDEF_INT;
    self->_userJoinPolicy = KALTURA_UNDEF_INT;
    self->_defaultPermissionLevel = KALTURA_UNDEF_INT;
    self->_contributionPolicy = KALTURA_UNDEF_INT;
    self->_partnerSortValue = KALTURA_UNDEF_INT;
    self->_moderation = KALTURA_UNDEF_BOOL;
    return self;
}

- (KalturaFieldType)getTypeOfRelativePath
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfReferenceId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDescription
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTags
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAppearInList
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPrivacy
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfInheritanceType
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUserJoinPolicy
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDefaultPermissionLevel
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfOwner
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfContributionPolicy
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerSortValue
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfModeration
{
    return KFT_Bool;
}

- (void)setAppearInListFromString:(NSString*)aPropVal
{
    self.appearInList = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPrivacyFromString:(NSString*)aPropVal
{
    self.privacy = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setInheritanceTypeFromString:(NSString*)aPropVal
{
    self.inheritanceType = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUserJoinPolicyFromString:(NSString*)aPropVal
{
    self.userJoinPolicy = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDefaultPermissionLevelFromString:(NSString*)aPropVal
{
    self.defaultPermissionLevel = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setContributionPolicyFromString:(NSString*)aPropVal
{
    self.contributionPolicy = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerSortValueFromString:(NSString*)aPropVal
{
    self.partnerSortValue = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setModerationFromString:(NSString*)aPropVal
{
    self.moderation = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaBulkUploadResultCategory"];
    [aParams addIfDefinedKey:@"relativePath" withString:self.relativePath];
    [aParams addIfDefinedKey:@"name" withString:self.name];
    [aParams addIfDefinedKey:@"referenceId" withString:self.referenceId];
    [aParams addIfDefinedKey:@"description" withString:self.description];
    [aParams addIfDefinedKey:@"tags" withString:self.tags];
    [aParams addIfDefinedKey:@"appearInList" withInt:self.appearInList];
    [aParams addIfDefinedKey:@"privacy" withInt:self.privacy];
    [aParams addIfDefinedKey:@"inheritanceType" withInt:self.inheritanceType];
    [aParams addIfDefinedKey:@"userJoinPolicy" withInt:self.userJoinPolicy];
    [aParams addIfDefinedKey:@"defaultPermissionLevel" withInt:self.defaultPermissionLevel];
    [aParams addIfDefinedKey:@"owner" withString:self.owner];
    [aParams addIfDefinedKey:@"contributionPolicy" withInt:self.contributionPolicy];
    [aParams addIfDefinedKey:@"partnerSortValue" withInt:self.partnerSortValue];
    [aParams addIfDefinedKey:@"moderation" withBool:self.moderation];
}

- (void)dealloc
{
    [self->_relativePath release];
    [self->_name release];
    [self->_referenceId release];
    [self->_description release];
    [self->_tags release];
    [self->_owner release];
    [super dealloc];
}

@end

@implementation KalturaBulkUploadResultCategoryUser
@synthesize categoryId = _categoryId;
@synthesize categoryReferenceId = _categoryReferenceId;
@synthesize userId = _userId;
@synthesize permissionLevel = _permissionLevel;
@synthesize updateMethod = _updateMethod;
@synthesize requiredObjectStatus = _requiredObjectStatus;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_categoryId = KALTURA_UNDEF_INT;
    self->_permissionLevel = KALTURA_UNDEF_INT;
    self->_updateMethod = KALTURA_UNDEF_INT;
    self->_requiredObjectStatus = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfCategoryId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCategoryReferenceId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfUserId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPermissionLevel
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdateMethod
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfRequiredObjectStatus
{
    return KFT_Int;
}

- (void)setCategoryIdFromString:(NSString*)aPropVal
{
    self.categoryId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPermissionLevelFromString:(NSString*)aPropVal
{
    self.permissionLevel = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdateMethodFromString:(NSString*)aPropVal
{
    self.updateMethod = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setRequiredObjectStatusFromString:(NSString*)aPropVal
{
    self.requiredObjectStatus = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaBulkUploadResultCategoryUser"];
    [aParams addIfDefinedKey:@"categoryId" withInt:self.categoryId];
    [aParams addIfDefinedKey:@"categoryReferenceId" withString:self.categoryReferenceId];
    [aParams addIfDefinedKey:@"userId" withString:self.userId];
    [aParams addIfDefinedKey:@"permissionLevel" withInt:self.permissionLevel];
    [aParams addIfDefinedKey:@"updateMethod" withInt:self.updateMethod];
    [aParams addIfDefinedKey:@"requiredObjectStatus" withInt:self.requiredObjectStatus];
}

- (void)dealloc
{
    [self->_categoryReferenceId release];
    [self->_userId release];
    [super dealloc];
}

@end

@implementation KalturaBulkUploadResultEntry
@synthesize entryId = _entryId;
@synthesize title = _title;
@synthesize description = _description;
@synthesize tags = _tags;
@synthesize url = _url;
@synthesize contentType = _contentType;
@synthesize conversionProfileId = _conversionProfileId;
@synthesize accessControlProfileId = _accessControlProfileId;
@synthesize category = _category;
@synthesize scheduleStartDate = _scheduleStartDate;
@synthesize scheduleEndDate = _scheduleEndDate;
@synthesize entryStatus = _entryStatus;
@synthesize thumbnailUrl = _thumbnailUrl;
@synthesize thumbnailSaved = _thumbnailSaved;
@synthesize sshPrivateKey = _sshPrivateKey;
@synthesize sshPublicKey = _sshPublicKey;
@synthesize sshKeyPassphrase = _sshKeyPassphrase;
@synthesize creatorId = _creatorId;
@synthesize entitledUsersEdit = _entitledUsersEdit;
@synthesize entitledUsersPublish = _entitledUsersPublish;
@synthesize ownerId = _ownerId;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_conversionProfileId = KALTURA_UNDEF_INT;
    self->_accessControlProfileId = KALTURA_UNDEF_INT;
    self->_scheduleStartDate = KALTURA_UNDEF_INT;
    self->_scheduleEndDate = KALTURA_UNDEF_INT;
    self->_entryStatus = KALTURA_UNDEF_INT;
    self->_thumbnailSaved = KALTURA_UNDEF_BOOL;
    return self;
}

- (KalturaFieldType)getTypeOfEntryId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTitle
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDescription
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTags
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfUrl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfContentType
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfConversionProfileId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfAccessControlProfileId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCategory
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfScheduleStartDate
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfScheduleEndDate
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfEntryStatus
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfThumbnailUrl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfThumbnailSaved
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfSshPrivateKey
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSshPublicKey
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSshKeyPassphrase
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreatorId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfEntitledUsersEdit
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfEntitledUsersPublish
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfOwnerId
{
    return KFT_String;
}

- (void)setConversionProfileIdFromString:(NSString*)aPropVal
{
    self.conversionProfileId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setAccessControlProfileIdFromString:(NSString*)aPropVal
{
    self.accessControlProfileId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setScheduleStartDateFromString:(NSString*)aPropVal
{
    self.scheduleStartDate = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setScheduleEndDateFromString:(NSString*)aPropVal
{
    self.scheduleEndDate = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setEntryStatusFromString:(NSString*)aPropVal
{
    self.entryStatus = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setThumbnailSavedFromString:(NSString*)aPropVal
{
    self.thumbnailSaved = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaBulkUploadResultEntry"];
    [aParams addIfDefinedKey:@"entryId" withString:self.entryId];
    [aParams addIfDefinedKey:@"title" withString:self.title];
    [aParams addIfDefinedKey:@"description" withString:self.description];
    [aParams addIfDefinedKey:@"tags" withString:self.tags];
    [aParams addIfDefinedKey:@"url" withString:self.url];
    [aParams addIfDefinedKey:@"contentType" withString:self.contentType];
    [aParams addIfDefinedKey:@"conversionProfileId" withInt:self.conversionProfileId];
    [aParams addIfDefinedKey:@"accessControlProfileId" withInt:self.accessControlProfileId];
    [aParams addIfDefinedKey:@"category" withString:self.category];
    [aParams addIfDefinedKey:@"scheduleStartDate" withInt:self.scheduleStartDate];
    [aParams addIfDefinedKey:@"scheduleEndDate" withInt:self.scheduleEndDate];
    [aParams addIfDefinedKey:@"entryStatus" withInt:self.entryStatus];
    [aParams addIfDefinedKey:@"thumbnailUrl" withString:self.thumbnailUrl];
    [aParams addIfDefinedKey:@"thumbnailSaved" withBool:self.thumbnailSaved];
    [aParams addIfDefinedKey:@"sshPrivateKey" withString:self.sshPrivateKey];
    [aParams addIfDefinedKey:@"sshPublicKey" withString:self.sshPublicKey];
    [aParams addIfDefinedKey:@"sshKeyPassphrase" withString:self.sshKeyPassphrase];
    [aParams addIfDefinedKey:@"creatorId" withString:self.creatorId];
    [aParams addIfDefinedKey:@"entitledUsersEdit" withString:self.entitledUsersEdit];
    [aParams addIfDefinedKey:@"entitledUsersPublish" withString:self.entitledUsersPublish];
    [aParams addIfDefinedKey:@"ownerId" withString:self.ownerId];
}

- (void)dealloc
{
    [self->_entryId release];
    [self->_title release];
    [self->_description release];
    [self->_tags release];
    [self->_url release];
    [self->_contentType release];
    [self->_category release];
    [self->_thumbnailUrl release];
    [self->_sshPrivateKey release];
    [self->_sshPublicKey release];
    [self->_sshKeyPassphrase release];
    [self->_creatorId release];
    [self->_entitledUsersEdit release];
    [self->_entitledUsersPublish release];
    [self->_ownerId release];
    [super dealloc];
}

@end

@implementation KalturaBulkUploadResultUser
@synthesize userId = _userId;
@synthesize screenName = _screenName;
@synthesize email = _email;
@synthesize description = _description;
@synthesize tags = _tags;
@synthesize dateOfBirth = _dateOfBirth;
@synthesize country = _country;
@synthesize state = _state;
@synthesize city = _city;
@synthesize zip = _zip;
@synthesize gender = _gender;
@synthesize firstName = _firstName;
@synthesize lastName = _lastName;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_dateOfBirth = KALTURA_UNDEF_INT;
    self->_gender = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfUserId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfScreenName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfEmail
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDescription
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTags
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDateOfBirth
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCountry
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfState
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCity
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfZip
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfGender
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfFirstName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfLastName
{
    return KFT_String;
}

- (void)setDateOfBirthFromString:(NSString*)aPropVal
{
    self.dateOfBirth = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setGenderFromString:(NSString*)aPropVal
{
    self.gender = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaBulkUploadResultUser"];
    [aParams addIfDefinedKey:@"userId" withString:self.userId];
    [aParams addIfDefinedKey:@"screenName" withString:self.screenName];
    [aParams addIfDefinedKey:@"email" withString:self.email];
    [aParams addIfDefinedKey:@"description" withString:self.description];
    [aParams addIfDefinedKey:@"tags" withString:self.tags];
    [aParams addIfDefinedKey:@"dateOfBirth" withInt:self.dateOfBirth];
    [aParams addIfDefinedKey:@"country" withString:self.country];
    [aParams addIfDefinedKey:@"state" withString:self.state];
    [aParams addIfDefinedKey:@"city" withString:self.city];
    [aParams addIfDefinedKey:@"zip" withString:self.zip];
    [aParams addIfDefinedKey:@"gender" withInt:self.gender];
    [aParams addIfDefinedKey:@"firstName" withString:self.firstName];
    [aParams addIfDefinedKey:@"lastName" withString:self.lastName];
}

- (void)dealloc
{
    [self->_userId release];
    [self->_screenName release];
    [self->_email release];
    [self->_description release];
    [self->_tags release];
    [self->_country release];
    [self->_state release];
    [self->_city release];
    [self->_zip release];
    [self->_firstName release];
    [self->_lastName release];
    [super dealloc];
}

@end

@implementation KalturaBulkUploadUserData
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaBulkUploadUserData"];
}

@end

@implementation KalturaCaptureThumbJobData
@synthesize srcFileSyncLocalPath = _srcFileSyncLocalPath;
@synthesize actualSrcFileSyncLocalPath = _actualSrcFileSyncLocalPath;
@synthesize srcFileSyncRemoteUrl = _srcFileSyncRemoteUrl;
@synthesize thumbParamsOutputId = _thumbParamsOutputId;
@synthesize thumbAssetId = _thumbAssetId;
@synthesize srcAssetId = _srcAssetId;
@synthesize srcAssetType = _srcAssetType;
@synthesize thumbPath = _thumbPath;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_thumbParamsOutputId = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfSrcFileSyncLocalPath
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfActualSrcFileSyncLocalPath
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSrcFileSyncRemoteUrl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfThumbParamsOutputId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfThumbAssetId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSrcAssetId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSrcAssetType
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfThumbPath
{
    return KFT_String;
}

- (void)setThumbParamsOutputIdFromString:(NSString*)aPropVal
{
    self.thumbParamsOutputId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaCaptureThumbJobData"];
    [aParams addIfDefinedKey:@"srcFileSyncLocalPath" withString:self.srcFileSyncLocalPath];
    [aParams addIfDefinedKey:@"actualSrcFileSyncLocalPath" withString:self.actualSrcFileSyncLocalPath];
    [aParams addIfDefinedKey:@"srcFileSyncRemoteUrl" withString:self.srcFileSyncRemoteUrl];
    [aParams addIfDefinedKey:@"thumbParamsOutputId" withInt:self.thumbParamsOutputId];
    [aParams addIfDefinedKey:@"thumbAssetId" withString:self.thumbAssetId];
    [aParams addIfDefinedKey:@"srcAssetId" withString:self.srcAssetId];
    [aParams addIfDefinedKey:@"srcAssetType" withString:self.srcAssetType];
    [aParams addIfDefinedKey:@"thumbPath" withString:self.thumbPath];
}

- (void)dealloc
{
    [self->_srcFileSyncLocalPath release];
    [self->_actualSrcFileSyncLocalPath release];
    [self->_srcFileSyncRemoteUrl release];
    [self->_thumbAssetId release];
    [self->_srcAssetId release];
    [self->_srcAssetType release];
    [self->_thumbPath release];
    [super dealloc];
}

@end

@implementation KalturaCategoryBaseFilter
@synthesize idEqual = _idEqual;
@synthesize idIn = _idIn;
@synthesize parentIdEqual = _parentIdEqual;
@synthesize parentIdIn = _parentIdIn;
@synthesize depthEqual = _depthEqual;
@synthesize fullNameEqual = _fullNameEqual;
@synthesize fullNameStartsWith = _fullNameStartsWith;
@synthesize fullNameIn = _fullNameIn;
@synthesize fullIdsEqual = _fullIdsEqual;
@synthesize fullIdsStartsWith = _fullIdsStartsWith;
@synthesize fullIdsMatchOr = _fullIdsMatchOr;
@synthesize createdAtGreaterThanOrEqual = _createdAtGreaterThanOrEqual;
@synthesize createdAtLessThanOrEqual = _createdAtLessThanOrEqual;
@synthesize updatedAtGreaterThanOrEqual = _updatedAtGreaterThanOrEqual;
@synthesize updatedAtLessThanOrEqual = _updatedAtLessThanOrEqual;
@synthesize tagsLike = _tagsLike;
@synthesize tagsMultiLikeOr = _tagsMultiLikeOr;
@synthesize tagsMultiLikeAnd = _tagsMultiLikeAnd;
@synthesize appearInListEqual = _appearInListEqual;
@synthesize privacyEqual = _privacyEqual;
@synthesize privacyIn = _privacyIn;
@synthesize inheritanceTypeEqual = _inheritanceTypeEqual;
@synthesize inheritanceTypeIn = _inheritanceTypeIn;
@synthesize referenceIdEqual = _referenceIdEqual;
@synthesize contributionPolicyEqual = _contributionPolicyEqual;
@synthesize membersCountGreaterThanOrEqual = _membersCountGreaterThanOrEqual;
@synthesize membersCountLessThanOrEqual = _membersCountLessThanOrEqual;
@synthesize pendingMembersCountGreaterThanOrEqual = _pendingMembersCountGreaterThanOrEqual;
@synthesize pendingMembersCountLessThanOrEqual = _pendingMembersCountLessThanOrEqual;
@synthesize privacyContextEqual = _privacyContextEqual;
@synthesize statusEqual = _statusEqual;
@synthesize statusIn = _statusIn;
@synthesize inheritedParentIdEqual = _inheritedParentIdEqual;
@synthesize inheritedParentIdIn = _inheritedParentIdIn;
@synthesize partnerSortValueGreaterThanOrEqual = _partnerSortValueGreaterThanOrEqual;
@synthesize partnerSortValueLessThanOrEqual = _partnerSortValueLessThanOrEqual;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_idEqual = KALTURA_UNDEF_INT;
    self->_parentIdEqual = KALTURA_UNDEF_INT;
    self->_depthEqual = KALTURA_UNDEF_INT;
    self->_createdAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_createdAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_updatedAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_updatedAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_appearInListEqual = KALTURA_UNDEF_INT;
    self->_privacyEqual = KALTURA_UNDEF_INT;
    self->_inheritanceTypeEqual = KALTURA_UNDEF_INT;
    self->_contributionPolicyEqual = KALTURA_UNDEF_INT;
    self->_membersCountGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_membersCountLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_pendingMembersCountGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_pendingMembersCountLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_statusEqual = KALTURA_UNDEF_INT;
    self->_inheritedParentIdEqual = KALTURA_UNDEF_INT;
    self->_partnerSortValueGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_partnerSortValueLessThanOrEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfParentIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfParentIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDepthEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfFullNameEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFullNameStartsWith
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFullNameIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFullIdsEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFullIdsStartsWith
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFullIdsMatchOr
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfTagsLike
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTagsMultiLikeOr
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTagsMultiLikeAnd
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAppearInListEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPrivacyEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPrivacyIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfInheritanceTypeEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfInheritanceTypeIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfReferenceIdEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfContributionPolicyEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfMembersCountGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfMembersCountLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPendingMembersCountGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPendingMembersCountLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPrivacyContextEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatusEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfStatusIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfInheritedParentIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfInheritedParentIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPartnerSortValueGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerSortValueLessThanOrEqual
{
    return KFT_Int;
}

- (void)setIdEqualFromString:(NSString*)aPropVal
{
    self.idEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setParentIdEqualFromString:(NSString*)aPropVal
{
    self.parentIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDepthEqualFromString:(NSString*)aPropVal
{
    self.depthEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.updatedAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.updatedAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setAppearInListEqualFromString:(NSString*)aPropVal
{
    self.appearInListEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPrivacyEqualFromString:(NSString*)aPropVal
{
    self.privacyEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setInheritanceTypeEqualFromString:(NSString*)aPropVal
{
    self.inheritanceTypeEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setContributionPolicyEqualFromString:(NSString*)aPropVal
{
    self.contributionPolicyEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setMembersCountGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.membersCountGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setMembersCountLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.membersCountLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPendingMembersCountGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.pendingMembersCountGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPendingMembersCountLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.pendingMembersCountLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusEqualFromString:(NSString*)aPropVal
{
    self.statusEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setInheritedParentIdEqualFromString:(NSString*)aPropVal
{
    self.inheritedParentIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerSortValueGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.partnerSortValueGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerSortValueLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.partnerSortValueLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaCategoryBaseFilter"];
    [aParams addIfDefinedKey:@"idEqual" withInt:self.idEqual];
    [aParams addIfDefinedKey:@"idIn" withString:self.idIn];
    [aParams addIfDefinedKey:@"parentIdEqual" withInt:self.parentIdEqual];
    [aParams addIfDefinedKey:@"parentIdIn" withString:self.parentIdIn];
    [aParams addIfDefinedKey:@"depthEqual" withInt:self.depthEqual];
    [aParams addIfDefinedKey:@"fullNameEqual" withString:self.fullNameEqual];
    [aParams addIfDefinedKey:@"fullNameStartsWith" withString:self.fullNameStartsWith];
    [aParams addIfDefinedKey:@"fullNameIn" withString:self.fullNameIn];
    [aParams addIfDefinedKey:@"fullIdsEqual" withString:self.fullIdsEqual];
    [aParams addIfDefinedKey:@"fullIdsStartsWith" withString:self.fullIdsStartsWith];
    [aParams addIfDefinedKey:@"fullIdsMatchOr" withString:self.fullIdsMatchOr];
    [aParams addIfDefinedKey:@"createdAtGreaterThanOrEqual" withInt:self.createdAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"createdAtLessThanOrEqual" withInt:self.createdAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"updatedAtGreaterThanOrEqual" withInt:self.updatedAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"updatedAtLessThanOrEqual" withInt:self.updatedAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"tagsLike" withString:self.tagsLike];
    [aParams addIfDefinedKey:@"tagsMultiLikeOr" withString:self.tagsMultiLikeOr];
    [aParams addIfDefinedKey:@"tagsMultiLikeAnd" withString:self.tagsMultiLikeAnd];
    [aParams addIfDefinedKey:@"appearInListEqual" withInt:self.appearInListEqual];
    [aParams addIfDefinedKey:@"privacyEqual" withInt:self.privacyEqual];
    [aParams addIfDefinedKey:@"privacyIn" withString:self.privacyIn];
    [aParams addIfDefinedKey:@"inheritanceTypeEqual" withInt:self.inheritanceTypeEqual];
    [aParams addIfDefinedKey:@"inheritanceTypeIn" withString:self.inheritanceTypeIn];
    [aParams addIfDefinedKey:@"referenceIdEqual" withString:self.referenceIdEqual];
    [aParams addIfDefinedKey:@"contributionPolicyEqual" withInt:self.contributionPolicyEqual];
    [aParams addIfDefinedKey:@"membersCountGreaterThanOrEqual" withInt:self.membersCountGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"membersCountLessThanOrEqual" withInt:self.membersCountLessThanOrEqual];
    [aParams addIfDefinedKey:@"pendingMembersCountGreaterThanOrEqual" withInt:self.pendingMembersCountGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"pendingMembersCountLessThanOrEqual" withInt:self.pendingMembersCountLessThanOrEqual];
    [aParams addIfDefinedKey:@"privacyContextEqual" withString:self.privacyContextEqual];
    [aParams addIfDefinedKey:@"statusEqual" withInt:self.statusEqual];
    [aParams addIfDefinedKey:@"statusIn" withString:self.statusIn];
    [aParams addIfDefinedKey:@"inheritedParentIdEqual" withInt:self.inheritedParentIdEqual];
    [aParams addIfDefinedKey:@"inheritedParentIdIn" withString:self.inheritedParentIdIn];
    [aParams addIfDefinedKey:@"partnerSortValueGreaterThanOrEqual" withInt:self.partnerSortValueGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"partnerSortValueLessThanOrEqual" withInt:self.partnerSortValueLessThanOrEqual];
}

- (void)dealloc
{
    [self->_idIn release];
    [self->_parentIdIn release];
    [self->_fullNameEqual release];
    [self->_fullNameStartsWith release];
    [self->_fullNameIn release];
    [self->_fullIdsEqual release];
    [self->_fullIdsStartsWith release];
    [self->_fullIdsMatchOr release];
    [self->_tagsLike release];
    [self->_tagsMultiLikeOr release];
    [self->_tagsMultiLikeAnd release];
    [self->_privacyIn release];
    [self->_inheritanceTypeIn release];
    [self->_referenceIdEqual release];
    [self->_privacyContextEqual release];
    [self->_statusIn release];
    [self->_inheritedParentIdIn release];
    [super dealloc];
}

@end

@implementation KalturaCategoryEntryBaseFilter
@synthesize categoryIdEqual = _categoryIdEqual;
@synthesize categoryIdIn = _categoryIdIn;
@synthesize entryIdEqual = _entryIdEqual;
@synthesize entryIdIn = _entryIdIn;
@synthesize createdAtGreaterThanOrEqual = _createdAtGreaterThanOrEqual;
@synthesize createdAtLessThanOrEqual = _createdAtLessThanOrEqual;
@synthesize categoryFullIdsStartsWith = _categoryFullIdsStartsWith;
@synthesize statusEqual = _statusEqual;
@synthesize statusIn = _statusIn;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_categoryIdEqual = KALTURA_UNDEF_INT;
    self->_createdAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_createdAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_statusEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfCategoryIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCategoryIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfEntryIdEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfEntryIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCategoryFullIdsStartsWith
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatusEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfStatusIn
{
    return KFT_String;
}

- (void)setCategoryIdEqualFromString:(NSString*)aPropVal
{
    self.categoryIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusEqualFromString:(NSString*)aPropVal
{
    self.statusEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaCategoryEntryBaseFilter"];
    [aParams addIfDefinedKey:@"categoryIdEqual" withInt:self.categoryIdEqual];
    [aParams addIfDefinedKey:@"categoryIdIn" withString:self.categoryIdIn];
    [aParams addIfDefinedKey:@"entryIdEqual" withString:self.entryIdEqual];
    [aParams addIfDefinedKey:@"entryIdIn" withString:self.entryIdIn];
    [aParams addIfDefinedKey:@"createdAtGreaterThanOrEqual" withInt:self.createdAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"createdAtLessThanOrEqual" withInt:self.createdAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"categoryFullIdsStartsWith" withString:self.categoryFullIdsStartsWith];
    [aParams addIfDefinedKey:@"statusEqual" withInt:self.statusEqual];
    [aParams addIfDefinedKey:@"statusIn" withString:self.statusIn];
}

- (void)dealloc
{
    [self->_categoryIdIn release];
    [self->_entryIdEqual release];
    [self->_entryIdIn release];
    [self->_categoryFullIdsStartsWith release];
    [self->_statusIn release];
    [super dealloc];
}

@end

@implementation KalturaCategoryIdentifier
@synthesize identifier = _identifier;

- (KalturaFieldType)getTypeOfIdentifier
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaCategoryIdentifier"];
    [aParams addIfDefinedKey:@"identifier" withString:self.identifier];
}

- (void)dealloc
{
    [self->_identifier release];
    [super dealloc];
}

@end

@implementation KalturaCategoryUserBaseFilter
@synthesize categoryIdEqual = _categoryIdEqual;
@synthesize categoryIdIn = _categoryIdIn;
@synthesize userIdEqual = _userIdEqual;
@synthesize userIdIn = _userIdIn;
@synthesize permissionLevelEqual = _permissionLevelEqual;
@synthesize permissionLevelIn = _permissionLevelIn;
@synthesize statusEqual = _statusEqual;
@synthesize statusIn = _statusIn;
@synthesize createdAtGreaterThanOrEqual = _createdAtGreaterThanOrEqual;
@synthesize createdAtLessThanOrEqual = _createdAtLessThanOrEqual;
@synthesize updatedAtGreaterThanOrEqual = _updatedAtGreaterThanOrEqual;
@synthesize updatedAtLessThanOrEqual = _updatedAtLessThanOrEqual;
@synthesize updateMethodEqual = _updateMethodEqual;
@synthesize updateMethodIn = _updateMethodIn;
@synthesize categoryFullIdsStartsWith = _categoryFullIdsStartsWith;
@synthesize categoryFullIdsEqual = _categoryFullIdsEqual;
@synthesize permissionNamesMatchAnd = _permissionNamesMatchAnd;
@synthesize permissionNamesMatchOr = _permissionNamesMatchOr;
@synthesize permissionNamesNotContains = _permissionNamesNotContains;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_categoryIdEqual = KALTURA_UNDEF_INT;
    self->_permissionLevelEqual = KALTURA_UNDEF_INT;
    self->_statusEqual = KALTURA_UNDEF_INT;
    self->_createdAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_createdAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_updatedAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_updatedAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_updateMethodEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfCategoryIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCategoryIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfUserIdEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfUserIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPermissionLevelEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPermissionLevelIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatusEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfStatusIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdateMethodEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdateMethodIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCategoryFullIdsStartsWith
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCategoryFullIdsEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPermissionNamesMatchAnd
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPermissionNamesMatchOr
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPermissionNamesNotContains
{
    return KFT_String;
}

- (void)setCategoryIdEqualFromString:(NSString*)aPropVal
{
    self.categoryIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPermissionLevelEqualFromString:(NSString*)aPropVal
{
    self.permissionLevelEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusEqualFromString:(NSString*)aPropVal
{
    self.statusEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.updatedAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.updatedAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdateMethodEqualFromString:(NSString*)aPropVal
{
    self.updateMethodEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaCategoryUserBaseFilter"];
    [aParams addIfDefinedKey:@"categoryIdEqual" withInt:self.categoryIdEqual];
    [aParams addIfDefinedKey:@"categoryIdIn" withString:self.categoryIdIn];
    [aParams addIfDefinedKey:@"userIdEqual" withString:self.userIdEqual];
    [aParams addIfDefinedKey:@"userIdIn" withString:self.userIdIn];
    [aParams addIfDefinedKey:@"permissionLevelEqual" withInt:self.permissionLevelEqual];
    [aParams addIfDefinedKey:@"permissionLevelIn" withString:self.permissionLevelIn];
    [aParams addIfDefinedKey:@"statusEqual" withInt:self.statusEqual];
    [aParams addIfDefinedKey:@"statusIn" withString:self.statusIn];
    [aParams addIfDefinedKey:@"createdAtGreaterThanOrEqual" withInt:self.createdAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"createdAtLessThanOrEqual" withInt:self.createdAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"updatedAtGreaterThanOrEqual" withInt:self.updatedAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"updatedAtLessThanOrEqual" withInt:self.updatedAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"updateMethodEqual" withInt:self.updateMethodEqual];
    [aParams addIfDefinedKey:@"updateMethodIn" withString:self.updateMethodIn];
    [aParams addIfDefinedKey:@"categoryFullIdsStartsWith" withString:self.categoryFullIdsStartsWith];
    [aParams addIfDefinedKey:@"categoryFullIdsEqual" withString:self.categoryFullIdsEqual];
    [aParams addIfDefinedKey:@"permissionNamesMatchAnd" withString:self.permissionNamesMatchAnd];
    [aParams addIfDefinedKey:@"permissionNamesMatchOr" withString:self.permissionNamesMatchOr];
    [aParams addIfDefinedKey:@"permissionNamesNotContains" withString:self.permissionNamesNotContains];
}

- (void)dealloc
{
    [self->_categoryIdIn release];
    [self->_userIdEqual release];
    [self->_userIdIn release];
    [self->_permissionLevelIn release];
    [self->_statusIn release];
    [self->_updateMethodIn release];
    [self->_categoryFullIdsStartsWith release];
    [self->_categoryFullIdsEqual release];
    [self->_permissionNamesMatchAnd release];
    [self->_permissionNamesMatchOr release];
    [self->_permissionNamesNotContains release];
    [super dealloc];
}

@end

@implementation KalturaClipAttributes
@synthesize offset = _offset;
@synthesize duration = _duration;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_offset = KALTURA_UNDEF_INT;
    self->_duration = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfOffset
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDuration
{
    return KFT_Int;
}

- (void)setOffsetFromString:(NSString*)aPropVal
{
    self.offset = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDurationFromString:(NSString*)aPropVal
{
    self.duration = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaClipAttributes"];
    [aParams addIfDefinedKey:@"offset" withInt:self.offset];
    [aParams addIfDefinedKey:@"duration" withInt:self.duration];
}

@end

@implementation KalturaIntegerValue
@synthesize value = _value;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_value = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfValue
{
    return KFT_Int;
}

- (void)setValueFromString:(NSString*)aPropVal
{
    self.value = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaIntegerValue"];
    [aParams addIfDefinedKey:@"value" withInt:self.value];
}

@end

@implementation KalturaCompareCondition
@synthesize value = _value;
@synthesize comparison = _comparison;

- (KalturaFieldType)getTypeOfValue
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfValue
{
    return @"KalturaIntegerValue";
}

- (KalturaFieldType)getTypeOfComparison
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaCompareCondition"];
    [aParams addIfDefinedKey:@"value" withObject:self.value];
    [aParams addIfDefinedKey:@"comparison" withString:self.comparison];
}

- (void)dealloc
{
    [self->_value release];
    [self->_comparison release];
    [super dealloc];
}

@end

@implementation KalturaControlPanelCommandBaseFilter
@synthesize idEqual = _idEqual;
@synthesize idIn = _idIn;
@synthesize createdAtGreaterThanOrEqual = _createdAtGreaterThanOrEqual;
@synthesize createdAtLessThanOrEqual = _createdAtLessThanOrEqual;
@synthesize createdByIdEqual = _createdByIdEqual;
@synthesize typeEqual = _typeEqual;
@synthesize typeIn = _typeIn;
@synthesize targetTypeEqual = _targetTypeEqual;
@synthesize targetTypeIn = _targetTypeIn;
@synthesize statusEqual = _statusEqual;
@synthesize statusIn = _statusIn;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_idEqual = KALTURA_UNDEF_INT;
    self->_createdAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_createdAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_createdByIdEqual = KALTURA_UNDEF_INT;
    self->_typeEqual = KALTURA_UNDEF_INT;
    self->_targetTypeEqual = KALTURA_UNDEF_INT;
    self->_statusEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreatedByIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfTypeEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfTypeIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTargetTypeEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfTargetTypeIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatusEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfStatusIn
{
    return KFT_String;
}

- (void)setIdEqualFromString:(NSString*)aPropVal
{
    self.idEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedByIdEqualFromString:(NSString*)aPropVal
{
    self.createdByIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setTypeEqualFromString:(NSString*)aPropVal
{
    self.typeEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setTargetTypeEqualFromString:(NSString*)aPropVal
{
    self.targetTypeEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusEqualFromString:(NSString*)aPropVal
{
    self.statusEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaControlPanelCommandBaseFilter"];
    [aParams addIfDefinedKey:@"idEqual" withInt:self.idEqual];
    [aParams addIfDefinedKey:@"idIn" withString:self.idIn];
    [aParams addIfDefinedKey:@"createdAtGreaterThanOrEqual" withInt:self.createdAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"createdAtLessThanOrEqual" withInt:self.createdAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"createdByIdEqual" withInt:self.createdByIdEqual];
    [aParams addIfDefinedKey:@"typeEqual" withInt:self.typeEqual];
    [aParams addIfDefinedKey:@"typeIn" withString:self.typeIn];
    [aParams addIfDefinedKey:@"targetTypeEqual" withInt:self.targetTypeEqual];
    [aParams addIfDefinedKey:@"targetTypeIn" withString:self.targetTypeIn];
    [aParams addIfDefinedKey:@"statusEqual" withInt:self.statusEqual];
    [aParams addIfDefinedKey:@"statusIn" withString:self.statusIn];
}

- (void)dealloc
{
    [self->_idIn release];
    [self->_typeIn release];
    [self->_targetTypeIn release];
    [self->_statusIn release];
    [super dealloc];
}

@end

@implementation KalturaConvartableJobData
@synthesize srcFileSyncLocalPath = _srcFileSyncLocalPath;
@synthesize actualSrcFileSyncLocalPath = _actualSrcFileSyncLocalPath;
@synthesize srcFileSyncRemoteUrl = _srcFileSyncRemoteUrl;
@synthesize engineVersion = _engineVersion;
@synthesize flavorParamsOutputId = _flavorParamsOutputId;
@synthesize flavorParamsOutput = _flavorParamsOutput;
@synthesize mediaInfoId = _mediaInfoId;
@synthesize currentOperationSet = _currentOperationSet;
@synthesize currentOperationIndex = _currentOperationIndex;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_engineVersion = KALTURA_UNDEF_INT;
    self->_flavorParamsOutputId = KALTURA_UNDEF_INT;
    self->_mediaInfoId = KALTURA_UNDEF_INT;
    self->_currentOperationSet = KALTURA_UNDEF_INT;
    self->_currentOperationIndex = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfSrcFileSyncLocalPath
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfActualSrcFileSyncLocalPath
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSrcFileSyncRemoteUrl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfEngineVersion
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfFlavorParamsOutputId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfFlavorParamsOutput
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfFlavorParamsOutput
{
    return @"KalturaFlavorParamsOutput";
}

- (KalturaFieldType)getTypeOfMediaInfoId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCurrentOperationSet
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCurrentOperationIndex
{
    return KFT_Int;
}

- (void)setEngineVersionFromString:(NSString*)aPropVal
{
    self.engineVersion = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setFlavorParamsOutputIdFromString:(NSString*)aPropVal
{
    self.flavorParamsOutputId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setMediaInfoIdFromString:(NSString*)aPropVal
{
    self.mediaInfoId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCurrentOperationSetFromString:(NSString*)aPropVal
{
    self.currentOperationSet = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCurrentOperationIndexFromString:(NSString*)aPropVal
{
    self.currentOperationIndex = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaConvartableJobData"];
    [aParams addIfDefinedKey:@"srcFileSyncLocalPath" withString:self.srcFileSyncLocalPath];
    [aParams addIfDefinedKey:@"actualSrcFileSyncLocalPath" withString:self.actualSrcFileSyncLocalPath];
    [aParams addIfDefinedKey:@"srcFileSyncRemoteUrl" withString:self.srcFileSyncRemoteUrl];
    [aParams addIfDefinedKey:@"engineVersion" withInt:self.engineVersion];
    [aParams addIfDefinedKey:@"flavorParamsOutputId" withInt:self.flavorParamsOutputId];
    [aParams addIfDefinedKey:@"flavorParamsOutput" withObject:self.flavorParamsOutput];
    [aParams addIfDefinedKey:@"mediaInfoId" withInt:self.mediaInfoId];
    [aParams addIfDefinedKey:@"currentOperationSet" withInt:self.currentOperationSet];
    [aParams addIfDefinedKey:@"currentOperationIndex" withInt:self.currentOperationIndex];
}

- (void)dealloc
{
    [self->_srcFileSyncLocalPath release];
    [self->_actualSrcFileSyncLocalPath release];
    [self->_srcFileSyncRemoteUrl release];
    [self->_flavorParamsOutput release];
    [super dealloc];
}

@end

@implementation KalturaConversionProfileAssetParamsBaseFilter
@synthesize conversionProfileIdEqual = _conversionProfileIdEqual;
@synthesize conversionProfileIdIn = _conversionProfileIdIn;
@synthesize assetParamsIdEqual = _assetParamsIdEqual;
@synthesize assetParamsIdIn = _assetParamsIdIn;
@synthesize readyBehaviorEqual = _readyBehaviorEqual;
@synthesize readyBehaviorIn = _readyBehaviorIn;
@synthesize originEqual = _originEqual;
@synthesize originIn = _originIn;
@synthesize systemNameEqual = _systemNameEqual;
@synthesize systemNameIn = _systemNameIn;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_conversionProfileIdEqual = KALTURA_UNDEF_INT;
    self->_assetParamsIdEqual = KALTURA_UNDEF_INT;
    self->_readyBehaviorEqual = KALTURA_UNDEF_INT;
    self->_originEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfConversionProfileIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfConversionProfileIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAssetParamsIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfAssetParamsIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfReadyBehaviorEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfReadyBehaviorIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfOriginEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfOriginIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSystemNameEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSystemNameIn
{
    return KFT_String;
}

- (void)setConversionProfileIdEqualFromString:(NSString*)aPropVal
{
    self.conversionProfileIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setAssetParamsIdEqualFromString:(NSString*)aPropVal
{
    self.assetParamsIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setReadyBehaviorEqualFromString:(NSString*)aPropVal
{
    self.readyBehaviorEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setOriginEqualFromString:(NSString*)aPropVal
{
    self.originEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaConversionProfileAssetParamsBaseFilter"];
    [aParams addIfDefinedKey:@"conversionProfileIdEqual" withInt:self.conversionProfileIdEqual];
    [aParams addIfDefinedKey:@"conversionProfileIdIn" withString:self.conversionProfileIdIn];
    [aParams addIfDefinedKey:@"assetParamsIdEqual" withInt:self.assetParamsIdEqual];
    [aParams addIfDefinedKey:@"assetParamsIdIn" withString:self.assetParamsIdIn];
    [aParams addIfDefinedKey:@"readyBehaviorEqual" withInt:self.readyBehaviorEqual];
    [aParams addIfDefinedKey:@"readyBehaviorIn" withString:self.readyBehaviorIn];
    [aParams addIfDefinedKey:@"originEqual" withInt:self.originEqual];
    [aParams addIfDefinedKey:@"originIn" withString:self.originIn];
    [aParams addIfDefinedKey:@"systemNameEqual" withString:self.systemNameEqual];
    [aParams addIfDefinedKey:@"systemNameIn" withString:self.systemNameIn];
}

- (void)dealloc
{
    [self->_conversionProfileIdIn release];
    [self->_assetParamsIdIn release];
    [self->_readyBehaviorIn release];
    [self->_originIn release];
    [self->_systemNameEqual release];
    [self->_systemNameIn release];
    [super dealloc];
}

@end

@implementation KalturaConversionProfileBaseFilter
@synthesize idEqual = _idEqual;
@synthesize idIn = _idIn;
@synthesize statusEqual = _statusEqual;
@synthesize statusIn = _statusIn;
@synthesize nameEqual = _nameEqual;
@synthesize systemNameEqual = _systemNameEqual;
@synthesize systemNameIn = _systemNameIn;
@synthesize tagsMultiLikeOr = _tagsMultiLikeOr;
@synthesize tagsMultiLikeAnd = _tagsMultiLikeAnd;
@synthesize defaultEntryIdEqual = _defaultEntryIdEqual;
@synthesize defaultEntryIdIn = _defaultEntryIdIn;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_idEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatusEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatusIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfNameEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSystemNameEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSystemNameIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTagsMultiLikeOr
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTagsMultiLikeAnd
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDefaultEntryIdEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDefaultEntryIdIn
{
    return KFT_String;
}

- (void)setIdEqualFromString:(NSString*)aPropVal
{
    self.idEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaConversionProfileBaseFilter"];
    [aParams addIfDefinedKey:@"idEqual" withInt:self.idEqual];
    [aParams addIfDefinedKey:@"idIn" withString:self.idIn];
    [aParams addIfDefinedKey:@"statusEqual" withString:self.statusEqual];
    [aParams addIfDefinedKey:@"statusIn" withString:self.statusIn];
    [aParams addIfDefinedKey:@"nameEqual" withString:self.nameEqual];
    [aParams addIfDefinedKey:@"systemNameEqual" withString:self.systemNameEqual];
    [aParams addIfDefinedKey:@"systemNameIn" withString:self.systemNameIn];
    [aParams addIfDefinedKey:@"tagsMultiLikeOr" withString:self.tagsMultiLikeOr];
    [aParams addIfDefinedKey:@"tagsMultiLikeAnd" withString:self.tagsMultiLikeAnd];
    [aParams addIfDefinedKey:@"defaultEntryIdEqual" withString:self.defaultEntryIdEqual];
    [aParams addIfDefinedKey:@"defaultEntryIdIn" withString:self.defaultEntryIdIn];
}

- (void)dealloc
{
    [self->_idIn release];
    [self->_statusEqual release];
    [self->_statusIn release];
    [self->_nameEqual release];
    [self->_systemNameEqual release];
    [self->_systemNameIn release];
    [self->_tagsMultiLikeOr release];
    [self->_tagsMultiLikeAnd release];
    [self->_defaultEntryIdEqual release];
    [self->_defaultEntryIdIn release];
    [super dealloc];
}

@end

@implementation KalturaConvertProfileJobData
@synthesize inputFileSyncLocalPath = _inputFileSyncLocalPath;
@synthesize thumbHeight = _thumbHeight;
@synthesize thumbBitrate = _thumbBitrate;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_thumbHeight = KALTURA_UNDEF_INT;
    self->_thumbBitrate = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfInputFileSyncLocalPath
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfThumbHeight
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfThumbBitrate
{
    return KFT_Int;
}

- (void)setThumbHeightFromString:(NSString*)aPropVal
{
    self.thumbHeight = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setThumbBitrateFromString:(NSString*)aPropVal
{
    self.thumbBitrate = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaConvertProfileJobData"];
    [aParams addIfDefinedKey:@"inputFileSyncLocalPath" withString:self.inputFileSyncLocalPath];
    [aParams addIfDefinedKey:@"thumbHeight" withInt:self.thumbHeight];
    [aParams addIfDefinedKey:@"thumbBitrate" withInt:self.thumbBitrate];
}

- (void)dealloc
{
    [self->_inputFileSyncLocalPath release];
    [super dealloc];
}

@end

@implementation KalturaCountryRestriction
@synthesize countryRestrictionType = _countryRestrictionType;
@synthesize countryList = _countryList;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_countryRestrictionType = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfCountryRestrictionType
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCountryList
{
    return KFT_String;
}

- (void)setCountryRestrictionTypeFromString:(NSString*)aPropVal
{
    self.countryRestrictionType = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaCountryRestriction"];
    [aParams addIfDefinedKey:@"countryRestrictionType" withInt:self.countryRestrictionType];
    [aParams addIfDefinedKey:@"countryList" withString:self.countryList];
}

- (void)dealloc
{
    [self->_countryList release];
    [super dealloc];
}

@end

@implementation KalturaDeleteFileJobData
@synthesize localFileSyncPath = _localFileSyncPath;

- (KalturaFieldType)getTypeOfLocalFileSyncPath
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDeleteFileJobData"];
    [aParams addIfDefinedKey:@"localFileSyncPath" withString:self.localFileSyncPath];
}

- (void)dealloc
{
    [self->_localFileSyncPath release];
    [super dealloc];
}

@end

@implementation KalturaDeleteJobData
@synthesize filter = _filter;

- (KalturaFieldType)getTypeOfFilter
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfFilter
{
    return @"KalturaFilter";
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDeleteJobData"];
    [aParams addIfDefinedKey:@"filter" withObject:self.filter];
}

- (void)dealloc
{
    [self->_filter release];
    [super dealloc];
}

@end

@implementation KalturaDirectoryRestriction
@synthesize directoryRestrictionType = _directoryRestrictionType;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_directoryRestrictionType = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfDirectoryRestrictionType
{
    return KFT_Int;
}

- (void)setDirectoryRestrictionTypeFromString:(NSString*)aPropVal
{
    self.directoryRestrictionType = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDirectoryRestriction"];
    [aParams addIfDefinedKey:@"directoryRestrictionType" withInt:self.directoryRestrictionType];
}

@end

@implementation KalturaCategoryUserFilter
@synthesize categoryDirectMembers = _categoryDirectMembers;
@synthesize freeText = _freeText;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_categoryDirectMembers = KALTURA_UNDEF_BOOL;
    return self;
}

- (KalturaFieldType)getTypeOfCategoryDirectMembers
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfFreeText
{
    return KFT_String;
}

- (void)setCategoryDirectMembersFromString:(NSString*)aPropVal
{
    self.categoryDirectMembers = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaCategoryUserFilter"];
    [aParams addIfDefinedKey:@"categoryDirectMembers" withBool:self.categoryDirectMembers];
    [aParams addIfDefinedKey:@"freeText" withString:self.freeText];
}

- (void)dealloc
{
    [self->_freeText release];
    [super dealloc];
}

@end

@implementation KalturaUserBaseFilter
@synthesize partnerIdEqual = _partnerIdEqual;
@synthesize screenNameLike = _screenNameLike;
@synthesize screenNameStartsWith = _screenNameStartsWith;
@synthesize emailLike = _emailLike;
@synthesize emailStartsWith = _emailStartsWith;
@synthesize tagsMultiLikeOr = _tagsMultiLikeOr;
@synthesize tagsMultiLikeAnd = _tagsMultiLikeAnd;
@synthesize statusEqual = _statusEqual;
@synthesize statusIn = _statusIn;
@synthesize createdAtGreaterThanOrEqual = _createdAtGreaterThanOrEqual;
@synthesize createdAtLessThanOrEqual = _createdAtLessThanOrEqual;
@synthesize firstNameStartsWith = _firstNameStartsWith;
@synthesize lastNameStartsWith = _lastNameStartsWith;
@synthesize isAdminEqual = _isAdminEqual;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_partnerIdEqual = KALTURA_UNDEF_INT;
    self->_statusEqual = KALTURA_UNDEF_INT;
    self->_createdAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_createdAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_isAdminEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfPartnerIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfScreenNameLike
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfScreenNameStartsWith
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfEmailLike
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfEmailStartsWith
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTagsMultiLikeOr
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTagsMultiLikeAnd
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatusEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfStatusIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfFirstNameStartsWith
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfLastNameStartsWith
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfIsAdminEqual
{
    return KFT_Int;
}

- (void)setPartnerIdEqualFromString:(NSString*)aPropVal
{
    self.partnerIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusEqualFromString:(NSString*)aPropVal
{
    self.statusEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setIsAdminEqualFromString:(NSString*)aPropVal
{
    self.isAdminEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaUserBaseFilter"];
    [aParams addIfDefinedKey:@"partnerIdEqual" withInt:self.partnerIdEqual];
    [aParams addIfDefinedKey:@"screenNameLike" withString:self.screenNameLike];
    [aParams addIfDefinedKey:@"screenNameStartsWith" withString:self.screenNameStartsWith];
    [aParams addIfDefinedKey:@"emailLike" withString:self.emailLike];
    [aParams addIfDefinedKey:@"emailStartsWith" withString:self.emailStartsWith];
    [aParams addIfDefinedKey:@"tagsMultiLikeOr" withString:self.tagsMultiLikeOr];
    [aParams addIfDefinedKey:@"tagsMultiLikeAnd" withString:self.tagsMultiLikeAnd];
    [aParams addIfDefinedKey:@"statusEqual" withInt:self.statusEqual];
    [aParams addIfDefinedKey:@"statusIn" withString:self.statusIn];
    [aParams addIfDefinedKey:@"createdAtGreaterThanOrEqual" withInt:self.createdAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"createdAtLessThanOrEqual" withInt:self.createdAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"firstNameStartsWith" withString:self.firstNameStartsWith];
    [aParams addIfDefinedKey:@"lastNameStartsWith" withString:self.lastNameStartsWith];
    [aParams addIfDefinedKey:@"isAdminEqual" withInt:self.isAdminEqual];
}

- (void)dealloc
{
    [self->_screenNameLike release];
    [self->_screenNameStartsWith release];
    [self->_emailLike release];
    [self->_emailStartsWith release];
    [self->_tagsMultiLikeOr release];
    [self->_tagsMultiLikeAnd release];
    [self->_statusIn release];
    [self->_firstNameStartsWith release];
    [self->_lastNameStartsWith release];
    [super dealloc];
}

@end

@implementation KalturaUserFilter
@synthesize idOrScreenNameStartsWith = _idOrScreenNameStartsWith;
@synthesize idEqual = _idEqual;
@synthesize idIn = _idIn;
@synthesize loginEnabledEqual = _loginEnabledEqual;
@synthesize roleIdEqual = _roleIdEqual;
@synthesize roleIdsEqual = _roleIdsEqual;
@synthesize roleIdsIn = _roleIdsIn;
@synthesize firstNameOrLastNameStartsWith = _firstNameOrLastNameStartsWith;
@synthesize permissionNamesMultiLikeOr = _permissionNamesMultiLikeOr;
@synthesize permissionNamesMultiLikeAnd = _permissionNamesMultiLikeAnd;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_loginEnabledEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfIdOrScreenNameStartsWith
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfIdEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfLoginEnabledEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfRoleIdEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfRoleIdsEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfRoleIdsIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFirstNameOrLastNameStartsWith
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPermissionNamesMultiLikeOr
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPermissionNamesMultiLikeAnd
{
    return KFT_String;
}

- (void)setLoginEnabledEqualFromString:(NSString*)aPropVal
{
    self.loginEnabledEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaUserFilter"];
    [aParams addIfDefinedKey:@"idOrScreenNameStartsWith" withString:self.idOrScreenNameStartsWith];
    [aParams addIfDefinedKey:@"idEqual" withString:self.idEqual];
    [aParams addIfDefinedKey:@"idIn" withString:self.idIn];
    [aParams addIfDefinedKey:@"loginEnabledEqual" withInt:self.loginEnabledEqual];
    [aParams addIfDefinedKey:@"roleIdEqual" withString:self.roleIdEqual];
    [aParams addIfDefinedKey:@"roleIdsEqual" withString:self.roleIdsEqual];
    [aParams addIfDefinedKey:@"roleIdsIn" withString:self.roleIdsIn];
    [aParams addIfDefinedKey:@"firstNameOrLastNameStartsWith" withString:self.firstNameOrLastNameStartsWith];
    [aParams addIfDefinedKey:@"permissionNamesMultiLikeOr" withString:self.permissionNamesMultiLikeOr];
    [aParams addIfDefinedKey:@"permissionNamesMultiLikeAnd" withString:self.permissionNamesMultiLikeAnd];
}

- (void)dealloc
{
    [self->_idOrScreenNameStartsWith release];
    [self->_idEqual release];
    [self->_idIn release];
    [self->_roleIdEqual release];
    [self->_roleIdsEqual release];
    [self->_roleIdsIn release];
    [self->_firstNameOrLastNameStartsWith release];
    [self->_permissionNamesMultiLikeOr release];
    [self->_permissionNamesMultiLikeAnd release];
    [super dealloc];
}

@end

@implementation KalturaEntryContext
@synthesize entryId = _entryId;

- (KalturaFieldType)getTypeOfEntryId
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaEntryContext"];
    [aParams addIfDefinedKey:@"entryId" withString:self.entryId];
}

- (void)dealloc
{
    [self->_entryId release];
    [super dealloc];
}

@end

@implementation KalturaEntryContextDataParams
@synthesize flavorAssetId = _flavorAssetId;
@synthesize flavorTags = _flavorTags;
@synthesize streamerType = _streamerType;
@synthesize mediaProtocol = _mediaProtocol;

- (KalturaFieldType)getTypeOfFlavorAssetId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFlavorTags
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStreamerType
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfMediaProtocol
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaEntryContextDataParams"];
    [aParams addIfDefinedKey:@"flavorAssetId" withString:self.flavorAssetId];
    [aParams addIfDefinedKey:@"flavorTags" withString:self.flavorTags];
    [aParams addIfDefinedKey:@"streamerType" withString:self.streamerType];
    [aParams addIfDefinedKey:@"mediaProtocol" withString:self.mediaProtocol];
}

- (void)dealloc
{
    [self->_flavorAssetId release];
    [self->_flavorTags release];
    [self->_streamerType release];
    [self->_mediaProtocol release];
    [super dealloc];
}

@end

@implementation KalturaEntryIdentifier
@synthesize identifier = _identifier;

- (KalturaFieldType)getTypeOfIdentifier
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaEntryIdentifier"];
    [aParams addIfDefinedKey:@"identifier" withString:self.identifier];
}

- (void)dealloc
{
    [self->_identifier release];
    [super dealloc];
}

@end

@implementation KalturaBooleanField
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaBooleanField"];
}

@end

@implementation KalturaFlattenJobData
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaFlattenJobData"];
}

@end

@implementation KalturaGenericSyndicationFeed
@synthesize feedDescription = _feedDescription;
@synthesize feedLandingPage = _feedLandingPage;

- (KalturaFieldType)getTypeOfFeedDescription
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFeedLandingPage
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaGenericSyndicationFeed"];
    [aParams addIfDefinedKey:@"feedDescription" withString:self.feedDescription];
    [aParams addIfDefinedKey:@"feedLandingPage" withString:self.feedLandingPage];
}

- (void)dealloc
{
    [self->_feedDescription release];
    [self->_feedLandingPage release];
    [super dealloc];
}

@end

@implementation KalturaGoogleVideoSyndicationFeed
@synthesize adultContent = _adultContent;

- (KalturaFieldType)getTypeOfAdultContent
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaGoogleVideoSyndicationFeed"];
    [aParams addIfDefinedKey:@"adultContent" withString:self.adultContent];
}

- (void)dealloc
{
    [self->_adultContent release];
    [super dealloc];
}

@end

@interface KalturaITunesSyndicationFeed()
@property (nonatomic,copy) NSString* category;
@end

@implementation KalturaITunesSyndicationFeed
@synthesize feedDescription = _feedDescription;
@synthesize language = _language;
@synthesize feedLandingPage = _feedLandingPage;
@synthesize ownerName = _ownerName;
@synthesize ownerEmail = _ownerEmail;
@synthesize feedImageUrl = _feedImageUrl;
@synthesize category = _category;
@synthesize adultContent = _adultContent;
@synthesize feedAuthor = _feedAuthor;

- (KalturaFieldType)getTypeOfFeedDescription
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfLanguage
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFeedLandingPage
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfOwnerName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfOwnerEmail
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFeedImageUrl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCategory
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAdultContent
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFeedAuthor
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaITunesSyndicationFeed"];
    [aParams addIfDefinedKey:@"feedDescription" withString:self.feedDescription];
    [aParams addIfDefinedKey:@"language" withString:self.language];
    [aParams addIfDefinedKey:@"feedLandingPage" withString:self.feedLandingPage];
    [aParams addIfDefinedKey:@"ownerName" withString:self.ownerName];
    [aParams addIfDefinedKey:@"ownerEmail" withString:self.ownerEmail];
    [aParams addIfDefinedKey:@"feedImageUrl" withString:self.feedImageUrl];
    [aParams addIfDefinedKey:@"adultContent" withString:self.adultContent];
    [aParams addIfDefinedKey:@"feedAuthor" withString:self.feedAuthor];
}

- (void)dealloc
{
    [self->_feedDescription release];
    [self->_language release];
    [self->_feedLandingPage release];
    [self->_ownerName release];
    [self->_ownerEmail release];
    [self->_feedImageUrl release];
    [self->_category release];
    [self->_adultContent release];
    [self->_feedAuthor release];
    [super dealloc];
}

@end

@implementation KalturaImportJobData
@synthesize srcFileUrl = _srcFileUrl;
@synthesize destFileLocalPath = _destFileLocalPath;
@synthesize flavorAssetId = _flavorAssetId;
@synthesize fileSize = _fileSize;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_fileSize = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfSrcFileUrl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDestFileLocalPath
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFlavorAssetId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFileSize
{
    return KFT_Int;
}

- (void)setFileSizeFromString:(NSString*)aPropVal
{
    self.fileSize = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaImportJobData"];
    [aParams addIfDefinedKey:@"srcFileUrl" withString:self.srcFileUrl];
    [aParams addIfDefinedKey:@"destFileLocalPath" withString:self.destFileLocalPath];
    [aParams addIfDefinedKey:@"flavorAssetId" withString:self.flavorAssetId];
    [aParams addIfDefinedKey:@"fileSize" withInt:self.fileSize];
}

- (void)dealloc
{
    [self->_srcFileUrl release];
    [self->_destFileLocalPath release];
    [self->_flavorAssetId release];
    [super dealloc];
}

@end

@implementation KalturaIndexJobData
@synthesize filter = _filter;
@synthesize lastIndexId = _lastIndexId;
@synthesize shouldUpdate = _shouldUpdate;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_lastIndexId = KALTURA_UNDEF_INT;
    self->_shouldUpdate = KALTURA_UNDEF_BOOL;
    return self;
}

- (KalturaFieldType)getTypeOfFilter
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfFilter
{
    return @"KalturaFilter";
}

- (KalturaFieldType)getTypeOfLastIndexId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfShouldUpdate
{
    return KFT_Bool;
}

- (void)setLastIndexIdFromString:(NSString*)aPropVal
{
    self.lastIndexId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setShouldUpdateFromString:(NSString*)aPropVal
{
    self.shouldUpdate = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaIndexJobData"];
    [aParams addIfDefinedKey:@"filter" withObject:self.filter];
    [aParams addIfDefinedKey:@"lastIndexId" withInt:self.lastIndexId];
    [aParams addIfDefinedKey:@"shouldUpdate" withBool:self.shouldUpdate];
}

- (void)dealloc
{
    [self->_filter release];
    [super dealloc];
}

@end

@implementation KalturaIpAddressRestriction
@synthesize ipAddressRestrictionType = _ipAddressRestrictionType;
@synthesize ipAddressList = _ipAddressList;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_ipAddressRestrictionType = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfIpAddressRestrictionType
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfIpAddressList
{
    return KFT_String;
}

- (void)setIpAddressRestrictionTypeFromString:(NSString*)aPropVal
{
    self.ipAddressRestrictionType = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaIpAddressRestriction"];
    [aParams addIfDefinedKey:@"ipAddressRestrictionType" withInt:self.ipAddressRestrictionType];
    [aParams addIfDefinedKey:@"ipAddressList" withString:self.ipAddressList];
}

- (void)dealloc
{
    [self->_ipAddressList release];
    [super dealloc];
}

@end

@implementation KalturaLimitFlavorsRestriction
@synthesize limitFlavorsRestrictionType = _limitFlavorsRestrictionType;
@synthesize flavorParamsIds = _flavorParamsIds;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_limitFlavorsRestrictionType = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfLimitFlavorsRestrictionType
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfFlavorParamsIds
{
    return KFT_String;
}

- (void)setLimitFlavorsRestrictionTypeFromString:(NSString*)aPropVal
{
    self.limitFlavorsRestrictionType = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaLimitFlavorsRestriction"];
    [aParams addIfDefinedKey:@"limitFlavorsRestrictionType" withInt:self.limitFlavorsRestrictionType];
    [aParams addIfDefinedKey:@"flavorParamsIds" withString:self.flavorParamsIds];
}

- (void)dealloc
{
    [self->_flavorParamsIds release];
    [super dealloc];
}

@end

@implementation KalturaMailJobData
@synthesize mailType = _mailType;
@synthesize mailPriority = _mailPriority;
@synthesize status = _status;
@synthesize recipientName = _recipientName;
@synthesize recipientEmail = _recipientEmail;
@synthesize recipientId = _recipientId;
@synthesize fromName = _fromName;
@synthesize fromEmail = _fromEmail;
@synthesize bodyParams = _bodyParams;
@synthesize subjectParams = _subjectParams;
@synthesize templatePath = _templatePath;
@synthesize culture = _culture;
@synthesize campaignId = _campaignId;
@synthesize minSendDate = _minSendDate;
@synthesize isHtml = _isHtml;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_mailPriority = KALTURA_UNDEF_INT;
    self->_status = KALTURA_UNDEF_INT;
    self->_recipientId = KALTURA_UNDEF_INT;
    self->_culture = KALTURA_UNDEF_INT;
    self->_campaignId = KALTURA_UNDEF_INT;
    self->_minSendDate = KALTURA_UNDEF_INT;
    self->_isHtml = KALTURA_UNDEF_BOOL;
    return self;
}

- (KalturaFieldType)getTypeOfMailType
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfMailPriority
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfStatus
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfRecipientName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfRecipientEmail
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfRecipientId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfFromName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFromEmail
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfBodyParams
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSubjectParams
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTemplatePath
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCulture
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCampaignId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfMinSendDate
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfIsHtml
{
    return KFT_Bool;
}

- (void)setMailPriorityFromString:(NSString*)aPropVal
{
    self.mailPriority = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusFromString:(NSString*)aPropVal
{
    self.status = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setRecipientIdFromString:(NSString*)aPropVal
{
    self.recipientId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCultureFromString:(NSString*)aPropVal
{
    self.culture = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCampaignIdFromString:(NSString*)aPropVal
{
    self.campaignId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setMinSendDateFromString:(NSString*)aPropVal
{
    self.minSendDate = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setIsHtmlFromString:(NSString*)aPropVal
{
    self.isHtml = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaMailJobData"];
    [aParams addIfDefinedKey:@"mailType" withString:self.mailType];
    [aParams addIfDefinedKey:@"mailPriority" withInt:self.mailPriority];
    [aParams addIfDefinedKey:@"status" withInt:self.status];
    [aParams addIfDefinedKey:@"recipientName" withString:self.recipientName];
    [aParams addIfDefinedKey:@"recipientEmail" withString:self.recipientEmail];
    [aParams addIfDefinedKey:@"recipientId" withInt:self.recipientId];
    [aParams addIfDefinedKey:@"fromName" withString:self.fromName];
    [aParams addIfDefinedKey:@"fromEmail" withString:self.fromEmail];
    [aParams addIfDefinedKey:@"bodyParams" withString:self.bodyParams];
    [aParams addIfDefinedKey:@"subjectParams" withString:self.subjectParams];
    [aParams addIfDefinedKey:@"templatePath" withString:self.templatePath];
    [aParams addIfDefinedKey:@"culture" withInt:self.culture];
    [aParams addIfDefinedKey:@"campaignId" withInt:self.campaignId];
    [aParams addIfDefinedKey:@"minSendDate" withInt:self.minSendDate];
    [aParams addIfDefinedKey:@"isHtml" withBool:self.isHtml];
}

- (void)dealloc
{
    [self->_mailType release];
    [self->_recipientName release];
    [self->_recipientEmail release];
    [self->_fromName release];
    [self->_fromEmail release];
    [self->_bodyParams release];
    [self->_subjectParams release];
    [self->_templatePath release];
    [super dealloc];
}

@end

@implementation KalturaMatchCondition
@synthesize values = _values;

- (KalturaFieldType)getTypeOfValues
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfValues
{
    return @"KalturaStringValue";
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaMatchCondition"];
    [aParams addIfDefinedKey:@"values" withArray:self.values];
}

- (void)dealloc
{
    [self->_values release];
    [super dealloc];
}

@end

@implementation KalturaMediaInfoBaseFilter
@synthesize flavorAssetIdEqual = _flavorAssetIdEqual;

- (KalturaFieldType)getTypeOfFlavorAssetIdEqual
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaMediaInfoBaseFilter"];
    [aParams addIfDefinedKey:@"flavorAssetIdEqual" withString:self.flavorAssetIdEqual];
}

- (void)dealloc
{
    [self->_flavorAssetIdEqual release];
    [super dealloc];
}

@end

@implementation KalturaMoveCategoryEntriesJobData
@synthesize srcCategoryId = _srcCategoryId;
@synthesize destCategoryId = _destCategoryId;
@synthesize lastMovedCategoryId = _lastMovedCategoryId;
@synthesize lastMovedCategoryPageIndex = _lastMovedCategoryPageIndex;
@synthesize lastMovedCategoryEntryPageIndex = _lastMovedCategoryEntryPageIndex;
@synthesize moveFromChildren = _moveFromChildren;
@synthesize copyOnly = _copyOnly;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_srcCategoryId = KALTURA_UNDEF_INT;
    self->_destCategoryId = KALTURA_UNDEF_INT;
    self->_lastMovedCategoryId = KALTURA_UNDEF_INT;
    self->_lastMovedCategoryPageIndex = KALTURA_UNDEF_INT;
    self->_lastMovedCategoryEntryPageIndex = KALTURA_UNDEF_INT;
    self->_moveFromChildren = KALTURA_UNDEF_BOOL;
    self->_copyOnly = KALTURA_UNDEF_BOOL;
    return self;
}

- (KalturaFieldType)getTypeOfSrcCategoryId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDestCategoryId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfLastMovedCategoryId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfLastMovedCategoryPageIndex
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfLastMovedCategoryEntryPageIndex
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfMoveFromChildren
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfCopyOnly
{
    return KFT_Bool;
}

- (void)setSrcCategoryIdFromString:(NSString*)aPropVal
{
    self.srcCategoryId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDestCategoryIdFromString:(NSString*)aPropVal
{
    self.destCategoryId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setLastMovedCategoryIdFromString:(NSString*)aPropVal
{
    self.lastMovedCategoryId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setLastMovedCategoryPageIndexFromString:(NSString*)aPropVal
{
    self.lastMovedCategoryPageIndex = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setLastMovedCategoryEntryPageIndexFromString:(NSString*)aPropVal
{
    self.lastMovedCategoryEntryPageIndex = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setMoveFromChildrenFromString:(NSString*)aPropVal
{
    self.moveFromChildren = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)setCopyOnlyFromString:(NSString*)aPropVal
{
    self.copyOnly = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaMoveCategoryEntriesJobData"];
    [aParams addIfDefinedKey:@"srcCategoryId" withInt:self.srcCategoryId];
    [aParams addIfDefinedKey:@"destCategoryId" withInt:self.destCategoryId];
    [aParams addIfDefinedKey:@"lastMovedCategoryId" withInt:self.lastMovedCategoryId];
    [aParams addIfDefinedKey:@"lastMovedCategoryPageIndex" withInt:self.lastMovedCategoryPageIndex];
    [aParams addIfDefinedKey:@"lastMovedCategoryEntryPageIndex" withInt:self.lastMovedCategoryEntryPageIndex];
    [aParams addIfDefinedKey:@"moveFromChildren" withBool:self.moveFromChildren];
    [aParams addIfDefinedKey:@"copyOnly" withBool:self.copyOnly];
}

@end

@implementation KalturaNotificationJobData
@synthesize userId = _userId;
@synthesize type = _type;
@synthesize typeAsString = _typeAsString;
@synthesize objectId = _objectId;
@synthesize status = _status;
@synthesize data = _data;
@synthesize numberOfAttempts = _numberOfAttempts;
@synthesize notificationResult = _notificationResult;
@synthesize objType = _objType;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_type = KALTURA_UNDEF_INT;
    self->_status = KALTURA_UNDEF_INT;
    self->_numberOfAttempts = KALTURA_UNDEF_INT;
    self->_objType = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfUserId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfType
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfTypeAsString
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfObjectId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatus
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfData
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfNumberOfAttempts
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfNotificationResult
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfObjType
{
    return KFT_Int;
}

- (void)setTypeFromString:(NSString*)aPropVal
{
    self.type = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusFromString:(NSString*)aPropVal
{
    self.status = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setNumberOfAttemptsFromString:(NSString*)aPropVal
{
    self.numberOfAttempts = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setObjTypeFromString:(NSString*)aPropVal
{
    self.objType = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaNotificationJobData"];
    [aParams addIfDefinedKey:@"userId" withString:self.userId];
    [aParams addIfDefinedKey:@"type" withInt:self.type];
    [aParams addIfDefinedKey:@"typeAsString" withString:self.typeAsString];
    [aParams addIfDefinedKey:@"objectId" withString:self.objectId];
    [aParams addIfDefinedKey:@"status" withInt:self.status];
    [aParams addIfDefinedKey:@"data" withString:self.data];
    [aParams addIfDefinedKey:@"numberOfAttempts" withInt:self.numberOfAttempts];
    [aParams addIfDefinedKey:@"notificationResult" withString:self.notificationResult];
    [aParams addIfDefinedKey:@"objType" withInt:self.objType];
}

- (void)dealloc
{
    [self->_userId release];
    [self->_typeAsString release];
    [self->_objectId release];
    [self->_data release];
    [self->_notificationResult release];
    [super dealloc];
}

@end

@implementation KalturaPartnerBaseFilter
@synthesize idEqual = _idEqual;
@synthesize idIn = _idIn;
@synthesize idNotIn = _idNotIn;
@synthesize nameLike = _nameLike;
@synthesize nameMultiLikeOr = _nameMultiLikeOr;
@synthesize nameMultiLikeAnd = _nameMultiLikeAnd;
@synthesize nameEqual = _nameEqual;
@synthesize statusEqual = _statusEqual;
@synthesize statusIn = _statusIn;
@synthesize partnerPackageEqual = _partnerPackageEqual;
@synthesize partnerPackageGreaterThanOrEqual = _partnerPackageGreaterThanOrEqual;
@synthesize partnerPackageLessThanOrEqual = _partnerPackageLessThanOrEqual;
@synthesize partnerNameDescriptionWebsiteAdminNameAdminEmailLike = _partnerNameDescriptionWebsiteAdminNameAdminEmailLike;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_idEqual = KALTURA_UNDEF_INT;
    self->_statusEqual = KALTURA_UNDEF_INT;
    self->_partnerPackageEqual = KALTURA_UNDEF_INT;
    self->_partnerPackageGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_partnerPackageLessThanOrEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfIdNotIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfNameLike
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfNameMultiLikeOr
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfNameMultiLikeAnd
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfNameEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatusEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfStatusIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPartnerPackageEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerPackageGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerPackageLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerNameDescriptionWebsiteAdminNameAdminEmailLike
{
    return KFT_String;
}

- (void)setIdEqualFromString:(NSString*)aPropVal
{
    self.idEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusEqualFromString:(NSString*)aPropVal
{
    self.statusEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerPackageEqualFromString:(NSString*)aPropVal
{
    self.partnerPackageEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerPackageGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.partnerPackageGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerPackageLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.partnerPackageLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaPartnerBaseFilter"];
    [aParams addIfDefinedKey:@"idEqual" withInt:self.idEqual];
    [aParams addIfDefinedKey:@"idIn" withString:self.idIn];
    [aParams addIfDefinedKey:@"idNotIn" withString:self.idNotIn];
    [aParams addIfDefinedKey:@"nameLike" withString:self.nameLike];
    [aParams addIfDefinedKey:@"nameMultiLikeOr" withString:self.nameMultiLikeOr];
    [aParams addIfDefinedKey:@"nameMultiLikeAnd" withString:self.nameMultiLikeAnd];
    [aParams addIfDefinedKey:@"nameEqual" withString:self.nameEqual];
    [aParams addIfDefinedKey:@"statusEqual" withInt:self.statusEqual];
    [aParams addIfDefinedKey:@"statusIn" withString:self.statusIn];
    [aParams addIfDefinedKey:@"partnerPackageEqual" withInt:self.partnerPackageEqual];
    [aParams addIfDefinedKey:@"partnerPackageGreaterThanOrEqual" withInt:self.partnerPackageGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"partnerPackageLessThanOrEqual" withInt:self.partnerPackageLessThanOrEqual];
    [aParams addIfDefinedKey:@"partnerNameDescriptionWebsiteAdminNameAdminEmailLike" withString:self.partnerNameDescriptionWebsiteAdminNameAdminEmailLike];
}

- (void)dealloc
{
    [self->_idIn release];
    [self->_idNotIn release];
    [self->_nameLike release];
    [self->_nameMultiLikeOr release];
    [self->_nameMultiLikeAnd release];
    [self->_nameEqual release];
    [self->_statusIn release];
    [self->_partnerNameDescriptionWebsiteAdminNameAdminEmailLike release];
    [super dealloc];
}

@end

@implementation KalturaPermissionBaseFilter
@synthesize idEqual = _idEqual;
@synthesize idIn = _idIn;
@synthesize typeEqual = _typeEqual;
@synthesize typeIn = _typeIn;
@synthesize nameEqual = _nameEqual;
@synthesize nameIn = _nameIn;
@synthesize friendlyNameLike = _friendlyNameLike;
@synthesize descriptionLike = _descriptionLike;
@synthesize statusEqual = _statusEqual;
@synthesize statusIn = _statusIn;
@synthesize partnerIdEqual = _partnerIdEqual;
@synthesize partnerIdIn = _partnerIdIn;
@synthesize dependsOnPermissionNamesMultiLikeOr = _dependsOnPermissionNamesMultiLikeOr;
@synthesize dependsOnPermissionNamesMultiLikeAnd = _dependsOnPermissionNamesMultiLikeAnd;
@synthesize tagsMultiLikeOr = _tagsMultiLikeOr;
@synthesize tagsMultiLikeAnd = _tagsMultiLikeAnd;
@synthesize createdAtGreaterThanOrEqual = _createdAtGreaterThanOrEqual;
@synthesize createdAtLessThanOrEqual = _createdAtLessThanOrEqual;
@synthesize updatedAtGreaterThanOrEqual = _updatedAtGreaterThanOrEqual;
@synthesize updatedAtLessThanOrEqual = _updatedAtLessThanOrEqual;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_idEqual = KALTURA_UNDEF_INT;
    self->_typeEqual = KALTURA_UNDEF_INT;
    self->_statusEqual = KALTURA_UNDEF_INT;
    self->_partnerIdEqual = KALTURA_UNDEF_INT;
    self->_createdAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_createdAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_updatedAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_updatedAtLessThanOrEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTypeEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfTypeIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfNameEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfNameIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFriendlyNameLike
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDescriptionLike
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatusEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfStatusIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPartnerIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDependsOnPermissionNamesMultiLikeOr
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDependsOnPermissionNamesMultiLikeAnd
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTagsMultiLikeOr
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTagsMultiLikeAnd
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (void)setIdEqualFromString:(NSString*)aPropVal
{
    self.idEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setTypeEqualFromString:(NSString*)aPropVal
{
    self.typeEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusEqualFromString:(NSString*)aPropVal
{
    self.statusEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerIdEqualFromString:(NSString*)aPropVal
{
    self.partnerIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.updatedAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.updatedAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaPermissionBaseFilter"];
    [aParams addIfDefinedKey:@"idEqual" withInt:self.idEqual];
    [aParams addIfDefinedKey:@"idIn" withString:self.idIn];
    [aParams addIfDefinedKey:@"typeEqual" withInt:self.typeEqual];
    [aParams addIfDefinedKey:@"typeIn" withString:self.typeIn];
    [aParams addIfDefinedKey:@"nameEqual" withString:self.nameEqual];
    [aParams addIfDefinedKey:@"nameIn" withString:self.nameIn];
    [aParams addIfDefinedKey:@"friendlyNameLike" withString:self.friendlyNameLike];
    [aParams addIfDefinedKey:@"descriptionLike" withString:self.descriptionLike];
    [aParams addIfDefinedKey:@"statusEqual" withInt:self.statusEqual];
    [aParams addIfDefinedKey:@"statusIn" withString:self.statusIn];
    [aParams addIfDefinedKey:@"partnerIdEqual" withInt:self.partnerIdEqual];
    [aParams addIfDefinedKey:@"partnerIdIn" withString:self.partnerIdIn];
    [aParams addIfDefinedKey:@"dependsOnPermissionNamesMultiLikeOr" withString:self.dependsOnPermissionNamesMultiLikeOr];
    [aParams addIfDefinedKey:@"dependsOnPermissionNamesMultiLikeAnd" withString:self.dependsOnPermissionNamesMultiLikeAnd];
    [aParams addIfDefinedKey:@"tagsMultiLikeOr" withString:self.tagsMultiLikeOr];
    [aParams addIfDefinedKey:@"tagsMultiLikeAnd" withString:self.tagsMultiLikeAnd];
    [aParams addIfDefinedKey:@"createdAtGreaterThanOrEqual" withInt:self.createdAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"createdAtLessThanOrEqual" withInt:self.createdAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"updatedAtGreaterThanOrEqual" withInt:self.updatedAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"updatedAtLessThanOrEqual" withInt:self.updatedAtLessThanOrEqual];
}

- (void)dealloc
{
    [self->_idIn release];
    [self->_typeIn release];
    [self->_nameEqual release];
    [self->_nameIn release];
    [self->_friendlyNameLike release];
    [self->_descriptionLike release];
    [self->_statusIn release];
    [self->_partnerIdIn release];
    [self->_dependsOnPermissionNamesMultiLikeOr release];
    [self->_dependsOnPermissionNamesMultiLikeAnd release];
    [self->_tagsMultiLikeOr release];
    [self->_tagsMultiLikeAnd release];
    [super dealloc];
}

@end

@implementation KalturaPermissionItemBaseFilter
@synthesize idEqual = _idEqual;
@synthesize idIn = _idIn;
@synthesize typeEqual = _typeEqual;
@synthesize typeIn = _typeIn;
@synthesize partnerIdEqual = _partnerIdEqual;
@synthesize partnerIdIn = _partnerIdIn;
@synthesize tagsMultiLikeOr = _tagsMultiLikeOr;
@synthesize tagsMultiLikeAnd = _tagsMultiLikeAnd;
@synthesize createdAtGreaterThanOrEqual = _createdAtGreaterThanOrEqual;
@synthesize createdAtLessThanOrEqual = _createdAtLessThanOrEqual;
@synthesize updatedAtGreaterThanOrEqual = _updatedAtGreaterThanOrEqual;
@synthesize updatedAtLessThanOrEqual = _updatedAtLessThanOrEqual;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_idEqual = KALTURA_UNDEF_INT;
    self->_partnerIdEqual = KALTURA_UNDEF_INT;
    self->_createdAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_createdAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_updatedAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_updatedAtLessThanOrEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTypeEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTypeIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPartnerIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTagsMultiLikeOr
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTagsMultiLikeAnd
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (void)setIdEqualFromString:(NSString*)aPropVal
{
    self.idEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerIdEqualFromString:(NSString*)aPropVal
{
    self.partnerIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.updatedAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.updatedAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaPermissionItemBaseFilter"];
    [aParams addIfDefinedKey:@"idEqual" withInt:self.idEqual];
    [aParams addIfDefinedKey:@"idIn" withString:self.idIn];
    [aParams addIfDefinedKey:@"typeEqual" withString:self.typeEqual];
    [aParams addIfDefinedKey:@"typeIn" withString:self.typeIn];
    [aParams addIfDefinedKey:@"partnerIdEqual" withInt:self.partnerIdEqual];
    [aParams addIfDefinedKey:@"partnerIdIn" withString:self.partnerIdIn];
    [aParams addIfDefinedKey:@"tagsMultiLikeOr" withString:self.tagsMultiLikeOr];
    [aParams addIfDefinedKey:@"tagsMultiLikeAnd" withString:self.tagsMultiLikeAnd];
    [aParams addIfDefinedKey:@"createdAtGreaterThanOrEqual" withInt:self.createdAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"createdAtLessThanOrEqual" withInt:self.createdAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"updatedAtGreaterThanOrEqual" withInt:self.updatedAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"updatedAtLessThanOrEqual" withInt:self.updatedAtLessThanOrEqual];
}

- (void)dealloc
{
    [self->_idIn release];
    [self->_typeEqual release];
    [self->_typeIn release];
    [self->_partnerIdIn release];
    [self->_tagsMultiLikeOr release];
    [self->_tagsMultiLikeAnd release];
    [super dealloc];
}

@end

@implementation KalturaProvisionJobData
@synthesize streamID = _streamID;
@synthesize backupStreamID = _backupStreamID;
@synthesize rtmp = _rtmp;
@synthesize encoderIP = _encoderIP;
@synthesize backupEncoderIP = _backupEncoderIP;
@synthesize encoderPassword = _encoderPassword;
@synthesize encoderUsername = _encoderUsername;
@synthesize endDate = _endDate;
@synthesize returnVal = _returnVal;
@synthesize mediaType = _mediaType;
@synthesize primaryBroadcastingUrl = _primaryBroadcastingUrl;
@synthesize secondaryBroadcastingUrl = _secondaryBroadcastingUrl;
@synthesize streamName = _streamName;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_endDate = KALTURA_UNDEF_INT;
    self->_mediaType = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfStreamID
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfBackupStreamID
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfRtmp
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfEncoderIP
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfBackupEncoderIP
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfEncoderPassword
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfEncoderUsername
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfEndDate
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfReturnVal
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfMediaType
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPrimaryBroadcastingUrl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSecondaryBroadcastingUrl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStreamName
{
    return KFT_String;
}

- (void)setEndDateFromString:(NSString*)aPropVal
{
    self.endDate = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setMediaTypeFromString:(NSString*)aPropVal
{
    self.mediaType = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaProvisionJobData"];
    [aParams addIfDefinedKey:@"streamID" withString:self.streamID];
    [aParams addIfDefinedKey:@"backupStreamID" withString:self.backupStreamID];
    [aParams addIfDefinedKey:@"rtmp" withString:self.rtmp];
    [aParams addIfDefinedKey:@"encoderIP" withString:self.encoderIP];
    [aParams addIfDefinedKey:@"backupEncoderIP" withString:self.backupEncoderIP];
    [aParams addIfDefinedKey:@"encoderPassword" withString:self.encoderPassword];
    [aParams addIfDefinedKey:@"encoderUsername" withString:self.encoderUsername];
    [aParams addIfDefinedKey:@"endDate" withInt:self.endDate];
    [aParams addIfDefinedKey:@"returnVal" withString:self.returnVal];
    [aParams addIfDefinedKey:@"mediaType" withInt:self.mediaType];
    [aParams addIfDefinedKey:@"primaryBroadcastingUrl" withString:self.primaryBroadcastingUrl];
    [aParams addIfDefinedKey:@"secondaryBroadcastingUrl" withString:self.secondaryBroadcastingUrl];
    [aParams addIfDefinedKey:@"streamName" withString:self.streamName];
}

- (void)dealloc
{
    [self->_streamID release];
    [self->_backupStreamID release];
    [self->_rtmp release];
    [self->_encoderIP release];
    [self->_backupEncoderIP release];
    [self->_encoderPassword release];
    [self->_encoderUsername release];
    [self->_returnVal release];
    [self->_primaryBroadcastingUrl release];
    [self->_secondaryBroadcastingUrl release];
    [self->_streamName release];
    [super dealloc];
}

@end

@implementation KalturaReportBaseFilter
@synthesize idEqual = _idEqual;
@synthesize idIn = _idIn;
@synthesize partnerIdEqual = _partnerIdEqual;
@synthesize partnerIdIn = _partnerIdIn;
@synthesize systemNameEqual = _systemNameEqual;
@synthesize systemNameIn = _systemNameIn;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_idEqual = KALTURA_UNDEF_INT;
    self->_partnerIdEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPartnerIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSystemNameEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSystemNameIn
{
    return KFT_String;
}

- (void)setIdEqualFromString:(NSString*)aPropVal
{
    self.idEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerIdEqualFromString:(NSString*)aPropVal
{
    self.partnerIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaReportBaseFilter"];
    [aParams addIfDefinedKey:@"idEqual" withInt:self.idEqual];
    [aParams addIfDefinedKey:@"idIn" withString:self.idIn];
    [aParams addIfDefinedKey:@"partnerIdEqual" withInt:self.partnerIdEqual];
    [aParams addIfDefinedKey:@"partnerIdIn" withString:self.partnerIdIn];
    [aParams addIfDefinedKey:@"systemNameEqual" withString:self.systemNameEqual];
    [aParams addIfDefinedKey:@"systemNameIn" withString:self.systemNameIn];
}

- (void)dealloc
{
    [self->_idIn release];
    [self->_partnerIdIn release];
    [self->_systemNameEqual release];
    [self->_systemNameIn release];
    [super dealloc];
}

@end

@implementation KalturaReportInputFilter
@synthesize keywords = _keywords;
@synthesize searchInTags = _searchInTags;
@synthesize searchInAdminTags = _searchInAdminTags;
@synthesize categories = _categories;
@synthesize timeZoneOffset = _timeZoneOffset;
@synthesize interval = _interval;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_searchInTags = KALTURA_UNDEF_BOOL;
    self->_searchInAdminTags = KALTURA_UNDEF_BOOL;
    self->_timeZoneOffset = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfKeywords
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSearchInTags
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfSearchInAdminTags
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfCategories
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTimeZoneOffset
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfInterval
{
    return KFT_String;
}

- (void)setSearchInTagsFromString:(NSString*)aPropVal
{
    self.searchInTags = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)setSearchInAdminTagsFromString:(NSString*)aPropVal
{
    self.searchInAdminTags = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)setTimeZoneOffsetFromString:(NSString*)aPropVal
{
    self.timeZoneOffset = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaReportInputFilter"];
    [aParams addIfDefinedKey:@"keywords" withString:self.keywords];
    [aParams addIfDefinedKey:@"searchInTags" withBool:self.searchInTags];
    [aParams addIfDefinedKey:@"searchInAdminTags" withBool:self.searchInAdminTags];
    [aParams addIfDefinedKey:@"categories" withString:self.categories];
    [aParams addIfDefinedKey:@"timeZoneOffset" withInt:self.timeZoneOffset];
    [aParams addIfDefinedKey:@"interval" withString:self.interval];
}

- (void)dealloc
{
    [self->_keywords release];
    [self->_categories release];
    [self->_interval release];
    [super dealloc];
}

@end

@implementation KalturaSearchCondition
@synthesize field = _field;
@synthesize value = _value;

- (KalturaFieldType)getTypeOfField
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfValue
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaSearchCondition"];
    [aParams addIfDefinedKey:@"field" withString:self.field];
    [aParams addIfDefinedKey:@"value" withString:self.value];
}

- (void)dealloc
{
    [self->_field release];
    [self->_value release];
    [super dealloc];
}

@end

@implementation KalturaSearchOperator
@synthesize type = _type;
@synthesize items = _items;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_type = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfType
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfItems
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfItems
{
    return @"KalturaSearchItem";
}

- (void)setTypeFromString:(NSString*)aPropVal
{
    self.type = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaSearchOperator"];
    [aParams addIfDefinedKey:@"type" withInt:self.type];
    [aParams addIfDefinedKey:@"items" withArray:self.items];
}

- (void)dealloc
{
    [self->_items release];
    [super dealloc];
}

@end

@implementation KalturaSessionRestriction
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaSessionRestriction"];
}

@end

@implementation KalturaSiteRestriction
@synthesize siteRestrictionType = _siteRestrictionType;
@synthesize siteList = _siteList;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_siteRestrictionType = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfSiteRestrictionType
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfSiteList
{
    return KFT_String;
}

- (void)setSiteRestrictionTypeFromString:(NSString*)aPropVal
{
    self.siteRestrictionType = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaSiteRestriction"];
    [aParams addIfDefinedKey:@"siteRestrictionType" withInt:self.siteRestrictionType];
    [aParams addIfDefinedKey:@"siteList" withString:self.siteList];
}

- (void)dealloc
{
    [self->_siteList release];
    [super dealloc];
}

@end

@implementation KalturaStorageJobData
@synthesize serverUrl = _serverUrl;
@synthesize serverUsername = _serverUsername;
@synthesize serverPassword = _serverPassword;
@synthesize ftpPassiveMode = _ftpPassiveMode;
@synthesize srcFileSyncLocalPath = _srcFileSyncLocalPath;
@synthesize srcFileSyncId = _srcFileSyncId;
@synthesize destFileSyncStoredPath = _destFileSyncStoredPath;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_ftpPassiveMode = KALTURA_UNDEF_BOOL;
    return self;
}

- (KalturaFieldType)getTypeOfServerUrl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfServerUsername
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfServerPassword
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFtpPassiveMode
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfSrcFileSyncLocalPath
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSrcFileSyncId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDestFileSyncStoredPath
{
    return KFT_String;
}

- (void)setFtpPassiveModeFromString:(NSString*)aPropVal
{
    self.ftpPassiveMode = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaStorageJobData"];
    [aParams addIfDefinedKey:@"serverUrl" withString:self.serverUrl];
    [aParams addIfDefinedKey:@"serverUsername" withString:self.serverUsername];
    [aParams addIfDefinedKey:@"serverPassword" withString:self.serverPassword];
    [aParams addIfDefinedKey:@"ftpPassiveMode" withBool:self.ftpPassiveMode];
    [aParams addIfDefinedKey:@"srcFileSyncLocalPath" withString:self.srcFileSyncLocalPath];
    [aParams addIfDefinedKey:@"srcFileSyncId" withString:self.srcFileSyncId];
    [aParams addIfDefinedKey:@"destFileSyncStoredPath" withString:self.destFileSyncStoredPath];
}

- (void)dealloc
{
    [self->_serverUrl release];
    [self->_serverUsername release];
    [self->_serverPassword release];
    [self->_srcFileSyncLocalPath release];
    [self->_srcFileSyncId release];
    [self->_destFileSyncStoredPath release];
    [super dealloc];
}

@end

@implementation KalturaStorageProfileBaseFilter
@synthesize idEqual = _idEqual;
@synthesize idIn = _idIn;
@synthesize createdAtGreaterThanOrEqual = _createdAtGreaterThanOrEqual;
@synthesize createdAtLessThanOrEqual = _createdAtLessThanOrEqual;
@synthesize updatedAtGreaterThanOrEqual = _updatedAtGreaterThanOrEqual;
@synthesize updatedAtLessThanOrEqual = _updatedAtLessThanOrEqual;
@synthesize partnerIdEqual = _partnerIdEqual;
@synthesize partnerIdIn = _partnerIdIn;
@synthesize systemNameEqual = _systemNameEqual;
@synthesize systemNameIn = _systemNameIn;
@synthesize statusEqual = _statusEqual;
@synthesize statusIn = _statusIn;
@synthesize protocolEqual = _protocolEqual;
@synthesize protocolIn = _protocolIn;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_idEqual = KALTURA_UNDEF_INT;
    self->_createdAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_createdAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_updatedAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_updatedAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_partnerIdEqual = KALTURA_UNDEF_INT;
    self->_statusEqual = KALTURA_UNDEF_INT;
    self->_protocolEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSystemNameEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSystemNameIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatusEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfStatusIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfProtocolEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfProtocolIn
{
    return KFT_String;
}

- (void)setIdEqualFromString:(NSString*)aPropVal
{
    self.idEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.updatedAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.updatedAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerIdEqualFromString:(NSString*)aPropVal
{
    self.partnerIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusEqualFromString:(NSString*)aPropVal
{
    self.statusEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setProtocolEqualFromString:(NSString*)aPropVal
{
    self.protocolEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaStorageProfileBaseFilter"];
    [aParams addIfDefinedKey:@"idEqual" withInt:self.idEqual];
    [aParams addIfDefinedKey:@"idIn" withString:self.idIn];
    [aParams addIfDefinedKey:@"createdAtGreaterThanOrEqual" withInt:self.createdAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"createdAtLessThanOrEqual" withInt:self.createdAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"updatedAtGreaterThanOrEqual" withInt:self.updatedAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"updatedAtLessThanOrEqual" withInt:self.updatedAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"partnerIdEqual" withInt:self.partnerIdEqual];
    [aParams addIfDefinedKey:@"partnerIdIn" withString:self.partnerIdIn];
    [aParams addIfDefinedKey:@"systemNameEqual" withString:self.systemNameEqual];
    [aParams addIfDefinedKey:@"systemNameIn" withString:self.systemNameIn];
    [aParams addIfDefinedKey:@"statusEqual" withInt:self.statusEqual];
    [aParams addIfDefinedKey:@"statusIn" withString:self.statusIn];
    [aParams addIfDefinedKey:@"protocolEqual" withInt:self.protocolEqual];
    [aParams addIfDefinedKey:@"protocolIn" withString:self.protocolIn];
}

- (void)dealloc
{
    [self->_idIn release];
    [self->_partnerIdIn release];
    [self->_systemNameEqual release];
    [self->_systemNameIn release];
    [self->_statusIn release];
    [self->_protocolIn release];
    [super dealloc];
}

@end

@interface KalturaTubeMogulSyndicationFeed()
@property (nonatomic,copy) NSString* category;
@end

@implementation KalturaTubeMogulSyndicationFeed
@synthesize category = _category;

- (KalturaFieldType)getTypeOfCategory
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaTubeMogulSyndicationFeed"];
}

- (void)dealloc
{
    [self->_category release];
    [super dealloc];
}

@end

@implementation KalturaUiConfBaseFilter
@synthesize idEqual = _idEqual;
@synthesize idIn = _idIn;
@synthesize nameLike = _nameLike;
@synthesize partnerIdEqual = _partnerIdEqual;
@synthesize partnerIdIn = _partnerIdIn;
@synthesize objTypeEqual = _objTypeEqual;
@synthesize objTypeIn = _objTypeIn;
@synthesize tagsMultiLikeOr = _tagsMultiLikeOr;
@synthesize tagsMultiLikeAnd = _tagsMultiLikeAnd;
@synthesize createdAtGreaterThanOrEqual = _createdAtGreaterThanOrEqual;
@synthesize createdAtLessThanOrEqual = _createdAtLessThanOrEqual;
@synthesize updatedAtGreaterThanOrEqual = _updatedAtGreaterThanOrEqual;
@synthesize updatedAtLessThanOrEqual = _updatedAtLessThanOrEqual;
@synthesize creationModeEqual = _creationModeEqual;
@synthesize creationModeIn = _creationModeIn;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_idEqual = KALTURA_UNDEF_INT;
    self->_partnerIdEqual = KALTURA_UNDEF_INT;
    self->_objTypeEqual = KALTURA_UNDEF_INT;
    self->_createdAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_createdAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_updatedAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_updatedAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_creationModeEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfNameLike
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPartnerIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfObjTypeEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfObjTypeIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTagsMultiLikeOr
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTagsMultiLikeAnd
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreationModeEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreationModeIn
{
    return KFT_String;
}

- (void)setIdEqualFromString:(NSString*)aPropVal
{
    self.idEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerIdEqualFromString:(NSString*)aPropVal
{
    self.partnerIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setObjTypeEqualFromString:(NSString*)aPropVal
{
    self.objTypeEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.updatedAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.updatedAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreationModeEqualFromString:(NSString*)aPropVal
{
    self.creationModeEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaUiConfBaseFilter"];
    [aParams addIfDefinedKey:@"idEqual" withInt:self.idEqual];
    [aParams addIfDefinedKey:@"idIn" withString:self.idIn];
    [aParams addIfDefinedKey:@"nameLike" withString:self.nameLike];
    [aParams addIfDefinedKey:@"partnerIdEqual" withInt:self.partnerIdEqual];
    [aParams addIfDefinedKey:@"partnerIdIn" withString:self.partnerIdIn];
    [aParams addIfDefinedKey:@"objTypeEqual" withInt:self.objTypeEqual];
    [aParams addIfDefinedKey:@"objTypeIn" withString:self.objTypeIn];
    [aParams addIfDefinedKey:@"tagsMultiLikeOr" withString:self.tagsMultiLikeOr];
    [aParams addIfDefinedKey:@"tagsMultiLikeAnd" withString:self.tagsMultiLikeAnd];
    [aParams addIfDefinedKey:@"createdAtGreaterThanOrEqual" withInt:self.createdAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"createdAtLessThanOrEqual" withInt:self.createdAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"updatedAtGreaterThanOrEqual" withInt:self.updatedAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"updatedAtLessThanOrEqual" withInt:self.updatedAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"creationModeEqual" withInt:self.creationModeEqual];
    [aParams addIfDefinedKey:@"creationModeIn" withString:self.creationModeIn];
}

- (void)dealloc
{
    [self->_idIn release];
    [self->_nameLike release];
    [self->_partnerIdIn release];
    [self->_objTypeIn release];
    [self->_tagsMultiLikeOr release];
    [self->_tagsMultiLikeAnd release];
    [self->_creationModeIn release];
    [super dealloc];
}

@end

@implementation KalturaUploadTokenBaseFilter
@synthesize idEqual = _idEqual;
@synthesize idIn = _idIn;
@synthesize userIdEqual = _userIdEqual;
@synthesize statusEqual = _statusEqual;
@synthesize statusIn = _statusIn;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_statusEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfIdEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfUserIdEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatusEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfStatusIn
{
    return KFT_String;
}

- (void)setStatusEqualFromString:(NSString*)aPropVal
{
    self.statusEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaUploadTokenBaseFilter"];
    [aParams addIfDefinedKey:@"idEqual" withString:self.idEqual];
    [aParams addIfDefinedKey:@"idIn" withString:self.idIn];
    [aParams addIfDefinedKey:@"userIdEqual" withString:self.userIdEqual];
    [aParams addIfDefinedKey:@"statusEqual" withInt:self.statusEqual];
    [aParams addIfDefinedKey:@"statusIn" withString:self.statusIn];
}

- (void)dealloc
{
    [self->_idEqual release];
    [self->_idIn release];
    [self->_userIdEqual release];
    [self->_statusIn release];
    [super dealloc];
}

@end

@implementation KalturaUserAgentRestriction
@synthesize userAgentRestrictionType = _userAgentRestrictionType;
@synthesize userAgentRegexList = _userAgentRegexList;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_userAgentRestrictionType = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfUserAgentRestrictionType
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUserAgentRegexList
{
    return KFT_String;
}

- (void)setUserAgentRestrictionTypeFromString:(NSString*)aPropVal
{
    self.userAgentRestrictionType = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaUserAgentRestriction"];
    [aParams addIfDefinedKey:@"userAgentRestrictionType" withInt:self.userAgentRestrictionType];
    [aParams addIfDefinedKey:@"userAgentRegexList" withString:self.userAgentRegexList];
}

- (void)dealloc
{
    [self->_userAgentRegexList release];
    [super dealloc];
}

@end

@implementation KalturaUserLoginDataBaseFilter
@synthesize loginEmailEqual = _loginEmailEqual;

- (KalturaFieldType)getTypeOfLoginEmailEqual
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaUserLoginDataBaseFilter"];
    [aParams addIfDefinedKey:@"loginEmailEqual" withString:self.loginEmailEqual];
}

- (void)dealloc
{
    [self->_loginEmailEqual release];
    [super dealloc];
}

@end

@implementation KalturaUserRoleBaseFilter
@synthesize idEqual = _idEqual;
@synthesize idIn = _idIn;
@synthesize nameEqual = _nameEqual;
@synthesize nameIn = _nameIn;
@synthesize systemNameEqual = _systemNameEqual;
@synthesize systemNameIn = _systemNameIn;
@synthesize descriptionLike = _descriptionLike;
@synthesize statusEqual = _statusEqual;
@synthesize statusIn = _statusIn;
@synthesize partnerIdEqual = _partnerIdEqual;
@synthesize partnerIdIn = _partnerIdIn;
@synthesize tagsMultiLikeOr = _tagsMultiLikeOr;
@synthesize tagsMultiLikeAnd = _tagsMultiLikeAnd;
@synthesize createdAtGreaterThanOrEqual = _createdAtGreaterThanOrEqual;
@synthesize createdAtLessThanOrEqual = _createdAtLessThanOrEqual;
@synthesize updatedAtGreaterThanOrEqual = _updatedAtGreaterThanOrEqual;
@synthesize updatedAtLessThanOrEqual = _updatedAtLessThanOrEqual;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_idEqual = KALTURA_UNDEF_INT;
    self->_statusEqual = KALTURA_UNDEF_INT;
    self->_partnerIdEqual = KALTURA_UNDEF_INT;
    self->_createdAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_createdAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_updatedAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_updatedAtLessThanOrEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfNameEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfNameIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSystemNameEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSystemNameIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDescriptionLike
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatusEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfStatusIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPartnerIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTagsMultiLikeOr
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTagsMultiLikeAnd
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (void)setIdEqualFromString:(NSString*)aPropVal
{
    self.idEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusEqualFromString:(NSString*)aPropVal
{
    self.statusEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerIdEqualFromString:(NSString*)aPropVal
{
    self.partnerIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.updatedAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.updatedAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaUserRoleBaseFilter"];
    [aParams addIfDefinedKey:@"idEqual" withInt:self.idEqual];
    [aParams addIfDefinedKey:@"idIn" withString:self.idIn];
    [aParams addIfDefinedKey:@"nameEqual" withString:self.nameEqual];
    [aParams addIfDefinedKey:@"nameIn" withString:self.nameIn];
    [aParams addIfDefinedKey:@"systemNameEqual" withString:self.systemNameEqual];
    [aParams addIfDefinedKey:@"systemNameIn" withString:self.systemNameIn];
    [aParams addIfDefinedKey:@"descriptionLike" withString:self.descriptionLike];
    [aParams addIfDefinedKey:@"statusEqual" withInt:self.statusEqual];
    [aParams addIfDefinedKey:@"statusIn" withString:self.statusIn];
    [aParams addIfDefinedKey:@"partnerIdEqual" withInt:self.partnerIdEqual];
    [aParams addIfDefinedKey:@"partnerIdIn" withString:self.partnerIdIn];
    [aParams addIfDefinedKey:@"tagsMultiLikeOr" withString:self.tagsMultiLikeOr];
    [aParams addIfDefinedKey:@"tagsMultiLikeAnd" withString:self.tagsMultiLikeAnd];
    [aParams addIfDefinedKey:@"createdAtGreaterThanOrEqual" withInt:self.createdAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"createdAtLessThanOrEqual" withInt:self.createdAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"updatedAtGreaterThanOrEqual" withInt:self.updatedAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"updatedAtLessThanOrEqual" withInt:self.updatedAtLessThanOrEqual];
}

- (void)dealloc
{
    [self->_idIn release];
    [self->_nameEqual release];
    [self->_nameIn release];
    [self->_systemNameEqual release];
    [self->_systemNameIn release];
    [self->_descriptionLike release];
    [self->_statusIn release];
    [self->_partnerIdIn release];
    [self->_tagsMultiLikeOr release];
    [self->_tagsMultiLikeAnd release];
    [super dealloc];
}

@end

@implementation KalturaWidgetBaseFilter
@synthesize idEqual = _idEqual;
@synthesize idIn = _idIn;
@synthesize sourceWidgetIdEqual = _sourceWidgetIdEqual;
@synthesize rootWidgetIdEqual = _rootWidgetIdEqual;
@synthesize partnerIdEqual = _partnerIdEqual;
@synthesize entryIdEqual = _entryIdEqual;
@synthesize uiConfIdEqual = _uiConfIdEqual;
@synthesize createdAtGreaterThanOrEqual = _createdAtGreaterThanOrEqual;
@synthesize createdAtLessThanOrEqual = _createdAtLessThanOrEqual;
@synthesize updatedAtGreaterThanOrEqual = _updatedAtGreaterThanOrEqual;
@synthesize updatedAtLessThanOrEqual = _updatedAtLessThanOrEqual;
@synthesize partnerDataLike = _partnerDataLike;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_partnerIdEqual = KALTURA_UNDEF_INT;
    self->_uiConfIdEqual = KALTURA_UNDEF_INT;
    self->_createdAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_createdAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_updatedAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_updatedAtLessThanOrEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfIdEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSourceWidgetIdEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfRootWidgetIdEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPartnerIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfEntryIdEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfUiConfIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerDataLike
{
    return KFT_String;
}

- (void)setPartnerIdEqualFromString:(NSString*)aPropVal
{
    self.partnerIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUiConfIdEqualFromString:(NSString*)aPropVal
{
    self.uiConfIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.updatedAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.updatedAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaWidgetBaseFilter"];
    [aParams addIfDefinedKey:@"idEqual" withString:self.idEqual];
    [aParams addIfDefinedKey:@"idIn" withString:self.idIn];
    [aParams addIfDefinedKey:@"sourceWidgetIdEqual" withString:self.sourceWidgetIdEqual];
    [aParams addIfDefinedKey:@"rootWidgetIdEqual" withString:self.rootWidgetIdEqual];
    [aParams addIfDefinedKey:@"partnerIdEqual" withInt:self.partnerIdEqual];
    [aParams addIfDefinedKey:@"entryIdEqual" withString:self.entryIdEqual];
    [aParams addIfDefinedKey:@"uiConfIdEqual" withInt:self.uiConfIdEqual];
    [aParams addIfDefinedKey:@"createdAtGreaterThanOrEqual" withInt:self.createdAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"createdAtLessThanOrEqual" withInt:self.createdAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"updatedAtGreaterThanOrEqual" withInt:self.updatedAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"updatedAtLessThanOrEqual" withInt:self.updatedAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"partnerDataLike" withString:self.partnerDataLike];
}

- (void)dealloc
{
    [self->_idEqual release];
    [self->_idIn release];
    [self->_sourceWidgetIdEqual release];
    [self->_rootWidgetIdEqual release];
    [self->_entryIdEqual release];
    [self->_partnerDataLike release];
    [super dealloc];
}

@end

@interface KalturaYahooSyndicationFeed()
@property (nonatomic,copy) NSString* category;
@end

@implementation KalturaYahooSyndicationFeed
@synthesize category = _category;
@synthesize adultContent = _adultContent;
@synthesize feedDescription = _feedDescription;
@synthesize feedLandingPage = _feedLandingPage;

- (KalturaFieldType)getTypeOfCategory
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAdultContent
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFeedDescription
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFeedLandingPage
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaYahooSyndicationFeed"];
    [aParams addIfDefinedKey:@"adultContent" withString:self.adultContent];
    [aParams addIfDefinedKey:@"feedDescription" withString:self.feedDescription];
    [aParams addIfDefinedKey:@"feedLandingPage" withString:self.feedLandingPage];
}

- (void)dealloc
{
    [self->_category release];
    [self->_adultContent release];
    [self->_feedDescription release];
    [self->_feedLandingPage release];
    [super dealloc];
}

@end

@implementation KalturaAccessControlFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAccessControlFilter"];
}

@end

@implementation KalturaAccessControlProfileFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAccessControlProfileFilter"];
}

@end

@implementation KalturaAkamaiProvisionJobData
@synthesize wsdlUsername = _wsdlUsername;
@synthesize wsdlPassword = _wsdlPassword;
@synthesize cpcode = _cpcode;
@synthesize emailId = _emailId;
@synthesize primaryContact = _primaryContact;
@synthesize secondaryContact = _secondaryContact;

- (KalturaFieldType)getTypeOfWsdlUsername
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfWsdlPassword
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCpcode
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfEmailId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPrimaryContact
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSecondaryContact
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAkamaiProvisionJobData"];
    [aParams addIfDefinedKey:@"wsdlUsername" withString:self.wsdlUsername];
    [aParams addIfDefinedKey:@"wsdlPassword" withString:self.wsdlPassword];
    [aParams addIfDefinedKey:@"cpcode" withString:self.cpcode];
    [aParams addIfDefinedKey:@"emailId" withString:self.emailId];
    [aParams addIfDefinedKey:@"primaryContact" withString:self.primaryContact];
    [aParams addIfDefinedKey:@"secondaryContact" withString:self.secondaryContact];
}

- (void)dealloc
{
    [self->_wsdlUsername release];
    [self->_wsdlPassword release];
    [self->_cpcode release];
    [self->_emailId release];
    [self->_primaryContact release];
    [self->_secondaryContact release];
    [super dealloc];
}

@end

@implementation KalturaAkamaiUniversalProvisionJobData
@synthesize streamId = _streamId;
@synthesize systemUserName = _systemUserName;
@synthesize systemPassword = _systemPassword;
@synthesize domainName = _domainName;
@synthesize dvrEnabled = _dvrEnabled;
@synthesize dvrWindow = _dvrWindow;
@synthesize primaryContact = _primaryContact;
@synthesize secondaryContact = _secondaryContact;
@synthesize streamType = _streamType;
@synthesize notificationEmail = _notificationEmail;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_streamId = KALTURA_UNDEF_INT;
    self->_dvrEnabled = KALTURA_UNDEF_INT;
    self->_dvrWindow = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfStreamId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfSystemUserName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSystemPassword
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDomainName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDvrEnabled
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDvrWindow
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPrimaryContact
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSecondaryContact
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStreamType
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfNotificationEmail
{
    return KFT_String;
}

- (void)setStreamIdFromString:(NSString*)aPropVal
{
    self.streamId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDvrEnabledFromString:(NSString*)aPropVal
{
    self.dvrEnabled = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDvrWindowFromString:(NSString*)aPropVal
{
    self.dvrWindow = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAkamaiUniversalProvisionJobData"];
    [aParams addIfDefinedKey:@"streamId" withInt:self.streamId];
    [aParams addIfDefinedKey:@"systemUserName" withString:self.systemUserName];
    [aParams addIfDefinedKey:@"systemPassword" withString:self.systemPassword];
    [aParams addIfDefinedKey:@"domainName" withString:self.domainName];
    [aParams addIfDefinedKey:@"dvrEnabled" withInt:self.dvrEnabled];
    [aParams addIfDefinedKey:@"dvrWindow" withInt:self.dvrWindow];
    [aParams addIfDefinedKey:@"primaryContact" withString:self.primaryContact];
    [aParams addIfDefinedKey:@"secondaryContact" withString:self.secondaryContact];
    [aParams addIfDefinedKey:@"streamType" withString:self.streamType];
    [aParams addIfDefinedKey:@"notificationEmail" withString:self.notificationEmail];
}

- (void)dealloc
{
    [self->_systemUserName release];
    [self->_systemPassword release];
    [self->_domainName release];
    [self->_primaryContact release];
    [self->_secondaryContact release];
    [self->_streamType release];
    [self->_notificationEmail release];
    [super dealloc];
}

@end

@implementation KalturaAssetFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAssetFilter"];
}

@end

@implementation KalturaAssetParamsFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAssetParamsFilter"];
}

@end

@implementation KalturaAssetResource
@synthesize assetId = _assetId;

- (KalturaFieldType)getTypeOfAssetId
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAssetResource"];
    [aParams addIfDefinedKey:@"assetId" withString:self.assetId];
}

- (void)dealloc
{
    [self->_assetId release];
    [super dealloc];
}

@end

@implementation KalturaBaseSyndicationFeedFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaBaseSyndicationFeedFilter"];
}

@end

@implementation KalturaBatchJobFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaBatchJobFilter"];
}

@end

@implementation KalturaBulkUploadFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaBulkUploadFilter"];
}

@end

@implementation KalturaCategoryEntryAdvancedFilter
@synthesize categoriesMatchOr = _categoriesMatchOr;
@synthesize categoryEntryStatusIn = _categoryEntryStatusIn;

- (KalturaFieldType)getTypeOfCategoriesMatchOr
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCategoryEntryStatusIn
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaCategoryEntryAdvancedFilter"];
    [aParams addIfDefinedKey:@"categoriesMatchOr" withString:self.categoriesMatchOr];
    [aParams addIfDefinedKey:@"categoryEntryStatusIn" withString:self.categoryEntryStatusIn];
}

- (void)dealloc
{
    [self->_categoriesMatchOr release];
    [self->_categoryEntryStatusIn release];
    [super dealloc];
}

@end

@implementation KalturaCategoryEntryFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaCategoryEntryFilter"];
}

@end

@implementation KalturaCategoryFilter
@synthesize freeText = _freeText;
@synthesize membersIn = _membersIn;
@synthesize nameOrReferenceIdStartsWith = _nameOrReferenceIdStartsWith;
@synthesize managerEqual = _managerEqual;
@synthesize memberEqual = _memberEqual;
@synthesize fullNameStartsWithIn = _fullNameStartsWithIn;
@synthesize ancestorIdIn = _ancestorIdIn;

- (KalturaFieldType)getTypeOfFreeText
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfMembersIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfNameOrReferenceIdStartsWith
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfManagerEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfMemberEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFullNameStartsWithIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAncestorIdIn
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaCategoryFilter"];
    [aParams addIfDefinedKey:@"freeText" withString:self.freeText];
    [aParams addIfDefinedKey:@"membersIn" withString:self.membersIn];
    [aParams addIfDefinedKey:@"nameOrReferenceIdStartsWith" withString:self.nameOrReferenceIdStartsWith];
    [aParams addIfDefinedKey:@"managerEqual" withString:self.managerEqual];
    [aParams addIfDefinedKey:@"memberEqual" withString:self.memberEqual];
    [aParams addIfDefinedKey:@"fullNameStartsWithIn" withString:self.fullNameStartsWithIn];
    [aParams addIfDefinedKey:@"ancestorIdIn" withString:self.ancestorIdIn];
}

- (void)dealloc
{
    [self->_freeText release];
    [self->_membersIn release];
    [self->_nameOrReferenceIdStartsWith release];
    [self->_managerEqual release];
    [self->_memberEqual release];
    [self->_fullNameStartsWithIn release];
    [self->_ancestorIdIn release];
    [super dealloc];
}

@end

@implementation KalturaControlPanelCommandFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaControlPanelCommandFilter"];
}

@end

@implementation KalturaConversionProfileFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaConversionProfileFilter"];
}

@end

@implementation KalturaConversionProfileAssetParamsFilter
@synthesize conversionProfileIdFilter = _conversionProfileIdFilter;
@synthesize assetParamsIdFilter = _assetParamsIdFilter;

- (KalturaFieldType)getTypeOfConversionProfileIdFilter
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfConversionProfileIdFilter
{
    return @"KalturaConversionProfileFilter";
}

- (KalturaFieldType)getTypeOfAssetParamsIdFilter
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfAssetParamsIdFilter
{
    return @"KalturaAssetParamsFilter";
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaConversionProfileAssetParamsFilter"];
    [aParams addIfDefinedKey:@"conversionProfileIdFilter" withObject:self.conversionProfileIdFilter];
    [aParams addIfDefinedKey:@"assetParamsIdFilter" withObject:self.assetParamsIdFilter];
}

- (void)dealloc
{
    [self->_conversionProfileIdFilter release];
    [self->_assetParamsIdFilter release];
    [super dealloc];
}

@end

@implementation KalturaConvertCollectionJobData
@synthesize destDirLocalPath = _destDirLocalPath;
@synthesize destDirRemoteUrl = _destDirRemoteUrl;
@synthesize destFileName = _destFileName;
@synthesize inputXmlLocalPath = _inputXmlLocalPath;
@synthesize inputXmlRemoteUrl = _inputXmlRemoteUrl;
@synthesize commandLinesStr = _commandLinesStr;
@synthesize flavors = _flavors;

- (KalturaFieldType)getTypeOfDestDirLocalPath
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDestDirRemoteUrl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDestFileName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfInputXmlLocalPath
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfInputXmlRemoteUrl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCommandLinesStr
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFlavors
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfFlavors
{
    return @"KalturaConvertCollectionFlavorData";
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaConvertCollectionJobData"];
    [aParams addIfDefinedKey:@"destDirLocalPath" withString:self.destDirLocalPath];
    [aParams addIfDefinedKey:@"destDirRemoteUrl" withString:self.destDirRemoteUrl];
    [aParams addIfDefinedKey:@"destFileName" withString:self.destFileName];
    [aParams addIfDefinedKey:@"inputXmlLocalPath" withString:self.inputXmlLocalPath];
    [aParams addIfDefinedKey:@"inputXmlRemoteUrl" withString:self.inputXmlRemoteUrl];
    [aParams addIfDefinedKey:@"commandLinesStr" withString:self.commandLinesStr];
    [aParams addIfDefinedKey:@"flavors" withArray:self.flavors];
}

- (void)dealloc
{
    [self->_destDirLocalPath release];
    [self->_destDirRemoteUrl release];
    [self->_destFileName release];
    [self->_inputXmlLocalPath release];
    [self->_inputXmlRemoteUrl release];
    [self->_commandLinesStr release];
    [self->_flavors release];
    [super dealloc];
}

@end

@implementation KalturaConvertJobData
@synthesize destFileSyncLocalPath = _destFileSyncLocalPath;
@synthesize destFileSyncRemoteUrl = _destFileSyncRemoteUrl;
@synthesize logFileSyncLocalPath = _logFileSyncLocalPath;
@synthesize logFileSyncRemoteUrl = _logFileSyncRemoteUrl;
@synthesize flavorAssetId = _flavorAssetId;
@synthesize remoteMediaId = _remoteMediaId;
@synthesize customData = _customData;

- (KalturaFieldType)getTypeOfDestFileSyncLocalPath
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDestFileSyncRemoteUrl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfLogFileSyncLocalPath
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfLogFileSyncRemoteUrl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFlavorAssetId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfRemoteMediaId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCustomData
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaConvertJobData"];
    [aParams addIfDefinedKey:@"destFileSyncLocalPath" withString:self.destFileSyncLocalPath];
    [aParams addIfDefinedKey:@"destFileSyncRemoteUrl" withString:self.destFileSyncRemoteUrl];
    [aParams addIfDefinedKey:@"logFileSyncLocalPath" withString:self.logFileSyncLocalPath];
    [aParams addIfDefinedKey:@"logFileSyncRemoteUrl" withString:self.logFileSyncRemoteUrl];
    [aParams addIfDefinedKey:@"flavorAssetId" withString:self.flavorAssetId];
    [aParams addIfDefinedKey:@"remoteMediaId" withString:self.remoteMediaId];
    [aParams addIfDefinedKey:@"customData" withString:self.customData];
}

- (void)dealloc
{
    [self->_destFileSyncLocalPath release];
    [self->_destFileSyncRemoteUrl release];
    [self->_logFileSyncLocalPath release];
    [self->_logFileSyncRemoteUrl release];
    [self->_flavorAssetId release];
    [self->_remoteMediaId release];
    [self->_customData release];
    [super dealloc];
}

@end

@implementation KalturaCountryCondition
@synthesize geoCoderType = _geoCoderType;

- (KalturaFieldType)getTypeOfGeoCoderType
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaCountryCondition"];
    [aParams addIfDefinedKey:@"geoCoderType" withString:self.geoCoderType];
}

- (void)dealloc
{
    [self->_geoCoderType release];
    [super dealloc];
}

@end

@implementation KalturaDataCenterContentResource
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDataCenterContentResource"];
}

@end

@implementation KalturaEndUserReportInputFilter
@synthesize application = _application;
@synthesize userIds = _userIds;
@synthesize playbackContext = _playbackContext;

- (KalturaFieldType)getTypeOfApplication
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfUserIds
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPlaybackContext
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaEndUserReportInputFilter"];
    [aParams addIfDefinedKey:@"application" withString:self.application];
    [aParams addIfDefinedKey:@"userIds" withString:self.userIds];
    [aParams addIfDefinedKey:@"playbackContext" withString:self.playbackContext];
}

- (void)dealloc
{
    [self->_application release];
    [self->_userIds release];
    [self->_playbackContext release];
    [super dealloc];
}

@end

@implementation KalturaEntryResource
@synthesize entryId = _entryId;
@synthesize flavorParamsId = _flavorParamsId;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_flavorParamsId = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfEntryId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFlavorParamsId
{
    return KFT_Int;
}

- (void)setFlavorParamsIdFromString:(NSString*)aPropVal
{
    self.flavorParamsId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaEntryResource"];
    [aParams addIfDefinedKey:@"entryId" withString:self.entryId];
    [aParams addIfDefinedKey:@"flavorParamsId" withInt:self.flavorParamsId];
}

- (void)dealloc
{
    [self->_entryId release];
    [super dealloc];
}

@end

@implementation KalturaExtractMediaJobData
@synthesize flavorAssetId = _flavorAssetId;

- (KalturaFieldType)getTypeOfFlavorAssetId
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaExtractMediaJobData"];
    [aParams addIfDefinedKey:@"flavorAssetId" withString:self.flavorAssetId];
}

- (void)dealloc
{
    [self->_flavorAssetId release];
    [super dealloc];
}

@end

@implementation KalturaIntegerField
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaIntegerField"];
}

@end

@implementation KalturaFieldCompareCondition
@synthesize field = _field;

- (KalturaFieldType)getTypeOfField
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfField
{
    return @"KalturaIntegerField";
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaFieldCompareCondition"];
    [aParams addIfDefinedKey:@"field" withObject:self.field];
}

- (void)dealloc
{
    [self->_field release];
    [super dealloc];
}

@end

@implementation KalturaStringField
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaStringField"];
}

@end

@implementation KalturaFieldMatchCondition
@synthesize field = _field;

- (KalturaFieldType)getTypeOfField
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfField
{
    return @"KalturaStringField";
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaFieldMatchCondition"];
    [aParams addIfDefinedKey:@"field" withObject:self.field];
}

- (void)dealloc
{
    [self->_field release];
    [super dealloc];
}

@end

@implementation KalturaFileSyncResource
@synthesize fileSyncObjectType = _fileSyncObjectType;
@synthesize objectSubType = _objectSubType;
@synthesize objectId = _objectId;
@synthesize version = _version;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_fileSyncObjectType = KALTURA_UNDEF_INT;
    self->_objectSubType = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfFileSyncObjectType
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfObjectSubType
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfObjectId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfVersion
{
    return KFT_String;
}

- (void)setFileSyncObjectTypeFromString:(NSString*)aPropVal
{
    self.fileSyncObjectType = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setObjectSubTypeFromString:(NSString*)aPropVal
{
    self.objectSubType = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaFileSyncResource"];
    [aParams addIfDefinedKey:@"fileSyncObjectType" withInt:self.fileSyncObjectType];
    [aParams addIfDefinedKey:@"objectSubType" withInt:self.objectSubType];
    [aParams addIfDefinedKey:@"objectId" withString:self.objectId];
    [aParams addIfDefinedKey:@"version" withString:self.version];
}

- (void)dealloc
{
    [self->_objectId release];
    [self->_version release];
    [super dealloc];
}

@end

@implementation KalturaGenericXsltSyndicationFeed
@synthesize xslt = _xslt;
@synthesize itemXpathsToExtend = _itemXpathsToExtend;

- (KalturaFieldType)getTypeOfXslt
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfItemXpathsToExtend
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfItemXpathsToExtend
{
    return @"KalturaString";
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaGenericXsltSyndicationFeed"];
    [aParams addIfDefinedKey:@"xslt" withString:self.xslt];
    [aParams addIfDefinedKey:@"itemXpathsToExtend" withArray:self.itemXpathsToExtend];
}

- (void)dealloc
{
    [self->_xslt release];
    [self->_itemXpathsToExtend release];
    [super dealloc];
}

@end

@implementation KalturaIndexAdvancedFilter
@synthesize indexIdGreaterThan = _indexIdGreaterThan;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_indexIdGreaterThan = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfIndexIdGreaterThan
{
    return KFT_Int;
}

- (void)setIndexIdGreaterThanFromString:(NSString*)aPropVal
{
    self.indexIdGreaterThan = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaIndexAdvancedFilter"];
    [aParams addIfDefinedKey:@"indexIdGreaterThan" withInt:self.indexIdGreaterThan];
}

@end

@implementation KalturaIpAddressCondition
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaIpAddressCondition"];
}

@end

@implementation KalturaMediaFlavorParams
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaMediaFlavorParams"];
}

@end

@implementation KalturaMediaInfoFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaMediaInfoFilter"];
}

@end

@implementation KalturaOperationResource
@synthesize resource = _resource;
@synthesize operationAttributes = _operationAttributes;
@synthesize assetParamsId = _assetParamsId;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_assetParamsId = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfResource
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfResource
{
    return @"KalturaContentResource";
}

- (KalturaFieldType)getTypeOfOperationAttributes
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfOperationAttributes
{
    return @"KalturaOperationAttributes";
}

- (KalturaFieldType)getTypeOfAssetParamsId
{
    return KFT_Int;
}

- (void)setAssetParamsIdFromString:(NSString*)aPropVal
{
    self.assetParamsId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaOperationResource"];
    [aParams addIfDefinedKey:@"resource" withObject:self.resource];
    [aParams addIfDefinedKey:@"operationAttributes" withArray:self.operationAttributes];
    [aParams addIfDefinedKey:@"assetParamsId" withInt:self.assetParamsId];
}

- (void)dealloc
{
    [self->_resource release];
    [self->_operationAttributes release];
    [super dealloc];
}

@end

@implementation KalturaPartnerFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaPartnerFilter"];
}

@end

@implementation KalturaPermissionFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaPermissionFilter"];
}

@end

@implementation KalturaPermissionItemFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaPermissionItemFilter"];
}

@end

@implementation KalturaPostConvertJobData
@synthesize flavorAssetId = _flavorAssetId;
@synthesize createThumb = _createThumb;
@synthesize thumbPath = _thumbPath;
@synthesize thumbOffset = _thumbOffset;
@synthesize thumbHeight = _thumbHeight;
@synthesize thumbBitrate = _thumbBitrate;
@synthesize customData = _customData;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_createThumb = KALTURA_UNDEF_BOOL;
    self->_thumbOffset = KALTURA_UNDEF_INT;
    self->_thumbHeight = KALTURA_UNDEF_INT;
    self->_thumbBitrate = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfFlavorAssetId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreateThumb
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfThumbPath
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfThumbOffset
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfThumbHeight
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfThumbBitrate
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCustomData
{
    return KFT_String;
}

- (void)setCreateThumbFromString:(NSString*)aPropVal
{
    self.createThumb = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)setThumbOffsetFromString:(NSString*)aPropVal
{
    self.thumbOffset = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setThumbHeightFromString:(NSString*)aPropVal
{
    self.thumbHeight = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setThumbBitrateFromString:(NSString*)aPropVal
{
    self.thumbBitrate = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaPostConvertJobData"];
    [aParams addIfDefinedKey:@"flavorAssetId" withString:self.flavorAssetId];
    [aParams addIfDefinedKey:@"createThumb" withBool:self.createThumb];
    [aParams addIfDefinedKey:@"thumbPath" withString:self.thumbPath];
    [aParams addIfDefinedKey:@"thumbOffset" withInt:self.thumbOffset];
    [aParams addIfDefinedKey:@"thumbHeight" withInt:self.thumbHeight];
    [aParams addIfDefinedKey:@"thumbBitrate" withInt:self.thumbBitrate];
    [aParams addIfDefinedKey:@"customData" withString:self.customData];
}

- (void)dealloc
{
    [self->_flavorAssetId release];
    [self->_thumbPath release];
    [self->_customData release];
    [super dealloc];
}

@end

@implementation KalturaPreviewRestriction
@synthesize previewLength = _previewLength;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_previewLength = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfPreviewLength
{
    return KFT_Int;
}

- (void)setPreviewLengthFromString:(NSString*)aPropVal
{
    self.previewLength = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaPreviewRestriction"];
    [aParams addIfDefinedKey:@"previewLength" withInt:self.previewLength];
}

@end

@implementation KalturaRegexCondition
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaRegexCondition"];
}

@end

@implementation KalturaRemoteStorageResources
@synthesize resources = _resources;

- (KalturaFieldType)getTypeOfResources
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfResources
{
    return @"KalturaRemoteStorageResource";
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaRemoteStorageResources"];
    [aParams addIfDefinedKey:@"resources" withArray:self.resources];
}

- (void)dealloc
{
    [self->_resources release];
    [super dealloc];
}

@end

@implementation KalturaReportFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaReportFilter"];
}

@end

@implementation KalturaSearchComparableCondition
@synthesize comparison = _comparison;

- (KalturaFieldType)getTypeOfComparison
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaSearchComparableCondition"];
    [aParams addIfDefinedKey:@"comparison" withString:self.comparison];
}

- (void)dealloc
{
    [self->_comparison release];
    [super dealloc];
}

@end

@implementation KalturaSiteCondition
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaSiteCondition"];
}

@end

@implementation KalturaSshImportJobData
@synthesize privateKey = _privateKey;
@synthesize publicKey = _publicKey;
@synthesize passPhrase = _passPhrase;

- (KalturaFieldType)getTypeOfPrivateKey
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPublicKey
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPassPhrase
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaSshImportJobData"];
    [aParams addIfDefinedKey:@"privateKey" withString:self.privateKey];
    [aParams addIfDefinedKey:@"publicKey" withString:self.publicKey];
    [aParams addIfDefinedKey:@"passPhrase" withString:self.passPhrase];
}

- (void)dealloc
{
    [self->_privateKey release];
    [self->_publicKey release];
    [self->_passPhrase release];
    [super dealloc];
}

@end

@implementation KalturaStorageDeleteJobData
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaStorageDeleteJobData"];
}

@end

@implementation KalturaStorageExportJobData
@synthesize force = _force;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_force = KALTURA_UNDEF_BOOL;
    return self;
}

- (KalturaFieldType)getTypeOfForce
{
    return KFT_Bool;
}

- (void)setForceFromString:(NSString*)aPropVal
{
    self.force = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaStorageExportJobData"];
    [aParams addIfDefinedKey:@"force" withBool:self.force];
}

@end

@implementation KalturaStorageProfileFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaStorageProfileFilter"];
}

@end

@implementation KalturaStringResource
@synthesize content = _content;

- (KalturaFieldType)getTypeOfContent
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaStringResource"];
    [aParams addIfDefinedKey:@"content" withString:self.content];
}

- (void)dealloc
{
    [self->_content release];
    [super dealloc];
}

@end

@implementation KalturaUiConfFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaUiConfFilter"];
}

@end

@implementation KalturaUploadTokenFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaUploadTokenFilter"];
}

@end

@implementation KalturaUserLoginDataFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaUserLoginDataFilter"];
}

@end

@implementation KalturaUserRoleFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaUserRoleFilter"];
}

@end

@implementation KalturaWidgetFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaWidgetFilter"];
}

@end

@implementation KalturaAdminUserBaseFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAdminUserBaseFilter"];
}

@end

@implementation KalturaAmazonS3StorageExportJobData
@synthesize filesPermissionInS3 = _filesPermissionInS3;

- (KalturaFieldType)getTypeOfFilesPermissionInS3
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAmazonS3StorageExportJobData"];
    [aParams addIfDefinedKey:@"filesPermissionInS3" withString:self.filesPermissionInS3];
}

- (void)dealloc
{
    [self->_filesPermissionInS3 release];
    [super dealloc];
}

@end

@implementation KalturaAmazonS3StorageProfileBaseFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAmazonS3StorageProfileBaseFilter"];
}

@end

@implementation KalturaApiActionPermissionItemBaseFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaApiActionPermissionItemBaseFilter"];
}

@end

@implementation KalturaApiParameterPermissionItemBaseFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaApiParameterPermissionItemBaseFilter"];
}

@end

@implementation KalturaAssetParamsOutputBaseFilter
@synthesize assetParamsIdEqual = _assetParamsIdEqual;
@synthesize assetParamsVersionEqual = _assetParamsVersionEqual;
@synthesize assetIdEqual = _assetIdEqual;
@synthesize assetVersionEqual = _assetVersionEqual;
@synthesize formatEqual = _formatEqual;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_assetParamsIdEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfAssetParamsIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfAssetParamsVersionEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAssetIdEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAssetVersionEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFormatEqual
{
    return KFT_String;
}

- (void)setAssetParamsIdEqualFromString:(NSString*)aPropVal
{
    self.assetParamsIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAssetParamsOutputBaseFilter"];
    [aParams addIfDefinedKey:@"assetParamsIdEqual" withInt:self.assetParamsIdEqual];
    [aParams addIfDefinedKey:@"assetParamsVersionEqual" withString:self.assetParamsVersionEqual];
    [aParams addIfDefinedKey:@"assetIdEqual" withString:self.assetIdEqual];
    [aParams addIfDefinedKey:@"assetVersionEqual" withString:self.assetVersionEqual];
    [aParams addIfDefinedKey:@"formatEqual" withString:self.formatEqual];
}

- (void)dealloc
{
    [self->_assetParamsVersionEqual release];
    [self->_assetIdEqual release];
    [self->_assetVersionEqual release];
    [self->_formatEqual release];
    [super dealloc];
}

@end

@implementation KalturaBatchJobFilterExt
@synthesize jobTypeAndSubTypeIn = _jobTypeAndSubTypeIn;

- (KalturaFieldType)getTypeOfJobTypeAndSubTypeIn
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaBatchJobFilterExt"];
    [aParams addIfDefinedKey:@"jobTypeAndSubTypeIn" withString:self.jobTypeAndSubTypeIn];
}

- (void)dealloc
{
    [self->_jobTypeAndSubTypeIn release];
    [super dealloc];
}

@end

@implementation KalturaCountryContextField
@synthesize geoCoderType = _geoCoderType;

- (KalturaFieldType)getTypeOfGeoCoderType
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaCountryContextField"];
    [aParams addIfDefinedKey:@"geoCoderType" withString:self.geoCoderType];
}

- (void)dealloc
{
    [self->_geoCoderType release];
    [super dealloc];
}

@end

@implementation KalturaDataEntryBaseFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDataEntryBaseFilter"];
}

@end

@implementation KalturaEvalBooleanField
@synthesize code = _code;

- (KalturaFieldType)getTypeOfCode
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaEvalBooleanField"];
    [aParams addIfDefinedKey:@"code" withString:self.code];
}

- (void)dealloc
{
    [self->_code release];
    [super dealloc];
}

@end

@implementation KalturaEvalStringField
@synthesize code = _code;

- (KalturaFieldType)getTypeOfCode
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaEvalStringField"];
    [aParams addIfDefinedKey:@"code" withString:self.code];
}

- (void)dealloc
{
    [self->_code release];
    [super dealloc];
}

@end

@implementation KalturaFlavorAssetBaseFilter
@synthesize flavorParamsIdEqual = _flavorParamsIdEqual;
@synthesize flavorParamsIdIn = _flavorParamsIdIn;
@synthesize statusEqual = _statusEqual;
@synthesize statusIn = _statusIn;
@synthesize statusNotIn = _statusNotIn;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_flavorParamsIdEqual = KALTURA_UNDEF_INT;
    self->_statusEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfFlavorParamsIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfFlavorParamsIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatusEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfStatusIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatusNotIn
{
    return KFT_String;
}

- (void)setFlavorParamsIdEqualFromString:(NSString*)aPropVal
{
    self.flavorParamsIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusEqualFromString:(NSString*)aPropVal
{
    self.statusEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaFlavorAssetBaseFilter"];
    [aParams addIfDefinedKey:@"flavorParamsIdEqual" withInt:self.flavorParamsIdEqual];
    [aParams addIfDefinedKey:@"flavorParamsIdIn" withString:self.flavorParamsIdIn];
    [aParams addIfDefinedKey:@"statusEqual" withInt:self.statusEqual];
    [aParams addIfDefinedKey:@"statusIn" withString:self.statusIn];
    [aParams addIfDefinedKey:@"statusNotIn" withString:self.statusNotIn];
}

- (void)dealloc
{
    [self->_flavorParamsIdIn release];
    [self->_statusIn release];
    [self->_statusNotIn release];
    [super dealloc];
}

@end

@implementation KalturaFlavorParamsBaseFilter
@synthesize formatEqual = _formatEqual;

- (KalturaFieldType)getTypeOfFormatEqual
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaFlavorParamsBaseFilter"];
    [aParams addIfDefinedKey:@"formatEqual" withString:self.formatEqual];
}

- (void)dealloc
{
    [self->_formatEqual release];
    [super dealloc];
}

@end

@implementation KalturaGenericSyndicationFeedBaseFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaGenericSyndicationFeedBaseFilter"];
}

@end

@implementation KalturaGoogleVideoSyndicationFeedBaseFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaGoogleVideoSyndicationFeedBaseFilter"];
}

@end

@implementation KalturaITunesSyndicationFeedBaseFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaITunesSyndicationFeedBaseFilter"];
}

@end

@implementation KalturaIpAddressContextField
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaIpAddressContextField"];
}

@end

@implementation KalturaMediaFlavorParamsOutput
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaMediaFlavorParamsOutput"];
}

@end

@implementation KalturaObjectIdField
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaObjectIdField"];
}

@end

@implementation KalturaPlaylistBaseFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaPlaylistBaseFilter"];
}

@end

@implementation KalturaServerFileResource
@synthesize localFilePath = _localFilePath;

- (KalturaFieldType)getTypeOfLocalFilePath
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaServerFileResource"];
    [aParams addIfDefinedKey:@"localFilePath" withString:self.localFilePath];
}

- (void)dealloc
{
    [self->_localFilePath release];
    [super dealloc];
}

@end

@implementation KalturaSshUrlResource
@synthesize privateKey = _privateKey;
@synthesize publicKey = _publicKey;
@synthesize keyPassphrase = _keyPassphrase;

- (KalturaFieldType)getTypeOfPrivateKey
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPublicKey
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfKeyPassphrase
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaSshUrlResource"];
    [aParams addIfDefinedKey:@"privateKey" withString:self.privateKey];
    [aParams addIfDefinedKey:@"publicKey" withString:self.publicKey];
    [aParams addIfDefinedKey:@"keyPassphrase" withString:self.keyPassphrase];
}

- (void)dealloc
{
    [self->_privateKey release];
    [self->_publicKey release];
    [self->_keyPassphrase release];
    [super dealloc];
}

@end

@implementation KalturaThumbAssetBaseFilter
@synthesize thumbParamsIdEqual = _thumbParamsIdEqual;
@synthesize thumbParamsIdIn = _thumbParamsIdIn;
@synthesize statusEqual = _statusEqual;
@synthesize statusIn = _statusIn;
@synthesize statusNotIn = _statusNotIn;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_thumbParamsIdEqual = KALTURA_UNDEF_INT;
    self->_statusEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfThumbParamsIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfThumbParamsIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatusEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfStatusIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatusNotIn
{
    return KFT_String;
}

- (void)setThumbParamsIdEqualFromString:(NSString*)aPropVal
{
    self.thumbParamsIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusEqualFromString:(NSString*)aPropVal
{
    self.statusEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaThumbAssetBaseFilter"];
    [aParams addIfDefinedKey:@"thumbParamsIdEqual" withInt:self.thumbParamsIdEqual];
    [aParams addIfDefinedKey:@"thumbParamsIdIn" withString:self.thumbParamsIdIn];
    [aParams addIfDefinedKey:@"statusEqual" withInt:self.statusEqual];
    [aParams addIfDefinedKey:@"statusIn" withString:self.statusIn];
    [aParams addIfDefinedKey:@"statusNotIn" withString:self.statusNotIn];
}

- (void)dealloc
{
    [self->_thumbParamsIdIn release];
    [self->_statusIn release];
    [self->_statusNotIn release];
    [super dealloc];
}

@end

@implementation KalturaThumbParamsBaseFilter
@synthesize formatEqual = _formatEqual;

- (KalturaFieldType)getTypeOfFormatEqual
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaThumbParamsBaseFilter"];
    [aParams addIfDefinedKey:@"formatEqual" withString:self.formatEqual];
}

- (void)dealloc
{
    [self->_formatEqual release];
    [super dealloc];
}

@end

@implementation KalturaTimeContextField
@synthesize offset = _offset;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_offset = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfOffset
{
    return KFT_Int;
}

- (void)setOffsetFromString:(NSString*)aPropVal
{
    self.offset = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaTimeContextField"];
    [aParams addIfDefinedKey:@"offset" withInt:self.offset];
}

@end

@implementation KalturaTubeMogulSyndicationFeedBaseFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaTubeMogulSyndicationFeedBaseFilter"];
}

@end

@implementation KalturaUploadedFileTokenResource
@synthesize token = _token;

- (KalturaFieldType)getTypeOfToken
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaUploadedFileTokenResource"];
    [aParams addIfDefinedKey:@"token" withString:self.token];
}

- (void)dealloc
{
    [self->_token release];
    [super dealloc];
}

@end

@implementation KalturaUserAgentCondition
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaUserAgentCondition"];
}

@end

@implementation KalturaUserAgentContextField
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaUserAgentContextField"];
}

@end

@implementation KalturaUserEmailContextField
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaUserEmailContextField"];
}

@end

@implementation KalturaWebcamTokenResource
@synthesize token = _token;

- (KalturaFieldType)getTypeOfToken
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaWebcamTokenResource"];
    [aParams addIfDefinedKey:@"token" withString:self.token];
}

- (void)dealloc
{
    [self->_token release];
    [super dealloc];
}

@end

@implementation KalturaYahooSyndicationFeedBaseFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaYahooSyndicationFeedBaseFilter"];
}

@end

@implementation KalturaAdminUserFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAdminUserFilter"];
}

@end

@implementation KalturaAmazonS3StorageProfileFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAmazonS3StorageProfileFilter"];
}

@end

@implementation KalturaApiActionPermissionItemFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaApiActionPermissionItemFilter"];
}

@end

@implementation KalturaApiParameterPermissionItemFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaApiParameterPermissionItemFilter"];
}

@end

@implementation KalturaAssetParamsOutputFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAssetParamsOutputFilter"];
}

@end

@implementation KalturaDataEntryFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDataEntryFilter"];
}

@end

@implementation KalturaFlavorAssetFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaFlavorAssetFilter"];
}

@end

@implementation KalturaFlavorParamsFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaFlavorParamsFilter"];
}

@end

@implementation KalturaGenericSyndicationFeedFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaGenericSyndicationFeedFilter"];
}

@end

@implementation KalturaGoogleVideoSyndicationFeedFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaGoogleVideoSyndicationFeedFilter"];
}

@end

@implementation KalturaITunesSyndicationFeedFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaITunesSyndicationFeedFilter"];
}

@end

@interface KalturaLiveStreamAdminEntry()
@property (nonatomic,copy) NSString* streamUsername;
@end

@implementation KalturaLiveStreamAdminEntry
@synthesize encodingIP1 = _encodingIP1;
@synthesize encodingIP2 = _encodingIP2;
@synthesize streamPassword = _streamPassword;
@synthesize streamUsername = _streamUsername;

- (KalturaFieldType)getTypeOfEncodingIP1
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfEncodingIP2
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStreamPassword
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStreamUsername
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaLiveStreamAdminEntry"];
    [aParams addIfDefinedKey:@"encodingIP1" withString:self.encodingIP1];
    [aParams addIfDefinedKey:@"encodingIP2" withString:self.encodingIP2];
    [aParams addIfDefinedKey:@"streamPassword" withString:self.streamPassword];
}

- (void)dealloc
{
    [self->_encodingIP1 release];
    [self->_encodingIP2 release];
    [self->_streamPassword release];
    [self->_streamUsername release];
    [super dealloc];
}

@end

@implementation KalturaPlaylistFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaPlaylistFilter"];
}

@end

@implementation KalturaThumbAssetFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaThumbAssetFilter"];
}

@end

@implementation KalturaThumbParamsFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaThumbParamsFilter"];
}

@end

@implementation KalturaTubeMogulSyndicationFeedFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaTubeMogulSyndicationFeedFilter"];
}

@end

@implementation KalturaYahooSyndicationFeedFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaYahooSyndicationFeedFilter"];
}

@end

@implementation KalturaFlavorParamsOutputBaseFilter
@synthesize flavorParamsIdEqual = _flavorParamsIdEqual;
@synthesize flavorParamsVersionEqual = _flavorParamsVersionEqual;
@synthesize flavorAssetIdEqual = _flavorAssetIdEqual;
@synthesize flavorAssetVersionEqual = _flavorAssetVersionEqual;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_flavorParamsIdEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfFlavorParamsIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfFlavorParamsVersionEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFlavorAssetIdEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFlavorAssetVersionEqual
{
    return KFT_String;
}

- (void)setFlavorParamsIdEqualFromString:(NSString*)aPropVal
{
    self.flavorParamsIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaFlavorParamsOutputBaseFilter"];
    [aParams addIfDefinedKey:@"flavorParamsIdEqual" withInt:self.flavorParamsIdEqual];
    [aParams addIfDefinedKey:@"flavorParamsVersionEqual" withString:self.flavorParamsVersionEqual];
    [aParams addIfDefinedKey:@"flavorAssetIdEqual" withString:self.flavorAssetIdEqual];
    [aParams addIfDefinedKey:@"flavorAssetVersionEqual" withString:self.flavorAssetVersionEqual];
}

- (void)dealloc
{
    [self->_flavorParamsVersionEqual release];
    [self->_flavorAssetIdEqual release];
    [self->_flavorAssetVersionEqual release];
    [super dealloc];
}

@end

@implementation KalturaGenericXsltSyndicationFeedBaseFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaGenericXsltSyndicationFeedBaseFilter"];
}

@end

@implementation KalturaMediaFlavorParamsBaseFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaMediaFlavorParamsBaseFilter"];
}

@end

@implementation KalturaMixEntryBaseFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaMixEntryBaseFilter"];
}

@end

@implementation KalturaThumbParamsOutputBaseFilter
@synthesize thumbParamsIdEqual = _thumbParamsIdEqual;
@synthesize thumbParamsVersionEqual = _thumbParamsVersionEqual;
@synthesize thumbAssetIdEqual = _thumbAssetIdEqual;
@synthesize thumbAssetVersionEqual = _thumbAssetVersionEqual;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_thumbParamsIdEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfThumbParamsIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfThumbParamsVersionEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfThumbAssetIdEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfThumbAssetVersionEqual
{
    return KFT_String;
}

- (void)setThumbParamsIdEqualFromString:(NSString*)aPropVal
{
    self.thumbParamsIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaThumbParamsOutputBaseFilter"];
    [aParams addIfDefinedKey:@"thumbParamsIdEqual" withInt:self.thumbParamsIdEqual];
    [aParams addIfDefinedKey:@"thumbParamsVersionEqual" withString:self.thumbParamsVersionEqual];
    [aParams addIfDefinedKey:@"thumbAssetIdEqual" withString:self.thumbAssetIdEqual];
    [aParams addIfDefinedKey:@"thumbAssetVersionEqual" withString:self.thumbAssetVersionEqual];
}

- (void)dealloc
{
    [self->_thumbParamsVersionEqual release];
    [self->_thumbAssetIdEqual release];
    [self->_thumbAssetVersionEqual release];
    [super dealloc];
}

@end

@implementation KalturaFlavorParamsOutputFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaFlavorParamsOutputFilter"];
}

@end

@implementation KalturaGenericXsltSyndicationFeedFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaGenericXsltSyndicationFeedFilter"];
}

@end

@implementation KalturaMediaFlavorParamsFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaMediaFlavorParamsFilter"];
}

@end

@implementation KalturaMixEntryFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaMixEntryFilter"];
}

@end

@implementation KalturaThumbParamsOutputFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaThumbParamsOutputFilter"];
}

@end

@implementation KalturaLiveStreamEntryBaseFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaLiveStreamEntryBaseFilter"];
}

@end

@implementation KalturaMediaFlavorParamsOutputBaseFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaMediaFlavorParamsOutputBaseFilter"];
}

@end

@implementation KalturaLiveStreamEntryFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaLiveStreamEntryFilter"];
}

@end

@implementation KalturaMediaFlavorParamsOutputFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaMediaFlavorParamsOutputFilter"];
}

@end

@implementation KalturaLiveStreamAdminEntryBaseFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaLiveStreamAdminEntryBaseFilter"];
}

@end

@implementation KalturaLiveStreamAdminEntryFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaLiveStreamAdminEntryFilter"];
}

@end

///////////////////////// services /////////////////////////
@implementation KalturaAccessControlProfileService
- (KalturaAccessControlProfile*)addWithAccessControlProfile:(KalturaAccessControlProfile*)aAccessControlProfile
{
    [self.client.params addIfDefinedKey:@"accessControlProfile" withObject:aAccessControlProfile];
    return [self.client queueObjectService:@"accesscontrolprofile" withAction:@"add" withExpectedType:@"KalturaAccessControlProfile"];
}

- (KalturaAccessControlProfile*)getWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    return [self.client queueObjectService:@"accesscontrolprofile" withAction:@"get" withExpectedType:@"KalturaAccessControlProfile"];
}

- (KalturaAccessControlProfile*)updateWithId:(int)aId withAccessControlProfile:(KalturaAccessControlProfile*)aAccessControlProfile
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client.params addIfDefinedKey:@"accessControlProfile" withObject:aAccessControlProfile];
    return [self.client queueObjectService:@"accesscontrolprofile" withAction:@"update" withExpectedType:@"KalturaAccessControlProfile"];
}

- (void)deleteWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client queueVoidService:@"accesscontrolprofile" withAction:@"delete"];
}

- (KalturaAccessControlProfileListResponse*)listWithFilter:(KalturaAccessControlProfileFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"accesscontrolprofile" withAction:@"list" withExpectedType:@"KalturaAccessControlProfileListResponse"];
}

- (KalturaAccessControlProfileListResponse*)listWithFilter:(KalturaAccessControlProfileFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaAccessControlProfileListResponse*)list
{
    return [self listWithFilter:nil];
}

@end

@implementation KalturaAccessControlService
- (KalturaAccessControl*)addWithAccessControl:(KalturaAccessControl*)aAccessControl
{
    [self.client.params addIfDefinedKey:@"accessControl" withObject:aAccessControl];
    return [self.client queueObjectService:@"accesscontrol" withAction:@"add" withExpectedType:@"KalturaAccessControl"];
}

- (KalturaAccessControl*)getWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    return [self.client queueObjectService:@"accesscontrol" withAction:@"get" withExpectedType:@"KalturaAccessControl"];
}

- (KalturaAccessControl*)updateWithId:(int)aId withAccessControl:(KalturaAccessControl*)aAccessControl
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client.params addIfDefinedKey:@"accessControl" withObject:aAccessControl];
    return [self.client queueObjectService:@"accesscontrol" withAction:@"update" withExpectedType:@"KalturaAccessControl"];
}

- (void)deleteWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client queueVoidService:@"accesscontrol" withAction:@"delete"];
}

- (KalturaAccessControlListResponse*)listWithFilter:(KalturaAccessControlFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"accesscontrol" withAction:@"list" withExpectedType:@"KalturaAccessControlListResponse"];
}

- (KalturaAccessControlListResponse*)listWithFilter:(KalturaAccessControlFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaAccessControlListResponse*)list
{
    return [self listWithFilter:nil];
}

@end

@implementation KalturaAdminUserService
- (KalturaAdminUser*)updatePasswordWithEmail:(NSString*)aEmail withPassword:(NSString*)aPassword withNewEmail:(NSString*)aNewEmail withNewPassword:(NSString*)aNewPassword
{
    [self.client.params addIfDefinedKey:@"email" withString:aEmail];
    [self.client.params addIfDefinedKey:@"password" withString:aPassword];
    [self.client.params addIfDefinedKey:@"newEmail" withString:aNewEmail];
    [self.client.params addIfDefinedKey:@"newPassword" withString:aNewPassword];
    return [self.client queueObjectService:@"adminuser" withAction:@"updatePassword" withExpectedType:@"KalturaAdminUser"];
}

- (KalturaAdminUser*)updatePasswordWithEmail:(NSString*)aEmail withPassword:(NSString*)aPassword withNewEmail:(NSString*)aNewEmail
{
    return [self updatePasswordWithEmail:aEmail withPassword:aPassword withNewEmail:aNewEmail withNewPassword:nil];
}

- (KalturaAdminUser*)updatePasswordWithEmail:(NSString*)aEmail withPassword:(NSString*)aPassword
{
    return [self updatePasswordWithEmail:aEmail withPassword:aPassword withNewEmail:nil];
}

- (void)resetPasswordWithEmail:(NSString*)aEmail
{
    [self.client.params addIfDefinedKey:@"email" withString:aEmail];
    [self.client queueVoidService:@"adminuser" withAction:@"resetPassword"];
}

- (NSString*)loginWithEmail:(NSString*)aEmail withPassword:(NSString*)aPassword withPartnerId:(int)aPartnerId
{
    [self.client.params addIfDefinedKey:@"email" withString:aEmail];
    [self.client.params addIfDefinedKey:@"password" withString:aPassword];
    [self.client.params addIfDefinedKey:@"partnerId" withInt:aPartnerId];
    return [self.client queueStringService:@"adminuser" withAction:@"login"];
}

- (NSString*)loginWithEmail:(NSString*)aEmail withPassword:(NSString*)aPassword
{
    return [self loginWithEmail:aEmail withPassword:aPassword withPartnerId:KALTURA_UNDEF_INT];
}

- (void)setInitialPasswordWithHashKey:(NSString*)aHashKey withNewPassword:(NSString*)aNewPassword
{
    [self.client.params addIfDefinedKey:@"hashKey" withString:aHashKey];
    [self.client.params addIfDefinedKey:@"newPassword" withString:aNewPassword];
    [self.client queueVoidService:@"adminuser" withAction:@"setInitialPassword"];
}

@end

@implementation KalturaBaseEntryService
- (KalturaBaseEntry*)addWithEntry:(KalturaBaseEntry*)aEntry withType:(NSString*)aType
{
    [self.client.params addIfDefinedKey:@"entry" withObject:aEntry];
    [self.client.params addIfDefinedKey:@"type" withString:aType];
    return [self.client queueObjectService:@"baseentry" withAction:@"add" withExpectedType:@"KalturaBaseEntry"];
}

- (KalturaBaseEntry*)addWithEntry:(KalturaBaseEntry*)aEntry
{
    return [self addWithEntry:aEntry withType:nil];
}

- (KalturaBaseEntry*)addContentWithEntryId:(NSString*)aEntryId withResource:(KalturaResource*)aResource
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"resource" withObject:aResource];
    return [self.client queueObjectService:@"baseentry" withAction:@"addContent" withExpectedType:@"KalturaBaseEntry"];
}

- (KalturaBaseEntry*)addFromUploadedFileWithEntry:(KalturaBaseEntry*)aEntry withUploadTokenId:(NSString*)aUploadTokenId withType:(NSString*)aType
{
    [self.client.params addIfDefinedKey:@"entry" withObject:aEntry];
    [self.client.params addIfDefinedKey:@"uploadTokenId" withString:aUploadTokenId];
    [self.client.params addIfDefinedKey:@"type" withString:aType];
    return [self.client queueObjectService:@"baseentry" withAction:@"addFromUploadedFile" withExpectedType:@"KalturaBaseEntry"];
}

- (KalturaBaseEntry*)addFromUploadedFileWithEntry:(KalturaBaseEntry*)aEntry withUploadTokenId:(NSString*)aUploadTokenId
{
    return [self addFromUploadedFileWithEntry:aEntry withUploadTokenId:aUploadTokenId withType:nil];
}

- (KalturaBaseEntry*)getWithEntryId:(NSString*)aEntryId withVersion:(int)aVersion
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"version" withInt:aVersion];
    return [self.client queueObjectService:@"baseentry" withAction:@"get" withExpectedType:@"KalturaBaseEntry"];
}

- (KalturaBaseEntry*)getWithEntryId:(NSString*)aEntryId
{
    return [self getWithEntryId:aEntryId withVersion:KALTURA_UNDEF_INT];
}

- (KalturaRemotePathListResponse*)getRemotePathsWithEntryId:(NSString*)aEntryId
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    return [self.client queueObjectService:@"baseentry" withAction:@"getRemotePaths" withExpectedType:@"KalturaRemotePathListResponse"];
}

- (KalturaBaseEntry*)updateWithEntryId:(NSString*)aEntryId withBaseEntry:(KalturaBaseEntry*)aBaseEntry
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"baseEntry" withObject:aBaseEntry];
    return [self.client queueObjectService:@"baseentry" withAction:@"update" withExpectedType:@"KalturaBaseEntry"];
}

- (KalturaBaseEntry*)updateContentWithEntryId:(NSString*)aEntryId withResource:(KalturaResource*)aResource withConversionProfileId:(int)aConversionProfileId
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"resource" withObject:aResource];
    [self.client.params addIfDefinedKey:@"conversionProfileId" withInt:aConversionProfileId];
    return [self.client queueObjectService:@"baseentry" withAction:@"updateContent" withExpectedType:@"KalturaBaseEntry"];
}

- (KalturaBaseEntry*)updateContentWithEntryId:(NSString*)aEntryId withResource:(KalturaResource*)aResource
{
    return [self updateContentWithEntryId:aEntryId withResource:aResource withConversionProfileId:KALTURA_UNDEF_INT];
}

- (NSMutableArray*)getByIdsWithEntryIds:(NSString*)aEntryIds
{
    [self.client.params addIfDefinedKey:@"entryIds" withString:aEntryIds];
    return [self.client queueArrayService:@"baseentry" withAction:@"getByIds" withExpectedType:@"KalturaBaseEntry"];
}

- (void)deleteWithEntryId:(NSString*)aEntryId
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client queueVoidService:@"baseentry" withAction:@"delete"];
}

- (KalturaBaseEntryListResponse*)listWithFilter:(KalturaBaseEntryFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"baseentry" withAction:@"list" withExpectedType:@"KalturaBaseEntryListResponse"];
}

- (KalturaBaseEntryListResponse*)listWithFilter:(KalturaBaseEntryFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaBaseEntryListResponse*)list
{
    return [self listWithFilter:nil];
}

- (void)listByReferenceIdWithRefId:(NSString*)aRefId withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"refId" withString:aRefId];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    [self.client queueVoidService:@"baseentry" withAction:@"listByReferenceId"];
}

- (void)listByReferenceIdWithRefId:(NSString*)aRefId
{
    [self listByReferenceIdWithRefId:aRefId withPager:nil];
}

- (int)countWithFilter:(KalturaBaseEntryFilter*)aFilter
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    return [self.client queueIntService:@"baseentry" withAction:@"count"];
}

- (int)count
{
    return [self countWithFilter:nil];
}

- (NSString*)uploadWithFileData:(NSString*)aFileData
{
    [self.client.params addIfDefinedKey:@"fileData" withFileName:aFileData];
    return [self.client queueStringService:@"baseentry" withAction:@"upload"];
}

- (KalturaBaseEntry*)updateThumbnailJpegWithEntryId:(NSString*)aEntryId withFileData:(NSString*)aFileData
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"fileData" withFileName:aFileData];
    return [self.client queueObjectService:@"baseentry" withAction:@"updateThumbnailJpeg" withExpectedType:@"KalturaBaseEntry"];
}

- (KalturaBaseEntry*)updateThumbnailFromUrlWithEntryId:(NSString*)aEntryId withUrl:(NSString*)aUrl
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"url" withString:aUrl];
    return [self.client queueObjectService:@"baseentry" withAction:@"updateThumbnailFromUrl" withExpectedType:@"KalturaBaseEntry"];
}

- (KalturaBaseEntry*)updateThumbnailFromSourceEntryWithEntryId:(NSString*)aEntryId withSourceEntryId:(NSString*)aSourceEntryId withTimeOffset:(int)aTimeOffset
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"sourceEntryId" withString:aSourceEntryId];
    [self.client.params addIfDefinedKey:@"timeOffset" withInt:aTimeOffset];
    return [self.client queueObjectService:@"baseentry" withAction:@"updateThumbnailFromSourceEntry" withExpectedType:@"KalturaBaseEntry"];
}

- (void)flagWithModerationFlag:(KalturaModerationFlag*)aModerationFlag
{
    [self.client.params addIfDefinedKey:@"moderationFlag" withObject:aModerationFlag];
    [self.client queueVoidService:@"baseentry" withAction:@"flag"];
}

- (void)rejectWithEntryId:(NSString*)aEntryId
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client queueVoidService:@"baseentry" withAction:@"reject"];
}

- (void)approveWithEntryId:(NSString*)aEntryId
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client queueVoidService:@"baseentry" withAction:@"approve"];
}

- (KalturaModerationFlagListResponse*)listFlagsWithEntryId:(NSString*)aEntryId withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"baseentry" withAction:@"listFlags" withExpectedType:@"KalturaModerationFlagListResponse"];
}

- (KalturaModerationFlagListResponse*)listFlagsWithEntryId:(NSString*)aEntryId
{
    return [self listFlagsWithEntryId:aEntryId withPager:nil];
}

- (void)anonymousRankWithEntryId:(NSString*)aEntryId withRank:(int)aRank
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"rank" withInt:aRank];
    [self.client queueVoidService:@"baseentry" withAction:@"anonymousRank"];
}

- (KalturaEntryContextDataResult*)getContextDataWithEntryId:(NSString*)aEntryId withContextDataParams:(KalturaEntryContextDataParams*)aContextDataParams
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"contextDataParams" withObject:aContextDataParams];
    return [self.client queueObjectService:@"baseentry" withAction:@"getContextData" withExpectedType:@"KalturaEntryContextDataResult"];
}

- (KalturaBaseEntry*)exportWithEntryId:(NSString*)aEntryId withStorageProfileId:(int)aStorageProfileId
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"storageProfileId" withInt:aStorageProfileId];
    return [self.client queueObjectService:@"baseentry" withAction:@"export" withExpectedType:@"KalturaBaseEntry"];
}

- (int)indexWithId:(NSString*)aId withShouldUpdate:(BOOL)aShouldUpdate
{
    [self.client.params addIfDefinedKey:@"id" withString:aId];
    [self.client.params addIfDefinedKey:@"shouldUpdate" withBool:aShouldUpdate];
    return [self.client queueIntService:@"baseentry" withAction:@"index"];
}

- (int)indexWithId:(NSString*)aId
{
    return [self indexWithId:aId withShouldUpdate:KALTURA_UNDEF_BOOL];
}

@end

@implementation KalturaBulkUploadService
- (KalturaBulkUpload*)addWithConversionProfileId:(int)aConversionProfileId withCsvFileData:(NSString*)aCsvFileData withBulkUploadType:(NSString*)aBulkUploadType withUploadedBy:(NSString*)aUploadedBy withFileName:(NSString*)aFileName
{
    [self.client.params addIfDefinedKey:@"conversionProfileId" withInt:aConversionProfileId];
    [self.client.params addIfDefinedKey:@"csvFileData" withFileName:aCsvFileData];
    [self.client.params addIfDefinedKey:@"bulkUploadType" withString:aBulkUploadType];
    [self.client.params addIfDefinedKey:@"uploadedBy" withString:aUploadedBy];
    [self.client.params addIfDefinedKey:@"fileName" withString:aFileName];
    return [self.client queueObjectService:@"bulkupload" withAction:@"add" withExpectedType:@"KalturaBulkUpload"];
}

- (KalturaBulkUpload*)addWithConversionProfileId:(int)aConversionProfileId withCsvFileData:(NSString*)aCsvFileData withBulkUploadType:(NSString*)aBulkUploadType withUploadedBy:(NSString*)aUploadedBy
{
    return [self addWithConversionProfileId:aConversionProfileId withCsvFileData:aCsvFileData withBulkUploadType:aBulkUploadType withUploadedBy:aUploadedBy withFileName:nil];
}

- (KalturaBulkUpload*)addWithConversionProfileId:(int)aConversionProfileId withCsvFileData:(NSString*)aCsvFileData withBulkUploadType:(NSString*)aBulkUploadType
{
    return [self addWithConversionProfileId:aConversionProfileId withCsvFileData:aCsvFileData withBulkUploadType:aBulkUploadType withUploadedBy:nil];
}

- (KalturaBulkUpload*)addWithConversionProfileId:(int)aConversionProfileId withCsvFileData:(NSString*)aCsvFileData
{
    return [self addWithConversionProfileId:aConversionProfileId withCsvFileData:aCsvFileData withBulkUploadType:nil];
}

- (KalturaBulkUpload*)getWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    return [self.client queueObjectService:@"bulkupload" withAction:@"get" withExpectedType:@"KalturaBulkUpload"];
}

- (KalturaBulkUploadListResponse*)listWithPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"bulkupload" withAction:@"list" withExpectedType:@"KalturaBulkUploadListResponse"];
}

- (KalturaBulkUploadListResponse*)list
{
    return [self listWithPager:nil];
}

- (NSString*)serveWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    return [self.client queueServeService:@"bulkupload" withAction:@"serve"];
}

- (NSString*)serveLogWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    return [self.client queueServeService:@"bulkupload" withAction:@"serveLog"];
}

- (KalturaBulkUpload*)abortWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    return [self.client queueObjectService:@"bulkupload" withAction:@"abort" withExpectedType:@"KalturaBulkUpload"];
}

@end

@implementation KalturaCategoryEntryService
- (KalturaCategoryEntry*)addWithCategoryEntry:(KalturaCategoryEntry*)aCategoryEntry
{
    [self.client.params addIfDefinedKey:@"categoryEntry" withObject:aCategoryEntry];
    return [self.client queueObjectService:@"categoryentry" withAction:@"add" withExpectedType:@"KalturaCategoryEntry"];
}

- (void)deleteWithEntryId:(NSString*)aEntryId withCategoryId:(int)aCategoryId
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"categoryId" withInt:aCategoryId];
    [self.client queueVoidService:@"categoryentry" withAction:@"delete"];
}

- (KalturaCategoryEntryListResponse*)listWithFilter:(KalturaCategoryEntryFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"categoryentry" withAction:@"list" withExpectedType:@"KalturaCategoryEntryListResponse"];
}

- (KalturaCategoryEntryListResponse*)listWithFilter:(KalturaCategoryEntryFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaCategoryEntryListResponse*)list
{
    return [self listWithFilter:nil];
}

- (int)indexWithEntryId:(NSString*)aEntryId withCategoryId:(int)aCategoryId withShouldUpdate:(BOOL)aShouldUpdate
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"categoryId" withInt:aCategoryId];
    [self.client.params addIfDefinedKey:@"shouldUpdate" withBool:aShouldUpdate];
    return [self.client queueIntService:@"categoryentry" withAction:@"index"];
}

- (int)indexWithEntryId:(NSString*)aEntryId withCategoryId:(int)aCategoryId
{
    return [self indexWithEntryId:aEntryId withCategoryId:aCategoryId withShouldUpdate:KALTURA_UNDEF_BOOL];
}

- (void)activateWithEntryId:(NSString*)aEntryId withCategoryId:(int)aCategoryId
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"categoryId" withInt:aCategoryId];
    [self.client queueVoidService:@"categoryentry" withAction:@"activate"];
}

- (void)rejectWithEntryId:(NSString*)aEntryId withCategoryId:(int)aCategoryId
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"categoryId" withInt:aCategoryId];
    [self.client queueVoidService:@"categoryentry" withAction:@"reject"];
}

@end

@implementation KalturaCategoryService
- (KalturaCategory*)addWithCategory:(KalturaCategory*)aCategory
{
    [self.client.params addIfDefinedKey:@"category" withObject:aCategory];
    return [self.client queueObjectService:@"category" withAction:@"add" withExpectedType:@"KalturaCategory"];
}

- (KalturaCategory*)getWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    return [self.client queueObjectService:@"category" withAction:@"get" withExpectedType:@"KalturaCategory"];
}

- (KalturaCategory*)updateWithId:(int)aId withCategory:(KalturaCategory*)aCategory
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client.params addIfDefinedKey:@"category" withObject:aCategory];
    return [self.client queueObjectService:@"category" withAction:@"update" withExpectedType:@"KalturaCategory"];
}

- (void)deleteWithId:(int)aId withMoveEntriesToParentCategory:(int)aMoveEntriesToParentCategory
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client.params addIfDefinedKey:@"moveEntriesToParentCategory" withInt:aMoveEntriesToParentCategory];
    [self.client queueVoidService:@"category" withAction:@"delete"];
}

- (void)deleteWithId:(int)aId
{
    [self deleteWithId:aId withMoveEntriesToParentCategory:KALTURA_UNDEF_INT];
}

- (KalturaCategoryListResponse*)listWithFilter:(KalturaCategoryFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"category" withAction:@"list" withExpectedType:@"KalturaCategoryListResponse"];
}

- (KalturaCategoryListResponse*)listWithFilter:(KalturaCategoryFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaCategoryListResponse*)list
{
    return [self listWithFilter:nil];
}

- (int)indexWithId:(int)aId withShouldUpdate:(BOOL)aShouldUpdate
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client.params addIfDefinedKey:@"shouldUpdate" withBool:aShouldUpdate];
    return [self.client queueIntService:@"category" withAction:@"index"];
}

- (int)indexWithId:(int)aId
{
    return [self indexWithId:aId withShouldUpdate:KALTURA_UNDEF_BOOL];
}

- (KalturaCategoryListResponse*)moveWithCategoryIds:(NSString*)aCategoryIds withTargetCategoryParentId:(int)aTargetCategoryParentId
{
    [self.client.params addIfDefinedKey:@"categoryIds" withString:aCategoryIds];
    [self.client.params addIfDefinedKey:@"targetCategoryParentId" withInt:aTargetCategoryParentId];
    return [self.client queueObjectService:@"category" withAction:@"move" withExpectedType:@"KalturaCategoryListResponse"];
}

- (void)unlockCategories
{
    [self.client queueVoidService:@"category" withAction:@"unlockCategories"];
}

- (KalturaBulkUpload*)addFromBulkUploadWithFileData:(NSString*)aFileData withBulkUploadData:(KalturaBulkUploadJobData*)aBulkUploadData withBulkUploadCategoryData:(KalturaBulkUploadCategoryData*)aBulkUploadCategoryData
{
    [self.client.params addIfDefinedKey:@"fileData" withFileName:aFileData];
    [self.client.params addIfDefinedKey:@"bulkUploadData" withObject:aBulkUploadData];
    [self.client.params addIfDefinedKey:@"bulkUploadCategoryData" withObject:aBulkUploadCategoryData];
    return [self.client queueObjectService:@"category" withAction:@"addFromBulkUpload" withExpectedType:@"KalturaBulkUpload"];
}

- (KalturaBulkUpload*)addFromBulkUploadWithFileData:(NSString*)aFileData withBulkUploadData:(KalturaBulkUploadJobData*)aBulkUploadData
{
    return [self addFromBulkUploadWithFileData:aFileData withBulkUploadData:aBulkUploadData withBulkUploadCategoryData:nil];
}

- (KalturaBulkUpload*)addFromBulkUploadWithFileData:(NSString*)aFileData
{
    return [self addFromBulkUploadWithFileData:aFileData withBulkUploadData:nil];
}

@end

@implementation KalturaCategoryUserService
- (KalturaCategoryUser*)addWithCategoryUser:(KalturaCategoryUser*)aCategoryUser
{
    [self.client.params addIfDefinedKey:@"categoryUser" withObject:aCategoryUser];
    return [self.client queueObjectService:@"categoryuser" withAction:@"add" withExpectedType:@"KalturaCategoryUser"];
}

- (KalturaCategoryUser*)getWithCategoryId:(int)aCategoryId withUserId:(NSString*)aUserId
{
    [self.client.params addIfDefinedKey:@"categoryId" withInt:aCategoryId];
    [self.client.params addIfDefinedKey:@"userId" withString:aUserId];
    return [self.client queueObjectService:@"categoryuser" withAction:@"get" withExpectedType:@"KalturaCategoryUser"];
}

- (KalturaCategoryUser*)updateWithCategoryId:(int)aCategoryId withUserId:(NSString*)aUserId withCategoryUser:(KalturaCategoryUser*)aCategoryUser withOverride:(BOOL)aOverride
{
    [self.client.params addIfDefinedKey:@"categoryId" withInt:aCategoryId];
    [self.client.params addIfDefinedKey:@"userId" withString:aUserId];
    [self.client.params addIfDefinedKey:@"categoryUser" withObject:aCategoryUser];
    [self.client.params addIfDefinedKey:@"override" withBool:aOverride];
    return [self.client queueObjectService:@"categoryuser" withAction:@"update" withExpectedType:@"KalturaCategoryUser"];
}

- (KalturaCategoryUser*)updateWithCategoryId:(int)aCategoryId withUserId:(NSString*)aUserId withCategoryUser:(KalturaCategoryUser*)aCategoryUser
{
    return [self updateWithCategoryId:aCategoryId withUserId:aUserId withCategoryUser:aCategoryUser withOverride:KALTURA_UNDEF_BOOL];
}

- (void)deleteWithCategoryId:(int)aCategoryId withUserId:(NSString*)aUserId
{
    [self.client.params addIfDefinedKey:@"categoryId" withInt:aCategoryId];
    [self.client.params addIfDefinedKey:@"userId" withString:aUserId];
    [self.client queueVoidService:@"categoryuser" withAction:@"delete"];
}

- (KalturaCategoryUser*)activateWithCategoryId:(int)aCategoryId withUserId:(NSString*)aUserId
{
    [self.client.params addIfDefinedKey:@"categoryId" withInt:aCategoryId];
    [self.client.params addIfDefinedKey:@"userId" withString:aUserId];
    return [self.client queueObjectService:@"categoryuser" withAction:@"activate" withExpectedType:@"KalturaCategoryUser"];
}

- (KalturaCategoryUser*)deactivateWithCategoryId:(int)aCategoryId withUserId:(NSString*)aUserId
{
    [self.client.params addIfDefinedKey:@"categoryId" withInt:aCategoryId];
    [self.client.params addIfDefinedKey:@"userId" withString:aUserId];
    return [self.client queueObjectService:@"categoryuser" withAction:@"deactivate" withExpectedType:@"KalturaCategoryUser"];
}

- (KalturaCategoryUserListResponse*)listWithFilter:(KalturaCategoryUserFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"categoryuser" withAction:@"list" withExpectedType:@"KalturaCategoryUserListResponse"];
}

- (KalturaCategoryUserListResponse*)listWithFilter:(KalturaCategoryUserFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaCategoryUserListResponse*)list
{
    return [self listWithFilter:nil];
}

- (void)copyFromCategoryWithCategoryId:(int)aCategoryId
{
    [self.client.params addIfDefinedKey:@"categoryId" withInt:aCategoryId];
    [self.client queueVoidService:@"categoryuser" withAction:@"copyFromCategory"];
}

- (int)indexWithUserId:(NSString*)aUserId withCategoryId:(int)aCategoryId withShouldUpdate:(BOOL)aShouldUpdate
{
    [self.client.params addIfDefinedKey:@"userId" withString:aUserId];
    [self.client.params addIfDefinedKey:@"categoryId" withInt:aCategoryId];
    [self.client.params addIfDefinedKey:@"shouldUpdate" withBool:aShouldUpdate];
    return [self.client queueIntService:@"categoryuser" withAction:@"index"];
}

- (int)indexWithUserId:(NSString*)aUserId withCategoryId:(int)aCategoryId
{
    return [self indexWithUserId:aUserId withCategoryId:aCategoryId withShouldUpdate:KALTURA_UNDEF_BOOL];
}

- (KalturaBulkUpload*)addFromBulkUploadWithFileData:(NSString*)aFileData withBulkUploadData:(KalturaBulkUploadJobData*)aBulkUploadData withBulkUploadCategoryUserData:(KalturaBulkUploadCategoryUserData*)aBulkUploadCategoryUserData
{
    [self.client.params addIfDefinedKey:@"fileData" withFileName:aFileData];
    [self.client.params addIfDefinedKey:@"bulkUploadData" withObject:aBulkUploadData];
    [self.client.params addIfDefinedKey:@"bulkUploadCategoryUserData" withObject:aBulkUploadCategoryUserData];
    return [self.client queueObjectService:@"categoryuser" withAction:@"addFromBulkUpload" withExpectedType:@"KalturaBulkUpload"];
}

- (KalturaBulkUpload*)addFromBulkUploadWithFileData:(NSString*)aFileData withBulkUploadData:(KalturaBulkUploadJobData*)aBulkUploadData
{
    return [self addFromBulkUploadWithFileData:aFileData withBulkUploadData:aBulkUploadData withBulkUploadCategoryUserData:nil];
}

- (KalturaBulkUpload*)addFromBulkUploadWithFileData:(NSString*)aFileData
{
    return [self addFromBulkUploadWithFileData:aFileData withBulkUploadData:nil];
}

@end

@implementation KalturaConversionProfileAssetParamsService
- (KalturaConversionProfileAssetParamsListResponse*)listWithFilter:(KalturaConversionProfileAssetParamsFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"conversionprofileassetparams" withAction:@"list" withExpectedType:@"KalturaConversionProfileAssetParamsListResponse"];
}

- (KalturaConversionProfileAssetParamsListResponse*)listWithFilter:(KalturaConversionProfileAssetParamsFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaConversionProfileAssetParamsListResponse*)list
{
    return [self listWithFilter:nil];
}

- (KalturaConversionProfileAssetParams*)updateWithConversionProfileId:(int)aConversionProfileId withAssetParamsId:(int)aAssetParamsId withConversionProfileAssetParams:(KalturaConversionProfileAssetParams*)aConversionProfileAssetParams
{
    [self.client.params addIfDefinedKey:@"conversionProfileId" withInt:aConversionProfileId];
    [self.client.params addIfDefinedKey:@"assetParamsId" withInt:aAssetParamsId];
    [self.client.params addIfDefinedKey:@"conversionProfileAssetParams" withObject:aConversionProfileAssetParams];
    return [self.client queueObjectService:@"conversionprofileassetparams" withAction:@"update" withExpectedType:@"KalturaConversionProfileAssetParams"];
}

@end

@implementation KalturaConversionProfileService
- (KalturaConversionProfile*)setAsDefaultWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    return [self.client queueObjectService:@"conversionprofile" withAction:@"setAsDefault" withExpectedType:@"KalturaConversionProfile"];
}

- (KalturaConversionProfile*)getDefault
{
    return [self.client queueObjectService:@"conversionprofile" withAction:@"getDefault" withExpectedType:@"KalturaConversionProfile"];
}

- (KalturaConversionProfile*)addWithConversionProfile:(KalturaConversionProfile*)aConversionProfile
{
    [self.client.params addIfDefinedKey:@"conversionProfile" withObject:aConversionProfile];
    return [self.client queueObjectService:@"conversionprofile" withAction:@"add" withExpectedType:@"KalturaConversionProfile"];
}

- (KalturaConversionProfile*)getWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    return [self.client queueObjectService:@"conversionprofile" withAction:@"get" withExpectedType:@"KalturaConversionProfile"];
}

- (KalturaConversionProfile*)updateWithId:(int)aId withConversionProfile:(KalturaConversionProfile*)aConversionProfile
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client.params addIfDefinedKey:@"conversionProfile" withObject:aConversionProfile];
    return [self.client queueObjectService:@"conversionprofile" withAction:@"update" withExpectedType:@"KalturaConversionProfile"];
}

- (void)deleteWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client queueVoidService:@"conversionprofile" withAction:@"delete"];
}

- (KalturaConversionProfileListResponse*)listWithFilter:(KalturaConversionProfileFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"conversionprofile" withAction:@"list" withExpectedType:@"KalturaConversionProfileListResponse"];
}

- (KalturaConversionProfileListResponse*)listWithFilter:(KalturaConversionProfileFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaConversionProfileListResponse*)list
{
    return [self listWithFilter:nil];
}

@end

@implementation KalturaDataService
- (KalturaDataEntry*)addWithDataEntry:(KalturaDataEntry*)aDataEntry
{
    [self.client.params addIfDefinedKey:@"dataEntry" withObject:aDataEntry];
    return [self.client queueObjectService:@"data" withAction:@"add" withExpectedType:@"KalturaDataEntry"];
}

- (KalturaDataEntry*)getWithEntryId:(NSString*)aEntryId withVersion:(int)aVersion
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"version" withInt:aVersion];
    return [self.client queueObjectService:@"data" withAction:@"get" withExpectedType:@"KalturaDataEntry"];
}

- (KalturaDataEntry*)getWithEntryId:(NSString*)aEntryId
{
    return [self getWithEntryId:aEntryId withVersion:KALTURA_UNDEF_INT];
}

- (KalturaDataEntry*)updateWithEntryId:(NSString*)aEntryId withDocumentEntry:(KalturaDataEntry*)aDocumentEntry
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"documentEntry" withObject:aDocumentEntry];
    return [self.client queueObjectService:@"data" withAction:@"update" withExpectedType:@"KalturaDataEntry"];
}

- (void)deleteWithEntryId:(NSString*)aEntryId
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client queueVoidService:@"data" withAction:@"delete"];
}

- (KalturaDataListResponse*)listWithFilter:(KalturaDataEntryFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"data" withAction:@"list" withExpectedType:@"KalturaDataListResponse"];
}

- (KalturaDataListResponse*)listWithFilter:(KalturaDataEntryFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaDataListResponse*)list
{
    return [self listWithFilter:nil];
}

- (NSString*)serveWithEntryId:(NSString*)aEntryId withVersion:(int)aVersion withForceProxy:(BOOL)aForceProxy
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"version" withInt:aVersion];
    [self.client.params addIfDefinedKey:@"forceProxy" withBool:aForceProxy];
    return [self.client queueServeService:@"data" withAction:@"serve"];
}

- (NSString*)serveWithEntryId:(NSString*)aEntryId withVersion:(int)aVersion
{
    return [self serveWithEntryId:aEntryId withVersion:aVersion withForceProxy:KALTURA_UNDEF_BOOL];
}

- (NSString*)serveWithEntryId:(NSString*)aEntryId
{
    return [self serveWithEntryId:aEntryId withVersion:KALTURA_UNDEF_INT];
}

@end

@implementation KalturaEmailIngestionProfileService
- (KalturaEmailIngestionProfile*)addWithEmailIP:(KalturaEmailIngestionProfile*)aEmailIP
{
    [self.client.params addIfDefinedKey:@"EmailIP" withObject:aEmailIP];
    return [self.client queueObjectService:@"emailingestionprofile" withAction:@"add" withExpectedType:@"KalturaEmailIngestionProfile"];
}

- (KalturaEmailIngestionProfile*)getByEmailAddressWithEmailAddress:(NSString*)aEmailAddress
{
    [self.client.params addIfDefinedKey:@"emailAddress" withString:aEmailAddress];
    return [self.client queueObjectService:@"emailingestionprofile" withAction:@"getByEmailAddress" withExpectedType:@"KalturaEmailIngestionProfile"];
}

- (KalturaEmailIngestionProfile*)getWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    return [self.client queueObjectService:@"emailingestionprofile" withAction:@"get" withExpectedType:@"KalturaEmailIngestionProfile"];
}

- (KalturaEmailIngestionProfile*)updateWithId:(int)aId withEmailIP:(KalturaEmailIngestionProfile*)aEmailIP
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client.params addIfDefinedKey:@"EmailIP" withObject:aEmailIP];
    return [self.client queueObjectService:@"emailingestionprofile" withAction:@"update" withExpectedType:@"KalturaEmailIngestionProfile"];
}

- (void)deleteWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client queueVoidService:@"emailingestionprofile" withAction:@"delete"];
}

- (KalturaMediaEntry*)addMediaEntryWithMediaEntry:(KalturaMediaEntry*)aMediaEntry withUploadTokenId:(NSString*)aUploadTokenId withEmailProfId:(int)aEmailProfId withFromAddress:(NSString*)aFromAddress withEmailMsgId:(NSString*)aEmailMsgId
{
    [self.client.params addIfDefinedKey:@"mediaEntry" withObject:aMediaEntry];
    [self.client.params addIfDefinedKey:@"uploadTokenId" withString:aUploadTokenId];
    [self.client.params addIfDefinedKey:@"emailProfId" withInt:aEmailProfId];
    [self.client.params addIfDefinedKey:@"fromAddress" withString:aFromAddress];
    [self.client.params addIfDefinedKey:@"emailMsgId" withString:aEmailMsgId];
    return [self.client queueObjectService:@"emailingestionprofile" withAction:@"addMediaEntry" withExpectedType:@"KalturaMediaEntry"];
}

@end

@implementation KalturaFlavorAssetService
- (KalturaFlavorAsset*)addWithEntryId:(NSString*)aEntryId withFlavorAsset:(KalturaFlavorAsset*)aFlavorAsset
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"flavorAsset" withObject:aFlavorAsset];
    return [self.client queueObjectService:@"flavorasset" withAction:@"add" withExpectedType:@"KalturaFlavorAsset"];
}

- (KalturaFlavorAsset*)updateWithId:(NSString*)aId withFlavorAsset:(KalturaFlavorAsset*)aFlavorAsset
{
    [self.client.params addIfDefinedKey:@"id" withString:aId];
    [self.client.params addIfDefinedKey:@"flavorAsset" withObject:aFlavorAsset];
    return [self.client queueObjectService:@"flavorasset" withAction:@"update" withExpectedType:@"KalturaFlavorAsset"];
}

- (KalturaFlavorAsset*)setContentWithId:(NSString*)aId withContentResource:(KalturaContentResource*)aContentResource
{
    [self.client.params addIfDefinedKey:@"id" withString:aId];
    [self.client.params addIfDefinedKey:@"contentResource" withObject:aContentResource];
    return [self.client queueObjectService:@"flavorasset" withAction:@"setContent" withExpectedType:@"KalturaFlavorAsset"];
}

- (KalturaFlavorAsset*)getWithId:(NSString*)aId
{
    [self.client.params addIfDefinedKey:@"id" withString:aId];
    return [self.client queueObjectService:@"flavorasset" withAction:@"get" withExpectedType:@"KalturaFlavorAsset"];
}

- (NSMutableArray*)getByEntryIdWithEntryId:(NSString*)aEntryId
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    return [self.client queueArrayService:@"flavorasset" withAction:@"getByEntryId" withExpectedType:@"KalturaFlavorAsset"];
}

- (KalturaFlavorAssetListResponse*)listWithFilter:(KalturaAssetFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"flavorasset" withAction:@"list" withExpectedType:@"KalturaFlavorAssetListResponse"];
}

- (KalturaFlavorAssetListResponse*)listWithFilter:(KalturaAssetFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaFlavorAssetListResponse*)list
{
    return [self listWithFilter:nil];
}

- (NSMutableArray*)getWebPlayableByEntryIdWithEntryId:(NSString*)aEntryId
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    return [self.client queueArrayService:@"flavorasset" withAction:@"getWebPlayableByEntryId" withExpectedType:@"KalturaFlavorAsset"];
}

- (void)convertWithEntryId:(NSString*)aEntryId withFlavorParamsId:(int)aFlavorParamsId withPriority:(int)aPriority
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"flavorParamsId" withInt:aFlavorParamsId];
    [self.client.params addIfDefinedKey:@"priority" withInt:aPriority];
    [self.client queueVoidService:@"flavorasset" withAction:@"convert"];
}

- (void)convertWithEntryId:(NSString*)aEntryId withFlavorParamsId:(int)aFlavorParamsId
{
    [self convertWithEntryId:aEntryId withFlavorParamsId:aFlavorParamsId withPriority:KALTURA_UNDEF_INT];
}

- (void)reconvertWithId:(NSString*)aId
{
    [self.client.params addIfDefinedKey:@"id" withString:aId];
    [self.client queueVoidService:@"flavorasset" withAction:@"reconvert"];
}

- (void)deleteWithId:(NSString*)aId
{
    [self.client.params addIfDefinedKey:@"id" withString:aId];
    [self.client queueVoidService:@"flavorasset" withAction:@"delete"];
}

- (NSString*)getUrlWithId:(NSString*)aId withStorageId:(int)aStorageId
{
    [self.client.params addIfDefinedKey:@"id" withString:aId];
    [self.client.params addIfDefinedKey:@"storageId" withInt:aStorageId];
    return [self.client queueStringService:@"flavorasset" withAction:@"getUrl"];
}

- (NSString*)getUrlWithId:(NSString*)aId
{
    return [self getUrlWithId:aId withStorageId:KALTURA_UNDEF_INT];
}

- (KalturaRemotePathListResponse*)getRemotePathsWithId:(NSString*)aId
{
    [self.client.params addIfDefinedKey:@"id" withString:aId];
    return [self.client queueObjectService:@"flavorasset" withAction:@"getRemotePaths" withExpectedType:@"KalturaRemotePathListResponse"];
}

- (NSString*)getDownloadUrlWithId:(NSString*)aId withUseCdn:(BOOL)aUseCdn
{
    [self.client.params addIfDefinedKey:@"id" withString:aId];
    [self.client.params addIfDefinedKey:@"useCdn" withBool:aUseCdn];
    return [self.client queueStringService:@"flavorasset" withAction:@"getDownloadUrl"];
}

- (NSString*)getDownloadUrlWithId:(NSString*)aId
{
    return [self getDownloadUrlWithId:aId withUseCdn:KALTURA_UNDEF_BOOL];
}

- (NSMutableArray*)getFlavorAssetsWithParamsWithEntryId:(NSString*)aEntryId
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    return [self.client queueArrayService:@"flavorasset" withAction:@"getFlavorAssetsWithParams" withExpectedType:@"KalturaFlavorAssetWithParams"];
}

- (KalturaFlavorAsset*)exportWithAssetId:(NSString*)aAssetId withStorageProfileId:(int)aStorageProfileId
{
    [self.client.params addIfDefinedKey:@"assetId" withString:aAssetId];
    [self.client.params addIfDefinedKey:@"storageProfileId" withInt:aStorageProfileId];
    return [self.client queueObjectService:@"flavorasset" withAction:@"export" withExpectedType:@"KalturaFlavorAsset"];
}

@end

@implementation KalturaFlavorParamsOutputService
- (KalturaFlavorParamsOutput*)getWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    return [self.client queueObjectService:@"flavorparamsoutput" withAction:@"get" withExpectedType:@"KalturaFlavorParamsOutput"];
}

- (KalturaFlavorParamsOutputListResponse*)listWithFilter:(KalturaFlavorParamsOutputFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"flavorparamsoutput" withAction:@"list" withExpectedType:@"KalturaFlavorParamsOutputListResponse"];
}

- (KalturaFlavorParamsOutputListResponse*)listWithFilter:(KalturaFlavorParamsOutputFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaFlavorParamsOutputListResponse*)list
{
    return [self listWithFilter:nil];
}

@end

@implementation KalturaFlavorParamsService
- (KalturaFlavorParams*)addWithFlavorParams:(KalturaFlavorParams*)aFlavorParams
{
    [self.client.params addIfDefinedKey:@"flavorParams" withObject:aFlavorParams];
    return [self.client queueObjectService:@"flavorparams" withAction:@"add" withExpectedType:@"KalturaFlavorParams"];
}

- (KalturaFlavorParams*)getWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    return [self.client queueObjectService:@"flavorparams" withAction:@"get" withExpectedType:@"KalturaFlavorParams"];
}

- (KalturaFlavorParams*)updateWithId:(int)aId withFlavorParams:(KalturaFlavorParams*)aFlavorParams
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client.params addIfDefinedKey:@"flavorParams" withObject:aFlavorParams];
    return [self.client queueObjectService:@"flavorparams" withAction:@"update" withExpectedType:@"KalturaFlavorParams"];
}

- (void)deleteWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client queueVoidService:@"flavorparams" withAction:@"delete"];
}

- (KalturaFlavorParamsListResponse*)listWithFilter:(KalturaFlavorParamsFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"flavorparams" withAction:@"list" withExpectedType:@"KalturaFlavorParamsListResponse"];
}

- (KalturaFlavorParamsListResponse*)listWithFilter:(KalturaFlavorParamsFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaFlavorParamsListResponse*)list
{
    return [self listWithFilter:nil];
}

- (NSMutableArray*)getByConversionProfileIdWithConversionProfileId:(int)aConversionProfileId
{
    [self.client.params addIfDefinedKey:@"conversionProfileId" withInt:aConversionProfileId];
    return [self.client queueArrayService:@"flavorparams" withAction:@"getByConversionProfileId" withExpectedType:@"KalturaFlavorParams"];
}

@end

@implementation KalturaLiveStreamService
- (KalturaLiveStreamAdminEntry*)addWithLiveStreamEntry:(KalturaLiveStreamAdminEntry*)aLiveStreamEntry withSourceType:(NSString*)aSourceType
{
    [self.client.params addIfDefinedKey:@"liveStreamEntry" withObject:aLiveStreamEntry];
    [self.client.params addIfDefinedKey:@"sourceType" withString:aSourceType];
    return [self.client queueObjectService:@"livestream" withAction:@"add" withExpectedType:@"KalturaLiveStreamAdminEntry"];
}

- (KalturaLiveStreamAdminEntry*)addWithLiveStreamEntry:(KalturaLiveStreamAdminEntry*)aLiveStreamEntry
{
    return [self addWithLiveStreamEntry:aLiveStreamEntry withSourceType:nil];
}

- (KalturaLiveStreamEntry*)getWithEntryId:(NSString*)aEntryId withVersion:(int)aVersion
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"version" withInt:aVersion];
    return [self.client queueObjectService:@"livestream" withAction:@"get" withExpectedType:@"KalturaLiveStreamEntry"];
}

- (KalturaLiveStreamEntry*)getWithEntryId:(NSString*)aEntryId
{
    return [self getWithEntryId:aEntryId withVersion:KALTURA_UNDEF_INT];
}

- (KalturaLiveStreamAdminEntry*)updateWithEntryId:(NSString*)aEntryId withLiveStreamEntry:(KalturaLiveStreamAdminEntry*)aLiveStreamEntry
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"liveStreamEntry" withObject:aLiveStreamEntry];
    return [self.client queueObjectService:@"livestream" withAction:@"update" withExpectedType:@"KalturaLiveStreamAdminEntry"];
}

- (void)deleteWithEntryId:(NSString*)aEntryId
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client queueVoidService:@"livestream" withAction:@"delete"];
}

- (KalturaLiveStreamListResponse*)listWithFilter:(KalturaLiveStreamEntryFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"livestream" withAction:@"list" withExpectedType:@"KalturaLiveStreamListResponse"];
}

- (KalturaLiveStreamListResponse*)listWithFilter:(KalturaLiveStreamEntryFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaLiveStreamListResponse*)list
{
    return [self listWithFilter:nil];
}

- (KalturaLiveStreamEntry*)updateOfflineThumbnailJpegWithEntryId:(NSString*)aEntryId withFileData:(NSString*)aFileData
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"fileData" withFileName:aFileData];
    return [self.client queueObjectService:@"livestream" withAction:@"updateOfflineThumbnailJpeg" withExpectedType:@"KalturaLiveStreamEntry"];
}

- (KalturaLiveStreamEntry*)updateOfflineThumbnailFromUrlWithEntryId:(NSString*)aEntryId withUrl:(NSString*)aUrl
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"url" withString:aUrl];
    return [self.client queueObjectService:@"livestream" withAction:@"updateOfflineThumbnailFromUrl" withExpectedType:@"KalturaLiveStreamEntry"];
}

- (BOOL)isLiveWithId:(NSString*)aId withProtocol:(NSString*)aProtocol
{
    [self.client.params addIfDefinedKey:@"id" withString:aId];
    [self.client.params addIfDefinedKey:@"protocol" withString:aProtocol];
    return [self.client queueBoolService:@"livestream" withAction:@"isLive"];
}

@end

@implementation KalturaMediaInfoService
- (KalturaMediaInfoListResponse*)listWithFilter:(KalturaMediaInfoFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"mediainfo" withAction:@"list" withExpectedType:@"KalturaMediaInfoListResponse"];
}

- (KalturaMediaInfoListResponse*)listWithFilter:(KalturaMediaInfoFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaMediaInfoListResponse*)list
{
    return [self listWithFilter:nil];
}

@end

@implementation KalturaMediaService
- (KalturaMediaEntry*)addWithEntry:(KalturaMediaEntry*)aEntry
{
    [self.client.params addIfDefinedKey:@"entry" withObject:aEntry];
    return [self.client queueObjectService:@"media" withAction:@"add" withExpectedType:@"KalturaMediaEntry"];
}

- (KalturaMediaEntry*)addContentWithEntryId:(NSString*)aEntryId withResource:(KalturaResource*)aResource
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"resource" withObject:aResource];
    return [self.client queueObjectService:@"media" withAction:@"addContent" withExpectedType:@"KalturaMediaEntry"];
}

- (KalturaMediaEntry*)addContentWithEntryId:(NSString*)aEntryId
{
    return [self addContentWithEntryId:aEntryId withResource:nil];
}

- (KalturaMediaEntry*)addFromUrlWithMediaEntry:(KalturaMediaEntry*)aMediaEntry withUrl:(NSString*)aUrl
{
    [self.client.params addIfDefinedKey:@"mediaEntry" withObject:aMediaEntry];
    [self.client.params addIfDefinedKey:@"url" withString:aUrl];
    return [self.client queueObjectService:@"media" withAction:@"addFromUrl" withExpectedType:@"KalturaMediaEntry"];
}

- (KalturaMediaEntry*)addFromSearchResultWithMediaEntry:(KalturaMediaEntry*)aMediaEntry withSearchResult:(KalturaSearchResult*)aSearchResult
{
    [self.client.params addIfDefinedKey:@"mediaEntry" withObject:aMediaEntry];
    [self.client.params addIfDefinedKey:@"searchResult" withObject:aSearchResult];
    return [self.client queueObjectService:@"media" withAction:@"addFromSearchResult" withExpectedType:@"KalturaMediaEntry"];
}

- (KalturaMediaEntry*)addFromSearchResultWithMediaEntry:(KalturaMediaEntry*)aMediaEntry
{
    return [self addFromSearchResultWithMediaEntry:aMediaEntry withSearchResult:nil];
}

- (KalturaMediaEntry*)addFromSearchResult
{
    return [self addFromSearchResultWithMediaEntry:nil];
}

- (KalturaMediaEntry*)addFromUploadedFileWithMediaEntry:(KalturaMediaEntry*)aMediaEntry withUploadTokenId:(NSString*)aUploadTokenId
{
    [self.client.params addIfDefinedKey:@"mediaEntry" withObject:aMediaEntry];
    [self.client.params addIfDefinedKey:@"uploadTokenId" withString:aUploadTokenId];
    return [self.client queueObjectService:@"media" withAction:@"addFromUploadedFile" withExpectedType:@"KalturaMediaEntry"];
}

- (KalturaMediaEntry*)addFromRecordedWebcamWithMediaEntry:(KalturaMediaEntry*)aMediaEntry withWebcamTokenId:(NSString*)aWebcamTokenId
{
    [self.client.params addIfDefinedKey:@"mediaEntry" withObject:aMediaEntry];
    [self.client.params addIfDefinedKey:@"webcamTokenId" withString:aWebcamTokenId];
    return [self.client queueObjectService:@"media" withAction:@"addFromRecordedWebcam" withExpectedType:@"KalturaMediaEntry"];
}

- (KalturaMediaEntry*)addFromEntryWithSourceEntryId:(NSString*)aSourceEntryId withMediaEntry:(KalturaMediaEntry*)aMediaEntry withSourceFlavorParamsId:(int)aSourceFlavorParamsId
{
    [self.client.params addIfDefinedKey:@"sourceEntryId" withString:aSourceEntryId];
    [self.client.params addIfDefinedKey:@"mediaEntry" withObject:aMediaEntry];
    [self.client.params addIfDefinedKey:@"sourceFlavorParamsId" withInt:aSourceFlavorParamsId];
    return [self.client queueObjectService:@"media" withAction:@"addFromEntry" withExpectedType:@"KalturaMediaEntry"];
}

- (KalturaMediaEntry*)addFromEntryWithSourceEntryId:(NSString*)aSourceEntryId withMediaEntry:(KalturaMediaEntry*)aMediaEntry
{
    return [self addFromEntryWithSourceEntryId:aSourceEntryId withMediaEntry:aMediaEntry withSourceFlavorParamsId:KALTURA_UNDEF_INT];
}

- (KalturaMediaEntry*)addFromEntryWithSourceEntryId:(NSString*)aSourceEntryId
{
    return [self addFromEntryWithSourceEntryId:aSourceEntryId withMediaEntry:nil];
}

- (KalturaMediaEntry*)addFromFlavorAssetWithSourceFlavorAssetId:(NSString*)aSourceFlavorAssetId withMediaEntry:(KalturaMediaEntry*)aMediaEntry
{
    [self.client.params addIfDefinedKey:@"sourceFlavorAssetId" withString:aSourceFlavorAssetId];
    [self.client.params addIfDefinedKey:@"mediaEntry" withObject:aMediaEntry];
    return [self.client queueObjectService:@"media" withAction:@"addFromFlavorAsset" withExpectedType:@"KalturaMediaEntry"];
}

- (KalturaMediaEntry*)addFromFlavorAssetWithSourceFlavorAssetId:(NSString*)aSourceFlavorAssetId
{
    return [self addFromFlavorAssetWithSourceFlavorAssetId:aSourceFlavorAssetId withMediaEntry:nil];
}

- (int)convertWithEntryId:(NSString*)aEntryId withConversionProfileId:(int)aConversionProfileId withDynamicConversionAttributes:(NSArray*)aDynamicConversionAttributes
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"conversionProfileId" withInt:aConversionProfileId];
    [self.client.params addIfDefinedKey:@"dynamicConversionAttributes" withArray:aDynamicConversionAttributes];
    return [self.client queueIntService:@"media" withAction:@"convert"];
}

- (int)convertWithEntryId:(NSString*)aEntryId withConversionProfileId:(int)aConversionProfileId
{
    return [self convertWithEntryId:aEntryId withConversionProfileId:aConversionProfileId withDynamicConversionAttributes:nil];
}

- (int)convertWithEntryId:(NSString*)aEntryId
{
    return [self convertWithEntryId:aEntryId withConversionProfileId:KALTURA_UNDEF_INT];
}

- (KalturaMediaEntry*)getWithEntryId:(NSString*)aEntryId withVersion:(int)aVersion
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"version" withInt:aVersion];
    return [self.client queueObjectService:@"media" withAction:@"get" withExpectedType:@"KalturaMediaEntry"];
}

- (KalturaMediaEntry*)getWithEntryId:(NSString*)aEntryId
{
    return [self getWithEntryId:aEntryId withVersion:KALTURA_UNDEF_INT];
}

- (NSString*)getMrssWithEntryId:(NSString*)aEntryId withExtendingItemsArray:(NSArray*)aExtendingItemsArray
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"extendingItemsArray" withArray:aExtendingItemsArray];
    return [self.client queueStringService:@"media" withAction:@"getMrss"];
}

- (NSString*)getMrssWithEntryId:(NSString*)aEntryId
{
    return [self getMrssWithEntryId:aEntryId withExtendingItemsArray:nil];
}

- (KalturaMediaEntry*)updateWithEntryId:(NSString*)aEntryId withMediaEntry:(KalturaMediaEntry*)aMediaEntry
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"mediaEntry" withObject:aMediaEntry];
    return [self.client queueObjectService:@"media" withAction:@"update" withExpectedType:@"KalturaMediaEntry"];
}

- (KalturaMediaEntry*)updateContentWithEntryId:(NSString*)aEntryId withResource:(KalturaResource*)aResource withConversionProfileId:(int)aConversionProfileId
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"resource" withObject:aResource];
    [self.client.params addIfDefinedKey:@"conversionProfileId" withInt:aConversionProfileId];
    return [self.client queueObjectService:@"media" withAction:@"updateContent" withExpectedType:@"KalturaMediaEntry"];
}

- (KalturaMediaEntry*)updateContentWithEntryId:(NSString*)aEntryId withResource:(KalturaResource*)aResource
{
    return [self updateContentWithEntryId:aEntryId withResource:aResource withConversionProfileId:KALTURA_UNDEF_INT];
}

- (void)deleteWithEntryId:(NSString*)aEntryId
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client queueVoidService:@"media" withAction:@"delete"];
}

- (KalturaMediaEntry*)approveReplaceWithEntryId:(NSString*)aEntryId
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    return [self.client queueObjectService:@"media" withAction:@"approveReplace" withExpectedType:@"KalturaMediaEntry"];
}

- (KalturaMediaEntry*)cancelReplaceWithEntryId:(NSString*)aEntryId
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    return [self.client queueObjectService:@"media" withAction:@"cancelReplace" withExpectedType:@"KalturaMediaEntry"];
}

- (KalturaMediaListResponse*)listWithFilter:(KalturaMediaEntryFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"media" withAction:@"list" withExpectedType:@"KalturaMediaListResponse"];
}

- (KalturaMediaListResponse*)listWithFilter:(KalturaMediaEntryFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaMediaListResponse*)list
{
    return [self listWithFilter:nil];
}

- (int)countWithFilter:(KalturaMediaEntryFilter*)aFilter
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    return [self.client queueIntService:@"media" withAction:@"count"];
}

- (int)count
{
    return [self countWithFilter:nil];
}

- (NSString*)uploadWithFileData:(NSString*)aFileData
{
    [self.client.params addIfDefinedKey:@"fileData" withFileName:aFileData];
    return [self.client queueStringService:@"media" withAction:@"upload"];
}

- (KalturaMediaEntry*)updateThumbnailWithEntryId:(NSString*)aEntryId withTimeOffset:(int)aTimeOffset withFlavorParamsId:(int)aFlavorParamsId
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"timeOffset" withInt:aTimeOffset];
    [self.client.params addIfDefinedKey:@"flavorParamsId" withInt:aFlavorParamsId];
    return [self.client queueObjectService:@"media" withAction:@"updateThumbnail" withExpectedType:@"KalturaMediaEntry"];
}

- (KalturaMediaEntry*)updateThumbnailWithEntryId:(NSString*)aEntryId withTimeOffset:(int)aTimeOffset
{
    return [self updateThumbnailWithEntryId:aEntryId withTimeOffset:aTimeOffset withFlavorParamsId:KALTURA_UNDEF_INT];
}

- (KalturaMediaEntry*)updateThumbnailFromSourceEntryWithEntryId:(NSString*)aEntryId withSourceEntryId:(NSString*)aSourceEntryId withTimeOffset:(int)aTimeOffset withFlavorParamsId:(int)aFlavorParamsId
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"sourceEntryId" withString:aSourceEntryId];
    [self.client.params addIfDefinedKey:@"timeOffset" withInt:aTimeOffset];
    [self.client.params addIfDefinedKey:@"flavorParamsId" withInt:aFlavorParamsId];
    return [self.client queueObjectService:@"media" withAction:@"updateThumbnailFromSourceEntry" withExpectedType:@"KalturaMediaEntry"];
}

- (KalturaMediaEntry*)updateThumbnailFromSourceEntryWithEntryId:(NSString*)aEntryId withSourceEntryId:(NSString*)aSourceEntryId withTimeOffset:(int)aTimeOffset
{
    return [self updateThumbnailFromSourceEntryWithEntryId:aEntryId withSourceEntryId:aSourceEntryId withTimeOffset:aTimeOffset withFlavorParamsId:KALTURA_UNDEF_INT];
}

- (KalturaMediaEntry*)updateThumbnailJpegWithEntryId:(NSString*)aEntryId withFileData:(NSString*)aFileData
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"fileData" withFileName:aFileData];
    return [self.client queueObjectService:@"media" withAction:@"updateThumbnailJpeg" withExpectedType:@"KalturaMediaEntry"];
}

- (KalturaBaseEntry*)updateThumbnailFromUrlWithEntryId:(NSString*)aEntryId withUrl:(NSString*)aUrl
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"url" withString:aUrl];
    return [self.client queueObjectService:@"media" withAction:@"updateThumbnailFromUrl" withExpectedType:@"KalturaBaseEntry"];
}

- (int)requestConversionWithEntryId:(NSString*)aEntryId withFileFormat:(NSString*)aFileFormat
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"fileFormat" withString:aFileFormat];
    return [self.client queueIntService:@"media" withAction:@"requestConversion"];
}

- (void)flagWithModerationFlag:(KalturaModerationFlag*)aModerationFlag
{
    [self.client.params addIfDefinedKey:@"moderationFlag" withObject:aModerationFlag];
    [self.client queueVoidService:@"media" withAction:@"flag"];
}

- (void)rejectWithEntryId:(NSString*)aEntryId
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client queueVoidService:@"media" withAction:@"reject"];
}

- (void)approveWithEntryId:(NSString*)aEntryId
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client queueVoidService:@"media" withAction:@"approve"];
}

- (KalturaModerationFlagListResponse*)listFlagsWithEntryId:(NSString*)aEntryId withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"media" withAction:@"listFlags" withExpectedType:@"KalturaModerationFlagListResponse"];
}

- (KalturaModerationFlagListResponse*)listFlagsWithEntryId:(NSString*)aEntryId
{
    return [self listFlagsWithEntryId:aEntryId withPager:nil];
}

- (void)anonymousRankWithEntryId:(NSString*)aEntryId withRank:(int)aRank
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"rank" withInt:aRank];
    [self.client queueVoidService:@"media" withAction:@"anonymousRank"];
}

- (KalturaBulkUpload*)bulkUploadAddWithFileData:(NSString*)aFileData withBulkUploadData:(KalturaBulkUploadJobData*)aBulkUploadData withBulkUploadEntryData:(KalturaBulkUploadEntryData*)aBulkUploadEntryData
{
    [self.client.params addIfDefinedKey:@"fileData" withFileName:aFileData];
    [self.client.params addIfDefinedKey:@"bulkUploadData" withObject:aBulkUploadData];
    [self.client.params addIfDefinedKey:@"bulkUploadEntryData" withObject:aBulkUploadEntryData];
    return [self.client queueObjectService:@"media" withAction:@"bulkUploadAdd" withExpectedType:@"KalturaBulkUpload"];
}

- (KalturaBulkUpload*)bulkUploadAddWithFileData:(NSString*)aFileData withBulkUploadData:(KalturaBulkUploadJobData*)aBulkUploadData
{
    return [self bulkUploadAddWithFileData:aFileData withBulkUploadData:aBulkUploadData withBulkUploadEntryData:nil];
}

- (KalturaBulkUpload*)bulkUploadAddWithFileData:(NSString*)aFileData
{
    return [self bulkUploadAddWithFileData:aFileData withBulkUploadData:nil];
}

@end

@implementation KalturaMixingService
- (KalturaMixEntry*)addWithMixEntry:(KalturaMixEntry*)aMixEntry
{
    [self.client.params addIfDefinedKey:@"mixEntry" withObject:aMixEntry];
    return [self.client queueObjectService:@"mixing" withAction:@"add" withExpectedType:@"KalturaMixEntry"];
}

- (KalturaMixEntry*)getWithEntryId:(NSString*)aEntryId withVersion:(int)aVersion
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"version" withInt:aVersion];
    return [self.client queueObjectService:@"mixing" withAction:@"get" withExpectedType:@"KalturaMixEntry"];
}

- (KalturaMixEntry*)getWithEntryId:(NSString*)aEntryId
{
    return [self getWithEntryId:aEntryId withVersion:KALTURA_UNDEF_INT];
}

- (KalturaMixEntry*)updateWithEntryId:(NSString*)aEntryId withMixEntry:(KalturaMixEntry*)aMixEntry
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"mixEntry" withObject:aMixEntry];
    return [self.client queueObjectService:@"mixing" withAction:@"update" withExpectedType:@"KalturaMixEntry"];
}

- (void)deleteWithEntryId:(NSString*)aEntryId
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client queueVoidService:@"mixing" withAction:@"delete"];
}

- (KalturaMixListResponse*)listWithFilter:(KalturaMixEntryFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"mixing" withAction:@"list" withExpectedType:@"KalturaMixListResponse"];
}

- (KalturaMixListResponse*)listWithFilter:(KalturaMixEntryFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaMixListResponse*)list
{
    return [self listWithFilter:nil];
}

- (int)countWithFilter:(KalturaMediaEntryFilter*)aFilter
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    return [self.client queueIntService:@"mixing" withAction:@"count"];
}

- (int)count
{
    return [self countWithFilter:nil];
}

- (KalturaMixEntry*)cloneWithEntryId:(NSString*)aEntryId
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    return [self.client queueObjectService:@"mixing" withAction:@"clone" withExpectedType:@"KalturaMixEntry"];
}

- (KalturaMixEntry*)appendMediaEntryWithMixEntryId:(NSString*)aMixEntryId withMediaEntryId:(NSString*)aMediaEntryId
{
    [self.client.params addIfDefinedKey:@"mixEntryId" withString:aMixEntryId];
    [self.client.params addIfDefinedKey:@"mediaEntryId" withString:aMediaEntryId];
    return [self.client queueObjectService:@"mixing" withAction:@"appendMediaEntry" withExpectedType:@"KalturaMixEntry"];
}

- (NSMutableArray*)getMixesByMediaIdWithMediaEntryId:(NSString*)aMediaEntryId
{
    [self.client.params addIfDefinedKey:@"mediaEntryId" withString:aMediaEntryId];
    return [self.client queueArrayService:@"mixing" withAction:@"getMixesByMediaId" withExpectedType:@"KalturaMixEntry"];
}

- (NSMutableArray*)getReadyMediaEntriesWithMixId:(NSString*)aMixId withVersion:(int)aVersion
{
    [self.client.params addIfDefinedKey:@"mixId" withString:aMixId];
    [self.client.params addIfDefinedKey:@"version" withInt:aVersion];
    return [self.client queueArrayService:@"mixing" withAction:@"getReadyMediaEntries" withExpectedType:@"KalturaMediaEntry"];
}

- (NSMutableArray*)getReadyMediaEntriesWithMixId:(NSString*)aMixId
{
    return [self getReadyMediaEntriesWithMixId:aMixId withVersion:KALTURA_UNDEF_INT];
}

- (void)anonymousRankWithEntryId:(NSString*)aEntryId withRank:(int)aRank
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"rank" withInt:aRank];
    [self.client queueVoidService:@"mixing" withAction:@"anonymousRank"];
}

@end

@implementation KalturaNotificationService
- (KalturaClientNotification*)getClientNotificationWithEntryId:(NSString*)aEntryId withType:(int)aType
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"type" withInt:aType];
    return [self.client queueObjectService:@"notification" withAction:@"getClientNotification" withExpectedType:@"KalturaClientNotification"];
}

@end

@implementation KalturaPartnerService
- (KalturaPartner*)registerWithPartner:(KalturaPartner*)aPartner withCmsPassword:(NSString*)aCmsPassword withTemplatePartnerId:(int)aTemplatePartnerId withSilent:(BOOL)aSilent
{
    [self.client.params addIfDefinedKey:@"partner" withObject:aPartner];
    [self.client.params addIfDefinedKey:@"cmsPassword" withString:aCmsPassword];
    [self.client.params addIfDefinedKey:@"templatePartnerId" withInt:aTemplatePartnerId];
    [self.client.params addIfDefinedKey:@"silent" withBool:aSilent];
    return [self.client queueObjectService:@"partner" withAction:@"register" withExpectedType:@"KalturaPartner"];
}

- (KalturaPartner*)registerWithPartner:(KalturaPartner*)aPartner withCmsPassword:(NSString*)aCmsPassword withTemplatePartnerId:(int)aTemplatePartnerId
{
    return [self registerWithPartner:aPartner withCmsPassword:aCmsPassword withTemplatePartnerId:aTemplatePartnerId withSilent:KALTURA_UNDEF_BOOL];
}

- (KalturaPartner*)registerWithPartner:(KalturaPartner*)aPartner withCmsPassword:(NSString*)aCmsPassword
{
    return [self registerWithPartner:aPartner withCmsPassword:aCmsPassword withTemplatePartnerId:KALTURA_UNDEF_INT];
}

- (KalturaPartner*)registerWithPartner:(KalturaPartner*)aPartner
{
    return [self registerWithPartner:aPartner withCmsPassword:nil];
}

- (KalturaPartner*)updateWithPartner:(KalturaPartner*)aPartner withAllowEmpty:(BOOL)aAllowEmpty
{
    [self.client.params addIfDefinedKey:@"partner" withObject:aPartner];
    [self.client.params addIfDefinedKey:@"allowEmpty" withBool:aAllowEmpty];
    return [self.client queueObjectService:@"partner" withAction:@"update" withExpectedType:@"KalturaPartner"];
}

- (KalturaPartner*)updateWithPartner:(KalturaPartner*)aPartner
{
    return [self updateWithPartner:aPartner withAllowEmpty:KALTURA_UNDEF_BOOL];
}

- (KalturaPartner*)getWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    return [self.client queueObjectService:@"partner" withAction:@"get" withExpectedType:@"KalturaPartner"];
}

- (KalturaPartner*)get
{
    return [self getWithId:KALTURA_UNDEF_INT];
}

- (KalturaPartner*)getSecretsWithPartnerId:(int)aPartnerId withAdminEmail:(NSString*)aAdminEmail withCmsPassword:(NSString*)aCmsPassword
{
    [self.client.params addIfDefinedKey:@"partnerId" withInt:aPartnerId];
    [self.client.params addIfDefinedKey:@"adminEmail" withString:aAdminEmail];
    [self.client.params addIfDefinedKey:@"cmsPassword" withString:aCmsPassword];
    return [self.client queueObjectService:@"partner" withAction:@"getSecrets" withExpectedType:@"KalturaPartner"];
}

- (KalturaPartner*)getInfo
{
    return [self.client queueObjectService:@"partner" withAction:@"getInfo" withExpectedType:@"KalturaPartner"];
}

- (KalturaPartnerUsage*)getUsageWithYear:(int)aYear withMonth:(int)aMonth withResolution:(NSString*)aResolution
{
    [self.client.params addIfDefinedKey:@"year" withInt:aYear];
    [self.client.params addIfDefinedKey:@"month" withInt:aMonth];
    [self.client.params addIfDefinedKey:@"resolution" withString:aResolution];
    return [self.client queueObjectService:@"partner" withAction:@"getUsage" withExpectedType:@"KalturaPartnerUsage"];
}

- (KalturaPartnerUsage*)getUsageWithYear:(int)aYear withMonth:(int)aMonth
{
    return [self getUsageWithYear:aYear withMonth:aMonth withResolution:nil];
}

- (KalturaPartnerUsage*)getUsageWithYear:(int)aYear
{
    return [self getUsageWithYear:aYear withMonth:KALTURA_UNDEF_INT];
}

- (KalturaPartnerUsage*)getUsage
{
    return [self getUsageWithYear:KALTURA_UNDEF_INT];
}

- (KalturaPartnerStatistics*)getStatistics
{
    return [self.client queueObjectService:@"partner" withAction:@"getStatistics" withExpectedType:@"KalturaPartnerStatistics"];
}

- (KalturaPartnerListResponse*)listPartnersForUserWithPartnerFilter:(KalturaPartnerFilter*)aPartnerFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"partnerFilter" withObject:aPartnerFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"partner" withAction:@"listPartnersForUser" withExpectedType:@"KalturaPartnerListResponse"];
}

- (KalturaPartnerListResponse*)listPartnersForUserWithPartnerFilter:(KalturaPartnerFilter*)aPartnerFilter
{
    return [self listPartnersForUserWithPartnerFilter:aPartnerFilter withPager:nil];
}

- (KalturaPartnerListResponse*)listPartnersForUser
{
    return [self listPartnersForUserWithPartnerFilter:nil];
}

- (KalturaPartnerListResponse*)listWithFilter:(KalturaPartnerFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"partner" withAction:@"list" withExpectedType:@"KalturaPartnerListResponse"];
}

- (KalturaPartnerListResponse*)listWithFilter:(KalturaPartnerFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaPartnerListResponse*)list
{
    return [self listWithFilter:nil];
}

- (KalturaFeatureStatusListResponse*)listFeatureStatus
{
    return [self.client queueObjectService:@"partner" withAction:@"listFeatureStatus" withExpectedType:@"KalturaFeatureStatusListResponse"];
}

- (int)countWithFilter:(KalturaPartnerFilter*)aFilter
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    return [self.client queueIntService:@"partner" withAction:@"count"];
}

- (int)count
{
    return [self countWithFilter:nil];
}

@end

@implementation KalturaPermissionItemService
- (KalturaPermissionItem*)addWithPermissionItem:(KalturaPermissionItem*)aPermissionItem
{
    [self.client.params addIfDefinedKey:@"permissionItem" withObject:aPermissionItem];
    return [self.client queueObjectService:@"permissionitem" withAction:@"add" withExpectedType:@"KalturaPermissionItem"];
}

- (KalturaPermissionItem*)getWithPermissionItemId:(int)aPermissionItemId
{
    [self.client.params addIfDefinedKey:@"permissionItemId" withInt:aPermissionItemId];
    return [self.client queueObjectService:@"permissionitem" withAction:@"get" withExpectedType:@"KalturaPermissionItem"];
}

- (KalturaPermissionItem*)updateWithPermissionItemId:(int)aPermissionItemId withPermissionItem:(KalturaPermissionItem*)aPermissionItem
{
    [self.client.params addIfDefinedKey:@"permissionItemId" withInt:aPermissionItemId];
    [self.client.params addIfDefinedKey:@"permissionItem" withObject:aPermissionItem];
    return [self.client queueObjectService:@"permissionitem" withAction:@"update" withExpectedType:@"KalturaPermissionItem"];
}

- (KalturaPermissionItem*)deleteWithPermissionItemId:(int)aPermissionItemId
{
    [self.client.params addIfDefinedKey:@"permissionItemId" withInt:aPermissionItemId];
    return [self.client queueObjectService:@"permissionitem" withAction:@"delete" withExpectedType:@"KalturaPermissionItem"];
}

- (KalturaPermissionItemListResponse*)listWithFilter:(KalturaPermissionItemFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"permissionitem" withAction:@"list" withExpectedType:@"KalturaPermissionItemListResponse"];
}

- (KalturaPermissionItemListResponse*)listWithFilter:(KalturaPermissionItemFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaPermissionItemListResponse*)list
{
    return [self listWithFilter:nil];
}

@end

@implementation KalturaPermissionService
- (KalturaPermission*)addWithPermission:(KalturaPermission*)aPermission
{
    [self.client.params addIfDefinedKey:@"permission" withObject:aPermission];
    return [self.client queueObjectService:@"permission" withAction:@"add" withExpectedType:@"KalturaPermission"];
}

- (KalturaPermission*)getWithPermissionName:(NSString*)aPermissionName
{
    [self.client.params addIfDefinedKey:@"permissionName" withString:aPermissionName];
    return [self.client queueObjectService:@"permission" withAction:@"get" withExpectedType:@"KalturaPermission"];
}

- (KalturaPermission*)updateWithPermissionName:(NSString*)aPermissionName withPermission:(KalturaPermission*)aPermission
{
    [self.client.params addIfDefinedKey:@"permissionName" withString:aPermissionName];
    [self.client.params addIfDefinedKey:@"permission" withObject:aPermission];
    return [self.client queueObjectService:@"permission" withAction:@"update" withExpectedType:@"KalturaPermission"];
}

- (KalturaPermission*)deleteWithPermissionName:(NSString*)aPermissionName
{
    [self.client.params addIfDefinedKey:@"permissionName" withString:aPermissionName];
    return [self.client queueObjectService:@"permission" withAction:@"delete" withExpectedType:@"KalturaPermission"];
}

- (KalturaPermissionListResponse*)listWithFilter:(KalturaPermissionFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"permission" withAction:@"list" withExpectedType:@"KalturaPermissionListResponse"];
}

- (KalturaPermissionListResponse*)listWithFilter:(KalturaPermissionFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaPermissionListResponse*)list
{
    return [self listWithFilter:nil];
}

- (NSString*)getCurrentPermissions
{
    return [self.client queueStringService:@"permission" withAction:@"getCurrentPermissions"];
}

@end

@implementation KalturaPlaylistService
- (KalturaPlaylist*)addWithPlaylist:(KalturaPlaylist*)aPlaylist withUpdateStats:(BOOL)aUpdateStats
{
    [self.client.params addIfDefinedKey:@"playlist" withObject:aPlaylist];
    [self.client.params addIfDefinedKey:@"updateStats" withBool:aUpdateStats];
    return [self.client queueObjectService:@"playlist" withAction:@"add" withExpectedType:@"KalturaPlaylist"];
}

- (KalturaPlaylist*)addWithPlaylist:(KalturaPlaylist*)aPlaylist
{
    return [self addWithPlaylist:aPlaylist withUpdateStats:KALTURA_UNDEF_BOOL];
}

- (KalturaPlaylist*)getWithId:(NSString*)aId withVersion:(int)aVersion
{
    [self.client.params addIfDefinedKey:@"id" withString:aId];
    [self.client.params addIfDefinedKey:@"version" withInt:aVersion];
    return [self.client queueObjectService:@"playlist" withAction:@"get" withExpectedType:@"KalturaPlaylist"];
}

- (KalturaPlaylist*)getWithId:(NSString*)aId
{
    return [self getWithId:aId withVersion:KALTURA_UNDEF_INT];
}

- (KalturaPlaylist*)updateWithId:(NSString*)aId withPlaylist:(KalturaPlaylist*)aPlaylist withUpdateStats:(BOOL)aUpdateStats
{
    [self.client.params addIfDefinedKey:@"id" withString:aId];
    [self.client.params addIfDefinedKey:@"playlist" withObject:aPlaylist];
    [self.client.params addIfDefinedKey:@"updateStats" withBool:aUpdateStats];
    return [self.client queueObjectService:@"playlist" withAction:@"update" withExpectedType:@"KalturaPlaylist"];
}

- (KalturaPlaylist*)updateWithId:(NSString*)aId withPlaylist:(KalturaPlaylist*)aPlaylist
{
    return [self updateWithId:aId withPlaylist:aPlaylist withUpdateStats:KALTURA_UNDEF_BOOL];
}

- (void)deleteWithId:(NSString*)aId
{
    [self.client.params addIfDefinedKey:@"id" withString:aId];
    [self.client queueVoidService:@"playlist" withAction:@"delete"];
}

- (KalturaPlaylist*)cloneWithId:(NSString*)aId withNewPlaylist:(KalturaPlaylist*)aNewPlaylist
{
    [self.client.params addIfDefinedKey:@"id" withString:aId];
    [self.client.params addIfDefinedKey:@"newPlaylist" withObject:aNewPlaylist];
    return [self.client queueObjectService:@"playlist" withAction:@"clone" withExpectedType:@"KalturaPlaylist"];
}

- (KalturaPlaylist*)cloneWithId:(NSString*)aId
{
    return [self cloneWithId:aId withNewPlaylist:nil];
}

- (KalturaPlaylistListResponse*)listWithFilter:(KalturaPlaylistFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"playlist" withAction:@"list" withExpectedType:@"KalturaPlaylistListResponse"];
}

- (KalturaPlaylistListResponse*)listWithFilter:(KalturaPlaylistFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaPlaylistListResponse*)list
{
    return [self listWithFilter:nil];
}

- (NSMutableArray*)executeWithId:(NSString*)aId withDetailed:(NSString*)aDetailed withPlaylistContext:(KalturaContext*)aPlaylistContext withFilter:(KalturaMediaEntryFilterForPlaylist*)aFilter
{
    [self.client.params addIfDefinedKey:@"id" withString:aId];
    [self.client.params addIfDefinedKey:@"detailed" withString:aDetailed];
    [self.client.params addIfDefinedKey:@"playlistContext" withObject:aPlaylistContext];
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    return [self.client queueArrayService:@"playlist" withAction:@"execute" withExpectedType:@"KalturaBaseEntry"];
}

- (NSMutableArray*)executeWithId:(NSString*)aId withDetailed:(NSString*)aDetailed withPlaylistContext:(KalturaContext*)aPlaylistContext
{
    return [self executeWithId:aId withDetailed:aDetailed withPlaylistContext:aPlaylistContext withFilter:nil];
}

- (NSMutableArray*)executeWithId:(NSString*)aId withDetailed:(NSString*)aDetailed
{
    return [self executeWithId:aId withDetailed:aDetailed withPlaylistContext:nil];
}

- (NSMutableArray*)executeWithId:(NSString*)aId
{
    return [self executeWithId:aId withDetailed:nil];
}

- (NSMutableArray*)executeFromContentWithPlaylistType:(int)aPlaylistType withPlaylistContent:(NSString*)aPlaylistContent withDetailed:(NSString*)aDetailed
{
    [self.client.params addIfDefinedKey:@"playlistType" withInt:aPlaylistType];
    [self.client.params addIfDefinedKey:@"playlistContent" withString:aPlaylistContent];
    [self.client.params addIfDefinedKey:@"detailed" withString:aDetailed];
    return [self.client queueArrayService:@"playlist" withAction:@"executeFromContent" withExpectedType:@"KalturaBaseEntry"];
}

- (NSMutableArray*)executeFromContentWithPlaylistType:(int)aPlaylistType withPlaylistContent:(NSString*)aPlaylistContent
{
    return [self executeFromContentWithPlaylistType:aPlaylistType withPlaylistContent:aPlaylistContent withDetailed:nil];
}

- (NSMutableArray*)executeFromFiltersWithFilters:(NSArray*)aFilters withTotalResults:(int)aTotalResults withDetailed:(NSString*)aDetailed
{
    [self.client.params addIfDefinedKey:@"filters" withArray:aFilters];
    [self.client.params addIfDefinedKey:@"totalResults" withInt:aTotalResults];
    [self.client.params addIfDefinedKey:@"detailed" withString:aDetailed];
    return [self.client queueArrayService:@"playlist" withAction:@"executeFromFilters" withExpectedType:@"KalturaBaseEntry"];
}

- (NSMutableArray*)executeFromFiltersWithFilters:(NSArray*)aFilters withTotalResults:(int)aTotalResults
{
    return [self executeFromFiltersWithFilters:aFilters withTotalResults:aTotalResults withDetailed:nil];
}

- (KalturaPlaylist*)getStatsFromContentWithPlaylistType:(int)aPlaylistType withPlaylistContent:(NSString*)aPlaylistContent
{
    [self.client.params addIfDefinedKey:@"playlistType" withInt:aPlaylistType];
    [self.client.params addIfDefinedKey:@"playlistContent" withString:aPlaylistContent];
    return [self.client queueObjectService:@"playlist" withAction:@"getStatsFromContent" withExpectedType:@"KalturaPlaylist"];
}

@end

@implementation KalturaReportService
- (NSMutableArray*)getGraphsWithReportType:(int)aReportType withReportInputFilter:(KalturaReportInputFilter*)aReportInputFilter withDimension:(NSString*)aDimension withObjectIds:(NSString*)aObjectIds
{
    [self.client.params addIfDefinedKey:@"reportType" withInt:aReportType];
    [self.client.params addIfDefinedKey:@"reportInputFilter" withObject:aReportInputFilter];
    [self.client.params addIfDefinedKey:@"dimension" withString:aDimension];
    [self.client.params addIfDefinedKey:@"objectIds" withString:aObjectIds];
    return [self.client queueArrayService:@"report" withAction:@"getGraphs" withExpectedType:@"KalturaReportGraph"];
}

- (NSMutableArray*)getGraphsWithReportType:(int)aReportType withReportInputFilter:(KalturaReportInputFilter*)aReportInputFilter withDimension:(NSString*)aDimension
{
    return [self getGraphsWithReportType:aReportType withReportInputFilter:aReportInputFilter withDimension:aDimension withObjectIds:nil];
}

- (NSMutableArray*)getGraphsWithReportType:(int)aReportType withReportInputFilter:(KalturaReportInputFilter*)aReportInputFilter
{
    return [self getGraphsWithReportType:aReportType withReportInputFilter:aReportInputFilter withDimension:nil];
}

- (KalturaReportTotal*)getTotalWithReportType:(int)aReportType withReportInputFilter:(KalturaReportInputFilter*)aReportInputFilter withObjectIds:(NSString*)aObjectIds
{
    [self.client.params addIfDefinedKey:@"reportType" withInt:aReportType];
    [self.client.params addIfDefinedKey:@"reportInputFilter" withObject:aReportInputFilter];
    [self.client.params addIfDefinedKey:@"objectIds" withString:aObjectIds];
    return [self.client queueObjectService:@"report" withAction:@"getTotal" withExpectedType:@"KalturaReportTotal"];
}

- (KalturaReportTotal*)getTotalWithReportType:(int)aReportType withReportInputFilter:(KalturaReportInputFilter*)aReportInputFilter
{
    return [self getTotalWithReportType:aReportType withReportInputFilter:aReportInputFilter withObjectIds:nil];
}

- (NSMutableArray*)getBaseTotalWithReportType:(int)aReportType withReportInputFilter:(KalturaReportInputFilter*)aReportInputFilter withObjectIds:(NSString*)aObjectIds
{
    [self.client.params addIfDefinedKey:@"reportType" withInt:aReportType];
    [self.client.params addIfDefinedKey:@"reportInputFilter" withObject:aReportInputFilter];
    [self.client.params addIfDefinedKey:@"objectIds" withString:aObjectIds];
    return [self.client queueArrayService:@"report" withAction:@"getBaseTotal" withExpectedType:@"KalturaReportBaseTotal"];
}

- (NSMutableArray*)getBaseTotalWithReportType:(int)aReportType withReportInputFilter:(KalturaReportInputFilter*)aReportInputFilter
{
    return [self getBaseTotalWithReportType:aReportType withReportInputFilter:aReportInputFilter withObjectIds:nil];
}

- (KalturaReportTable*)getTableWithReportType:(int)aReportType withReportInputFilter:(KalturaReportInputFilter*)aReportInputFilter withPager:(KalturaFilterPager*)aPager withOrder:(NSString*)aOrder withObjectIds:(NSString*)aObjectIds
{
    [self.client.params addIfDefinedKey:@"reportType" withInt:aReportType];
    [self.client.params addIfDefinedKey:@"reportInputFilter" withObject:aReportInputFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    [self.client.params addIfDefinedKey:@"order" withString:aOrder];
    [self.client.params addIfDefinedKey:@"objectIds" withString:aObjectIds];
    return [self.client queueObjectService:@"report" withAction:@"getTable" withExpectedType:@"KalturaReportTable"];
}

- (KalturaReportTable*)getTableWithReportType:(int)aReportType withReportInputFilter:(KalturaReportInputFilter*)aReportInputFilter withPager:(KalturaFilterPager*)aPager withOrder:(NSString*)aOrder
{
    return [self getTableWithReportType:aReportType withReportInputFilter:aReportInputFilter withPager:aPager withOrder:aOrder withObjectIds:nil];
}

- (KalturaReportTable*)getTableWithReportType:(int)aReportType withReportInputFilter:(KalturaReportInputFilter*)aReportInputFilter withPager:(KalturaFilterPager*)aPager
{
    return [self getTableWithReportType:aReportType withReportInputFilter:aReportInputFilter withPager:aPager withOrder:nil];
}

- (NSString*)getUrlForReportAsCsvWithReportTitle:(NSString*)aReportTitle withReportText:(NSString*)aReportText withHeaders:(NSString*)aHeaders withReportType:(int)aReportType withReportInputFilter:(KalturaReportInputFilter*)aReportInputFilter withDimension:(NSString*)aDimension withPager:(KalturaFilterPager*)aPager withOrder:(NSString*)aOrder withObjectIds:(NSString*)aObjectIds
{
    [self.client.params addIfDefinedKey:@"reportTitle" withString:aReportTitle];
    [self.client.params addIfDefinedKey:@"reportText" withString:aReportText];
    [self.client.params addIfDefinedKey:@"headers" withString:aHeaders];
    [self.client.params addIfDefinedKey:@"reportType" withInt:aReportType];
    [self.client.params addIfDefinedKey:@"reportInputFilter" withObject:aReportInputFilter];
    [self.client.params addIfDefinedKey:@"dimension" withString:aDimension];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    [self.client.params addIfDefinedKey:@"order" withString:aOrder];
    [self.client.params addIfDefinedKey:@"objectIds" withString:aObjectIds];
    return [self.client queueStringService:@"report" withAction:@"getUrlForReportAsCsv"];
}

- (NSString*)getUrlForReportAsCsvWithReportTitle:(NSString*)aReportTitle withReportText:(NSString*)aReportText withHeaders:(NSString*)aHeaders withReportType:(int)aReportType withReportInputFilter:(KalturaReportInputFilter*)aReportInputFilter withDimension:(NSString*)aDimension withPager:(KalturaFilterPager*)aPager withOrder:(NSString*)aOrder
{
    return [self getUrlForReportAsCsvWithReportTitle:aReportTitle withReportText:aReportText withHeaders:aHeaders withReportType:aReportType withReportInputFilter:aReportInputFilter withDimension:aDimension withPager:aPager withOrder:aOrder withObjectIds:nil];
}

- (NSString*)getUrlForReportAsCsvWithReportTitle:(NSString*)aReportTitle withReportText:(NSString*)aReportText withHeaders:(NSString*)aHeaders withReportType:(int)aReportType withReportInputFilter:(KalturaReportInputFilter*)aReportInputFilter withDimension:(NSString*)aDimension withPager:(KalturaFilterPager*)aPager
{
    return [self getUrlForReportAsCsvWithReportTitle:aReportTitle withReportText:aReportText withHeaders:aHeaders withReportType:aReportType withReportInputFilter:aReportInputFilter withDimension:aDimension withPager:aPager withOrder:nil];
}

- (NSString*)getUrlForReportAsCsvWithReportTitle:(NSString*)aReportTitle withReportText:(NSString*)aReportText withHeaders:(NSString*)aHeaders withReportType:(int)aReportType withReportInputFilter:(KalturaReportInputFilter*)aReportInputFilter withDimension:(NSString*)aDimension
{
    return [self getUrlForReportAsCsvWithReportTitle:aReportTitle withReportText:aReportText withHeaders:aHeaders withReportType:aReportType withReportInputFilter:aReportInputFilter withDimension:aDimension withPager:nil];
}

- (NSString*)getUrlForReportAsCsvWithReportTitle:(NSString*)aReportTitle withReportText:(NSString*)aReportText withHeaders:(NSString*)aHeaders withReportType:(int)aReportType withReportInputFilter:(KalturaReportInputFilter*)aReportInputFilter
{
    return [self getUrlForReportAsCsvWithReportTitle:aReportTitle withReportText:aReportText withHeaders:aHeaders withReportType:aReportType withReportInputFilter:aReportInputFilter withDimension:nil];
}

- (KalturaReportResponse*)executeWithId:(int)aId withParams:(NSArray*)aParams
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client.params addIfDefinedKey:@"params" withArray:aParams];
    return [self.client queueObjectService:@"report" withAction:@"execute" withExpectedType:@"KalturaReportResponse"];
}

- (KalturaReportResponse*)executeWithId:(int)aId
{
    return [self executeWithId:aId withParams:nil];
}

- (NSString*)getCsvWithId:(int)aId withParams:(NSArray*)aParams
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client.params addIfDefinedKey:@"params" withArray:aParams];
    return [self.client queueServeService:@"report" withAction:@"getCsv"];
}

- (NSString*)getCsvWithId:(int)aId
{
    return [self getCsvWithId:aId withParams:nil];
}

- (NSString*)getCsvFromStringParamsWithId:(int)aId withParams:(NSString*)aParams
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client.params addIfDefinedKey:@"params" withString:aParams];
    return [self.client queueServeService:@"report" withAction:@"getCsvFromStringParams"];
}

- (NSString*)getCsvFromStringParamsWithId:(int)aId
{
    return [self getCsvFromStringParamsWithId:aId withParams:nil];
}

@end

@implementation KalturaSchemaService
- (NSString*)serveWithType:(NSString*)aType
{
    [self.client.params addIfDefinedKey:@"type" withString:aType];
    return [self.client queueServeService:@"schema" withAction:@"serve"];
}

@end

@implementation KalturaSearchService
- (KalturaSearchResultResponse*)searchWithSearch:(KalturaSearch*)aSearch withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"search" withObject:aSearch];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"search" withAction:@"search" withExpectedType:@"KalturaSearchResultResponse"];
}

- (KalturaSearchResultResponse*)searchWithSearch:(KalturaSearch*)aSearch
{
    return [self searchWithSearch:aSearch withPager:nil];
}

- (KalturaSearchResult*)getMediaInfoWithSearchResult:(KalturaSearchResult*)aSearchResult
{
    [self.client.params addIfDefinedKey:@"searchResult" withObject:aSearchResult];
    return [self.client queueObjectService:@"search" withAction:@"getMediaInfo" withExpectedType:@"KalturaSearchResult"];
}

- (KalturaSearchResult*)searchUrlWithMediaType:(int)aMediaType withUrl:(NSString*)aUrl
{
    [self.client.params addIfDefinedKey:@"mediaType" withInt:aMediaType];
    [self.client.params addIfDefinedKey:@"url" withString:aUrl];
    return [self.client queueObjectService:@"search" withAction:@"searchUrl" withExpectedType:@"KalturaSearchResult"];
}

- (KalturaSearchAuthData*)externalLoginWithSearchSource:(int)aSearchSource withUserName:(NSString*)aUserName withPassword:(NSString*)aPassword
{
    [self.client.params addIfDefinedKey:@"searchSource" withInt:aSearchSource];
    [self.client.params addIfDefinedKey:@"userName" withString:aUserName];
    [self.client.params addIfDefinedKey:@"password" withString:aPassword];
    return [self.client queueObjectService:@"search" withAction:@"externalLogin" withExpectedType:@"KalturaSearchAuthData"];
}

@end

@implementation KalturaSessionService
- (NSString*)startWithSecret:(NSString*)aSecret withUserId:(NSString*)aUserId withType:(int)aType withPartnerId:(int)aPartnerId withExpiry:(int)aExpiry withPrivileges:(NSString*)aPrivileges
{
    [self.client.params addIfDefinedKey:@"secret" withString:aSecret];
    [self.client.params addIfDefinedKey:@"userId" withString:aUserId];
    [self.client.params addIfDefinedKey:@"type" withInt:aType];
    [self.client.params addIfDefinedKey:@"partnerId" withInt:aPartnerId];
    [self.client.params addIfDefinedKey:@"expiry" withInt:aExpiry];
    [self.client.params addIfDefinedKey:@"privileges" withString:aPrivileges];
    return [self.client queueStringService:@"session" withAction:@"start"];
}

- (NSString*)startWithSecret:(NSString*)aSecret withUserId:(NSString*)aUserId withType:(int)aType withPartnerId:(int)aPartnerId withExpiry:(int)aExpiry
{
    return [self startWithSecret:aSecret withUserId:aUserId withType:aType withPartnerId:aPartnerId withExpiry:aExpiry withPrivileges:nil];
}

- (NSString*)startWithSecret:(NSString*)aSecret withUserId:(NSString*)aUserId withType:(int)aType withPartnerId:(int)aPartnerId
{
    return [self startWithSecret:aSecret withUserId:aUserId withType:aType withPartnerId:aPartnerId withExpiry:KALTURA_UNDEF_INT];
}

- (NSString*)startWithSecret:(NSString*)aSecret withUserId:(NSString*)aUserId withType:(int)aType
{
    return [self startWithSecret:aSecret withUserId:aUserId withType:aType withPartnerId:KALTURA_UNDEF_INT];
}

- (NSString*)startWithSecret:(NSString*)aSecret withUserId:(NSString*)aUserId
{
    return [self startWithSecret:aSecret withUserId:aUserId withType:KALTURA_UNDEF_INT];
}

- (NSString*)startWithSecret:(NSString*)aSecret
{
    return [self startWithSecret:aSecret withUserId:nil];
}

- (void)end
{
    [self.client queueVoidService:@"session" withAction:@"end"];
}

- (NSString*)impersonateWithSecret:(NSString*)aSecret withImpersonatedPartnerId:(int)aImpersonatedPartnerId withUserId:(NSString*)aUserId withType:(int)aType withPartnerId:(int)aPartnerId withExpiry:(int)aExpiry withPrivileges:(NSString*)aPrivileges
{
    [self.client.params addIfDefinedKey:@"secret" withString:aSecret];
    [self.client.params addIfDefinedKey:@"impersonatedPartnerId" withInt:aImpersonatedPartnerId];
    [self.client.params addIfDefinedKey:@"userId" withString:aUserId];
    [self.client.params addIfDefinedKey:@"type" withInt:aType];
    [self.client.params addIfDefinedKey:@"partnerId" withInt:aPartnerId];
    [self.client.params addIfDefinedKey:@"expiry" withInt:aExpiry];
    [self.client.params addIfDefinedKey:@"privileges" withString:aPrivileges];
    return [self.client queueStringService:@"session" withAction:@"impersonate"];
}

- (NSString*)impersonateWithSecret:(NSString*)aSecret withImpersonatedPartnerId:(int)aImpersonatedPartnerId withUserId:(NSString*)aUserId withType:(int)aType withPartnerId:(int)aPartnerId withExpiry:(int)aExpiry
{
    return [self impersonateWithSecret:aSecret withImpersonatedPartnerId:aImpersonatedPartnerId withUserId:aUserId withType:aType withPartnerId:aPartnerId withExpiry:aExpiry withPrivileges:nil];
}

- (NSString*)impersonateWithSecret:(NSString*)aSecret withImpersonatedPartnerId:(int)aImpersonatedPartnerId withUserId:(NSString*)aUserId withType:(int)aType withPartnerId:(int)aPartnerId
{
    return [self impersonateWithSecret:aSecret withImpersonatedPartnerId:aImpersonatedPartnerId withUserId:aUserId withType:aType withPartnerId:aPartnerId withExpiry:KALTURA_UNDEF_INT];
}

- (NSString*)impersonateWithSecret:(NSString*)aSecret withImpersonatedPartnerId:(int)aImpersonatedPartnerId withUserId:(NSString*)aUserId withType:(int)aType
{
    return [self impersonateWithSecret:aSecret withImpersonatedPartnerId:aImpersonatedPartnerId withUserId:aUserId withType:aType withPartnerId:KALTURA_UNDEF_INT];
}

- (NSString*)impersonateWithSecret:(NSString*)aSecret withImpersonatedPartnerId:(int)aImpersonatedPartnerId withUserId:(NSString*)aUserId
{
    return [self impersonateWithSecret:aSecret withImpersonatedPartnerId:aImpersonatedPartnerId withUserId:aUserId withType:KALTURA_UNDEF_INT];
}

- (NSString*)impersonateWithSecret:(NSString*)aSecret withImpersonatedPartnerId:(int)aImpersonatedPartnerId
{
    return [self impersonateWithSecret:aSecret withImpersonatedPartnerId:aImpersonatedPartnerId withUserId:nil];
}

- (KalturaSessionInfo*)impersonateByKsWithSession:(NSString*)aSession withType:(int)aType withExpiry:(int)aExpiry withPrivileges:(NSString*)aPrivileges
{
    [self.client.params addIfDefinedKey:@"session" withString:aSession];
    [self.client.params addIfDefinedKey:@"type" withInt:aType];
    [self.client.params addIfDefinedKey:@"expiry" withInt:aExpiry];
    [self.client.params addIfDefinedKey:@"privileges" withString:aPrivileges];
    return [self.client queueObjectService:@"session" withAction:@"impersonateByKs" withExpectedType:@"KalturaSessionInfo"];
}

- (KalturaSessionInfo*)impersonateByKsWithSession:(NSString*)aSession withType:(int)aType withExpiry:(int)aExpiry
{
    return [self impersonateByKsWithSession:aSession withType:aType withExpiry:aExpiry withPrivileges:nil];
}

- (KalturaSessionInfo*)impersonateByKsWithSession:(NSString*)aSession withType:(int)aType
{
    return [self impersonateByKsWithSession:aSession withType:aType withExpiry:KALTURA_UNDEF_INT];
}

- (KalturaSessionInfo*)impersonateByKsWithSession:(NSString*)aSession
{
    return [self impersonateByKsWithSession:aSession withType:KALTURA_UNDEF_INT];
}

- (KalturaSessionInfo*)getWithSession:(NSString*)aSession
{
    [self.client.params addIfDefinedKey:@"session" withString:aSession];
    return [self.client queueObjectService:@"session" withAction:@"get" withExpectedType:@"KalturaSessionInfo"];
}

- (KalturaSessionInfo*)get
{
    return [self getWithSession:nil];
}

- (KalturaStartWidgetSessionResponse*)startWidgetSessionWithWidgetId:(NSString*)aWidgetId withExpiry:(int)aExpiry
{
    [self.client.params addIfDefinedKey:@"widgetId" withString:aWidgetId];
    [self.client.params addIfDefinedKey:@"expiry" withInt:aExpiry];
    return [self.client queueObjectService:@"session" withAction:@"startWidgetSession" withExpectedType:@"KalturaStartWidgetSessionResponse"];
}

- (KalturaStartWidgetSessionResponse*)startWidgetSessionWithWidgetId:(NSString*)aWidgetId
{
    return [self startWidgetSessionWithWidgetId:aWidgetId withExpiry:KALTURA_UNDEF_INT];
}

@end

@implementation KalturaStatsService
- (BOOL)collectWithEvent:(KalturaStatsEvent*)aEvent
{
    [self.client.params addIfDefinedKey:@"event" withObject:aEvent];
    return [self.client queueBoolService:@"stats" withAction:@"collect"];
}

- (void)kmcCollectWithKmcEvent:(KalturaStatsKmcEvent*)aKmcEvent
{
    [self.client.params addIfDefinedKey:@"kmcEvent" withObject:aKmcEvent];
    [self.client queueVoidService:@"stats" withAction:@"kmcCollect"];
}

- (KalturaCEError*)reportKceErrorWithKalturaCEError:(KalturaCEError*)aKalturaCEError
{
    [self.client.params addIfDefinedKey:@"kalturaCEError" withObject:aKalturaCEError];
    return [self.client queueObjectService:@"stats" withAction:@"reportKceError" withExpectedType:@"KalturaCEError"];
}

- (void)reportErrorWithErrorCode:(NSString*)aErrorCode withErrorMessage:(NSString*)aErrorMessage
{
    [self.client.params addIfDefinedKey:@"errorCode" withString:aErrorCode];
    [self.client.params addIfDefinedKey:@"errorMessage" withString:aErrorMessage];
    [self.client queueVoidService:@"stats" withAction:@"reportError"];
}

@end

@implementation KalturaStorageProfileService
- (KalturaStorageProfile*)addWithStorageProfile:(KalturaStorageProfile*)aStorageProfile
{
    [self.client.params addIfDefinedKey:@"storageProfile" withObject:aStorageProfile];
    return [self.client queueObjectService:@"storageprofile" withAction:@"add" withExpectedType:@"KalturaStorageProfile"];
}

- (void)updateStatusWithStorageId:(int)aStorageId withStatus:(int)aStatus
{
    [self.client.params addIfDefinedKey:@"storageId" withInt:aStorageId];
    [self.client.params addIfDefinedKey:@"status" withInt:aStatus];
    [self.client queueVoidService:@"storageprofile" withAction:@"updateStatus"];
}

- (KalturaStorageProfile*)getWithStorageProfileId:(int)aStorageProfileId
{
    [self.client.params addIfDefinedKey:@"storageProfileId" withInt:aStorageProfileId];
    return [self.client queueObjectService:@"storageprofile" withAction:@"get" withExpectedType:@"KalturaStorageProfile"];
}

- (KalturaStorageProfile*)updateWithStorageProfileId:(int)aStorageProfileId withStorageProfile:(KalturaStorageProfile*)aStorageProfile
{
    [self.client.params addIfDefinedKey:@"storageProfileId" withInt:aStorageProfileId];
    [self.client.params addIfDefinedKey:@"storageProfile" withObject:aStorageProfile];
    return [self.client queueObjectService:@"storageprofile" withAction:@"update" withExpectedType:@"KalturaStorageProfile"];
}

- (KalturaStorageProfileListResponse*)listWithFilter:(KalturaStorageProfileFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"storageprofile" withAction:@"list" withExpectedType:@"KalturaStorageProfileListResponse"];
}

- (KalturaStorageProfileListResponse*)listWithFilter:(KalturaStorageProfileFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaStorageProfileListResponse*)list
{
    return [self listWithFilter:nil];
}

@end

@implementation KalturaSyndicationFeedService
- (KalturaBaseSyndicationFeed*)addWithSyndicationFeed:(KalturaBaseSyndicationFeed*)aSyndicationFeed
{
    [self.client.params addIfDefinedKey:@"syndicationFeed" withObject:aSyndicationFeed];
    return [self.client queueObjectService:@"syndicationfeed" withAction:@"add" withExpectedType:@"KalturaBaseSyndicationFeed"];
}

- (KalturaBaseSyndicationFeed*)getWithId:(NSString*)aId
{
    [self.client.params addIfDefinedKey:@"id" withString:aId];
    return [self.client queueObjectService:@"syndicationfeed" withAction:@"get" withExpectedType:@"KalturaBaseSyndicationFeed"];
}

- (KalturaBaseSyndicationFeed*)updateWithId:(NSString*)aId withSyndicationFeed:(KalturaBaseSyndicationFeed*)aSyndicationFeed
{
    [self.client.params addIfDefinedKey:@"id" withString:aId];
    [self.client.params addIfDefinedKey:@"syndicationFeed" withObject:aSyndicationFeed];
    return [self.client queueObjectService:@"syndicationfeed" withAction:@"update" withExpectedType:@"KalturaBaseSyndicationFeed"];
}

- (void)deleteWithId:(NSString*)aId
{
    [self.client.params addIfDefinedKey:@"id" withString:aId];
    [self.client queueVoidService:@"syndicationfeed" withAction:@"delete"];
}

- (KalturaBaseSyndicationFeedListResponse*)listWithFilter:(KalturaBaseSyndicationFeedFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"syndicationfeed" withAction:@"list" withExpectedType:@"KalturaBaseSyndicationFeedListResponse"];
}

- (KalturaBaseSyndicationFeedListResponse*)listWithFilter:(KalturaBaseSyndicationFeedFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaBaseSyndicationFeedListResponse*)list
{
    return [self listWithFilter:nil];
}

- (KalturaSyndicationFeedEntryCount*)getEntryCountWithFeedId:(NSString*)aFeedId
{
    [self.client.params addIfDefinedKey:@"feedId" withString:aFeedId];
    return [self.client queueObjectService:@"syndicationfeed" withAction:@"getEntryCount" withExpectedType:@"KalturaSyndicationFeedEntryCount"];
}

- (NSString*)requestConversionWithFeedId:(NSString*)aFeedId
{
    [self.client.params addIfDefinedKey:@"feedId" withString:aFeedId];
    return [self.client queueStringService:@"syndicationfeed" withAction:@"requestConversion"];
}

@end

@implementation KalturaSystemService
- (BOOL)ping
{
    return [self.client queueBoolService:@"system" withAction:@"ping"];
}

- (int)getTime
{
    return [self.client queueIntService:@"system" withAction:@"getTime"];
}

@end

@implementation KalturaThumbAssetService
- (KalturaThumbAsset*)addWithEntryId:(NSString*)aEntryId withThumbAsset:(KalturaThumbAsset*)aThumbAsset
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"thumbAsset" withObject:aThumbAsset];
    return [self.client queueObjectService:@"thumbasset" withAction:@"add" withExpectedType:@"KalturaThumbAsset"];
}

- (KalturaThumbAsset*)setContentWithId:(NSString*)aId withContentResource:(KalturaContentResource*)aContentResource
{
    [self.client.params addIfDefinedKey:@"id" withString:aId];
    [self.client.params addIfDefinedKey:@"contentResource" withObject:aContentResource];
    return [self.client queueObjectService:@"thumbasset" withAction:@"setContent" withExpectedType:@"KalturaThumbAsset"];
}

- (KalturaThumbAsset*)updateWithId:(NSString*)aId withThumbAsset:(KalturaThumbAsset*)aThumbAsset
{
    [self.client.params addIfDefinedKey:@"id" withString:aId];
    [self.client.params addIfDefinedKey:@"thumbAsset" withObject:aThumbAsset];
    return [self.client queueObjectService:@"thumbasset" withAction:@"update" withExpectedType:@"KalturaThumbAsset"];
}

- (NSString*)serveByEntryIdWithEntryId:(NSString*)aEntryId withThumbParamId:(int)aThumbParamId
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"thumbParamId" withInt:aThumbParamId];
    return [self.client queueServeService:@"thumbasset" withAction:@"serveByEntryId"];
}

- (NSString*)serveByEntryIdWithEntryId:(NSString*)aEntryId
{
    return [self serveByEntryIdWithEntryId:aEntryId withThumbParamId:KALTURA_UNDEF_INT];
}

- (NSString*)serveWithThumbAssetId:(NSString*)aThumbAssetId withVersion:(int)aVersion withThumbParams:(KalturaThumbParams*)aThumbParams
{
    [self.client.params addIfDefinedKey:@"thumbAssetId" withString:aThumbAssetId];
    [self.client.params addIfDefinedKey:@"version" withInt:aVersion];
    [self.client.params addIfDefinedKey:@"thumbParams" withObject:aThumbParams];
    return [self.client queueServeService:@"thumbasset" withAction:@"serve"];
}

- (NSString*)serveWithThumbAssetId:(NSString*)aThumbAssetId withVersion:(int)aVersion
{
    return [self serveWithThumbAssetId:aThumbAssetId withVersion:aVersion withThumbParams:nil];
}

- (NSString*)serveWithThumbAssetId:(NSString*)aThumbAssetId
{
    return [self serveWithThumbAssetId:aThumbAssetId withVersion:KALTURA_UNDEF_INT];
}

- (void)setAsDefaultWithThumbAssetId:(NSString*)aThumbAssetId
{
    [self.client.params addIfDefinedKey:@"thumbAssetId" withString:aThumbAssetId];
    [self.client queueVoidService:@"thumbasset" withAction:@"setAsDefault"];
}

- (KalturaThumbAsset*)generateByEntryIdWithEntryId:(NSString*)aEntryId withDestThumbParamsId:(int)aDestThumbParamsId
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"destThumbParamsId" withInt:aDestThumbParamsId];
    return [self.client queueObjectService:@"thumbasset" withAction:@"generateByEntryId" withExpectedType:@"KalturaThumbAsset"];
}

- (KalturaThumbAsset*)generateWithEntryId:(NSString*)aEntryId withThumbParams:(KalturaThumbParams*)aThumbParams withSourceAssetId:(NSString*)aSourceAssetId
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"thumbParams" withObject:aThumbParams];
    [self.client.params addIfDefinedKey:@"sourceAssetId" withString:aSourceAssetId];
    return [self.client queueObjectService:@"thumbasset" withAction:@"generate" withExpectedType:@"KalturaThumbAsset"];
}

- (KalturaThumbAsset*)generateWithEntryId:(NSString*)aEntryId withThumbParams:(KalturaThumbParams*)aThumbParams
{
    return [self generateWithEntryId:aEntryId withThumbParams:aThumbParams withSourceAssetId:nil];
}

- (KalturaThumbAsset*)regenerateWithThumbAssetId:(NSString*)aThumbAssetId
{
    [self.client.params addIfDefinedKey:@"thumbAssetId" withString:aThumbAssetId];
    return [self.client queueObjectService:@"thumbasset" withAction:@"regenerate" withExpectedType:@"KalturaThumbAsset"];
}

- (KalturaThumbAsset*)getWithThumbAssetId:(NSString*)aThumbAssetId
{
    [self.client.params addIfDefinedKey:@"thumbAssetId" withString:aThumbAssetId];
    return [self.client queueObjectService:@"thumbasset" withAction:@"get" withExpectedType:@"KalturaThumbAsset"];
}

- (NSMutableArray*)getByEntryIdWithEntryId:(NSString*)aEntryId
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    return [self.client queueArrayService:@"thumbasset" withAction:@"getByEntryId" withExpectedType:@"KalturaThumbAsset"];
}

- (KalturaThumbAssetListResponse*)listWithFilter:(KalturaAssetFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"thumbasset" withAction:@"list" withExpectedType:@"KalturaThumbAssetListResponse"];
}

- (KalturaThumbAssetListResponse*)listWithFilter:(KalturaAssetFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaThumbAssetListResponse*)list
{
    return [self listWithFilter:nil];
}

- (KalturaThumbAsset*)addFromUrlWithEntryId:(NSString*)aEntryId withUrl:(NSString*)aUrl
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"url" withString:aUrl];
    return [self.client queueObjectService:@"thumbasset" withAction:@"addFromUrl" withExpectedType:@"KalturaThumbAsset"];
}

- (KalturaThumbAsset*)addFromImageWithEntryId:(NSString*)aEntryId withFileData:(NSString*)aFileData
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"fileData" withFileName:aFileData];
    return [self.client queueObjectService:@"thumbasset" withAction:@"addFromImage" withExpectedType:@"KalturaThumbAsset"];
}

- (void)deleteWithThumbAssetId:(NSString*)aThumbAssetId
{
    [self.client.params addIfDefinedKey:@"thumbAssetId" withString:aThumbAssetId];
    [self.client queueVoidService:@"thumbasset" withAction:@"delete"];
}

- (NSString*)getUrlWithId:(NSString*)aId withStorageId:(int)aStorageId withThumbParams:(KalturaThumbParams*)aThumbParams
{
    [self.client.params addIfDefinedKey:@"id" withString:aId];
    [self.client.params addIfDefinedKey:@"storageId" withInt:aStorageId];
    [self.client.params addIfDefinedKey:@"thumbParams" withObject:aThumbParams];
    return [self.client queueStringService:@"thumbasset" withAction:@"getUrl"];
}

- (NSString*)getUrlWithId:(NSString*)aId withStorageId:(int)aStorageId
{
    return [self getUrlWithId:aId withStorageId:aStorageId withThumbParams:nil];
}

- (NSString*)getUrlWithId:(NSString*)aId
{
    return [self getUrlWithId:aId withStorageId:KALTURA_UNDEF_INT];
}

- (KalturaRemotePathListResponse*)getRemotePathsWithId:(NSString*)aId
{
    [self.client.params addIfDefinedKey:@"id" withString:aId];
    return [self.client queueObjectService:@"thumbasset" withAction:@"getRemotePaths" withExpectedType:@"KalturaRemotePathListResponse"];
}

@end

@implementation KalturaThumbParamsOutputService
- (KalturaThumbParamsOutput*)getWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    return [self.client queueObjectService:@"thumbparamsoutput" withAction:@"get" withExpectedType:@"KalturaThumbParamsOutput"];
}

- (KalturaThumbParamsOutputListResponse*)listWithFilter:(KalturaThumbParamsOutputFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"thumbparamsoutput" withAction:@"list" withExpectedType:@"KalturaThumbParamsOutputListResponse"];
}

- (KalturaThumbParamsOutputListResponse*)listWithFilter:(KalturaThumbParamsOutputFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaThumbParamsOutputListResponse*)list
{
    return [self listWithFilter:nil];
}

@end

@implementation KalturaThumbParamsService
- (KalturaThumbParams*)addWithThumbParams:(KalturaThumbParams*)aThumbParams
{
    [self.client.params addIfDefinedKey:@"thumbParams" withObject:aThumbParams];
    return [self.client queueObjectService:@"thumbparams" withAction:@"add" withExpectedType:@"KalturaThumbParams"];
}

- (KalturaThumbParams*)getWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    return [self.client queueObjectService:@"thumbparams" withAction:@"get" withExpectedType:@"KalturaThumbParams"];
}

- (KalturaThumbParams*)updateWithId:(int)aId withThumbParams:(KalturaThumbParams*)aThumbParams
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client.params addIfDefinedKey:@"thumbParams" withObject:aThumbParams];
    return [self.client queueObjectService:@"thumbparams" withAction:@"update" withExpectedType:@"KalturaThumbParams"];
}

- (void)deleteWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client queueVoidService:@"thumbparams" withAction:@"delete"];
}

- (KalturaThumbParamsListResponse*)listWithFilter:(KalturaThumbParamsFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"thumbparams" withAction:@"list" withExpectedType:@"KalturaThumbParamsListResponse"];
}

- (KalturaThumbParamsListResponse*)listWithFilter:(KalturaThumbParamsFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaThumbParamsListResponse*)list
{
    return [self listWithFilter:nil];
}

- (NSMutableArray*)getByConversionProfileIdWithConversionProfileId:(int)aConversionProfileId
{
    [self.client.params addIfDefinedKey:@"conversionProfileId" withInt:aConversionProfileId];
    return [self.client queueArrayService:@"thumbparams" withAction:@"getByConversionProfileId" withExpectedType:@"KalturaThumbParams"];
}

@end

@implementation KalturaUiConfService
- (KalturaUiConf*)addWithUiConf:(KalturaUiConf*)aUiConf
{
    [self.client.params addIfDefinedKey:@"uiConf" withObject:aUiConf];
    return [self.client queueObjectService:@"uiconf" withAction:@"add" withExpectedType:@"KalturaUiConf"];
}

- (KalturaUiConf*)updateWithId:(int)aId withUiConf:(KalturaUiConf*)aUiConf
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client.params addIfDefinedKey:@"uiConf" withObject:aUiConf];
    return [self.client queueObjectService:@"uiconf" withAction:@"update" withExpectedType:@"KalturaUiConf"];
}

- (KalturaUiConf*)getWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    return [self.client queueObjectService:@"uiconf" withAction:@"get" withExpectedType:@"KalturaUiConf"];
}

- (void)deleteWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client queueVoidService:@"uiconf" withAction:@"delete"];
}

- (KalturaUiConf*)cloneWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    return [self.client queueObjectService:@"uiconf" withAction:@"clone" withExpectedType:@"KalturaUiConf"];
}

- (KalturaUiConfListResponse*)listTemplatesWithFilter:(KalturaUiConfFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"uiconf" withAction:@"listTemplates" withExpectedType:@"KalturaUiConfListResponse"];
}

- (KalturaUiConfListResponse*)listTemplatesWithFilter:(KalturaUiConfFilter*)aFilter
{
    return [self listTemplatesWithFilter:aFilter withPager:nil];
}

- (KalturaUiConfListResponse*)listTemplates
{
    return [self listTemplatesWithFilter:nil];
}

- (KalturaUiConfListResponse*)listWithFilter:(KalturaUiConfFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"uiconf" withAction:@"list" withExpectedType:@"KalturaUiConfListResponse"];
}

- (KalturaUiConfListResponse*)listWithFilter:(KalturaUiConfFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaUiConfListResponse*)list
{
    return [self listWithFilter:nil];
}

- (NSMutableArray*)getAvailableTypes
{
    return [self.client queueArrayService:@"uiconf" withAction:@"getAvailableTypes" withExpectedType:@"KalturaUiConfTypeInfo"];
}

@end

@implementation KalturaUploadService
- (NSString*)uploadWithFileData:(NSString*)aFileData
{
    [self.client.params addIfDefinedKey:@"fileData" withFileName:aFileData];
    return [self.client queueStringService:@"upload" withAction:@"upload"];
}

- (KalturaUploadResponse*)getUploadedFileTokenByFileNameWithFileName:(NSString*)aFileName
{
    [self.client.params addIfDefinedKey:@"fileName" withString:aFileName];
    return [self.client queueObjectService:@"upload" withAction:@"getUploadedFileTokenByFileName" withExpectedType:@"KalturaUploadResponse"];
}

@end

@implementation KalturaUploadTokenService
- (KalturaUploadToken*)addWithUploadToken:(KalturaUploadToken*)aUploadToken
{
    [self.client.params addIfDefinedKey:@"uploadToken" withObject:aUploadToken];
    return [self.client queueObjectService:@"uploadtoken" withAction:@"add" withExpectedType:@"KalturaUploadToken"];
}

- (KalturaUploadToken*)add
{
    return [self addWithUploadToken:nil];
}

- (KalturaUploadToken*)getWithUploadTokenId:(NSString*)aUploadTokenId
{
    [self.client.params addIfDefinedKey:@"uploadTokenId" withString:aUploadTokenId];
    return [self.client queueObjectService:@"uploadtoken" withAction:@"get" withExpectedType:@"KalturaUploadToken"];
}

- (KalturaUploadToken*)uploadWithUploadTokenId:(NSString*)aUploadTokenId withFileData:(NSString*)aFileData withResume:(BOOL)aResume withFinalChunk:(BOOL)aFinalChunk withResumeAt:(double)aResumeAt
{
    [self.client.params addIfDefinedKey:@"uploadTokenId" withString:aUploadTokenId];
    [self.client.params addIfDefinedKey:@"fileData" withFileName:aFileData];
    [self.client.params addIfDefinedKey:@"resume" withBool:aResume];
    [self.client.params addIfDefinedKey:@"finalChunk" withBool:aFinalChunk];
    [self.client.params addIfDefinedKey:@"resumeAt" withFloat:aResumeAt];
    return [self.client queueObjectService:@"uploadtoken" withAction:@"upload" withExpectedType:@"KalturaUploadToken"];
}

- (KalturaUploadToken*)uploadWithUploadTokenId:(NSString*)aUploadTokenId withFileData:(NSString*)aFileData withResume:(BOOL)aResume withFinalChunk:(BOOL)aFinalChunk
{
    return [self uploadWithUploadTokenId:aUploadTokenId withFileData:aFileData withResume:aResume withFinalChunk:aFinalChunk withResumeAt:KALTURA_UNDEF_FLOAT];
}

- (KalturaUploadToken*)uploadWithUploadTokenId:(NSString*)aUploadTokenId withFileData:(NSString*)aFileData withResume:(BOOL)aResume
{
    return [self uploadWithUploadTokenId:aUploadTokenId withFileData:aFileData withResume:aResume withFinalChunk:KALTURA_UNDEF_BOOL];
}

- (KalturaUploadToken*)uploadWithUploadTokenId:(NSString*)aUploadTokenId withFileData:(NSString*)aFileData
{
    return [self uploadWithUploadTokenId:aUploadTokenId withFileData:aFileData withResume:KALTURA_UNDEF_BOOL];
}

- (void)deleteWithUploadTokenId:(NSString*)aUploadTokenId
{
    [self.client.params addIfDefinedKey:@"uploadTokenId" withString:aUploadTokenId];
    [self.client queueVoidService:@"uploadtoken" withAction:@"delete"];
}

- (KalturaUploadTokenListResponse*)listWithFilter:(KalturaUploadTokenFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"uploadtoken" withAction:@"list" withExpectedType:@"KalturaUploadTokenListResponse"];
}

- (KalturaUploadTokenListResponse*)listWithFilter:(KalturaUploadTokenFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaUploadTokenListResponse*)list
{
    return [self listWithFilter:nil];
}

@end

@implementation KalturaUserRoleService
- (KalturaUserRole*)addWithUserRole:(KalturaUserRole*)aUserRole
{
    [self.client.params addIfDefinedKey:@"userRole" withObject:aUserRole];
    return [self.client queueObjectService:@"userrole" withAction:@"add" withExpectedType:@"KalturaUserRole"];
}

- (KalturaUserRole*)getWithUserRoleId:(int)aUserRoleId
{
    [self.client.params addIfDefinedKey:@"userRoleId" withInt:aUserRoleId];
    return [self.client queueObjectService:@"userrole" withAction:@"get" withExpectedType:@"KalturaUserRole"];
}

- (KalturaUserRole*)updateWithUserRoleId:(int)aUserRoleId withUserRole:(KalturaUserRole*)aUserRole
{
    [self.client.params addIfDefinedKey:@"userRoleId" withInt:aUserRoleId];
    [self.client.params addIfDefinedKey:@"userRole" withObject:aUserRole];
    return [self.client queueObjectService:@"userrole" withAction:@"update" withExpectedType:@"KalturaUserRole"];
}

- (KalturaUserRole*)deleteWithUserRoleId:(int)aUserRoleId
{
    [self.client.params addIfDefinedKey:@"userRoleId" withInt:aUserRoleId];
    return [self.client queueObjectService:@"userrole" withAction:@"delete" withExpectedType:@"KalturaUserRole"];
}

- (KalturaUserRoleListResponse*)listWithFilter:(KalturaUserRoleFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"userrole" withAction:@"list" withExpectedType:@"KalturaUserRoleListResponse"];
}

- (KalturaUserRoleListResponse*)listWithFilter:(KalturaUserRoleFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaUserRoleListResponse*)list
{
    return [self listWithFilter:nil];
}

- (KalturaUserRole*)cloneWithUserRoleId:(int)aUserRoleId
{
    [self.client.params addIfDefinedKey:@"userRoleId" withInt:aUserRoleId];
    return [self.client queueObjectService:@"userrole" withAction:@"clone" withExpectedType:@"KalturaUserRole"];
}

@end

@implementation KalturaUserService
- (KalturaUser*)addWithUser:(KalturaUser*)aUser
{
    [self.client.params addIfDefinedKey:@"user" withObject:aUser];
    return [self.client queueObjectService:@"user" withAction:@"add" withExpectedType:@"KalturaUser"];
}

- (KalturaUser*)updateWithUserId:(NSString*)aUserId withUser:(KalturaUser*)aUser
{
    [self.client.params addIfDefinedKey:@"userId" withString:aUserId];
    [self.client.params addIfDefinedKey:@"user" withObject:aUser];
    return [self.client queueObjectService:@"user" withAction:@"update" withExpectedType:@"KalturaUser"];
}

- (KalturaUser*)getWithUserId:(NSString*)aUserId
{
    [self.client.params addIfDefinedKey:@"userId" withString:aUserId];
    return [self.client queueObjectService:@"user" withAction:@"get" withExpectedType:@"KalturaUser"];
}

- (KalturaUser*)get
{
    return [self getWithUserId:nil];
}

- (KalturaUser*)getByLoginIdWithLoginId:(NSString*)aLoginId
{
    [self.client.params addIfDefinedKey:@"loginId" withString:aLoginId];
    return [self.client queueObjectService:@"user" withAction:@"getByLoginId" withExpectedType:@"KalturaUser"];
}

- (KalturaUser*)deleteWithUserId:(NSString*)aUserId
{
    [self.client.params addIfDefinedKey:@"userId" withString:aUserId];
    return [self.client queueObjectService:@"user" withAction:@"delete" withExpectedType:@"KalturaUser"];
}

- (KalturaUserListResponse*)listWithFilter:(KalturaUserFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"user" withAction:@"list" withExpectedType:@"KalturaUserListResponse"];
}

- (KalturaUserListResponse*)listWithFilter:(KalturaUserFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaUserListResponse*)list
{
    return [self listWithFilter:nil];
}

- (void)notifyBanWithUserId:(NSString*)aUserId
{
    [self.client.params addIfDefinedKey:@"userId" withString:aUserId];
    [self.client queueVoidService:@"user" withAction:@"notifyBan"];
}

- (NSString*)loginWithPartnerId:(int)aPartnerId withUserId:(NSString*)aUserId withPassword:(NSString*)aPassword withExpiry:(int)aExpiry withPrivileges:(NSString*)aPrivileges
{
    [self.client.params addIfDefinedKey:@"partnerId" withInt:aPartnerId];
    [self.client.params addIfDefinedKey:@"userId" withString:aUserId];
    [self.client.params addIfDefinedKey:@"password" withString:aPassword];
    [self.client.params addIfDefinedKey:@"expiry" withInt:aExpiry];
    [self.client.params addIfDefinedKey:@"privileges" withString:aPrivileges];
    return [self.client queueStringService:@"user" withAction:@"login"];
}

- (NSString*)loginWithPartnerId:(int)aPartnerId withUserId:(NSString*)aUserId withPassword:(NSString*)aPassword withExpiry:(int)aExpiry
{
    return [self loginWithPartnerId:aPartnerId withUserId:aUserId withPassword:aPassword withExpiry:aExpiry withPrivileges:nil];
}

- (NSString*)loginWithPartnerId:(int)aPartnerId withUserId:(NSString*)aUserId withPassword:(NSString*)aPassword
{
    return [self loginWithPartnerId:aPartnerId withUserId:aUserId withPassword:aPassword withExpiry:KALTURA_UNDEF_INT];
}

- (NSString*)loginByLoginIdWithLoginId:(NSString*)aLoginId withPassword:(NSString*)aPassword withPartnerId:(int)aPartnerId withExpiry:(int)aExpiry withPrivileges:(NSString*)aPrivileges
{
    [self.client.params addIfDefinedKey:@"loginId" withString:aLoginId];
    [self.client.params addIfDefinedKey:@"password" withString:aPassword];
    [self.client.params addIfDefinedKey:@"partnerId" withInt:aPartnerId];
    [self.client.params addIfDefinedKey:@"expiry" withInt:aExpiry];
    [self.client.params addIfDefinedKey:@"privileges" withString:aPrivileges];
    return [self.client queueStringService:@"user" withAction:@"loginByLoginId"];
}

- (NSString*)loginByLoginIdWithLoginId:(NSString*)aLoginId withPassword:(NSString*)aPassword withPartnerId:(int)aPartnerId withExpiry:(int)aExpiry
{
    return [self loginByLoginIdWithLoginId:aLoginId withPassword:aPassword withPartnerId:aPartnerId withExpiry:aExpiry withPrivileges:nil];
}

- (NSString*)loginByLoginIdWithLoginId:(NSString*)aLoginId withPassword:(NSString*)aPassword withPartnerId:(int)aPartnerId
{
    return [self loginByLoginIdWithLoginId:aLoginId withPassword:aPassword withPartnerId:aPartnerId withExpiry:KALTURA_UNDEF_INT];
}

- (NSString*)loginByLoginIdWithLoginId:(NSString*)aLoginId withPassword:(NSString*)aPassword
{
    return [self loginByLoginIdWithLoginId:aLoginId withPassword:aPassword withPartnerId:KALTURA_UNDEF_INT];
}

- (void)updateLoginDataWithOldLoginId:(NSString*)aOldLoginId withPassword:(NSString*)aPassword withNewLoginId:(NSString*)aNewLoginId withNewPassword:(NSString*)aNewPassword withNewFirstName:(NSString*)aNewFirstName withNewLastName:(NSString*)aNewLastName
{
    [self.client.params addIfDefinedKey:@"oldLoginId" withString:aOldLoginId];
    [self.client.params addIfDefinedKey:@"password" withString:aPassword];
    [self.client.params addIfDefinedKey:@"newLoginId" withString:aNewLoginId];
    [self.client.params addIfDefinedKey:@"newPassword" withString:aNewPassword];
    [self.client.params addIfDefinedKey:@"newFirstName" withString:aNewFirstName];
    [self.client.params addIfDefinedKey:@"newLastName" withString:aNewLastName];
    [self.client queueVoidService:@"user" withAction:@"updateLoginData"];
}

- (void)updateLoginDataWithOldLoginId:(NSString*)aOldLoginId withPassword:(NSString*)aPassword withNewLoginId:(NSString*)aNewLoginId withNewPassword:(NSString*)aNewPassword withNewFirstName:(NSString*)aNewFirstName
{
    [self updateLoginDataWithOldLoginId:aOldLoginId withPassword:aPassword withNewLoginId:aNewLoginId withNewPassword:aNewPassword withNewFirstName:aNewFirstName withNewLastName:nil];
}

- (void)updateLoginDataWithOldLoginId:(NSString*)aOldLoginId withPassword:(NSString*)aPassword withNewLoginId:(NSString*)aNewLoginId withNewPassword:(NSString*)aNewPassword
{
    [self updateLoginDataWithOldLoginId:aOldLoginId withPassword:aPassword withNewLoginId:aNewLoginId withNewPassword:aNewPassword withNewFirstName:nil];
}

- (void)updateLoginDataWithOldLoginId:(NSString*)aOldLoginId withPassword:(NSString*)aPassword withNewLoginId:(NSString*)aNewLoginId
{
    [self updateLoginDataWithOldLoginId:aOldLoginId withPassword:aPassword withNewLoginId:aNewLoginId withNewPassword:nil];
}

- (void)updateLoginDataWithOldLoginId:(NSString*)aOldLoginId withPassword:(NSString*)aPassword
{
    [self updateLoginDataWithOldLoginId:aOldLoginId withPassword:aPassword withNewLoginId:nil];
}

- (void)resetPasswordWithEmail:(NSString*)aEmail
{
    [self.client.params addIfDefinedKey:@"email" withString:aEmail];
    [self.client queueVoidService:@"user" withAction:@"resetPassword"];
}

- (void)setInitialPasswordWithHashKey:(NSString*)aHashKey withNewPassword:(NSString*)aNewPassword
{
    [self.client.params addIfDefinedKey:@"hashKey" withString:aHashKey];
    [self.client.params addIfDefinedKey:@"newPassword" withString:aNewPassword];
    [self.client queueVoidService:@"user" withAction:@"setInitialPassword"];
}

- (KalturaUser*)enableLoginWithUserId:(NSString*)aUserId withLoginId:(NSString*)aLoginId withPassword:(NSString*)aPassword
{
    [self.client.params addIfDefinedKey:@"userId" withString:aUserId];
    [self.client.params addIfDefinedKey:@"loginId" withString:aLoginId];
    [self.client.params addIfDefinedKey:@"password" withString:aPassword];
    return [self.client queueObjectService:@"user" withAction:@"enableLogin" withExpectedType:@"KalturaUser"];
}

- (KalturaUser*)enableLoginWithUserId:(NSString*)aUserId withLoginId:(NSString*)aLoginId
{
    return [self enableLoginWithUserId:aUserId withLoginId:aLoginId withPassword:nil];
}

- (KalturaUser*)disableLoginWithUserId:(NSString*)aUserId withLoginId:(NSString*)aLoginId
{
    [self.client.params addIfDefinedKey:@"userId" withString:aUserId];
    [self.client.params addIfDefinedKey:@"loginId" withString:aLoginId];
    return [self.client queueObjectService:@"user" withAction:@"disableLogin" withExpectedType:@"KalturaUser"];
}

- (KalturaUser*)disableLoginWithUserId:(NSString*)aUserId
{
    return [self disableLoginWithUserId:aUserId withLoginId:nil];
}

- (KalturaUser*)disableLogin
{
    return [self disableLoginWithUserId:nil];
}

- (NSString*)indexWithId:(NSString*)aId withShouldUpdate:(BOOL)aShouldUpdate
{
    [self.client.params addIfDefinedKey:@"id" withString:aId];
    [self.client.params addIfDefinedKey:@"shouldUpdate" withBool:aShouldUpdate];
    return [self.client queueStringService:@"user" withAction:@"index"];
}

- (NSString*)indexWithId:(NSString*)aId
{
    return [self indexWithId:aId withShouldUpdate:KALTURA_UNDEF_BOOL];
}

- (KalturaBulkUpload*)addFromBulkUploadWithFileData:(NSString*)aFileData withBulkUploadData:(KalturaBulkUploadJobData*)aBulkUploadData withBulkUploadUserData:(KalturaBulkUploadUserData*)aBulkUploadUserData
{
    [self.client.params addIfDefinedKey:@"fileData" withFileName:aFileData];
    [self.client.params addIfDefinedKey:@"bulkUploadData" withObject:aBulkUploadData];
    [self.client.params addIfDefinedKey:@"bulkUploadUserData" withObject:aBulkUploadUserData];
    return [self.client queueObjectService:@"user" withAction:@"addFromBulkUpload" withExpectedType:@"KalturaBulkUpload"];
}

- (KalturaBulkUpload*)addFromBulkUploadWithFileData:(NSString*)aFileData withBulkUploadData:(KalturaBulkUploadJobData*)aBulkUploadData
{
    return [self addFromBulkUploadWithFileData:aFileData withBulkUploadData:aBulkUploadData withBulkUploadUserData:nil];
}

- (KalturaBulkUpload*)addFromBulkUploadWithFileData:(NSString*)aFileData
{
    return [self addFromBulkUploadWithFileData:aFileData withBulkUploadData:nil];
}

- (BOOL)checkLoginDataExistsWithFilter:(KalturaUserLoginDataFilter*)aFilter
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    return [self.client queueBoolService:@"user" withAction:@"checkLoginDataExists"];
}

@end

@implementation KalturaWidgetService
- (KalturaWidget*)addWithWidget:(KalturaWidget*)aWidget
{
    [self.client.params addIfDefinedKey:@"widget" withObject:aWidget];
    return [self.client queueObjectService:@"widget" withAction:@"add" withExpectedType:@"KalturaWidget"];
}

- (KalturaWidget*)updateWithId:(NSString*)aId withWidget:(KalturaWidget*)aWidget
{
    [self.client.params addIfDefinedKey:@"id" withString:aId];
    [self.client.params addIfDefinedKey:@"widget" withObject:aWidget];
    return [self.client queueObjectService:@"widget" withAction:@"update" withExpectedType:@"KalturaWidget"];
}

- (KalturaWidget*)getWithId:(NSString*)aId
{
    [self.client.params addIfDefinedKey:@"id" withString:aId];
    return [self.client queueObjectService:@"widget" withAction:@"get" withExpectedType:@"KalturaWidget"];
}

- (KalturaWidget*)cloneWithWidget:(KalturaWidget*)aWidget
{
    [self.client.params addIfDefinedKey:@"widget" withObject:aWidget];
    return [self.client queueObjectService:@"widget" withAction:@"clone" withExpectedType:@"KalturaWidget"];
}

- (KalturaWidgetListResponse*)listWithFilter:(KalturaWidgetFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"widget" withAction:@"list" withExpectedType:@"KalturaWidgetListResponse"];
}

- (KalturaWidgetListResponse*)listWithFilter:(KalturaWidgetFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaWidgetListResponse*)list
{
    return [self listWithFilter:nil];
}

@end

@implementation KalturaXInternalService
- (NSString*)xAddBulkDownloadWithEntryIds:(NSString*)aEntryIds withFlavorParamsId:(NSString*)aFlavorParamsId
{
    [self.client.params addIfDefinedKey:@"entryIds" withString:aEntryIds];
    [self.client.params addIfDefinedKey:@"flavorParamsId" withString:aFlavorParamsId];
    return [self.client queueStringService:@"xinternal" withAction:@"xAddBulkDownload"];
}

- (NSString*)xAddBulkDownloadWithEntryIds:(NSString*)aEntryIds
{
    return [self xAddBulkDownloadWithEntryIds:aEntryIds withFlavorParamsId:nil];
}

@end

@implementation KalturaClient

- (id)initWithConfig:(KalturaClientConfiguration*)aConfig
{
    self = [super initWithConfig:aConfig];
    if (self == nil)
        return nil;
    self.apiVersion = API_VERSION;
    return self;
}

- (KalturaAccessControlProfileService*)accessControlProfile
{
    if (self->_accessControlProfile == nil)
    	self->_accessControlProfile = [[KalturaAccessControlProfileService alloc] initWithClient:self];
    return self->_accessControlProfile;
}

- (KalturaAccessControlService*)accessControl
{
    if (self->_accessControl == nil)
    	self->_accessControl = [[KalturaAccessControlService alloc] initWithClient:self];
    return self->_accessControl;
}

- (KalturaAdminUserService*)adminUser
{
    if (self->_adminUser == nil)
    	self->_adminUser = [[KalturaAdminUserService alloc] initWithClient:self];
    return self->_adminUser;
}

- (KalturaBaseEntryService*)baseEntry
{
    if (self->_baseEntry == nil)
    	self->_baseEntry = [[KalturaBaseEntryService alloc] initWithClient:self];
    return self->_baseEntry;
}

- (KalturaBulkUploadService*)bulkUpload
{
    if (self->_bulkUpload == nil)
    	self->_bulkUpload = [[KalturaBulkUploadService alloc] initWithClient:self];
    return self->_bulkUpload;
}

- (KalturaCategoryEntryService*)categoryEntry
{
    if (self->_categoryEntry == nil)
    	self->_categoryEntry = [[KalturaCategoryEntryService alloc] initWithClient:self];
    return self->_categoryEntry;
}

- (KalturaCategoryService*)category
{
    if (self->_category == nil)
    	self->_category = [[KalturaCategoryService alloc] initWithClient:self];
    return self->_category;
}

- (KalturaCategoryUserService*)categoryUser
{
    if (self->_categoryUser == nil)
    	self->_categoryUser = [[KalturaCategoryUserService alloc] initWithClient:self];
    return self->_categoryUser;
}

- (KalturaConversionProfileAssetParamsService*)conversionProfileAssetParams
{
    if (self->_conversionProfileAssetParams == nil)
    	self->_conversionProfileAssetParams = [[KalturaConversionProfileAssetParamsService alloc] initWithClient:self];
    return self->_conversionProfileAssetParams;
}

- (KalturaConversionProfileService*)conversionProfile
{
    if (self->_conversionProfile == nil)
    	self->_conversionProfile = [[KalturaConversionProfileService alloc] initWithClient:self];
    return self->_conversionProfile;
}

- (KalturaDataService*)data
{
    if (self->_data == nil)
    	self->_data = [[KalturaDataService alloc] initWithClient:self];
    return self->_data;
}

- (KalturaEmailIngestionProfileService*)EmailIngestionProfile
{
    if (self->_EmailIngestionProfile == nil)
    	self->_EmailIngestionProfile = [[KalturaEmailIngestionProfileService alloc] initWithClient:self];
    return self->_EmailIngestionProfile;
}

- (KalturaFlavorAssetService*)flavorAsset
{
    if (self->_flavorAsset == nil)
    	self->_flavorAsset = [[KalturaFlavorAssetService alloc] initWithClient:self];
    return self->_flavorAsset;
}

- (KalturaFlavorParamsOutputService*)flavorParamsOutput
{
    if (self->_flavorParamsOutput == nil)
    	self->_flavorParamsOutput = [[KalturaFlavorParamsOutputService alloc] initWithClient:self];
    return self->_flavorParamsOutput;
}

- (KalturaFlavorParamsService*)flavorParams
{
    if (self->_flavorParams == nil)
    	self->_flavorParams = [[KalturaFlavorParamsService alloc] initWithClient:self];
    return self->_flavorParams;
}

- (KalturaLiveStreamService*)liveStream
{
    if (self->_liveStream == nil)
    	self->_liveStream = [[KalturaLiveStreamService alloc] initWithClient:self];
    return self->_liveStream;
}

- (KalturaMediaInfoService*)mediaInfo
{
    if (self->_mediaInfo == nil)
    	self->_mediaInfo = [[KalturaMediaInfoService alloc] initWithClient:self];
    return self->_mediaInfo;
}

- (KalturaMediaService*)media
{
    if (self->_media == nil)
    	self->_media = [[KalturaMediaService alloc] initWithClient:self];
    return self->_media;
}

- (KalturaMixingService*)mixing
{
    if (self->_mixing == nil)
    	self->_mixing = [[KalturaMixingService alloc] initWithClient:self];
    return self->_mixing;
}

- (KalturaNotificationService*)notification
{
    if (self->_notification == nil)
    	self->_notification = [[KalturaNotificationService alloc] initWithClient:self];
    return self->_notification;
}

- (KalturaPartnerService*)partner
{
    if (self->_partner == nil)
    	self->_partner = [[KalturaPartnerService alloc] initWithClient:self];
    return self->_partner;
}

- (KalturaPermissionItemService*)permissionItem
{
    if (self->_permissionItem == nil)
    	self->_permissionItem = [[KalturaPermissionItemService alloc] initWithClient:self];
    return self->_permissionItem;
}

- (KalturaPermissionService*)permission
{
    if (self->_permission == nil)
    	self->_permission = [[KalturaPermissionService alloc] initWithClient:self];
    return self->_permission;
}

- (KalturaPlaylistService*)playlist
{
    if (self->_playlist == nil)
    	self->_playlist = [[KalturaPlaylistService alloc] initWithClient:self];
    return self->_playlist;
}

- (KalturaReportService*)report
{
    if (self->_report == nil)
    	self->_report = [[KalturaReportService alloc] initWithClient:self];
    return self->_report;
}

- (KalturaSchemaService*)schema
{
    if (self->_schema == nil)
    	self->_schema = [[KalturaSchemaService alloc] initWithClient:self];
    return self->_schema;
}

- (KalturaSearchService*)search
{
    if (self->_search == nil)
    	self->_search = [[KalturaSearchService alloc] initWithClient:self];
    return self->_search;
}

- (KalturaSessionService*)session
{
    if (self->_session == nil)
    	self->_session = [[KalturaSessionService alloc] initWithClient:self];
    return self->_session;
}

- (KalturaStatsService*)stats
{
    if (self->_stats == nil)
    	self->_stats = [[KalturaStatsService alloc] initWithClient:self];
    return self->_stats;
}

- (KalturaStorageProfileService*)storageProfile
{
    if (self->_storageProfile == nil)
    	self->_storageProfile = [[KalturaStorageProfileService alloc] initWithClient:self];
    return self->_storageProfile;
}

- (KalturaSyndicationFeedService*)syndicationFeed
{
    if (self->_syndicationFeed == nil)
    	self->_syndicationFeed = [[KalturaSyndicationFeedService alloc] initWithClient:self];
    return self->_syndicationFeed;
}

- (KalturaSystemService*)system
{
    if (self->_system == nil)
    	self->_system = [[KalturaSystemService alloc] initWithClient:self];
    return self->_system;
}

- (KalturaThumbAssetService*)thumbAsset
{
    if (self->_thumbAsset == nil)
    	self->_thumbAsset = [[KalturaThumbAssetService alloc] initWithClient:self];
    return self->_thumbAsset;
}

- (KalturaThumbParamsOutputService*)thumbParamsOutput
{
    if (self->_thumbParamsOutput == nil)
    	self->_thumbParamsOutput = [[KalturaThumbParamsOutputService alloc] initWithClient:self];
    return self->_thumbParamsOutput;
}

- (KalturaThumbParamsService*)thumbParams
{
    if (self->_thumbParams == nil)
    	self->_thumbParams = [[KalturaThumbParamsService alloc] initWithClient:self];
    return self->_thumbParams;
}

- (KalturaUiConfService*)uiConf
{
    if (self->_uiConf == nil)
    	self->_uiConf = [[KalturaUiConfService alloc] initWithClient:self];
    return self->_uiConf;
}

- (KalturaUploadService*)upload
{
    if (self->_upload == nil)
    	self->_upload = [[KalturaUploadService alloc] initWithClient:self];
    return self->_upload;
}

- (KalturaUploadTokenService*)uploadToken
{
    if (self->_uploadToken == nil)
    	self->_uploadToken = [[KalturaUploadTokenService alloc] initWithClient:self];
    return self->_uploadToken;
}

- (KalturaUserRoleService*)userRole
{
    if (self->_userRole == nil)
    	self->_userRole = [[KalturaUserRoleService alloc] initWithClient:self];
    return self->_userRole;
}

- (KalturaUserService*)user
{
    if (self->_user == nil)
    	self->_user = [[KalturaUserService alloc] initWithClient:self];
    return self->_user;
}

- (KalturaWidgetService*)widget
{
    if (self->_widget == nil)
    	self->_widget = [[KalturaWidgetService alloc] initWithClient:self];
    return self->_widget;
}

- (KalturaXInternalService*)xInternal
{
    if (self->_xInternal == nil)
    	self->_xInternal = [[KalturaXInternalService alloc] initWithClient:self];
    return self->_xInternal;
}

- (void)dealloc
{
    [self->_accessControlProfile release];
    [self->_accessControl release];
    [self->_adminUser release];
    [self->_baseEntry release];
    [self->_bulkUpload release];
    [self->_categoryEntry release];
    [self->_category release];
    [self->_categoryUser release];
    [self->_conversionProfileAssetParams release];
    [self->_conversionProfile release];
    [self->_data release];
    [self->_EmailIngestionProfile release];
    [self->_flavorAsset release];
    [self->_flavorParamsOutput release];
    [self->_flavorParams release];
    [self->_liveStream release];
    [self->_mediaInfo release];
    [self->_media release];
    [self->_mixing release];
    [self->_notification release];
    [self->_partner release];
    [self->_permissionItem release];
    [self->_permission release];
    [self->_playlist release];
    [self->_report release];
    [self->_schema release];
    [self->_search release];
    [self->_session release];
    [self->_stats release];
    [self->_storageProfile release];
    [self->_syndicationFeed release];
    [self->_system release];
    [self->_thumbAsset release];
    [self->_thumbParamsOutput release];
    [self->_thumbParams release];
    [self->_uiConf release];
    [self->_upload release];
    [self->_uploadToken release];
    [self->_userRole release];
    [self->_user release];
    [self->_widget release];
    [self->_xInternal release];
	[super dealloc];
}

@end

