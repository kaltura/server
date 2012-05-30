<?php
/**
 * Call flavor assete setters to migrate from old columns to new custom data fields.
 * After all flavors will be migrated we can remove the columns from the db.
 *
 * @package Deployment
 * @subpackage updates
 */ 

$countLimitEachLoop = 500;

//------------------------------------------------------

require_once(dirname(__FILE__).'/../../bootstrap.php');

$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
	
$c = new Criteria();

if (isset($argv[1]))
{
    $c->addAnd(BulkUploadResultPeer::ID, $argv[1], Criteria::GREATER_EQUAL);
}
if (isset($argv[2]))
{
    $c->addAnd(BulkUploadResultPeer::PARTNER_ID, $argv[2], Criteria::EQUAL);
}
if (isset($argv[3]))
{
    $c->addAnd(BulkUploadResultPeer::UPDATED_AT, $argv[3], Criteria::GREATER_EQUAL);
}
$c->addAscendingOrderByColumn(BulkUploadResultPeer::UPDATED_AT);
$c->setLimit($countLimitEachLoop);
$bulkUploadResults = BulkUploadResultPeer::doSelect($c, $con);

while($bulkUploadResults && count($bulkUploadResults))
{
	foreach($bulkUploadResults as $bulkUploadResult)
	{
		/* @var $bulkUploadResult BulkUploadResult */
		$bulkUploadResult->putInCustomData("title", $bulkUploadResult->getTitle());
		$bulkUploadResult->putInCustomData("description", $bulkUploadResult->getDescription());
		$bulkUploadResult->putInCustomData("tags", $bulkUploadResult->getTags());
		$bulkUploadResult->putInCustomData("category", $bulkUploadResult->getCategory());
		$bulkUploadResult->putInCustomData("content_type", $bulkUploadResult->getContentType());
		$bulkUploadResult->putInCustomData("conversion_profile_id", $bulkUploadResult->getConversionProfileId());
		$bulkUploadResult->putInCustomData("access_control_profile_id", $bulkUploadResult->getAccessControlProfileId());
		$bulkUploadResult->putInCustomData("url", $bulkUploadResult->getUrl());
		$bulkUploadResult->putInCustomData("entry_status", $bulkUploadResult->getEntryStatus());
		$bulkUploadResult->putInCustomData("thumbnail_url", $bulkUploadResult->getThumbnailUrl());
		$bulkUploadResult->putInCustomData("thumbnail_saved", $bulkUploadResult->getThumbnailSaved());
		$bulkUploadResult->putInCustomData("schedule_end_date", $bulkUploadResult->getScheduleEndDate());
		$bulkUploadResult->putInCustomData("schedule_start_date", $bulkUploadResult->getScheduleStartDate());
		
		$bulkUploadResult->save();
		
		var_dump("Last handled id: ".$bulkUploadResult->getId());
		
	}
	$countLimitEachLoop += $countLimitEachLoop;
	$c->setOffset($countLimitEachLoop);
	$bulkUploadResults = BulkUploadResultPeer::doSelect($c, $con);
	usleep(100);
}
