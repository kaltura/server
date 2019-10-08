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
        list($entryId, $lastPlayedAt, $plays, $views) = explode($sep, $s);
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
                $entry->setViews ($views);
                $entry->setPlays ($plays);
		$entry->setLastPlayedAt($lastPlayedAt);

		try {
			// update entry without setting the updated at
			$lastPlayedDate = date('Y-m-d H:i:s', $lastPlayedAt);
			$updateSql = "UPDATE entry set views='$views',plays='$plays',last_played_at='$lastPlayedDate' WHERE id='$entryId'";
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
			$sphinxLog->setIndexName($entry->getSphinxIndexName());
			$sphinxLog->save(myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_SPHINX_LOG));

			//update elastic via sphinx log
			$params = array(
				'index' => $entry->getElasticIndexName(),
				'type' => $entry->getElasticObjectType(),
				'id' => $entry->getElasticId(),
				'action' => ElasticMethodType::UPDATE,
				'body' => array(
					'doc' => array(
						'plays' => $entry->getPlays(),
						'views' => $entry->getViews(),
						'last_played_at' => $entry->getLastPlayedAt(null)
					)
				)
			);

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
