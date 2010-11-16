<?php
interface BatchJobType extends BaseEnum
{
	const CONVERT = 0;
	const IMPORT = 1;
	const DELETE = 2;
	const FLATTEN = 3;
	const BULKUPLOAD = 4;
	const DVDCREATOR = 5;
	const DOWNLOAD = 6;
	const OOCONVERT = 7;
	const CONVERT_PROFILE = 10;
	const POSTCONVERT = 11;
	const PULL = 12;
	const REMOTE_CONVERT = 13;
	const EXTRACT_MEDIA = 14;
	const MAIL = 15;
	const NOTIFICATION = 16;
	const CLEANUP = 17;
	const SCHEDULER_HELPER = 18;
	const BULKDOWNLOAD = 19;
	const DB_CLEANUP = 20;
	const PROVISION_PROVIDE = 21;
	const CONVERT_COLLECTION = 22;
	const STORAGE_EXPORT = 23;
	const PROVISION_DELETE = 24;
	const STORAGE_DELETE = 25;
	const EMAIL_INGESTION = 26;
	const METADATA_IMPORT = 27;
	const METADATA_TRANSFORM = 28;
	const FILESYNC_IMPORT = 29;
	
	const PROJECT = 1000;
}
