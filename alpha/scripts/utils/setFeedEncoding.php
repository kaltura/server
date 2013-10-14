<?php

if($argc != 4)
{
	echo "Arguments missing.\n\n";
	echo "Usage: php " . __FILE__ . " {feed id} {encoding} {realrun / dryrun}\n";
	exit;
} 
$feedId = $argv[1];
$encoding = $argv[2];
$dryRun = ($argv[3] != 'realrun');

require_once(__DIR__ . '/../bootstrap.php');

KalturaStatement::setDryRun($dryRun);

$feed = syndicationFeedPeer::retrieveByPK($feedId);
$mrssParameters = $feed->getMrssParameters();
if(!$mrssParameters)
{
	$mrssParameters = new kMrssParameters();
	$mrssParameters->setItemXpathsToExtend(array());
}
	
$mrssParameters->setEncoding($encoding);
$feed->setMrssParameters($mrssParameters);
$feed->save();

KalturaLog::debug('Done');
