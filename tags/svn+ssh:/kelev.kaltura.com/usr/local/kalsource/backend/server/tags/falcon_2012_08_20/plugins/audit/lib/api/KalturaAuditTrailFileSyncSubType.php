<?php
/**
 * @package plugins.audit
 * @subpackage api.enums
 */
class KalturaAuditTrailFileSyncSubType extends KalturaEnum 
{
	const ENTRY_DATA = 11;
	const ENTRY_DATA_EDIT = 12;
	const ENTRY_THUMB = 13;
	const ENTRY_ARCHIVE = 14;
	const ENTRY_DOWNLOAD = 15;
	const ENTRY_OFFLINE_THUMB = 16;
	const ENTRY_ISM = 17;
	const ENTRY_ISMC = 18;
	const ENTRY_CONVERSION_LOG = 19;
	
	const UICONF_DATA = 21;
	const UICONF_FEATURES = 22;
	
	const BATCHJOB_BULKUPLOADCSV = 31;
	const BATCHJOB_BULKUPLOADLOG = 32;
	const BATCHJOB_CONFIG = 33;
	
	const FLAVOR_ASSET_ASSET = 41;
	const FLAVOR_ASSET_CONVERT_LOG = 42;
	
	const METADATA_DATA = 51;
	
	const METADATA_PROFILE_DEFINITION = 61;
	const METADATA_PROFILE_VIEWS = 62;
}