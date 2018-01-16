<?php

require_once (dirname(__FILE__).'/../bootstrap.php');

$f = fopen("php://stdin", "r");
$count = 0;
$sphinxMgr = new kSphinxSearchManager();
$dbConf = kConf::getDB();
DbManager::setConfig($dbConf);
DbManager::initialize();
$connection = Propel::getConnection();
while($s = trim(fgets($f))){
        $sep = strpos($s, "\t") ? "\t" : " ";
        list($entryId, $plays, $views) = explode($sep, $s);
        myPartnerUtils::resetAllFilters();
        myPartnerUtils::resetPartnerFilter('entry');
        $entry = entryPeer::retrieveByPK ( $entryId);
        if (is_null ( $entry )) {
                KalturaLog::err ('Couldn\'t find entry [' . $entryId . ']' );
                continue;
        }

        if ( $entry->getMediaType() == entry::ENTRY_MEDIA_TYPE_IMAGE ) {
			$plays = $views;
        }

        if ($entry->getViews() != $views || $entry->getPlays() != $plays){
                $entry->setViews ( $views );
                $entry->setPlays ( $plays );


		try {
			// Update last_played_at date/time to NOW
			$now = time();
			$entry->setLastPlayedAt( $now );
			
			// update entry without setting the updated at
			$mysqlNow = date( 'Y-m-d H:i:s', $now );
			$updateSql = "UPDATE entry set views='$views',plays='$plays',last_played_at='$mysqlNow' WHERE id='$entryId'";
			$stmt = $connection->prepare($updateSql);
			$stmt->execute();
			KalturaLog::debug ( 'Successfully saved entry [' . $entryId . ']' );

			$affectedRows = $stmt->rowCount();
			KalturaLog::log("AffectedRows: ". $affectedRows);
			// update sphinx log directly
			$sql = $sphinxMgr->getSphinxSaveSql($entry, false);
			$sphinxLog = new SphinxLog();
			$sphinxLog->setEntryId($entryId);
			$sphinxLog->setPartnerId($entry->getPartnerId());
			$sphinxLog->setSql($sql);
			$sphinxLog->setType(SphinxLogType::SPHINX);
			$sphinxLog->save(myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_SPHINX_LOG));

			//update elastic via sphinx log
			$params['body']['doc']['plays'] = $entry->getPlays();
			$params['body']['doc']['views'] = $entry->getViews();
			$params['body']['doc']['last_played_at'] = $entry->getLastPlayedAt(null);
			$params['index'] = $entry->getElasticIndexName();
			$params['type'] = $entry->getElasticObjectType();
			$params['id'] = $entry->getElasticId();
			$params['action'] = ElasticMethodType::UPDATE;

			$elasticLog = new SphinxLog();
			$command = serialize($params);
			$elasticLog->setSql($command);
			$elasticLog->setObjectId($entry->getId());
			$elasticLog->setObjectType($entry->getElasticObjectName());
			$elasticLog->setEntryId($entry->getId());
			$elasticLog->setPartnerId($entry->getPartnerId());
			$elasticLog->setType(SphinxLogType::ELASTIC);
			$elasticLog->save(myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_SPHINX_LOG));

		} catch (Exception $e) {
			KalturaLog::log($e->getMessage(), Propel::LOG_ERR);

		}
        }
        $count++;
	if ($count % 500 === 0){
	    entryPeer::clearInstancePool ();
	}
}
?>
