<?php
$config = null;
$clientConfig = null;
/* @var $clientConfig KalturaConfiguration */
$client = null;
/* @var $client KalturaClient */

require_once __DIR__ . '/lib/init.php';


$logrotate = $config['dwh']['logRotateBin'];
$appDir = $config['global']['appDir'];
$dwhDir = $config['dwh']['baseDir'];


/**
 * Start a new session
 */
$partnerId = $config['session']['partnerId'];
$adminSecretForSigning = $config['session']['adminSecret'];
$client->setKs($client->generateSessionV2($adminSecretForSigning, 'sanity-user', KalturaSessionType::USER, $partnerId, 86400, ''));




/**
 * List players
 */
$playersFilter = new KalturaUiConfFilter();
$playersFilter->objTypeEqual = KalturaUiConfObjType::PLAYER_V3;
$playersFilter->orderBy = KalturaUiConfOrderBy::CREATED_AT_DESC;

$playersPager = new KalturaFilterPager();
$playersPager->pageSize = 1;

$playersList = $client->media->listAction($playersFilter, $playersPager);
/* @var $playersList KalturaUiConfListResponse */

if(!$playersList || !$playersList->totalCount || !count($playersList->objects))
{
	echo "No player found\n";
	exit(-1);
}
$player = reset($playersList->objects);
/* @var $player KalturaUiConf */




/**
 * List ready media entries
 */
$entriesFilter = new KalturaMediaEntryFilter();
$entriesFilter->mediaTypeEqual = KalturaMediaType::VIDEO;
$entriesFilter->statusEqual = KalturaEntryStatus::READY;
$entriesFilter->orderBy = KalturaMediaEntryOrderBy::DURATION_ASC;

$entriesPager = new KalturaFilterPager();
$entriesPager->pageSize = 1;

$entriesList = $client->media->listAction($entriesFilter, $entriesPager);
/* @var $entriesList KalturaMediaListResponse */

if(!$entriesList || !$entriesList->totalCount || !count($entriesList->objects))
{
	echo "No ready media entry found\n";
	exit(-1);
}
$entry = reset($entriesList->objects);
/* @var $entry KalturaMediaEntry */



/**
 * Calls stats.collect
 * 
 * TODO:
 *  - Run it once on each API server.
 */
$client->getConfig()->method = KalturaClientBase::METHOD_GET;

$event = new KalturaStatsEvent();
$event->isFirstInSession = false;
$event->seek = false;

$event->clientVer = '3.0:v3.7';
$event->referrer =  $clientConfig->serviceUrl . 'sanity/tests';
$event->sessionId = uniqid('SANITY-TEST-');

$event->entryId = $entry->id;
$event->partnerId = $entry->partnerId;
$event->duration = $entry->duration;
$event->currentPoint = 0;

$event->uiconfId = $player->id;

$event->eventType = KalturaStatsEventType::WIDGET_LOADED;
$event->eventTimestamp = microtime(true);
$client->stats->collect($event);

$event->eventType = KalturaStatsEventType::MEDIA_LOADED;
$event->eventTimestamp = microtime(true);
$client->stats->collect($event);

$event->eventType = KalturaStatsEventType::PLAY;
$event->eventTimestamp = microtime(true);
$client->stats->collect($event);

$quarter = ceil(($entry->msDuration / 4) * 1000);

usleep($quarter);
$event->currentPoint += $quarter;
$event->eventType = KalturaStatsEventType::PLAY_REACHED_25;
$event->eventTimestamp = microtime(true);
$client->stats->collect($event);

usleep($quarter);
$event->currentPoint += $quarter;
$event->eventType = KalturaStatsEventType::PLAY_REACHED_50;
$event->eventTimestamp = microtime(true);
$client->stats->collect($event);

usleep($quarter);
$event->currentPoint += $quarter;
$event->eventType = KalturaStatsEventType::PLAY_REACHED_75;
$event->eventTimestamp = microtime(true);
$client->stats->collect($event);

usleep($quarter);
$event->currentPoint = $entry->msDuration;
$event->eventType = KalturaStatsEventType::PLAY_REACHED_100;
$event->eventTimestamp = microtime(true);
$client->stats->collect($event);

