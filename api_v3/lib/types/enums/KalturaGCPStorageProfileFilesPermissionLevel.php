<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaGCPStorageProfileFilesPermissionLevel extends KalturaStringEnum
{
	// ACL flags
	const ACL_PRIVATE = 'private';
	const ACL_PUBLIC_READ = 'publicRead';
	const ACL_PROJECT_PRIVATE = 'projectPrivate';
	const ACL_AUTHENTICATED_READ = 'authenticatedRead';
	const ACL_BUCKET_OWNER_READ = 'bucketOwnerRead';
	const ACL_BUCKET_OWNER_FULL_CONTROL = 'bucketOwnerFullControl';
}
