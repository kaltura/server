<?php

if($argc < 4)
{
	echo "Arguments missing.\n\n";
	echo "Usage: php " . __FILE__ . " {csvFilePath} {partnerId} {csvOutputFilePath} <realrun / dryrun>" . PHP_EOL;
	exit;
}

require_once(__DIR__ . '/../bootstrap.php');
$csvFilePath = $argv[1];
kCurrentContext::$partner_id = $argv[2];
$csvOutputFilePath = $argv[3];
$dryRun = ($argv[4] === "dryrun");



KalturaStatement::setDryRun($dryRun);

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

writeHeaderToCsv($csvOutputFilePath);

while (($row = fgetcsv($file)) !== false)
{
	list($meetingId, $topic, $host, $email, $username, $startTime) = $row;

	// Search for the entry by name (Topic column)
	$entries = searchForEntryByName($topic, $meetingId);
	if (empty($entries) || !$entries[0]->getObject())
	{
		echo "---- Entry with name [$topic] not found" . PHP_EOL;
		writeResultsToCsv($csvOutputFilePath, array($meetingId, $topic, $host, $email, $username, 'entry not found'));
		continue;
	}
	$entry = $entries[0]->getObject();

	// Search for the user by Username (Puser ID)
	$user = searchForUserByPuserId($username, $email);
	if (!$user)
	{
		echo "---- User with Puser ID [$username] not found" . PHP_EOL;
		writeResultsToCsv($csvOutputFilePath, array($meetingId, $topic, $host, $email, $username, 'user not found'));
		continue;
	}
	echo "---- User with Puser email [$email] and puser_id [$username] found" . PHP_EOL;

	/* @var $entry entry */
	$entry->setKuserId($user->getId());
	$entry->setPuserId($user->getPuserId());
	$entry->save();
	
	kEventsManager::flushEvents();
	kMemoryManager::clearMemory();
}

fclose($file);

echo "Done" . PHP_EOL;
KalturaLog::debug('Done');

function searchForEntryByName($entryName, $meetingId)
{
	$entryItemName = new ESearchEntryItem();
	$entryItemName->setItemType(ESearchItemType::EXACT_MATCH);
	$entryItemName->setFieldName(ESearchEntryFieldName::NAME);
	$entryItemName->setSearchTerm($entryName);
	$entryItemDescription = new ESearchEntryItem();
	$entryItemDescription->setItemType(ESearchItemType::PARTIAL);
	$entryItemDescription->setFieldName(ESearchEntryFieldName::DESCRIPTION);
	$entryItemDescription->setSearchTerm(str_replace(' ', '', $meetingId));
	$searchItems = array($entryItemName, $entryItemDescription);
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

function searchForUserByPuserId($userId, $email)
{
	$c = new Criteria();
	$c->add(kuserPeer::PARTNER_ID, kCurrentContext::$partner_id);
	$c->add(kuserPeer::PUSER_ID, $userId);
	$user = kuserPeer::doSelectOne($c);

	if (!$user)
	{
		echo "---- User with Puser ID [$userId] NOT found" . PHP_EOL;
		$c->remove(kuserPeer::PUSER_ID);
		$c->add(kuserPeer::EMAIL, $email);
		$user = kuserPeer::doSelectOne($c);

	}

	return $user;
}

function writeHeaderToCsv($outputPath)
{
	$file = fopen($outputPath, 'w');
	if (!$file)
	{
		KalturaLog::err("Error: Failed to create file $outputPath");
		return;
	}

	$headerRow = array('Meeting ID', 'Topic', 'Host', 'Email', 'Username', 'Reason');
	fputcsv($file, $headerRow);
	fclose($file);
}

function writeResultsToCsv($outputPath, $row)
{
	$file = fopen($outputPath, 'a');
	if (!$file)
	{
		KalturaLog::err("Error: Failed to create file $outputPath");
		return;
	}

	fputcsv($file, $row);

	fclose($file);
}
