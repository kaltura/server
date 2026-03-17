<?php

if($argc < 2) {
	echo "Arguments missing" . PHP_EOL;
	echo "Usage: php restoreDropFolder.php {drop folder id}" . PHP_EOL;
	echo "Example: php restoreDropFolder.php 3" . PHP_EOL;
	exit;
}

$dropFolderId = $argv[1];

require_once(__DIR__ . '/../bootstrap.php');

// Disable the default criteria filter to find deleted folders
DropFolderPeer::setUseCriteriaFilter(false);
$dropFolder = DropFolderPeer::retrieveByPK($dropFolderId);
DropFolderPeer::setUseCriteriaFilter(true);

exitIfNull($dropFolder, "Did not find drop folder with ID [$dropFolderId]");

$currentStatus = $dropFolder->getStatus();
$statusName = getStatusName($currentStatus);

printInYellow("Found drop folder ID: {$dropFolderId}");
printInYellow("Name: " . $dropFolder->getName());
printInYellow("Partner ID: " . $dropFolder->getPartnerId());
printInYellow("Current Status: {$currentStatus} ({$statusName})");

if ($currentStatus != DropFolderStatus::DELETED) {
	printInRed("WARNING: Drop folder is not in DELETED status. Current status is {$statusName}.");
	echo "Do you want to restore it to ENABLED anyway? (yes/no): ";
	$handle = fopen("php://stdin", "r");
	$line = fgets($handle);
	if(trim($line) != 'yes'){
		printInRed("Operation cancelled.");
		exit;
	}
	fclose($handle);
}

printInYellow("Restoring drop folder to ENABLED status...");

$dropFolder->setStatus(DropFolderStatus::ENABLED);
$dropFolder->save();

printInGreen("SUCCESS! Drop folder ID {$dropFolderId} has been restored to ENABLED status.");
printInGreen("New Status: " . $dropFolder->getStatus() . " (" . getStatusName($dropFolder->getStatus()) . ")");

// Helper functions
function exitIfNull($val, $error) {
	if (!$val) {
		printInRed($error);
		exit(1);
	}
}

function printInGreen($str) {
	echo PHP_EOL . "\033[32m$str\033[0m" . PHP_EOL;
}

function printInRed($str) {
	echo PHP_EOL . "\033[31m$str\033[0m" . PHP_EOL;
}

function printInYellow($str) {
	echo PHP_EOL . "\033[33m$str\033[0m" . PHP_EOL;
}

function getStatusName($status) {
	switch($status) {
		case DropFolderStatus::DISABLED:
			return "DISABLED";
		case DropFolderStatus::ENABLED:
			return "ENABLED";
		case DropFolderStatus::DELETED:
			return "DELETED";
		case DropFolderStatus::ERROR:
			return "ERROR";
		default:
			return "UNKNOWN";
	}
}
