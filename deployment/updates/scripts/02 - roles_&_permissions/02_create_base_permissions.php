<?php

$dryRun = true; //TODO: change for real run
if($argc > 1 && $argv[1] == 'realrun')
	$dryRun = false;
	
//------------------------------------------------------

require_once(dirname(__FILE__).'/../../../bootstrap.php');

//------------------------------------------------------

$permissionsData = array (
	array (-1, PermissionType::API_ACCESS, PermissionName::BASE_BATCH_SYSTEM_PERMISSION, 'Batch system permission'),
	array (0, PermissionType::API_ACCESS,PermissionName::USER_SESSION_PERMISSION, 'User session permission'),
	array (0, PermissionType::API_ACCESS,PermissionName::NO_SESSION_PERMISSION, 'No session permission'),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_INGEST_UPLOAD, 'Upload'),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_INGEST_BULK_UPLOAD, 'Bulk upload'),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_INGEST_FEED, 'Feed subscription'),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MANAGE_MIX, 'Manage remix'),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MANAGE_BASE, 'Basic content management'),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MANAGE_METADATA, 'Modify metadata'),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MANAGE_ASSIGN_CATEGORIES, 'Assign categories'),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MANAGE_THUMBNAIL, 'Modify thumbnails'),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MANAGE_SCHEDULE, 'Modify scheduling'),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MANAGE_ACCESS_CONTROL, 'Modify content access control'),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MANAGE_CUSTOM_DATA, 'Modify custom data'),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MANAGE_DELETE, 'Delete content'),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MANAGE_RECONVERT, 'Reconvert flavors'),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MANAGE_EDIT_CATEGORIES, 'Manage categories'),
	array (0, PermissionType::EXTERNAL,PermissionName::CONTENT_MANAGE_EMBED_CODE, 'Grab embed code'),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MANAGE_ANNOTATION, 'Annotate'),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MANAGE_SHARE, 'Share content'),
	array (0, PermissionType::API_ACCESS,PermissionName::LIVE_STREAM_BASE, 'Live streams access'),
	array (0, PermissionType::API_ACCESS,PermissionName::LIVE_STREAM_ADD, 'Add live streams'),
	array (0, PermissionType::API_ACCESS,PermissionName::LIVE_STREAM_UPDATE, 'Modify live streams'),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MODERATE_BASE, 'Basic moderation'),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MODERATE_METADATA, 'Moderate metadata'),
	array (0, PermissionType::API_ACCESS,PermissionName::CONTENT_MODERATE_CUSTOM_DATA, 'Moderate custom data'),
	array (0, PermissionType::API_ACCESS,PermissionName::PLAYLIST_BASE, 'Playlist access'),
	array (0, PermissionType::API_ACCESS,PermissionName::PLAYLIST_ADD, 'Add playlists'),
	array (0, PermissionType::API_ACCESS,PermissionName::PLAYLIST_UPDATE, 'Modify playlists'),
	array (0, PermissionType::API_ACCESS,PermissionName::PLAYLIST_DELETE, 'Delete playlists'),
	array (0, PermissionType::EXTERNAL,PermissionName::PLAYLIST_EMBED_CODE, 'Grab playlist embed code'),
	array (0, PermissionType::API_ACCESS,PermissionName::SYNDICATION_BASE, 'Syndication feeds access'),
	array (0, PermissionType::API_ACCESS,PermissionName::SYNDICATION_ADD, 'Create syndication feeds'),
	array (0, PermissionType::API_ACCESS,PermissionName::SYNDICATION_UPDATE, 'Modify syndication feeds'),
	array (0, PermissionType::API_ACCESS,PermissionName::SYNDICATION_DELETE, 'Delete syndication feeds'),
	array (0, PermissionType::API_ACCESS,PermissionName::STUDIO_BASE, 'Appstudio access'),
	array (0, PermissionType::API_ACCESS,PermissionName::STUDIO_ADD_UICONF, 'Create players'),
	array (0, PermissionType::API_ACCESS,PermissionName::STUDIO_UPDATE_UICONF, 'Modify players'),
	array (0, PermissionType::API_ACCESS,PermissionName::STUDIO_DELETE_UICONF, 'Delete players'),
	array (0, PermissionType::EXTERNAL,PermissionName::STUDIO_BRAND_UICONF, 'Set player branding'),
	array (0, PermissionType::EXTERNAL,PermissionName::STUDIO_SELECT_CONTENT, 'Select player content'),
	array (0, PermissionType::API_ACCESS,PermissionName::ADVERTISING_BASE, 'Advertising access'),
	array (0, PermissionType::API_ACCESS,PermissionName::ADVERTISING_UPDATE_SETTINGS, 'Modify advertising settings'),
	array (0, PermissionType::EXTERNAL,PermissionName::ACCOUNT_BASE, 'Account settings access'),
	array (0, PermissionType::EXTERNAL,PermissionName::ACCOUNT_UPDATE_SETTINGS, 'Modify account settings'),
	array (0, PermissionType::API_ACCESS,PermissionName::INTEGRATION_BASE, 'Integration settings access'),
	array (0, PermissionType::API_ACCESS,PermissionName::INTEGRATION_UPDATE_SETTINGS, 'Modify integration settings'),
	array (0, PermissionType::API_ACCESS,PermissionName::ACCESS_CONTROL_BASE, 'Access control profiles access'),
	array (0, PermissionType::API_ACCESS,PermissionName::ACCESS_CONTROL_ADD, 'Create access control profiles'),
	array (0, PermissionType::API_ACCESS,PermissionName::ACCESS_CONTROL_UPDATE, 'Modify access control profiles'),
	array (0, PermissionType::API_ACCESS,PermissionName::ACCESS_CONTROL_DELETE, 'Delete access control profiles'),
	array (0, PermissionType::API_ACCESS,PermissionName::TRANSCODING_BASE, 'Transcoding profiles access'),
	array (0, PermissionType::API_ACCESS,PermissionName::TRANSCODING_ADD, 'Create transcoding profiles'),
	array (0, PermissionType::API_ACCESS,PermissionName::TRANSCODING_UPDATE, 'Modify transcoding profiles'),
	array (0, PermissionType::API_ACCESS,PermissionName::TRANSCODING_DELETE, 'Delete transcoding profiles'),
	array (0, PermissionType::API_ACCESS,PermissionName::CUSTOM_DATA_PROFILE_BASE, 'Custom data access'),
	array (0, PermissionType::API_ACCESS,PermissionName::CUSTOM_DATA_PROFILE_ADD, 'Create custom data'),
	array (0, PermissionType::API_ACCESS,PermissionName::CUSTOM_DATA_PROFILE_UPDATE, 'Modify custom data'),
	array (0, PermissionType::API_ACCESS,PermissionName::CUSTOM_DATA_PROFILE_DELETE, 'Delete custom data'),
	array (0, PermissionType::API_ACCESS,PermissionName::ADMIN_BASE, 'Administration settings access'),
	array (0, PermissionType::API_ACCESS,PermissionName::ADMIN_USER_ADD, 'Add users'),
	array (0, PermissionType::API_ACCESS,PermissionName::ADMIN_USER_UPDATE, 'Modify users'),
	array (0, PermissionType::API_ACCESS,PermissionName::ADMIN_USER_DELETE, 'Delete users'),
	array (0, PermissionType::API_ACCESS,PermissionName::ADMIN_ROLE_ADD, 'Add roles'),
	array (0, PermissionType::API_ACCESS,PermissionName::ADMIN_ROLE_UPDATE, 'Modify roles'),
	array (0, PermissionType::API_ACCESS,PermissionName::ADMIN_ROLE_DELETE, 'Delete roles'),
	array (0, PermissionType::API_ACCESS,PermissionName::ADMIN_PUBLISHER_MANAGE, 'Manage publishers'),
	array (0, PermissionType::API_ACCESS,PermissionName::ADMIN_WHITE_BRANDING, 'Manage whitebranding'),
	array (0, PermissionType::API_ACCESS,PermissionName::ANALYTICS_BASE, 'Analytics access'),
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


