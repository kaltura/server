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

	$user = searchForUserByKuser($id);
	if (!$user)
	{
		echo "---- User with email [$puserId] not found" . PHP_EOL;
		continue;
	}

	/* @var $user kuser */
	$user->setStatus(kUserStatus::DELETED);
	$user->save();

	echo "---- User with email [$puserId] deleted" . PHP_EOL;

	kEventsManager::flushEvents();
	kMemoryManager::clearMemory();
}

echo "Done" . PHP_EOL;
KalturaLog::debug('Done');

function searchForUserByKuser($id)
{
	$user = kuserPeer::retrieveByPK($id);

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

