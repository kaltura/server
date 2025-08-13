<?php

if($argc < 3)
{
	echo "Arguments missing.\n\n";
	echo "Usage: php " . __FILE__ . " {csvFilePath} {partnerId} <realrun / dryrun>" . PHP_EOL;
	exit;
}
$csvFilePath = $argv[1];
$dryRun = ($argv[3] === "dryrun");

require_once(__DIR__ . '/../bootstrap.php');

KalturaStatement::setDryRun($dryRun);

kCurrentContext::$partner_id = $argv[2];
if (!file_exists($csvFilePath) || !is_readable($csvFilePath))
{
	echo "---- Error: CSV file not found or not readable." . PHP_EOL;
	exit;
}

$file = fopen($csvFilePath, 'r');
$headers = fgetcsv($file); // Read the header row

if ($headers !== ['Meeting ID', 'Topic', 'Host', 'Email', 'Username', 'Start Time']) {
	echo "---- Error: CSV file headers do not match the expected format." . PHP_EOL;
	fclose($file);
	exit;
}

while (($row = fgetcsv($file)) !== false)
{
	list($meetingId, $topic, $host, $email, $username, $startTime) = $row;

	// Search for the entry by name (Topic column)
	$entries = searchForEntryByName($topic);
	if (empty($entries) || !$entries[0]->getObject())
	{
		echo "---- Entry with name [$topic] not found" . PHP_EOL;
		continue;
	}
	$entry = $entries[0]->getObject();

	// Search for the user by Username (Puser ID)
	$user = searchForUserByPuserId($username);
	if (!$user)
	{
		echo "---- User with Puser ID [$username] not found" . PHP_EOL;
		continue;
	}

	/* @var $entry entry */
	$entry->setKuserId($user->getId());
	$entry->setCreatorKuserId($user->getId());
	$entry->setCreatorPuserId($username);
	$entry->save();
	
	kEventsManager::flushEvents();
	kMemoryManager::clearMemory();
}

fclose($file);

echo "Done" . PHP_EOL;
KalturaLog::debug('Done');

function searchForEntryByName($entryName)
{
	$entryItem = new ESearchEntryItem();
	$entryItem->setItemType(ESearchItemType::EXACT_MATCH);
	$entryItem->setFieldName(ESearchEntryFieldName::NAME);
	$entryItem->setSearchTerm($entryName);
	$searchItems = array($entryItem);
	$operator = new ESearchOperator();
	$operator->setOperator(ESearchOperatorType::AND_OP);
	$operator->setSearchItems($searchItems);

	$pager = new kPager();
	$pager->setPageSize(1);

	$entrySearch = new kEntrySearch();
	$eSearchResults = $entrySearch->doSearch($operator, $pager, array(), null);
	list($coreResults, $objectCount) = kESearchCoreAdapter::transformElasticToCoreObject($eSearchResults, $entrySearch);

	return $coreResults;
}

function searchForUserByPuserId($userId)
{
	$c = new Criteria();
	$c->add(kuserPeer::PARTNER_ID, kCurrentContext::$partner_id);
	$c->add(kuserPeer::PUSER_ID, $userId);

	$user = kuserPeer::doSelectOne($c);
	return $user;
}
