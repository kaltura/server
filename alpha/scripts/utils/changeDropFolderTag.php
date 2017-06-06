<?php

if($argc < 3) {
	echo "Arguments missing" .PHP_EOL;
	echo "Usage: php changeDropFolderTag.php {old tag} {new tag}". PHP_EOL;
	exit;
}

$oldTag = $argv[1];
$newTag = $argv[2];
chdir(__DIR__.'/../');
require_once(__DIR__ . '/../bootstrap.php');

$c = new Criteria();
//$c->addAnd( self::STATUS, array(DropFolderStatus::ENABLED, DropFolderStatus::ERROR), Criteria::IN);
$c->addAnd(DropFolderPeer::TAGS, $oldTag, Criteria::EQUAL);
$dropFolders = DropFolderPeer::doSelect($c);
exitIfNull($dropFolders, "Did not find drop folder with ID [$oldTag]");
foreach ($dropFolders as $dropFolder){
	printInGreen("For DropFolderId {$dropFolder->getId()} the tag is " . $dropFolder->getTags() . " Changing to {$newTag}" . PHP_EOL);
	$dropFolder->setTags($newTag);
	$dropFolder->save();
}
printInGreen("DONE");


function exitIfNull($val, $error) {if (!$val) exit(PHP_EOL . $error);}

function printInGreen($str) {
	echo PHP_EOL . "\033[32m$str\033[0m";
}


