<?php

if($argc < 4)
{
	echo "Arguments missing.\n\n";
	echo "Usage: php " . __FILE__ . " {xmlFilePath} {outputFileForMultipleUsers}{partnerId} <realrun / dryrun>" . PHP_EOL;
	exit;
}

require_once(__DIR__ . '/../bootstrap.php');
$xmlFilePath = $argv[1];
$csvOutputFile = $argv[2];
kCurrentContext::$partner_id = $argv[3];
$dryRun = ($argv[4] === "dryrun");



KalturaStatement::setDryRun($dryRun);

if (!file_exists($xmlFilePath) || !is_readable($xmlFilePath))
{
	echo "---- Error: xml file not found or not readable." . PHP_EOL;
	exit;
}

writeHeaderToCsv($csvOutputFile);

$xmlString = file_get_contents($xmlFilePath);
$xmlContent = simplexml_load_string($xmlString);
if ($xmlContent === false) {
	echo "Failed to load XML from string.\n";
	print_r(libxml_get_errors());
}

foreach ($xmlContent->DATA_RECORD as $record)
{
	$id = (string)$record->id;
	$puserId = (string)$record->puser_id;
	$createdAt = (string)$record->created_at;
	$email = (string)$record->email;
	$customData = (string)$record->custom_data;

	$users = searchForUserByEmail($puserId);
	if (count($users) > 1)
	{
		echo "---- Found multiple user with email [$puserId]. Did not handle ownership of entries" . PHP_EOL;
		writeResultsToCsv($csvOutputFile, array($puserId));
		continue;
	}
	if (!$users || !$users[0])
	{
		continue;
	}
	$user = $users[0]->getObject();
	if (!$user)
	{
		echo "---- User with email [$puserId] not found" . PHP_EOL;
		continue;
	}
	echo "---- User with email [$puserId] found" . PHP_EOL;

	// Search for the entry by name (Topic column)
	$entries = searchForEntriesByPuser($id);
	if (!$entries)
	{
		continue;
	}
	foreach ($entries as $entry)
	{
		if (!$entry)
		{
			continue;
		}
		$coreEntry = $entry->getObject();
		/* @var $coreEntry entry */
		$coreEntry->setKuserId($user->getId());
		$coreEntry->setPuserId($user->getPuserId());
		$coreEntry->save();
	}
	// Search for the user by Username (Puser ID)

	kEventsManager::flushEvents();
	kMemoryManager::clearMemory();
}

echo "Done" . PHP_EOL;
KalturaLog::debug('Done');

function searchForEntriesByPuser($kuserId)
{
	$entryItemName = new ESearchEntryItem();
	$entryItemName->setItemType(ESearchItemType::EXACT_MATCH);
	$entryItemName->setFieldName(ESearchEntryFieldName::CREATOR_ID);
	$entryItemName->setSearchTerm($kuserId);
	$searchItems = array($entryItemName);
	$operator = new ESearchOperator();
	$operator->setOperator(ESearchOperatorType::AND_OP);
	$operator->setSearchItems($searchItems);

	$pager = new kPager();
	$pager->setPageSize(50);

	$entrySearch = new kEntrySearch();
	$eSearchResults = $entrySearch->doSearch($operator, $pager, array(), null);
	list($coreResults, $objectCount) = kESearchCoreAdapter::transformElasticToCoreObject($eSearchResults, $entrySearch);

	return $coreResults;
}

function searchForUserByEmail($email)
{
	$userItemEmail = new ESearchUserItem();
	$userItemEmail->setItemType(ESearchItemType::EXACT_MATCH);
	$userItemEmail->setFieldName(ESearchUserFieldName::EMAIL);
	$userItemEmail->setSearchTerm($email);

	$searchItems = array($userItemEmail);
	$operator = new ESearchOperator();
	$operator->setOperator(ESearchOperatorType::AND_OP);
	$operator->setSearchItems($searchItems);

	$pager = new kPager();
	$pager->setPageSize(1);

	$userSearch = new kUserSearch();
	$eSearchResults = $userSearch->doSearch($operator, $pager, array(), null);
	list($coreResults, $objectCount) = kESearchCoreAdapter::transformElasticToCoreObject($eSearchResults, $userSearch);

	return $coreResults;
}


function writeHeaderToCsv($outputPath)
{
	$file = fopen($outputPath, 'w');
	if (!$file)
	{
		KalturaLog::err("Error: Failed to create file $outputPath");
		return;
	}

	$headerRow = array('user email');
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
