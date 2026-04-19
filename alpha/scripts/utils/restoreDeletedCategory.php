<?php
/**
 * Script to restore a deleted category and all its related data.
 * 
 * This script restores:
 * 1. The deleted category itself
 * 2. All categoryEntry records that were deleted at the same time (linking entries to the category)
 * 3. All categoryKuser records that were deleted at the same time (category user memberships)
 * 
 * Usage:
 *   php restoreDeletedCategory.php <categoryId> [realrun|dryrun] [timeDeltaSeconds]
 * 
 * Arguments:
 *   categoryId        - The ID of the category to restore
 *   realrun|dryrun    - Optional. Default is 'dryrun'. Use 'realrun' to actually make changes
 *   timeDeltaSeconds  - Optional. Time delta in seconds to match related deleted records. Default: 60
 * 
 * Example:
 *   php restoreDeletedCategory.php 12345 dryrun
 *   php restoreDeletedCategory.php 12345 realrun 120
 * 
 * @package Core
 * @subpackage scripts
 */

ini_set('memory_limit', '1024M');
set_time_limit(0);

if ($argc < 2)
{
	echo PHP_EOL . ' ---- Restore Deleted Category ---- ' . PHP_EOL;
	echo ' Usage: php ' . $argv[0] . ' <categoryId> [realrun|dryrun] [timeDeltaSeconds]' . PHP_EOL;
	echo PHP_EOL;
	echo ' Arguments:' . PHP_EOL;
	echo '   categoryId        - The ID of the category to restore' . PHP_EOL;
	echo '   realrun|dryrun    - Optional. Default is "dryrun". Use "realrun" to actually make changes' . PHP_EOL;
	echo '   timeDeltaSeconds  - Optional. Time delta in seconds to match related deleted records. Default: 60' . PHP_EOL;
	echo PHP_EOL;
	echo ' Example:' . PHP_EOL;
	echo '   php ' . $argv[0] . ' 12345 dryrun' . PHP_EOL;
	echo '   php ' . $argv[0] . ' 12345 realrun 120' . PHP_EOL;
	echo PHP_EOL;
	die(' Error: Missing categoryId parameter' . PHP_EOL . PHP_EOL);
}

$categoryId = $argv[1];

$dryRun = true;
if (isset($argv[2]) && $argv[2] === 'realrun')
{
	$dryRun = false;
}

// Time delta in seconds - used to find related records that were deleted around the same time
$timeDeltaSeconds = 60;
if (isset($argv[3]) && is_numeric($argv[3]))
{
	$timeDeltaSeconds = (int)$argv[3];
}

require_once(__DIR__ . '/../bootstrap.php');

KalturaStatement::setDryRun($dryRun);
KalturaLog::info($dryRun ? 'DRY RUN MODE - No changes will be made' : 'REAL RUN MODE - Changes will be saved');
KalturaLog::info("Category ID: $categoryId");
KalturaLog::info("Time delta for matching related records: $timeDeltaSeconds seconds");

// Step 1: Retrieve the deleted category
KalturaLog::info("Step 1: Looking for deleted category with ID: $categoryId");
categoryPeer::setUseCriteriaFilter(false);
$category = categoryPeer::retrieveByPK($categoryId);
categoryPeer::setUseCriteriaFilter(true);

if (!$category)
{
	KalturaLog::err("Category with ID $categoryId not found");
	die("Error: Category with ID $categoryId not found" . PHP_EOL);
}

if ($category->getStatus() !== CategoryStatus::DELETED)
{
	KalturaLog::warning("Category with ID $categoryId is not in DELETED status (current status: " . $category->getStatus() . ")");
	die("Error: Category with ID $categoryId is not deleted (status: " . $category->getStatus() . ")" . PHP_EOL);
}

// getDeletedAt(null) returns UNIX timestamp when format is null, otherwise formatted string
$deletedAt = $category->getDeletedAt(null);
if (!$deletedAt)
{
	KalturaLog::err("Category with ID $categoryId has no deletion timestamp");
	die("Error: Category with ID $categoryId has no deletion timestamp" . PHP_EOL);
}

$deletedAtFormatted = date('Y-m-d H:i:s', $deletedAt);
KalturaLog::info("Category '$categoryId' was deleted at: $deletedAtFormatted");
KalturaLog::info("Category name: " . $category->getName());
KalturaLog::info("Category full name: " . $category->getFullName());
KalturaLog::info("Partner ID: " . $category->getPartnerId());

// Calculate time range for finding related deleted records
$deletionTimeStart = $deletedAt - $timeDeltaSeconds;
$deletionTimeEnd = $deletedAt + $timeDeltaSeconds;
$deletionTimeStartFormatted = date('Y-m-d H:i:s', $deletionTimeStart);
$deletionTimeEndFormatted = date('Y-m-d H:i:s', $deletionTimeEnd);
KalturaLog::info("Looking for related records deleted between $deletionTimeStartFormatted and $deletionTimeEndFormatted");

// Step 2: Find all categoryEntry records that were deleted around the same time
KalturaLog::info("Step 2: Looking for deleted categoryEntry records for category ID: $categoryId");
categoryEntryPeer::setUseCriteriaFilter(false);

$categoryEntryCriteria = new Criteria();
$categoryEntryCriteria->add(categoryEntryPeer::CATEGORY_ID, $categoryId);
$categoryEntryCriteria->add(categoryEntryPeer::STATUS, CategoryEntryStatus::DELETED);
$categoryEntryCriteria->add(categoryEntryPeer::UPDATED_AT, $deletionTimeStart, Criteria::GREATER_EQUAL);
$categoryEntryCriteria->add(categoryEntryPeer::UPDATED_AT, $deletionTimeEnd, Criteria::LESS_EQUAL);

