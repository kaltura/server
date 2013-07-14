<?php

require_once(__DIR__ . '/../bootstrap.php');

$entryIds = file('entries.csv');
$sizeFile = fopen('size.csv', 'a');
$entries = entryPeer::retrieveByPKs($entryIds);
foreach($entries as $entry)
{
	$size = myEntryUtils::calcStorageSize($entry);
	fputcsv($sizeFile, array($size, $entry->getId()));
}
fclose($sizeFile);


echo 'Done';
