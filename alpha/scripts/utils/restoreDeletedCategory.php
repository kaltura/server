<?php
/**
 * Script to restore a deleted category and all its related data.
 * 
 * This script restores:
 * 1. The deleted category itself (status from DELETED to ACTIVE)
 * 2. Child categories recursively
 * 3. CategoryEntry records that were deleted at the same time (if entries weren't moved to parent)
 * 4. CategoryKuser records for MANUAL inheritance categories
 * 5. Recalculates all counts (entries, members, sub-categories)
 * 6. Rebuilds hierarchy fields (fullIds, depth, fullName)
 * 7. Triggers re-indexing for search
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

// Constants for batch processing and display
const DATE_FORMAT = 'Y-m-d H:i:s';
const CATEGORY_BATCH_SIZE = 50;
const RELATION_BATCH_SIZE = 100;
const DISPLAY_LIMIT = 50;

ini_set('memory_limit', '2048M');
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

$deletedAtFormatted = date(DATE_FORMAT, $deletedAt);
KalturaLog::info("Category '$categoryId' was deleted at: $deletedAtFormatted");
KalturaLog::info("Category name: " . $category->getName());
KalturaLog::info("Category full name: " . $category->getFullName());
KalturaLog::info("Partner ID: " . $category->getPartnerId());
KalturaLog::info("Inheritance Type: " . ($category->getInheritanceType() === InheritanceType::MANUAL ? 'MANUAL' : 'INHERIT'));

// Calculate time range for finding related deleted records
$deletionTimeStart = $deletedAt - $timeDeltaSeconds;
$deletionTimeEnd = $deletedAt + $timeDeltaSeconds;
$deletionTimeStartFormatted = date(DATE_FORMAT, $deletionTimeStart);
$deletionTimeEndFormatted = date(DATE_FORMAT, $deletionTimeEnd);
KalturaLog::info("Looking for related records deleted between $deletionTimeStartFormatted and $deletionTimeEndFormatted");

// Step 2: Find all deleted child categories (using fullIds prefix match)
KalturaLog::info("Step 2: Looking for deleted child categories");
$fullIds = $category->getFullIds();
// Escape special SQL LIKE characters to prevent unintended matches
// Full IDs are numeric category IDs separated by '>' so shouldn't contain these, but escape for safety
$escapedFullIds = str_replace(array('%', '_'), array('\\%', '\\_'), $fullIds);
categoryPeer::setUseCriteriaFilter(false);

$childCategoriesCriteria = new Criteria();
$childCategoriesCriteria->add(categoryPeer::FULL_IDS, $escapedFullIds . categoryPeer::CATEGORY_SEPARATOR . '%', Criteria::LIKE);
$childCategoriesCriteria->add(categoryPeer::STATUS, CategoryStatus::DELETED);
$childCategoriesCriteria->add(categoryPeer::DELETED_AT, $deletionTimeStart, Criteria::GREATER_EQUAL);
$childCategoriesCriteria->add(categoryPeer::DELETED_AT, $deletionTimeEnd, Criteria::LESS_EQUAL);
$childCategoriesCriteria->addAscendingOrderByColumn(categoryPeer::DEPTH); // Restore parents before children

$deletedChildCategories = categoryPeer::doSelect($childCategoriesCriteria);
categoryPeer::setUseCriteriaFilter(true);

$childCategoriesCount = count($deletedChildCategories);
KalturaLog::info("Found $childCategoriesCount deleted child categories to restore");

// Build list of all category IDs to restore (parent + children)
$allCategoryIds = array($categoryId);
foreach ($deletedChildCategories as $childCategory)
{
	$allCategoryIds[] = $childCategory->getId();
}

// Step 3: Find all categoryEntry records for all categories to restore
KalturaLog::info("Step 3: Looking for deleted categoryEntry records for all categories");
categoryEntryPeer::setUseCriteriaFilter(false);

$categoryEntryCriteria = new Criteria();
$categoryEntryCriteria->add(categoryEntryPeer::CATEGORY_ID, $allCategoryIds, Criteria::IN);
$categoryEntryCriteria->add(categoryEntryPeer::STATUS, CategoryEntryStatus::DELETED);
$categoryEntryCriteria->add(categoryEntryPeer::UPDATED_AT, $deletionTimeStart, Criteria::GREATER_EQUAL);
$categoryEntryCriteria->add(categoryEntryPeer::UPDATED_AT, $deletionTimeEnd, Criteria::LESS_EQUAL);

$deletedCategoryEntries = categoryEntryPeer::doSelect($categoryEntryCriteria);
categoryEntryPeer::setUseCriteriaFilter(true);

$categoryEntriesCount = count($deletedCategoryEntries);
KalturaLog::info("Found $categoryEntriesCount deleted categoryEntry records to restore");

// Step 4: Find all categoryKuser records for MANUAL inheritance categories only
KalturaLog::info("Step 4: Looking for deleted categoryKuser records (MANUAL inheritance only)");

// Filter category IDs to only include MANUAL inheritance types
$manualInheritanceCategoryIds = array();
if ($category->getInheritanceType() === InheritanceType::MANUAL)
{
	$manualInheritanceCategoryIds[] = $categoryId;
}
foreach ($deletedChildCategories as $childCategory)
{
	if ($childCategory->getInheritanceType() === InheritanceType::MANUAL)
	{
		$manualInheritanceCategoryIds[] = $childCategory->getId();
	}
}

$deletedCategoryKusers = array();
$categoryKusersCount = 0;

if (!empty($manualInheritanceCategoryIds))
{
	categoryKuserPeer::setUseCriteriaFilter(false);
	
	$categoryKuserCriteria = new Criteria();
	$categoryKuserCriteria->add(categoryKuserPeer::CATEGORY_ID, $manualInheritanceCategoryIds, Criteria::IN);
	$categoryKuserCriteria->add(categoryKuserPeer::STATUS, CategoryKuserStatus::DELETED);
	$categoryKuserCriteria->add(categoryKuserPeer::UPDATED_AT, $deletionTimeStart, Criteria::GREATER_EQUAL);
	$categoryKuserCriteria->add(categoryKuserPeer::UPDATED_AT, $deletionTimeEnd, Criteria::LESS_EQUAL);
	
	$deletedCategoryKusers = categoryKuserPeer::doSelect($categoryKuserCriteria);
	categoryKuserPeer::setUseCriteriaFilter(true);
	
	$categoryKusersCount = count($deletedCategoryKusers);
}

KalturaLog::info("Found $categoryKusersCount deleted categoryKuser records to restore (from " . count($manualInheritanceCategoryIds) . " MANUAL inheritance categories)");

// Summary before restore
KalturaLog::info("=== Summary of records to restore ===");
KalturaLog::info("Main Category: $categoryId (" . $category->getName() . ")");
KalturaLog::info("Child Categories: $childCategoriesCount");
KalturaLog::info("Category Entries: $categoryEntriesCount");
KalturaLog::info("Category Users (MANUAL inheritance only): $categoryKusersCount");

if ($dryRun)
{
	KalturaLog::info("=== DRY RUN - No changes will be made ===");
	
	// List child categories that would be restored
	if ($childCategoriesCount > 0)
	{
		KalturaLog::info("Child categories that would be restored:");
		foreach ($deletedChildCategories as $childCategory)
		{
			$deletedAtFormatted = $childCategory->getDeletedAt(DATE_FORMAT);
			$inheritanceType = $childCategory->getInheritanceType() === InheritanceType::MANUAL ? 'MANUAL' : 'INHERIT';
			KalturaLog::info("  - Category ID: " . $childCategory->getId() . 
				", Name: " . $childCategory->getName() .
				", Depth: " . $childCategory->getDepth() .
				", Inheritance: " . $inheritanceType .
				", Deleted at: " . $deletedAtFormatted);
		}
	}
	
	// List category entries that would be restored
	if ($categoryEntriesCount > 0)
	{
		KalturaLog::info("CategoryEntry records that would be restored:");
		$displayCount = 0;
		foreach ($deletedCategoryEntries as $categoryEntry)
		{
			if ($displayCount >= DISPLAY_LIMIT)
			{
				KalturaLog::info("  ... and " . ($categoryEntriesCount - DISPLAY_LIMIT) . " more");
				break;
			}
			$updatedAtFormatted = $categoryEntry->getUpdatedAt(DATE_FORMAT);
			KalturaLog::info("  - CategoryEntry ID: " . $categoryEntry->getId() . 
				", Category ID: " . $categoryEntry->getCategoryId() .
				", Entry ID: " . $categoryEntry->getEntryId() . 
				", Updated at: " . $updatedAtFormatted);
			$displayCount++;
		}
	}
	
	// List category users that would be restored
	if ($categoryKusersCount > 0)
	{
		KalturaLog::info("CategoryKuser records that would be restored:");
		$displayCount = 0;
		foreach ($deletedCategoryKusers as $categoryKuser)
		{
			if ($displayCount >= DISPLAY_LIMIT)
			{
				KalturaLog::info("  ... and " . ($categoryKusersCount - DISPLAY_LIMIT) . " more");
				break;
			}
			$updatedAtFormatted = $categoryKuser->getUpdatedAt(DATE_FORMAT);
			KalturaLog::info("  - CategoryKuser ID: " . $categoryKuser->getId() . 
				", Category ID: " . $categoryKuser->getCategoryId() .
				", Kuser ID: " . $categoryKuser->getKuserId() . 
				", Puser ID: " . $categoryKuser->getPuserId() .
				", Updated at: " . $updatedAtFormatted);
			$displayCount++;
		}
	}
}
else
{
	KalturaLog::info("=== Starting restore process ===");
	
	// Step 5: Restore the main category
	KalturaLog::info("Step 5: Restoring main category ID: $categoryId");
	$category->setStatus(CategoryStatus::ACTIVE);
	$category->setDeletedAt(null);
	$category->save();
	KalturaLog::info("Main category $categoryId restored to ACTIVE status");
	
	// Step 6: Restore child categories (ordered by depth - parents first)
	KalturaLog::info("Step 6: Restoring child categories");
	$restoredChildCategories = 0;
	foreach ($deletedChildCategories as $childCategory)
	{
		KalturaLog::info("Restoring child category ID: " . $childCategory->getId() . 
			" (Name: " . $childCategory->getName() . ", Depth: " . $childCategory->getDepth() . ")");
		$childCategory->setStatus(CategoryStatus::ACTIVE);
		$childCategory->setDeletedAt(null);
		$childCategory->save();
		$restoredChildCategories++;
		
		if ($restoredChildCategories % CATEGORY_BATCH_SIZE === 0)
		{
			categoryPeer::clearInstancePool();
			KalturaLog::info("Restored $restoredChildCategories child categories so far...");
		}
	}
	KalturaLog::info("Restored $restoredChildCategories child categories");
	
	// Step 7: Restore categoryEntry records
	KalturaLog::info("Step 7: Restoring categoryEntry records");
	$restoredCategoryEntries = 0;
	foreach ($deletedCategoryEntries as $categoryEntry)
	{
		$categoryEntry->setStatus(CategoryEntryStatus::ACTIVE);
		$categoryEntry->save();
		$restoredCategoryEntries++;
		
		if ($restoredCategoryEntries % RELATION_BATCH_SIZE === 0)
		{
			categoryEntryPeer::clearInstancePool();
			KalturaLog::info("Restored $restoredCategoryEntries categoryEntry records so far...");
		}
	}
	categoryEntryPeer::clearInstancePool();
	KalturaLog::info("Restored $restoredCategoryEntries categoryEntry records");
	
	// Step 8: Restore categoryKuser records (MANUAL inheritance only)
	KalturaLog::info("Step 8: Restoring categoryKuser records (MANUAL inheritance only)");
	$restoredCategoryKusers = 0;
	foreach ($deletedCategoryKusers as $categoryKuser)
	{
		$categoryKuser->setStatus(CategoryKuserStatus::ACTIVE);
		$categoryKuser->save();
		$restoredCategoryKusers++;
		
		if ($restoredCategoryKusers % RELATION_BATCH_SIZE === 0)
		{
			categoryKuserPeer::clearInstancePool();
			KalturaLog::info("Restored $restoredCategoryKusers categoryKuser records so far...");
		}
	}
	categoryKuserPeer::clearInstancePool();
	KalturaLog::info("Restored $restoredCategoryKusers categoryKuser records");
	
	// Step 9: Rebuild hierarchy and recalculate counts for all restored categories
	KalturaLog::info("Step 9: Rebuilding hierarchy fields and recalculating counts");
	
	// Re-fetch all categories to process in a single batch query
	categoryPeer::clearInstancePool();
	$allCategoryIdsToProcess = array($categoryId);
	foreach ($deletedChildCategories as $childCategory)
	{
		$allCategoryIdsToProcess[] = $childCategory->getId();
	}
	
	categoryPeer::setUseCriteriaFilter(false);
	$allCategoriesToProcess = categoryPeer::retrieveByPKs($allCategoryIdsToProcess);
	categoryPeer::setUseCriteriaFilter(true);
	
	// Sort by depth to ensure parents are processed before children
	usort($allCategoriesToProcess, function($a, $b) {
		return $a->getDepth() - $b->getDepth();
	});
	
	$processedCategories = 0;
	foreach ($allCategoriesToProcess as $categoryToProcess)
	{
		$catId = $categoryToProcess->getId();
		KalturaLog::info("Processing category ID: $catId - Rebuilding hierarchy and counts");
		
		// Rebuild hierarchy fields
		$categoryToProcess->reSetFullIds();
		$categoryToProcess->reSetDepth();
		$categoryToProcess->reSetFullName();
		
		// Recalculate counts
		$categoryToProcess->reSetEntriesCount();
		$categoryToProcess->reSetDirectEntriesCount();
		$categoryToProcess->reSetDirectSubCategoriesCount();
		
		// Recalculate member counts for MANUAL inheritance categories
		if ($categoryToProcess->getInheritanceType() === InheritanceType::MANUAL)
		{
			$categoryToProcess->reSetMembersCount();
			$categoryToProcess->reSetPendingMembersCount();
		}
		
		$categoryToProcess->save();
		$processedCategories++;
		
		if ($processedCategories % CATEGORY_BATCH_SIZE === 0)
		{
			categoryPeer::clearInstancePool();
			KalturaLog::info("Processed $processedCategories categories so far...");
		}
	}
	KalturaLog::info("Processed $processedCategories categories for hierarchy rebuild and count recalculation");
	
	// Step 10: Update parent category's direct sub-categories count
	KalturaLog::info("Step 10: Updating parent category's sub-categories count");
	// Re-fetch main category after processing
	categoryPeer::setUseCriteriaFilter(false);
	$category = categoryPeer::retrieveByPK($categoryId);
	categoryPeer::setUseCriteriaFilter(true);
	
	$parentId = $category->getParentId();
	if ($parentId)
	{
		categoryPeer::setUseCriteriaFilter(false);
		$parentCategory = categoryPeer::retrieveByPK($parentId);
		categoryPeer::setUseCriteriaFilter(true);
		
		if ($parentCategory && $parentCategory->getStatus() === CategoryStatus::ACTIVE)
		{
			$parentCategory->reSetDirectSubCategoriesCount();
			$parentCategory->save();
			KalturaLog::info("Updated parent category $parentId direct sub-categories count");
		}
	}
	
	// Step 11: Trigger re-indexing for search
	KalturaLog::info("Step 11: Triggering re-indexing for search");
	foreach ($allCategoriesToProcess as $categoryToIndex)
	{
		$categoryToIndex->indexToSearchIndex();
	}
	KalturaLog::info("Triggered re-indexing for " . count($allCategoriesToProcess) . " categories");
	
	// Flush any pending events
	kEventsManager::flushEvents();
	
	KalturaLog::info("=== Restore completed ===");
	KalturaLog::info("Main category restored: 1");
	KalturaLog::info("Child categories restored: $restoredChildCategories");
	KalturaLog::info("Category entries restored: $restoredCategoryEntries");
	KalturaLog::info("Category users restored: $restoredCategoryKusers");
	KalturaLog::info("Categories processed for hierarchy rebuild: $processedCategories");
}

KalturaLog::info("Script finished successfully");
