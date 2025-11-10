<?php
require_once('/opt/kaltura/app/alpha/scripts/bootstrap.php');
echo "Starting script to update membersCount for category\n";

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
$currentMembersCount = $category->getMembersCount();

$c = new Criteria();
$c->add(categoryKuserPeer::CATEGORY_ID, $categoryId);
$c->add(categoryKuserPeer::STATUS, CategoryKuserStatus::ACTIVE, Criteria::EQUAL);
$numOfActiveCategoryMembers = categoryKuserPeer::doCount($c);
KalturaLog::debug("Current category members for category with ID $categoryId is $numOfActiveCategoryMembers\n");

if ($currentMembersCount == $numOfActiveCategoryMembers)
{
	KalturaLog::debug("No update needed, current members count is already $currentMembersCount\n");
	exit(0);
}

$category->setMembersCount($numOfActiveCategoryMembers);
$category->save();

KalturaLog::debug("Updated membersCount to $numOfActiveCategoryMembers for category with ID $categoryId\n");

