<?php

if($argc < 3) {
	echo "Arguments missing" .PHP_EOL;
	echo "Usage: php changeDropFolderTag.php {drop folder id} {new tag}". PHP_EOL;
	exit;
}

$dropFolderId = $argv[1];
$newTag = $argv[2];
chdir(__DIR__.'/../');
require_once(__DIR__ . '/../bootstrap.php');

$dropFolder = DropFolderPeer::retrieveByPK($dropFolderId);
exitIfNull($dropFolder, "Did not find drop folder with ID [$dropFolderId]");
printInGreen("For DropFolderId {$dropFolderId} the tag is " . $dropFolder->getTags() . " Changing to {$newTag}" . PHP_EOL);
$dropFolder->setTags($newTag);
$dropFolder->save();

printInGreen("DONE");


function exitIfNull($val, $error) {if (!$val) exit(PHP_EOL . $error);}

function printInGreen($str) {
	echo PHP_EOL . "\033[32m$str\033[0m";
}


