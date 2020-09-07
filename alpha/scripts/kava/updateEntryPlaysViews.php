<?php

require_once (dirname(__FILE__).'/../bootstrap.php');

$f = fopen("php://stdin", "r");
$count = 0;
KalturaLog::log('Script Started');
$sphinxMgr = new kSphinxSearchManager();
$dbConf = kConf::getDB();
DbManager::setConfig($dbConf);
DbManager::initialize();
$connection = Propel::getConnection();

$playsViewsCache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_PLAYS_VIEWS);

while($s = trim(fgets($f)))
{
	$sep = strpos($s, "\t") ? "\t" : " ";
	list($entryId, $lastPlayedAt, $plays, $views, $plays30days, $views30days,
		$plays7days, $views7days, $plays1day, $views1day) = explode($sep, $s);

	myPartnerUtils::resetAllFilters();
	myPartnerUtils::resetPartnerFilter('entry');
	$entry = entryPeer::retrieveByPK($entryId);
	if (!$entry)
	{
		KalturaLog::err('Couldn\'t find entry [' . $entryId . ']' );
		continue;
	}

	if ($playsViewsCache)
	{
		//in case we have cache don't use propel so we wont get the values from cache
		$entrySql = 'SELECT e.plays, e.views FROM entry AS e WHERE e.status<>3 and e.id = ?';
		$stmt = $connection->prepare($entrySql);
		$stmt->bindValue(1, $entryId);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if (!$row)
		{
			KalturaLog::err('Couldn\'t find entry [' . $entryId . ']');
			continue;
		}
	}

	$entryPlays = $playsViewsCache ? $row['plays'] : $entry->getPlays();
	$entryViews = $playsViewsCache ? $row['views'] : $entry->getViews();

	if ($entry->getMediaType() == entry::ENTRY_MEDIA_TYPE_IMAGE)
	{
		$plays = $views;
	}

	// update mysql if plays/views changed
	if ($entryViews != $views || $entryPlays != $plays)
	{
		try
		{
			// update entry without setting the updated at
			$lastPlayedDate = date('Y-m-d H:i:s', $lastPlayedAt);
			$updateSql = "UPDATE entry set views='$views',plays='$plays',last_played_at='$lastPlayedDate' WHERE id='$entryId'";
			$stmt = $connection->prepare($updateSql);
			$stmt->execute();
			KalturaLog::debug('Successfully saved entry [' . $entryId . ']');
			$affectedRows = $stmt->rowCount();
			KalturaLog::log("AffectedRows: " . $affectedRows);
		}
		catch (Exception $e)
		{
			KalturaLog::log($e->getMessage(), Propel::LOG_ERR);
		}
	}

	// update cache - if one of the values changed
	if ($playsViewsCache && ($entry->getPlays() != $plays || $entry->getViews() != $views ||
			$entry->getPlaysLast30Days() != $plays30days ||
			$entry->getPlaysLast7Days() != $plays7days || $entry->getPlaysLastDay() != $plays1day ||
			$entry->getViewsLast30Days() != $views30days || $entry->getViewsLast7Days() != $views7days ||
			$entry->getViewsLastDay() != $views1day))
	{
		KalturaLog::debug('Updating playsViewsCache');
		try
		{
			$key = entry::PLAYSVIEWS_CACHE_KEY_PREFIX . $entryId;
			$cacheItem = array(
				entry::PLAYS_CACHE_KEY => $plays,
				entry::VIEWS_CACHE_KEY => $views,
				entry::LAST_PLAYED_AT_CACHE_KEY => $lastPlayedAt,
				entry::PLAYS_30_DAYS_CACHE_KEY => $plays30days,
				entry::VIEWS_30_DAYS_CACHE_KEY => $views30days,
				entry::PLAYS_7_DAYS_CACHE_KEY => $plays7days,
				entry::VIEWS_7_DAYS_CACHE_KEY => $views7days,
				entry::PLAYS_1_DAY_CACHE_KEY => $plays1day,
				entry::VIEWS_1_DAY_CACHE_KEY => $views1day,
			);
			$cacheItem = json_encode($cacheItem);
			$playsViewsCache->set($key, $cacheItem, 0);
		}
		catch (Exception $e)
		{
			KalturaLog::log($e->getMessage(), Propel::LOG_ERR);
		}
	}

	// update sphinx
	if ($entryViews != $views || $entryPlays != $plays)
	{
		KalturaLog::debug('Updating sphinx');
		$entry->setViews($views);
		$entry->setPlays($plays);
		$entry->setLastPlayedAt($lastPlayedAt);
		try
		{
			$sql = $sphinxMgr->getSphinxSaveSql($entry, false);
			$sphinxLog = new SphinxLog();
			$sphinxLog->setEntryId($entryId);
			$sphinxLog->setObjectId($entryId);
			$indexClass = $entry->getIndexObjectName();
			$sphinxLog->setObjectType($indexClass::getObjectName());
			$sphinxLog->setPartnerId($entry->getPartnerId());
			$sphinxLog->setSql($sql);
			$sphinxLog->setType(SphinxLogType::SPHINX);
			$sphinxLog->setIndexName($entry->getSphinxIndexName());
			$sphinxLog->save(myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_SPHINX_LOG));
		}
		catch (Exception $e)
		{
			KalturaLog::log($e->getMessage(), Propel::LOG_ERR);
		}
	}

	// update elastic
	if ($entryViews != $views || $entryPlays != $plays || ($playsViewsCache && ($entry->getPlaysLast30Days() != $plays30days ||
			$entry->getPlaysLast7Days() != $plays7days || $entry->getPlaysLastDay() != $plays1day ||
			$entry->getViewsLast30Days() != $views30days || $entry->getViewsLast7Days() != $views7days ||
			$entry->getViewsLastDay() != $views1day)))
	{
		KalturaLog::debug('Updating elastic');
		try
		{
			$doc = array(
				'plays' => $plays,
				'views' => $views,
				'last_played_at' => $lastPlayedAt
			);

			if ($playsViewsCache)
			{
				$cacheDoc = array(
					'plays_30days' => $plays30days,
					'views_30days' => $views30days,
					'plays_7days' => $plays7days,
					'views_7days' => $views7days,
					'views_1day' => $views1day,
					'plays_1day' => $plays1day
				);
				$doc = array_merge($doc, $cacheDoc);
			}

			$doc = array_map('intval', $doc);

			$params = array(
				'index' => $entry->getElasticIndexName(),
				'type' => $entry->getElasticObjectType(),
				'id' => $entry->getElasticId(),
				'action' => ElasticMethodType::UPDATE,
				'body' => array(
					'doc' => $doc
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
		}
		catch (Exception $e)
		{
			KalturaLog::log($e->getMessage(), Propel::LOG_ERR);
		}
	}

	$count++;
	if ($count % 500 === 0)
	{
		entryPeer::clearInstancePool();
	}
}
KalturaLog::log('Script Finished, Handled ' . $count . ' entries');
