<?php
set_time_limit(0);

require_once('../../../lib/KalturaClient.php');

$savePath = null;
if($argc > 1)
	$savePath = $argv[1];

class clientLogger implements IKalturaLogger
{
	function log($msg)
	{
		echo "$msg\n";
	}
}
	
$partnerId = 101;
$config = new KalturaConfiguration($partnerId);
$config->curlTimeout = 300;
$config->serviceUrl = 'http://kaltura.trunk';
$config->setLogger(new clientLogger());

$client = new KalturaClient($config);
$ks = $client->generateSession('815d617b032593a8519c4dcc5f61b25f', 'tester', KalturaSessionType::ADMIN, $partnerId);
$client->setKs($ks);

$filter = new KalturaAccessControlFilter();
$filter->orderBy = KalturaAccessControlOrderBy::CREATED_AT_ASC;

$pager = new KalturaFilterPager();
$pager->pageSize = 200;
$pager->pageIndex = 0;

$results = $client->accessControl->listAction($filter, $pager);
/* @var $results KalturaAccessControlListResponse */

while(count($results->objects))
{
	echo count($results->objects) . "\n";
	echo $results->totalCount . "\n";
	
	if($savePath)
	{
		foreach($results->objects as $object)
		{
			echo "Saving: {$savePath}/{$object->id}.ser\n";
			file_put_contents("{$savePath}/{$object->id}.ser", serialize($object));
		}
	}
	
	$pager->pageIndex++;
	$results = $client->accessControl->listAction($filter, $pager);
}