$deletedCategoryEntries = categoryEntryPeer::doSelect($categoryEntryCriteria);
categoryEntryPeer::setUseCriteriaFilter(true);

$categoryEntriesCount = count($deletedCategoryEntries);
KalturaLog::info("Found $categoryEntriesCount deleted categoryEntry records to restore");

// Step 3: Find all categoryKuser records that were deleted around the same time
KalturaLog::info("Step 3: Looking for deleted categoryKuser records for category ID: $categoryId");
categoryKuserPeer::setUseCriteriaFilter(false);

$categoryKuserCriteria = new Criteria();
$categoryKuserCriteria->add(categoryKuserPeer::CATEGORY_ID, $categoryId);
$categoryKuserCriteria->add(categoryKuserPeer::STATUS, CategoryKuserStatus::DELETED);
$categoryKuserCriteria->add(categoryKuserPeer::UPDATED_AT, $deletionTimeStart, Criteria::GREATER_EQUAL);
$categoryKuserCriteria->add(categoryKuserPeer::UPDATED_AT, $deletionTimeEnd, Criteria::LESS_EQUAL);

$deletedCategoryKusers = categoryKuserPeer::doSelect($categoryKuserCriteria);
categoryKuserPeer::setUseCriteriaFilter(true);

$categoryKusersCount = count($deletedCategoryKusers);
KalturaLog::info("Found $categoryKusersCount deleted categoryKuser records to restore");

// Summary before restore
KalturaLog::info("=== Summary of records to restore ===");
KalturaLog::info("Category: $categoryId (" . $category->getName() . ")");
KalturaLog::info("Category Entries: $categoryEntriesCount");
KalturaLog::info("Category Users: $categoryKusersCount");

if ($dryRun)
{
	KalturaLog::info("=== DRY RUN - No changes will be made ===");
	
	// List category entries that would be restored
	if ($categoryEntriesCount > 0)
	{
		KalturaLog::info("CategoryEntry records that would be restored:");
		foreach ($deletedCategoryEntries as $categoryEntry)
		{
			$updatedAtFormatted = $categoryEntry->getUpdatedAt('Y-m-d H:i:s');
			KalturaLog::info("  - CategoryEntry ID: " . $categoryEntry->getId() . 
				", Entry ID: " . $categoryEntry->getEntryId() . 
				", Updated at: " . $updatedAtFormatted);
		}
	}
	
	// List category users that would be restored
	if ($categoryKusersCount > 0)
	{
		KalturaLog::info("CategoryKuser records that would be restored:");
		foreach ($deletedCategoryKusers as $categoryKuser)
		{
			$updatedAtFormatted = $categoryKuser->getUpdatedAt('Y-m-d H:i:s');
			KalturaLog::info("  - CategoryKuser ID: " . $categoryKuser->getId() . 
				", Kuser ID: " . $categoryKuser->getKuserId() . 
				", Puser ID: " . $categoryKuser->getPuserId() .
				", Updated at: " . $updatedAtFormatted);
		}
	}
}
else
{
	KalturaLog::info("=== Starting restore process ===");
	
	// Step 4: Restore the category
	KalturaLog::info("Step 4: Restoring category ID: $categoryId");
	$category->setStatus(CategoryStatus::ACTIVE);
	$category->setDeletedAt(null);
	$category->save();
	KalturaLog::info("Category $categoryId restored to ACTIVE status");
	categoryPeer::clearInstancePool();
	
	// Step 5: Restore categoryEntry records
	KalturaLog::info("Step 5: Restoring categoryEntry records");
	$restoredCategoryEntries = 0;
	foreach ($deletedCategoryEntries as $categoryEntry)
	{
		KalturaLog::info("Restoring categoryEntry ID: " . $categoryEntry->getId() . 
			" (Entry ID: " . $categoryEntry->getEntryId() . ")");
		$categoryEntry->setStatus(CategoryEntryStatus::ACTIVE);
		$categoryEntry->save();
		$restoredCategoryEntries++;
		
		// Clear instance pool periodically to manage memory
		if ($restoredCategoryEntries % 100 === 0)
		{
			categoryEntryPeer::clearInstancePool();
			KalturaLog::info("Restored $restoredCategoryEntries categoryEntry records so far...");
		}
	}
	categoryEntryPeer::clearInstancePool();
	KalturaLog::info("Restored $restoredCategoryEntries categoryEntry records");
	
	// Step 6: Restore categoryKuser records
	KalturaLog::info("Step 6: Restoring categoryKuser records");
	$restoredCategoryKusers = 0;
	foreach ($deletedCategoryKusers as $categoryKuser)
	{
		KalturaLog::info("Restoring categoryKuser ID: " . $categoryKuser->getId() . 
			" (Kuser ID: " . $categoryKuser->getKuserId() . ", Puser ID: " . $categoryKuser->getPuserId() . ")");
		$categoryKuser->setStatus(CategoryKuserStatus::ACTIVE);
		$categoryKuser->save();
		$restoredCategoryKusers++;
		
		// Clear instance pool periodically to manage memory
		if ($restoredCategoryKusers % 100 === 0)
		{
			categoryKuserPeer::clearInstancePool();
			KalturaLog::info("Restored $restoredCategoryKusers categoryKuser records so far...");
		}
	}
	categoryKuserPeer::clearInstancePool();
	KalturaLog::info("Restored $restoredCategoryKusers categoryKuser records");
	
	// Flush any pending events
	kEventsManager::flushEvents();
	
	KalturaLog::info("=== Restore completed ===");
	KalturaLog::info("Total category entries restored: $restoredCategoryEntries");
	KalturaLog::info("Total category users restored: $restoredCategoryKusers");
}

KalturaLog::info("Script finished successfully");
