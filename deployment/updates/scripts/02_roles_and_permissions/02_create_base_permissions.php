<?php

$dryRun = true; //TODO: change for real run
if($argc > 1 && $argv[1] == 'realrun')
	$dryRun = false;
	
//------------------------------------------------------

require_once(dirname(__FILE__).'/../../../bootstrap.php');

//------------------------------------------------------

$permissionsData = array (
	// batch system
	array (-1, PermissionType::API_ACCESS, PermissionName::BATCH_BASE, 'Batch system permission', null),
	
	// all partners
	array (0, PermissionType::API_ACCESS,PermissionName::USER_SESSION_PERMISSION, 'User session permission', null),
	array (0, PermissionType::API_ACCESS,PermissionName::ALWAYS_ALLOWED_ACTIONS, 'No session permission', null),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_INGEST_UPLOAD, 'Upload', null),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_INGEST_BULK_UPLOAD, 'Bulk upload', null),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_INGEST_FEED, 'Feed subscription', null),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MANAGE_MIX, 'Manage remix', null),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MANAGE_BASE, 'Basic content management', null),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MANAGE_METADATA, 'Modify metadata', null),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MANAGE_ASSIGN_CATEGORIES, 'Assign categories', null),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MANAGE_THUMBNAIL, 'Modify thumbnails', null),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MANAGE_SCHEDULE, 'Modify scheduling', null),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MANAGE_ACCESS_CONTROL, 'Modify content access control', null),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MANAGE_CUSTOM_DATA, 'Modify custom data', PermissionPeer::getPermissionNameFromPluginName(MetadataPlugin::getPluginName())),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MANAGE_DELETE, 'Delete content', null),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MANAGE_RECONVERT, 'Reconvert flavors', null),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MANAGE_EDIT_CATEGORIES, 'Manage categories', null),
	array (0, PermissionType::EXTERNAL,PermissionName::CONTENT_MANAGE_EMBED_CODE, 'Grab embed code', null),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MANAGE_DISTRIBUTION_BASE, 'Distribution base', PermissionPeer::getPermissionNameFromPluginName(ContentDistributionPlugin::getPluginName())),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MANAGE_DISTRIBUTION_WHERE, 'Where to distribute', PermissionPeer::getPermissionNameFromPluginName(ContentDistributionPlugin::getPluginName())),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MANAGE_DISTRIBUTION_SEND, 'Distribution submit', PermissionPeer::getPermissionNameFromPluginName(ContentDistributionPlugin::getPluginName())),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MANAGE_DISTRIBUTION_REMOVE, 'Distribution remove', PermissionPeer::getPermissionNameFromPluginName(ContentDistributionPlugin::getPluginName())),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MANAGE_DISTRIBUTION_PROFILE_BASE, 'Distribution profile base', PermissionPeer::getPermissionNameFromPluginName(ContentDistributionPlugin::getPluginName())),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MANAGE_DISTRIBUTION_PROFILE_MODIFY, 'Distribution profile manage', PermissionPeer::getPermissionNameFromPluginName(ContentDistributionPlugin::getPluginName())),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MANAGE_VIRUS_SCAN, 'Virus scan actions', PermissionPeer::getPermissionNameFromPluginName(VirusScanPlugin::getPluginName())),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MANAGE_DOWNLOAD, 'Content download', null),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MANAGE_ANNOTATION, 'Annotate', PermissionPeer::getPermissionNameFromPluginName(AnnotationPlugin::getPluginName())),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MANAGE_SHARE, 'Share content', null),
	array (0, PermissionType::API_ACCESS,PermissionName::LIVE_STREAM_ADD, 'Add live streams', PermissionName::FEATURE_LIVE_STREAM),
	array (0, PermissionType::API_ACCESS,PermissionName::LIVE_STREAM_UPDATE, 'Modify live streams', PermissionName::FEATURE_LIVE_STREAM),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MODERATE_BASE, 'Basic moderation', null),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MODERATE_METADATA, 'Moderate metadata', null),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MODERATE_CUSTOM_DATA, 'Moderate custom data', PermissionPeer::getPermissionNameFromPluginName(MetadataPlugin::getPluginName())),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MODERATE_APPROVE_REJECT, 'Approve/Reject content', null),
	array (0, PermissionType::API_ACCESS,PermissionName::PLAYLIST_BASE, 'Playlist access', null),
	array (0, PermissionType::API_ACCESS,PermissionName::PLAYLIST_ADD, 'Add playlists', null),
	array (0, PermissionType::API_ACCESS,PermissionName::PLAYLIST_UPDATE, 'Modify playlists', null),
	array (0, PermissionType::API_ACCESS,PermissionName::PLAYLIST_DELETE, 'Delete playlists', null),
	array (0, PermissionType::EXTERNAL,PermissionName::PLAYLIST_EMBED_CODE, 'Grab playlist embed code', null),
	array (0, PermissionType::API_ACCESS,PermissionName::SYNDICATION_BASE, 'Syndication feeds access', null),
	array (0, PermissionType::API_ACCESS,PermissionName::SYNDICATION_ADD, 'Create syndication feeds', null),
	array (0, PermissionType::API_ACCESS,PermissionName::SYNDICATION_UPDATE, 'Modify syndication feeds', null),
	array (0, PermissionType::API_ACCESS,PermissionName::SYNDICATION_DELETE, 'Delete syndication feeds', null),
	array (0, PermissionType::API_ACCESS,PermissionName::STUDIO_BASE, 'Appstudio access', null),
	array (0, PermissionType::API_ACCESS,PermissionName::STUDIO_ADD_UICONF, 'Create players', null),
	array (0, PermissionType::API_ACCESS,PermissionName::STUDIO_UPDATE_UICONF, 'Modify players', null),
	array (0, PermissionType::API_ACCESS,PermissionName::STUDIO_DELETE_UICONF, 'Delete players', null),
	array (0, PermissionType::EXTERNAL,PermissionName::STUDIO_BRAND_UICONF, 'Set player branding', null),
	array (0, PermissionType::EXTERNAL,PermissionName::STUDIO_SELECT_CONTENT, 'Select player content', null),
	array (0, PermissionType::API_ACCESS,PermissionName::ADVERTISING_BASE, 'Advertising access', null),
	array (0, PermissionType::API_ACCESS,PermissionName::ADVERTISING_UPDATE_SETTINGS, 'Modify advertising settings', null),
	array (0, PermissionType::API_ACCESS,PermissionName::ACCOUNT_BASE, 'Account settings access', null),
	array (0, PermissionType::API_ACCESS,PermissionName::ACCOUNT_UPDATE_SETTINGS, 'Modify account settings', null),
	array (0, PermissionType::API_ACCESS,PermissionName::INTEGRATION_BASE, 'Integration settings access', null),
	array (0, PermissionType::API_ACCESS,PermissionName::INTEGRATION_UPDATE_SETTINGS, 'Modify integration settings', null),
	array (0, PermissionType::API_ACCESS,PermissionName::ACCESS_CONTROL_BASE, 'Access control profiles access', null),
	array (0, PermissionType::API_ACCESS,PermissionName::ACCESS_CONTROL_ADD, 'Create access control profiles', null),
	array (0, PermissionType::API_ACCESS,PermissionName::ACCESS_CONTROL_UPDATE, 'Modify access control profiles', null),
	array (0, PermissionType::API_ACCESS,PermissionName::ACCESS_CONTROL_DELETE, 'Delete access control profiles', null),
	array (0, PermissionType::API_ACCESS,PermissionName::TRANSCODING_BASE, 'Transcoding profiles access', null),
	array (0, PermissionType::API_ACCESS,PermissionName::TRANSCODING_ADD, 'Create transcoding profiles', null),
	array (0, PermissionType::API_ACCESS,PermissionName::TRANSCODING_UPDATE, 'Modify transcoding profiles', null),
	array (0, PermissionType::API_ACCESS,PermissionName::TRANSCODING_DELETE, 'Delete transcoding profiles', null),
	array (0, PermissionType::API_ACCESS,PermissionName::CUSTOM_DATA_PROFILE_BASE, 'Custom data access', PermissionPeer::getPermissionNameFromPluginName(MetadataPlugin::getPluginName())),
	array (0, PermissionType::API_ACCESS,PermissionName::CUSTOM_DATA_PROFILE_ADD, 'Create custom data', PermissionPeer::getPermissionNameFromPluginName(MetadataPlugin::getPluginName())),
	array (0, PermissionType::API_ACCESS,PermissionName::CUSTOM_DATA_PROFILE_UPDATE, 'Modify custom data', PermissionPeer::getPermissionNameFromPluginName(MetadataPlugin::getPluginName())),
	array (0, PermissionType::API_ACCESS,PermissionName::CUSTOM_DATA_PROFILE_DELETE, 'Delete custom data', PermissionPeer::getPermissionNameFromPluginName(MetadataPlugin::getPluginName())),
	array (0, PermissionType::API_ACCESS,PermissionName::ADMIN_BASE, 'Administration settings access', null),
	array (0, PermissionType::API_ACCESS,PermissionName::ADMIN_USER_ADD, 'Add users', null),
	array (0, PermissionType::API_ACCESS,PermissionName::ADMIN_USER_UPDATE, 'Modify users', null),
	array (0, PermissionType::API_ACCESS,PermissionName::ADMIN_USER_DELETE, 'Delete users', null),
	array (0, PermissionType::API_ACCESS,PermissionName::ADMIN_ROLE_ADD, 'Add roles', null),
	array (0, PermissionType::API_ACCESS,PermissionName::ADMIN_ROLE_UPDATE, 'Modify roles', null),
	array (0, PermissionType::API_ACCESS,PermissionName::ADMIN_ROLE_DELETE, 'Delete roles', null),
	array (0, PermissionType::API_ACCESS,PermissionName::ADMIN_PUBLISHER_MANAGE, 'Manage publishers', null),
	array (0, PermissionType::API_ACCESS,PermissionName::ADMIN_WHITE_BRANDING, 'Manage whitebranding', null),
	array (0, PermissionType::API_ACCESS,PermissionName::ANALYTICS_BASE, 'Analytics access', null),
	array (0, PermissionType::API_ACCESS,PermissionName::WIDGET_ADMIN, 'Widget admin', null),
	array (0, PermissionType::API_ACCESS,PermissionName::ANALYTICS_SEND_DATA, 'Send analytics data', null),
	array (0, PermissionType::API_ACCESS,PermissionName::SYSTEM_BASE_ACTIONS, 'Basic system actions', null),
	array (0, PermissionType::API_ACCESS,PermissionName::WIDGET_ADMIN, 'Widget admin', null),
	array (0, PermissionType::API_ACCESS,PermissionName::SEARCH_SERVICE, 'Search service', null),
	array (0, PermissionType::API_ACCESS,PermissionName::ANALYTICS_SEND_DATA, 'Send analytics data', null),
	array (0, PermissionType::API_ACCESS,PermissionName::AUDIT_TRAIL_BASE, 'Audit trail base', PermissionPeer::getPermissionNameFromPluginName(AuditPlugin::getPluginName())),
	array (0, PermissionType::API_ACCESS,PermissionName::AUDIT_TRAIL_ADD, 'Audit trail add', PermissionPeer::getPermissionNameFromPluginName(AuditPlugin::getPluginName())),
	array (0, PermissionType::API_ACCESS,PermissionName::SYSTEM_FILESYNC, 'Filesync actions', null),
	array (0, PermissionType::EXTERNAL,PermissionName::KMC_ACCESS, 'KMC access', null),	
	
	// system admin and admin console permissions
	array (-2, PermissionType::API_ACCESS,PermissionName::SYSTEM_ADMIN_BASE, 'Base system admin permission', null),
	array (-2, PermissionType::API_ACCESS,PermissionName::SYSTEM_ADMIN_PUBLISHER_BASE, 'Base system admin permission', null),
	array (-2, PermissionType::API_ACCESS,PermissionName::SYSTEM_ADMIN_PUBLISHER_KMC_ACCESS, 'Base system admin permission', null),
	array (-2, PermissionType::API_ACCESS,PermissionName::SYSTEM_ADMIN_PUBLISHER_CONFIG, 'Base system admin permission', null),
	array (-2, PermissionType::API_ACCESS,PermissionName::SYSTEM_ADMIN_PUBLISHER_BLOCK, 'Base system admin permission', null),
	array (-2, PermissionType::API_ACCESS,PermissionName::SYSTEM_ADMIN_PUBLISHER_REMOVE, 'Base system admin permission', null),
	array (-2, PermissionType::API_ACCESS,PermissionName::SYSTEM_ADMIN_PUBLISHER_ADD, 'Base system admin permission', null),
	array (-2, PermissionType::API_ACCESS,PermissionName::SYSTEM_ADMIN_PUBLISHER_USAGE, 'Base system admin permission', null),
	array (-2, PermissionType::API_ACCESS,PermissionName::SYSTEM_ADMIN_USER_MANAGE, 'Base system admin permission', null),
	array (-2, PermissionType::EXTERNAL,PermissionName::SYSTEM_ADMIN_SYSTEM_MONITOR, 'Base system admin permission', null),
	array (-2, PermissionType::EXTERNAL,PermissionName::SYSTEM_ADMIN_DEVELOPERS_TAB, 'Base system admin permission', null),
	array (-2, PermissionType::API_ACCESS,PermissionName::SYSTEM_ADMIN_BATCH_CONTROL, 'Base system admin permission', null),
	array (-2, PermissionType::EXTERNAL,PermissionName::SYSTEM_ADMIN_BATCH_CONTROL_INPROGRESS, 'Base system admin permission', null),
	array (-2, PermissionType::EXTERNAL,PermissionName::SYSTEM_ADMIN_BATCH_CONTROL_FAILED, 'Base system admin permission', null),
	array (-2, PermissionType::API_ACCESS,PermissionName::SYSTEM_ADMIN_BATCH_CONTROL_SETUP, 'Base system admin permission', null),
	array (-2, PermissionType::API_ACCESS,PermissionName::SYSTEM_ADMIN_STORAGE, 'Base system admin permission', null),
	array (-2, PermissionType::API_ACCESS,PermissionName::SYSTEM_ADMIN_VIRUS_SCAN, 'Base system admin permission', null),
	array (-2, PermissionType::API_ACCESS,PermissionName::SYSTEM_ADMIN_EMAIL_INGESTION, 'Base system admin permission', null),
	array (-2, PermissionType::API_ACCESS,PermissionName::SYSTEM_ADMIN_CONTENT_DISTRIBUTION_BASE, 'Base system admin permission', null),
	array (-2, PermissionType::API_ACCESS,PermissionName::SYSTEM_ADMIN_CONTENT_DISTRIBUTION_MODIFY, 'Base system admin permission', null),
	array (-2, PermissionType::API_ACCESS,PermissionName::SYSTEM_ADMIN_PERMISSIONS_MANAGE, 'Base system admin permission', null),
	array (-2, PermissionType::API_ACCESS,PermissionName::SYSTEM_INTERNAL, 'System internal actions', null),
	
	// template partner
	array (99, PermissionType::SPECIAL_FEATURE,PermissionName::FEATURE_PS2_PERMISSIONS_VALIDATION, 'PS2 permissions validation', null),
	
);

//------------------------------------------------------


$allPermissions = array();

foreach ($permissionsData as $data)
{
	$permission = new Permission();
	$permission->setPartnerId($data[0]);
	$permission->setType($data[1]);	
	$permission->setName($data[2]);
	$permission->setFriendlyName($data[3]);
	$permission->setDependsOnPermissionNames($data[4]);
	$permission->setStatus(PermissionStatus::ACTIVE);
	$allPermissions[] = $permission;
}


//------------------------------------------------------

foreach ($allPermissions as $permission)
{
	if ($dryRun) {
		KalturaLog::log('DRY RUN - Adding new permission ['.$permission->getName().'] to partner ['.$permission->getPartnerId().']');
	}
	else {
		KalturaLog::log('Adding new permission ['.$permission->getName().'] to partner ['.$permission->getPartnerId().']');
		$permission->save();
	}
}

$msg = 'Done' . ($dryRun ? 'DRY RUN!' : 'REAL RUN!');
KalturaLog::log($msg);
echo $msg;
//------------------------------------------------------


