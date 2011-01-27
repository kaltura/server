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
$role->setPermissionNames(UserRole::ALL_PARTNER_PERMISSIONS_WILDCARD);
$role->setStatus(UserRoleStatus::ACTIVE);
$role->setTags('kmc,partner_admin');
$role->setPartnerId(0);
$userRoles[] = $role;

$role = new UserRole();
$role->setName('Manager');
$role->setDescription('Full control over publisher account functionalities');
$managerPermissions = 'KMC_ACCESS,KMC_READ_ONLY,CONTENT_MODERATE_APPROVE_REJECT,CONTENT_INGEST_UPLOAD,CONTENT_INGEST_BULK_UPLOAD,CONTENT_INGEST_FEED,CONTENT_MANAGE_DISTRIBUTION_BASE,CONTENT_MANAGE_DISTRIBUTION_WHERE,CONTENT_MANAGE_DISTRIBUTION_SEND,CONTENT_MANAGE_DISTRIBUTION_REMOVE,CONTENT_MANAGE_DISTRIBUTION_PROFILE_MODIFY,CONTENT_MANAGE_VIRUS_SCAN,CONTENT_MANAGE_MIX,CONTENT_MANAGE_BASE,CONTENT_MANAGE_METADATA,CONTENT_MANAGE_ASSIGN_CATEGORIES,CONTENT_MANAGE_THUMBNAIL,CONTENT_MANAGE_SCHEDULE,CONTENT_MANAGE_ACCESS_CONTROL,CONTENT_MANAGE_CUSTOM_DATA,CONTENT_MANAGE_DELETE,CONTENT_MANAGE_RECONVERT,CONTENT_MANAGE_EDIT_CATEGORIES,CONTENT_MANAGE_ANNOTATION,CONTENT_MANAGE_SHARE,CONTENT_MANAGE_DOWNLOAD,LIVE_STREAM_ADD,LIVE_STREAM_UPDATE,PLAYLIST_BASE,PLAYLIST_ADD,PLAYLIST_UPDATE,PLAYLIST_DELETE,CONTENT_MANAGE_EMBED_CODE,PLAYLIST_EMBED_CODE,CONTENT_MODERATE_BASE,CONTENT_MODERATE_METADATA,CONTENT_MODERATE_CUSTOM_DATA,SYNDICATION_BASE,SYNDICATION_ADD,SYNDICATION_UPDATE,SYNDICATION_DELETE,STUDIO_BASE,STUDIO_ADD_UICONF,STUDIO_UPDATE_UICONF,STUDIO_DELETE_UICONF,ACCOUNT_BASE,INTEGRATION_BASE,ACCESS_CONTROL_BASE,ACCESS_CONTROL_ADD,ACCESS_CONTROL_UPDATE,ACCESS_CONTROL_DELETE,TRANSCODING_BASE,TRANSCODING_ADD,TRANSCODING_UPDATE,TRANSCODING_DELETE,CUSTOM_DATA_PROFILE_BASE,CUSTOM_DATA_PROFILE_ADD,CUSTOM_DATA_PROFILE_UPDATE,CUSTOM_DATA_PROFILE_DELETE,ADMIN_BASE,ANALYTICS_BASE,WIDGET_ADMIN,SEARCH_SERVICE,ANALYTICS_SEND_DATA,AUDIT_TRAIL_BASE,AUDIT_TRAIL_ADD,ADVERTISING_BASE,ADVERTISING_UPDATE_SETTINGS,PLAYLIST_EMBED_CODE,STUDIO_BRAND_UICONF,STUDIO_SELECT_CONTENT,CONTENT_MANAGE_EMBED_CODE,PLAYLIST_EMBED_CODE';
$role->setPermissionNames($managerPermissions);
$role->setStatus(UserRoleStatus::ACTIVE);
$role->setTags('kmc');
$role->setPartnerId(99);
$userRoles[] = $role;

$role = new UserRole();
$role->setName('Content Uploader');
$role->setDescription('Access to content ingestion and management functionalities');
$uploaderPermissions = 'KMC_ACCESS,KMC_READ_ONLY,CONTENT_INGEST_UPLOAD,CONTENT_INGEST_BULK_UPLOAD,CONTENT_MANAGE_DISTRIBUTION_BASE,CONTENT_MANAGE_BASE';
$role->setPermissionNames($uploaderPermissions);
$role->setStatus(UserRoleStatus::ACTIVE);
$role->setTags('kmc');
$role->setPartnerId(99);
$userRoles[] = $role;

$role = new UserRole();
$role->setName('Content Moderator');
$role->setDescription('Access to publisher content moderation panel');
$moderatorPermissions = 'KMC_ACCESS,KMC_READ_ONLY,CONTENT_MODERATE_APPROVE_REJECT,CONTENT_MODERATE_BASE,CONTENT_MODERATE_METADATA,CONTENT_MODERATE_CUSTOM_DATA';
$role->setPermissionNames($moderatorPermissions);
$role->setStatus(UserRoleStatus::ACTIVE);
$role->setTags('kmc');
$role->setPartnerId(99);
$userRoles[] = $role;

$role = new UserRole();
$role->setName('Player Designer');
$role->setDescription('Access to publisher studio');
$designerPermissions = 'KMC_ACCESS,KMC_READ_ONLY,STUDIO_BASE,STUDIO_ADD_UICONF,STUDIO_UPDATE_UICONF,STUDIO_DELETE_UICONF,STUDIO_BRAND_UICONF';
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

$msg = 'Done - ' . ($dryRun ? 'DRY RUN!' : 'REAL RUN!');
KalturaLog::log($msg);
echo $msg;