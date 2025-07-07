<?php
require_once('/opt/kaltura/app/alpha/scripts/bootstrap.php');
echo "Starting script to update entriesCount for category\n";

if($argc < 3)
	die("Usage: php $argv[0] <categoryID> [<realrun | dryrun>] \n");

$categoryId = $argv[1];

$dryrun = true;
if ($argc == 3 && $argv[2] == 'realrun') {
	$dryrun = false;
}

KalturaStatement::setDryRun($dryrun);
KalturaLog::debug("dryrun value: [$dryrun]\n");

$category = categoryPeer::retrieveByPK($categoryId);
$currentEntriesCount = $category->getEntriesCount();

$c = new Criteria();
$c->add(categoryEntryPeer::CATEGORY_ID, $categoryId);
$c->add(categoryEntryPeer::STATUS, CategoryEntryStatus::ACTIVE, Criteria::EQUAL);
$numOfActiveCategoryEntries = categoryEntryPeer::doCount($c);
KalturaLog::debug("Current category entries for category with ID $categoryId is $numOfActiveCategoryEntries\n");

if ($currentEntriesCount == $numOfActiveCategoryEntries)
{
	KalturaLog::debug("No update needed, current entries count is already $currentEntriesCount\n");
	exit(0);
}

$category->setEntriesCount($numOfActiveCategoryEntries);
$category->save();

KalturaLog::debug("Updated entriesCount to $numOfActiveCategoryEntries for category with ID $categoryId\n");

