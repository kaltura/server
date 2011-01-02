<?php

$dryRun = true; //TODO: change for real run
if($argc > 1 && $argv[1] == 'realrun')
	$dryRun = false;
	
//------------------------------------------------------

require_once(dirname(__FILE__).'/../../../bootstrap.php');

//------------------------------------------------------

$permissionsData = array (
	array (-1, PermissionType::API_ACCESS, PermissionName::BASE_BATCH_SYSTEM_PERMISSION, 'Batch system permission', null),
	array (0, PermissionType::API_ACCESS,PermissionName::USER_SESSION_PERMISSION, 'User session permission', null),
	array (0, PermissionType::API_ACCESS,PermissionName::NO_SESSION_PERMISSION, 'No session permission', null),
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
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MANAGE_ANNOTATION, 'Annotate', PermissionPeer::getPermissionNameFromPluginName(AnnotationPlugin::getPluginName())),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MANAGE_SHARE, 'Share content', null),
	array (0, PermissionType::API_ACCESS,PermissionName::LIVE_STREAM_BASE, 'Live streams access', PermissionName::FEATURE_LIVE_STREAM),
	array (0, PermissionType::API_ACCESS,PermissionName::LIVE_STREAM_ADD, 'Add live streams', PermissionName::FEATURE_LIVE_STREAM),
	array (0, PermissionType::API_ACCESS,PermissionName::LIVE_STREAM_UPDATE, 'Modify live streams', PermissionName::FEATURE_LIVE_STREAM),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MODERATE_BASE, 'Basic moderation', null),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MODERATE_METADATA, 'Moderate metadata', null),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MODERATE_CUSTOM_DATA, 'Moderate custom data', PermissionPeer::getPermissionNameFromPluginName(MetadataPlugin::getPluginName())),
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
	array (0, PermissionType::EXTERNAL,PermissionName::ACCOUNT_BASE, 'Account settings access', null),
	array (0, PermissionType::EXTERNAL,PermissionName::ACCOUNT_UPDATE_SETTINGS, 'Modify account settings', null),
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

KalturaLog::log('Done' . $dryRun ? 'REAL RUN!' : 'DRY RUN!');
echo 'Done' . $dryRun ? 'REAL RUN!' : 'DRY RUN!';
//------------------------------------------------------


