<?php

$dryRun = true; //TODO: change for real run
if($argc > 1 && $argv[1] == 'realrun')
	$dryRun = false;
	
//------------------------------------------------------

require_once(dirname(__FILE__).'/../../../bootstrap.php');

//------------------------------------------------------

$userRoles = array();

$role = new UserRole();
$role->setName('Basic User Session Role');
$role->setStrId(UserRoleId::BASE_USER_SESSION_ROLE);
$role->setDescription('Allowed actions for a basic user with no special permissions');
$role->setPermissionNames('BASE_USER_SESSION_PERMISSION');
$role->setStatus(UserRoleStatus::ACTIVE);
$role->setPartnerId(0);
$userRoles[] = $role;

$role = new UserRole();
$role->setName('Publisher Administrator');
$role->setStrId(UserRoleId::PARTNER_ADMIN_ROLE);
$role->setDescription('Full control over publisher account and user management functionalities');
$role->setPermissionNames('*');
$role->setStatus(UserRoleStatus::ACTIVE);
$role->setTags('kmc');
$role->setPartnerId(0);
$userRoles[] = $role;

$role = new UserRole();
$role->setName('Manager');
$role->setDescription('Full control over publisher account functionalities');
$managerPermissions = 'CONTENT_UPLOAD,CONTENT_BULK_UPLOAD,CONTENT_MANAGE_MIX,CONTENT_MANAGE_BASE,CONTENT_MANAGE_METADATA,CONTENT_MANAGE_ASSIGN_CATEGORIES,CONTENT_MANAGE_THUMBNAIL,CONTENT_MANAGE_SCHEDULE,CONTENT_MANAGE_ACCESS_CONTROL,CONTENT_MANAGE_CUSTOM_DATA,CONTENT_MANAGE_DELETE,CONTENT_MANAGE_RECONVERT,CONTENT_MANAGE_EDIT_CATEGORIES,LIVE_STREAM_BASE,LIVE_STREAM_ADD,LIVE_STREAM_UPDATE,CONTENT_MODERATE_BASE,CONTENT_MODERATE_METADATA,PLAYLIST_BASE,PLAYLIST_ADD,PLAYLIST_UPDATE,PLAYLIST_DELETE,SYNDICATION_BASE,SYNDICATION_ADD,SYNDICATION_UPDATE,SYNDICATION_DELETE,STUDIO_BASE,STUDIO_ADD_UICONF,STUDIO_UPDATE_UICONF,STUDIO_DELETE_UICONF,ACCOUNT_BASE,INTEGRATION_BASE,ACCESS_CONTROL_BASE,ACCESS_CONTROL_ADD,ACCESS_CONTROL_UPDATE,ACCESS_CONTROL_DELETE,TRANSCODING_BASE,TRANSCODING_ADD,TRANSCODING_UPDATE,TRANSCODING_DELETE,CUSTOM_DATA_PROFILE_BASE,CUSTOM_DATA_PROFILE_ADD,CUSTOM_DATA_PROFILE_UPDATE,CUSTOM_DATA_PROFILE_DELETE,ADMIN_BASE,ADMIN_ROLE_ADD,ADMIN_ROLE_UPDATE,ADMIN_ROLE_DELETE,ANALYTICS_BASE,ADVERTISING_BASE,ADVERTISING_UPDATE_SETTINGS,PLAYLIST_EMBED_CODE,STUDIO_BRAND_UICONF,CONTENT_MANAGE_EMBED_CODE';
$role->setPermissionNames($managerPermissions);
$role->setStatus(UserRoleStatus::ACTIVE);
$role->setTags('kmc');
$role->setPartnerId(99);
$userRoles[] = $role;

$role = new UserRole();
$role->setName('Upload Only');
$role->setDescription('Access to content ingestion and management functionalities');
$uploaderPermissions = 'CONTENT_UPLOAD,CONTENT_BULK_UPLOAD,CONTENT_MANAGE_MIX,CONTENT_MANAGE_BASE,CONTENT_MANAGE_METADATA,CONTENT_MANAGE_ASSIGN_CATEGORIES,CONTENT_MANAGE_THUMBNAIL,CONTENT_MANAGE_SCHEDULE,CONTENT_MANAGE_ACCESS_CONTROL,CONTENT_MANAGE_CUSTOM_DATA,CONTENT_MANAGE_DELETE,CONTENT_MANAGE_RECONVERT,CONTENT_MANAGE_EDIT_CATEGORIES,CONTENT_MANAGE_EMBED_CODE,LIVE_STREAM_BASE,LIVE_STREAM_ADD,LIVE_STREAM_UPDATE';
$role->setPermissionNames($uploaderPermissions);
$role->setStatus(UserRoleStatus::ACTIVE);
$role->setTags('kmc');
$role->setPartnerId(99);
$userRoles[] = $role;

$role = new UserRole();
$role->setName('Content Moderator');
$role->setDescription('Access to publisher content moderation panel');
$moderatorPermissions = 'CONTENT_MODERATE_BASE,CONTENT_MODERATE_METADATA';
$role->setPermissionNames($moderatorPermissions);
$role->setStatus(UserRoleStatus::ACTIVE);
$role->setTags('kmc');
$role->setPartnerId(99);
$userRoles[] = $role;

$role = new UserRole();
$role->setName('Player Designer');
$role->setDescription('Access to publisher studio');
$designerPermissions = 'STUDIO_BASE,STUDIO_ADD_UICONF,STUDIO_UPDATE_UICONF,STUDIO_DELETE_UICONF,STUDIO_BRAND_UICONF';
$role->setPermissionNames($designerPermissions);
$role->setStatus(UserRoleStatus::ACTIVE);
$role->setTags('kmc');
$role->setPartnerId(99);
$userRoles[] = $role;


//------------------------------------------------------

foreach ($userRoles as $newRole)
{
	if ($dryRun) {
		KalturaLog::log('DRY RUN - Adding new role - '.print_r($newRole, true));
	}
	else {
		KalturaLog::log('Adding new role - '.print_r($newRole, true));
		$newRole->save();
	}
}

$msg = 'Done' . ($dryRun ? 'DRY RUN!' : 'REAL RUN!');
KalturaLog::log($msg);
echo $msg;