$client->getConfig()->method = KalturaClientBase::METHOD_POST;




/**
 * Rotate logs.
 */
$returnedValue = null;
$cmd = "$logrotate $appDir/tests/sanity/lib/logrotate.ini";
echo "Executing: $cmd\n";
passthru($cmd, $returnedValue);
if($returnedValue !== 0)
{
	echo "Execution failed [$cmd]\n";
	exit(-1);
}



/**
 * Run hourly scripts.
 */
$cmd = "$dwhDir/etlsource/execute/etl_hourly.sh";
echo "Executing: $cmd\n";
passthru($cmd, $returnedValue);
if($returnedValue !== 0)
{
	echo "Execution failed [$cmd]\n";
	exit(-1);
}


/**
 * Run update dimensions.
 */
$cmd = "$dwhDir/etlsource/execute/etl_update_dims.sh";
echo "Executing: $cmd\n";
passthru($cmd, $returnedValue);
if($returnedValue !== 0)
{
	echo "Execution failed [$cmd]\n";
	exit(-1);
}



/**
 * Run daily scripts.
 */
$cmd = "$dwhDir/etlsource/execute/etl_daily.sh";
echo "Executing: $cmd\n";
passthru($cmd, $returnedValue);
if($returnedValue !== 0)
{
	echo "Execution failed [$cmd]\n";
	exit(-1);
}



/**
 * Validate the results using the API
 * 
 * TODO:
 *  - Validate that the data collected from all API machines.
 */
$reportInputFilter = new KalturaReportInputFilter();
$reportInputFilter->fromDay = date('Ymd', time() - (60 * 60 * 24));
$reportInputFilter->toDay = date('Ymd');

$reportInputPager = new KalturaFilterPager();
$reportTable = $client->report->getTable(KalturaReportType::TOP_CONTENT, $reportInputFilter, $reportInputPager, null, $entry->id);
/* @var $reportTable KalturaReportTable */

if($reportTable->totalCount != 1)
{
	echo "Reported wrong total count [$reportTable->totalCount]\n";
	exit(-1);
}

$titles = explode(',', $reportTable->header);
$data = explode(';', $reportTable->data);
if(!$reportTable->data || count($data) != 1)
{
	echo "Reported wrong data count\n";
	exit(-1);
}

$record = array_combine($titles, reset($data));
if($record['object_id'] != $entry->id)
{
	echo "Reported data of wrong entry [" . $record['object_id'] . "]\n";
	exit(-1);
}
if(!isset($record['count_plays']) || !$record['count_plays'] || !intval($record['count_plays']))
{
	echo "Reported wrong plays count [" . $record['count_plays'] . "]\n";
	exit(-1);
}
if(!isset($record['count_loads']) || !$record['count_loads'] || !intval($record['count_loads']))
{
	echo "Reported wrong loads count [" . $record['count_loads'] . "]\n";
	exit(-1);
}
$expectedTimeViewed = $entry->duration / 60;
if(!isset($record['sum_time_viewed']) || !$record['sum_time_viewed'] || intval($record['sum_time_viewed']) < $expectedTimeViewed)
{
	echo "Reported wrong view time [" . $record['sum_time_viewed'] . "] expected at least [$expectedTimeViewed]\n";
	exit(-1);
}



/**
 * Syncyng plays and view from the dwh to the operational db
 */
$cmd = "$appDir/scripts/dwh/dwh_plays_views_sync.sh";
echo "Executing: $cmd\n";
passthru($cmd, $returnedValue);
if($returnedValue !== 0)
{
	echo "Execution failed [$cmd]\n";
	exit(-1);
}



/**
 * Reload the entry and check plays and views
 */
$reloadedEntry = $client->media->get($entry->id);
/* @var $reloadedEntry KalturaMediaEntry */

if($reloadedEntry->plays <= $entry->plays)
{
	echo "Entry [$entry->id] plays [$reloadedEntry->plays] did not incremented\n";
	exit(-1);
}
if($reloadedEntry->views <= $entry->views)
{
	echo "Entry [$entry->id] views [$reloadedEntry->views] did not incremented\n";
	exit(-1);
}



exit(0);
