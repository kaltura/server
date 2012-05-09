<?php
/**
 * Call flavor assete setters to migrate from old columns to new custom data fields.
 * After all flavors will be migrated we can remove the columns from the db.
 *
 * @package Deployment
 * @subpackage updates
 */ 


$dryRun = true; //TODO: change for real run
if(in_array('realrun', $argv))
	$dryRun = false;
	
$stopFile = dirname(__FILE__).'/stop_flavor_migration'; // creating this file will stop the script
$countLimitEachLoop = 500;

//------------------------------------------------------

require_once(dirname(__FILE__).'/../../bootstrap.php');

$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
KalturaStatement::setDryRun($dryRun);
	
$c = new Criteria();

$bulkUploadResults = BulkUploadResultPeer::doSelect($c, $con);

while($bulkUploadResults && count($bulkUploadResults))
{
	foreach($bulkUploadResults as $bulkUploadResult)
	{
		/* @var $bulkUploadResult BulkUploadResult */
		$bulkUploadResult->putInCustomData(BulkUploadResultEntry::TITLE, $bulkUploadResult->getTitle());
		$bulkUploadResult->putInCustomData(BulkUploadResultEntry::DESCRIPTION, $bulkUploadResult->getDescription());
		$bulkUploadResult->putInCustomData(BulkUploadResultEntry::TAGS, $bulkUploadResult->getTags());
		$bulkUploadResult->putInCustomData(BulkUploadResultEntry::CATEGORY, $bulkUploadResult->getCategory());
		$bulkUploadResult->putInCustomData(BulkUploadResultEntry::CONTENT_TYPE, $bulkUploadResult->getContentType());
		$bulkUploadResult->putInCustomData(BulkUploadResultEntry::CONVERSION_PROFILE_ID, $bulkUploadResult->getConversionProfileId());
		$bulkUploadResult->putInCustomData(BulkUploadResultEntry::ACCESS_CONSTROL_PROFILE_ID, $bulkUploadResult->getAccessControlProfileId());
		$bulkUploadResult->putInCustomData(BulkUploadResultEntry::URL, $bulkUploadResult->getUrl());
		$bulkUploadResult->putInCustomData(BulkUploadResultEntry::ENTRY_STATUS, $bulkUploadResult->getEntryStatus());
		$bulkUploadResult->putInCustomData(BulkUploadResultEntry::THUMBNAIL_URL, $bulkUploadResult->getThumbnailUrl());
		$bulkUploadResult->putInCustomData(BulkUploadResultEntry::THUMBNAIL_SAVED, $bulkUploadResult->getThumbnailSaved());
		$bulkUploadResult->putInCustomData(BulkUploadResultEntry::SCHEDULE_END_DATE, $bulkUploadResult->getScheduleEndDate());
		$bulkUploadResult->putInCustomData(BulkUploadResultEntry::SCHEDULE_START_DATE, $bulkUploadResult->getScheduleStartDate());
		
		$bulkUploadResult->save();
		
		
	}
	$bulkUploadResults = BulkUploadResultPeer::doSelect($c, $con);
	sleep(1);
}
