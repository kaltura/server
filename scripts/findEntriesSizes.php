<?php
/**
 * This script calculates the storage-delta for each partner for each day.
 * If a partner only deletes an entry at specific date, he might have negative storage-delta.
 *
 * Script inputs:
 * (Mandatory) start date (yyyy-mm-dd, e.g: php findEntriesSizes.php 2009-09-24)
 * (Optional)  range end date (yyyy-mm-dd, e.g: php findEntriesSizes.php 2009-09-30)
 *
 * According to the example inputs above, the script will find all entries that their
 * modified_at field is greater than or equal to 2009-09-24 and is less than 2009-09-30
 *
 * The script will, then, calculate the size of each entry, and if that size is different from the last size written on the entry,
 * it will add it to the sum-storage-delta of that entry's partner for the modified_at date
 *
 * This script should never delete any rows from partner_activity table.
 */

require_once ( dirname(__FILE__)."/define.php" );

ini_set( "memory_limit","512M" );

DbManager::setConfig(kConf::getDB());
DbManager::initialize();

$stderr = fopen("php://stderr", "w");

$stats = array();
$kusers = array();

$limit_size = 100;
$i = 0;

$requested_day = @$argv[1];
if (!$requested_day)
{
	fprintf($stderr, "usage: findEntriesSizes yyyy-mm-dd\n");
	die;
}

$next_day = @$argv[2] ? $argv[2] : date('Y-m-d', strtotime($requested_day) + 86400);

while(1)
{
	$c = new Criteria();
	$c->setLimit ( $limit_size );
	$c->setOffset( $i );
	$c->add(entryPeer::MODIFIED_AT, $requested_day, Criteria::GREATER_EQUAL);
	$c->addAnd(entryPeer::MODIFIED_AT, $next_day, Criteria::LESS_THAN);
	$c->addAnd(entryPeer::TYPE, entry::ENTRY_TYPE_MEDIACLIP);
	$c->addAnd(entryPeer::PARTNER_ID, 100, Criteria::NOT_EQUAL);
	/**
	 * Allow selecting deleted entries as well, so we will actually deduct storage for partners
	 */
	entryPeer::allowDeletedInCriteriaFilter();
	$entries = entryPeer::doSelect($c);
	
	if (!count($entries))
		break;

	foreach($entries as $entry)
	{
		echo $entry->getId().PHP_EOL;
		$prev_size = $entry->getStorageSize();
		$size = myEntryUtils::calcStorageSize($entry);
		
		if ($prev_size != $size) // update entry if size changed
		{
			$entry->setStorageSize($size);
			$entry->justSave();
		}
		else
		{
			$i++;
			continue; // skip entry because there was no change in size.
			/**
			 * either partner_activity is already updated with this entry (from previous run)
			 * or entry size was 0 and stayed 0 so it doesn't need to be added to delta
			 */
		}
		
		/**
		 * THE DATE ON OF THE ACTIVITY IS THE Y-m-d format of the entry's modified_at
		 *
		 * Since the modified_at must be in the range of dates given by the user
		 *  -------- WE MIGHT GET ROWS UPDATED BY THIS !!!!! --------
		 */
		$date = $entry->getModifiedAt('Y-m-d');
		if ($date < '2008-06-01')
			$date = '2008-06-01';
		
		$partner_id = $entry->getPartnerId();

		if (!array_key_exists($partner_id, $stats)) // will only happen on first entry, kept for ease of reading the code
			$stats[$partner_id] = array();
		
		if (!array_key_exists($date, $stats[$partner_id]))
			$stats[$partner_id][$date] = array("size" => 0, "count" => 0);
		
		if (!array_key_exists($entry->getKuserId(), $kusers))
			$kusers[$entry->getKuserId()] = 0;

		$kusers[$entry->getKuserId()] += ($size - $prev_size) / 1024;
		
		$stats[$partner_id][$date]["size"] += ($size - $prev_size) / 1024;
		$stats[$partner_id][$date]["count"]++;
		
		$i++;
	}
	fprintf($stderr, "$i\r");
}

$connection = Propel::getConnection();

foreach($stats as $partner_id => $dates) 
{
	foreach($dates as $date => $pstats)
	{
		/**
		 * we are deleting entries for a specific partner on a specific date.
		 *
		 * if we run twice on the same date, this way we only re-insert partners found in this run
		 * and not in the first run.
		 */
		$query = "DELETE FROM ".PartnerActivityPeer::TABLE_NAME.
			" WHERE ".PartnerActivityPeer::ACTIVITY."=".PartnerActivity::PARTNER_ACTIVITY_STORAGE.
			" AND ".PartnerActivityPeer::PARTNER_ID."=".$partner_id.
			" AND ".PartnerActivityPeer::ACTIVITY_DATE."='".$date."'";
			
		$statement = $connection->prepareStatement($query);
		$resultset = $statement->executeQuery();
		
		//print "$partner_id $date ".$pstats["size"]." ".$pstats["count"]."\n";
		// output partner ID and date, 
		print "$partner_id $date \n";
		PartnerActivity::incrementActivity($partner_id, PartnerActivity::PARTNER_ACTIVITY_STORAGE, PartnerActivity::PARTNER_SUB_ACTIVITY_STORAGE_SIZE, floor($pstats["size"]/1024), $date);
		PartnerActivity::incrementActivity($partner_id, PartnerActivity::PARTNER_ACTIVITY_STORAGE, PartnerActivity::PARTNER_SUB_ACTIVITY_STORAGE_COUNT, $pstats["count"], $date);
	}
}

foreach( $kusers as $kuser_id => $size )
{
	$kuser = kuserPeer::retrieveByPK($kuser_id);
	if ($kuser)
	{
		$kuser->setStorageSize( $kuser->getStorageSize() + $size );
		$kuser->save();
	}
	unset($kuser);
	//echo 'kuser '.$kuser_id.' has total daily size of '.$size.PHP_EOL;
}
?>
