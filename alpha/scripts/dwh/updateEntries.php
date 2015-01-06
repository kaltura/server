<?php

set_time_limit(0);

ini_set("memory_limit","2048M");

chdir(dirname(__FILE__));


define('ROOT_DIR', "/opt/kaltura/app/");
require_once(ROOT_DIR . 'alpha/scripts/bootstrap.php');

KalturaLog::setLogger(new KalturaStdoutLogger());

error_reporting(E_ALL);
kCurrentContext::$ps_vesion = 'ps3';

$dbConf = kConf::getDB();
DbManager::setConfig($dbConf);
DbManager::initialize();

$sphinxMgr = new kSphinxSearchManager();

$updateCon = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_MASTER);

$f = fopen("php://stdin", "r");
$count = 0;

$indexName = kSphinxSearchManager::getSphinxIndexName(entryPeer::OM_CLASS);

function checkSphinxLag()
{
	$dcs = kDataCenterMgr::getAllDcs(true);

	while(1)
	{
		// fetch last log id
		$c = new Criteria();
		$c->addDescendingOrderByColumn(SphinxLogPeer::ID);
		$c->setLimit(1);
		$lastLog = SphinxLogPeer::doSelectOne($c);
		$lastLogId = $lastLog->getId();

		// ensure that at least two servers in each DC are not lagging
		$minLogServer = "";
		$minLogId = $lastLogId;
		foreach($dcs as $dcId => $dc)
		{
			$c = new Criteria();
			$c->add(SphinxLogServerPeer::SERVER, $dc["name"]."%", Criteria::LIKE);
			$c->add(SphinxLogServerPeer::DC, kDataCenterMgr::getCurrentDcId()); 
			$c->addDescendingOrderByColumn(SphinxLogServerPeer::LAST_LOG_ID);
			$c->setLimit(2);
			$servers = SphinxLogServerPeer::doSelect($c);
			$server = end($servers);
			if ($server)
			{
				$logId = $server->getLastLogId();
				if ($logId <= $minLogId)
				{
					$minLogId = $logId;
					$minLogServer = $server->getServer();
				}
			}
		}

		if ($minLogId > $lastLogId - 1000) // close enough - no lag
			break;

		KalturaLog::debug ("SphinxLag: $minLogServer $minLogId lastLogId: $lastLogId diff:".($lastLogId - $minLogId));
		SphinxLogServerPeer::clearInstancePool();
		sleep(10);
	}
}

$last_played_at = time();

$increment = @$argv[1] == "increment";
$reset  = @$argv[1] == "reset";

if (!$increment && !$reset)
{
	die("Invalid usage: specify either increment or reset\n");
}


while($s = trim(fgets($f)))
{
	$sep = strpos($s, "\t") ? "\t" : " ";
        list($partnerId, $entryId, $plays, $views) = explode($sep, $s);

	myPartnerUtils::resetAllFilters();
	entryPeer::setDefaultCriteriaFilter();
        $entry = entryPeer::retrieveByPK ( $entryId);
        if (is_null ( $entry )) {
                KalturaLog::err ( 'Couldn\'t find entry [' . $entryId . ']' );
                continue;
        }

	$partnerId = $entry->getPartnerId();

	$newViews = $views;
	$newPlays = $plays;

	if ($increment)
	{
		$views += $entry->getViews();
		$plays += $entry->getPlays();
	}

	if ($entry->getMediaType() == entry::ENTRY_MEDIA_TYPE_IMAGE)
		$plays = $views;

	$viewsChanged =  $entry->getViews() != $views;
	$playsChanged =  $entry->getPlays() != $plays;
	//$viewsChanged =  $entry->getViews() < $views;
	//$playsChanged =  $entry->getPlays() < $plays;

	if ($viewsChanged || $playsChanged)
	{
		KalturaLog::debug("changes: $partnerId $entryId $newPlays $newViews {$entry->getPlays()} {$entry->getViews()} $plays $views");

		$sqlSet = array();
		if ($viewsChanged)
			$sqlSet[] = "views=$views";

		if ($playsChanged)
			$sqlSet[] = "plays=$plays,last_played_at=$last_played_at";

		$sql = "update $indexName set ".implode($sqlSet, ",")." where match('@entry_id $entryId')";

		KalturaLog::debug($entryId." UPDATED: ".$entry->getUpdatedAt()."\n");

		// update sphinx log directly		
		$sphinxLog = new SphinxLog();
                $sphinxLog->setEntryId($entryId);
                $sphinxLog->setPartnerId($partnerId);
                $sphinxLog->setSql($sql);
		try {
                	$sphinxLog->save(myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_SPHINX_LOG));
		} catch (Exception $e) {
                        KalturaLog::log("FAILED UPDATE SPHINX: ".$e->getMessage(), Propel::LOG_ERR);
			kFile::closeDbConnections();
                }

		try {
			$sqlSet = array();
			if ($viewsChanged)
				$sqlSet[] = "views='$views'";

			if ($playsChanged)
				$sqlSet[] = "plays='$plays',last_played_at=from_unixtime('$last_played_at')";

			$updateSql = "UPDATE entry set ".implode($sqlSet, ",")." WHERE id='$entryId'";

			$stmt = $updateCon->prepare($updateSql);
			$stmt->execute();
			$affectedRows = $stmt->rowCount();
			KalturaLog::log("AffectedRows: ". $affectedRows);
		} catch (Exception $e) {
			KalturaLog::log("FAILED UPDATE DB: ".$e->getMessage(), Propel::LOG_ERR);
			kFile::closeDbConnections();
		}

        	KalturaLog::debug ( 'Successfully saved entry [' . $entryId . '] memory: '.memory_get_usage() );

	}
	else
	{
        	KalturaLog::debug ( 'UNCHANGED entry [' . $entryId . '] memory: '.memory_get_usage() );
	}

        $count ++;
        if ($count % 500 == 0)
	{
		kMemoryManager::clearMemory();
		checkSphinxLag();
	}
}
