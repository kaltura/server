<?php
if($argc < 2)
	die ('Usage: php ' . __FILE__ . " <FILE_SYNC_ID> [decrypt]" . PHP_EOL);
require_once(__DIR__ . '/../bootstrap.php');
$maxSize = 100000;

$fileSyncId = $argv[1];
$fs = FileSyncPeer::retrieveByPK($fileSyncId);
if (!$fs)
	die('No file sync found');

$path = $fs->createTempClear();
echo "\33[32mDone - created temp file at [$path]\33[0m" . PHP_EOL;
if (filesize($path) < $maxSize)
	echo "\033[32mThis is the File Content: \033[0m" . PHP_EOL . file_get_contents($path) . PHP_EOL;

if (isset($argv[2]) && $argv[2] == 'decrypt')
{
	echo 'Decrypt the original file' . PHP_EOL;
	$plainData = $fs->decrypt();
	$realPath = realpath($fs->getFullPath());
	echo "Decrypt the original file in path [$realPath]" . PHP_EOL;
	kFileBase::setFileContent( $realPath, $plainData);
	$fs->putInCustomData("encryptionKey", null);
	$fs->save();
}


