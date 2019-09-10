<?php
if($argc < 3)
{
	die("Usage $argv[0]  <OUTPUT_PATH> <DC> <DROP_FOLDER_TYPE>\r\nFor all types leave DROP_FOLDER_TYPE empty for specific types: 1=local 2=ftp 3=scp 4=sftp 5=s3 \r\n");
}

require_once(dirname(__FILE__) . '/../bootstrap.php');

$outputFile = fopen($argv[1], "w") or die("Unable to open output file");
$dc = $argv[2];
$type = isset($argv[3]) ? $argv[3] : null;

$tags = getTagsByDcAndType($dc, $type);
foreach ($tags as $tag)
{
	fwrite($outputFile, $tag['TAGS']. PHP_EOL);
}
fclose($outputFile);


function getTagsByDcAndType($dc, $type)
{
	$c = new Criteria();
	$c->add( DropFolderPeer::DC, $dc, Criteria::EQUAL);
	if($type)
	{
		$c->add( DropFolderPeer::TYPE, $type, Criteria::EQUAL);
	}
	$c->addSelectColumn(DropFolderPeer::TAGS);
	$c->setDistinct();
	$stmt = DropFolderPeer::doSelectStmt($c);
	$tags = $stmt->fetchAll(PDO::FETCH_ASSOC);
	return $tags;
}