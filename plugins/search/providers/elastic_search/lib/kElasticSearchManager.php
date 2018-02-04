<?php
/**
 * @package plugins.elasticSearch
 * @subpackage lib
 */
class kElasticSearchManager implements kObjectReadyForIndexEventConsumer, kObjectReadyForElasticIndexEventConsumer, kObjectUpdatedEventConsumer, kObjectAddedEventConsumer, kObjectChangedEventConsumer
{

    const CACHE_PREFIX = 'executed_elastic_cluster_';
    const MAX_LENGTH = 32766;
    const MAX_CUE_POINTS = 5000;
    const MAX_SQL_LENGTH = 131072;// 128 * 1024
    const CACHE_PREFIX_STICKY_SESSIONS = 'elastic_large_sql_lock_';
    const REPETITIVE_UPDATES_CONFIG_KEY = 'skip_elastic_repetitive_updates';

    /**
     * @param BaseObject $object
     * @param BatchJob $raisedJob
     * @return bool true if should continue to the next consumer
     */
    public function objectReadyForIndex(BaseObject $object, BatchJob $raisedJob = null)
    {
        if($object instanceof CuePoint && in_array($object->getType(), CuePointPlugin::getElasticIndexOnEntryTypes()) && !in_array($object->getType(), CuePointPlugin::getIndexOnEntryTypes()))
        {
            kCuePointManager::reIndexCuePointEntry($object, false, true);//reindex the entry only on elastic
        }

        if($object instanceof IElasticIndexable)
        {
            if($object->shouldDeleteFromElastic())
                $this->deleteFromElastic($object);
            else
                $this->saveToElastic($object);
        }

        return true;
    }

    /**
     * @param BaseObject $object
     * @return bool true if the consumer should handle the event
     */
    public function shouldConsumeReadyForIndexEvent(BaseObject $object)
    {
        if($object instanceof CuePoint && in_array($object->getType(), CuePointPlugin::getElasticIndexOnEntryTypes()) && !in_array($object->getType(), CuePointPlugin::getIndexOnEntryTypes()))
            return true;

        if($object instanceof IElasticIndexable)
            return true;

        return false;
    }

    public function saveToElastic(IElasticIndexable $object ,$params = null)
    {
        if(kConf::get('disableElastic', 'elastic', true))
            return true;

        $skipSave = $this->checkRepetitiveUpdates($object);
        if($skipSave)
            return true;

        $cmd = $this->getElasticSaveParams($object, $params);

        if(!$cmd)
            return true;

        return $this->execElastic($cmd, $object, $object->getElasticSaveMethod());
    }

    private function checkRepetitiveUpdates($object)
    {
        $className = get_class($object);
        $objectId = $object->getId();

        // track repetitive updates of the same object (e.g. adding many annotations which cause updates of the entry)
        // once hitting a threshold, we will avoid more than one update per minute
        // threshold is defined by kConf parameter skip_elastic_repetitive_updates using lowercase of {className}_{service}_{action}={threshold}
        $saveCounter = 0;
        $cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_LOCK_KEYS);
        if ($cache)
        {
            $cacheKey = "ELSSave:$className:$objectId";
            $cache->add($cacheKey, 0, 60);
            $saveCounter = $cache->increment($cacheKey);
        }

        $updatesKey = strtolower($className."_".kCurrentContext::$service."_".kCurrentContext::$action);
        $skipElasticRepetitiveUpdates = kConf::get(self::REPETITIVE_UPDATES_CONFIG_KEY, 'local', array());
        $skipSave = isset($skipElasticRepetitiveUpdates[$updatesKey]) && $saveCounter > $skipElasticRepetitiveUpdates[$updatesKey];
        KalturaLog::debug("Saving to elastic for object [$className] [$objectId] count [ $saveCounter  ] " . kCurrentContext::$service . ' ' . kCurrentContext::$action . " [$skipSave]");

        if ($skipSave)
            return true;

        return false;
    }

    public function getElasticSaveParams($object, $params)
    {
        $cmd['body'] = $this->trimParamFields($object->getObjectParams($params));

        $pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaElasticSearchDataContributor');
        $dataContributionPath = null;

        if(isset($cmd['body']['doc']))
            $dataContributionPath = &$cmd['body']['doc'];
        else
            $dataContributionPath = &$cmd['body'];

        foreach($pluginInstances as $pluginName => $pluginInstance)
        {
            KalturaLog::debug("Loading $pluginName elastic data contribution");
            $elasticPluginData = null;
            try
            {
                $elasticPluginData = $pluginInstance::getElasticSearchData($object);
            }
            catch(Exception $e)
            {
                KalturaLog::err($e->getMessage());
                continue;
            }

            if($elasticPluginData)
            {
                $dataContributionPath = array_merge($dataContributionPath, $elasticPluginData);
            }
        }

        elasticSearchUtils::prepareForInsertToElastic($cmd);
        return $cmd;
    }

    //exe the curl
    public function execElastic($params, IElasticIndexable $object, $action)
    {
        if($object->getElasticParentId())
            $params['parent'] = $object->getElasticParentId();

        $params['index'] = $object->getElasticIndexName();
        $params['type'] = $object->getElasticObjectType();
        $params['id'] = $object->getElasticId();
        $params['action'] = $action;

        try
        {
            if(kConf::get('disableElastic', 'elastic', true))
                return true;

            $this->saveToSphinxLog($object, $params);

            if(!kConf::get('exec_elastic', 'local', 0))
                return true;

            $client = new elasticClient();
            $ret = $client->$action($params);
            if(!$ret)
            {
                KalturaLog::err('Failed to Execute elasticSearch query: '.print_r($params,true));
                return false;
            }
        }
        catch (Exception $e)
        {
            KalturaLog::warning('Failed to execute elastic');
        }

        return true;
    }

    private function saveToSphinxLog($object, $params)
    {
        $command = serialize($params);
        $skipSave = $this->shouldSkipSaveToSphinxLog($object, $command);
        if($skipSave)
            return;

        $elasticLog = new SphinxLog();
        $elasticLog->setSql($command);
        $elasticLog->setExecutedServerId($this->retrieveElasticClusterId());
        $elasticLog->setObjectId($object->getId());
        $elasticLog->setObjectType($object->getElasticObjectName());
        $elasticLog->setEntryId($object->getElasticEntryId());
        $elasticLog->setPartnerId($object->getPartnerId());
        $elasticLog->setType(SphinxLogType::ELASTIC);
        $elasticLog->save(myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_SPHINX_LOG));
    }

    private function shouldSkipSaveToSphinxLog($object, &$command)
    {
        // limit the number of large SQLs to 1/min per object, since they load the sphinx log database and elastic servers
        if (strlen($command) > self::MAX_SQL_LENGTH)
        {
            $lockKey = self::CACHE_PREFIX_STICKY_SESSIONS . $object->getElasticObjectName() . '_' . $object->getId();
            $cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_ELASTIC_STICKY_SESSIONS);
            if ($cache && !$cache->add($lockKey, true, 60))
            {
                KalturaLog::log('skipping saving elastic sphinxLog sql for key ' . $lockKey);
                return true;
            }
        }
        return false;
    }

    private function retrieveElasticClusterId()
    {
        $elasticClusterId = null;
        if(kConf::hasParam('exec_elastic') && kConf::get('exec_elastic'))
        {
            $elasticClusterName = kConf::get('elasticCluster', 'elastic', 0);
            $elasticClusterCacheStore = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_ELASTIC_EXECUTED_CLUSTER);
            if ($elasticClusterCacheStore)
            {
                $elasticClusterId = $elasticClusterCacheStore->get(self::CACHE_PREFIX . $elasticClusterName);
                if ($elasticClusterId)
                    return $elasticClusterId;
            }
            $elasticCluster = SphinxLogServerPeer::retrieveByLocalServer($elasticClusterName);
            if($elasticCluster)
            {
                $elasticClusterId = $elasticCluster->getId();
                if ($elasticClusterCacheStore)
                    $elasticClusterCacheStore->set(self::CACHE_PREFIX . $elasticClusterName, $elasticClusterId);
            }
        }

        return $elasticClusterId;
    }

    /**
     * @param $object
     * @param $params
     * @return bool true if should continue to the next consumer
     */
    public function objectReadyForElasticIndex($object, $params = null)
    {
        if($object->shouldDeleteFromElastic())
            $this->deleteFromElastic($object);
        else
            $this->saveToElastic($object);
        return true;
    }

    /**
     * @param  $object
     * @param  $params
     * @return bool true if the consumer should handle the event
     */
    public function shouldConsumeReadyForElasticIndexEvent($object, $params = null)
    {
        if($object instanceof IElasticIndexable)
            return true;

        return false;
    }

    /**
     * @param BaseObject $object
     * @param BatchJob $raisedJob
     * @return bool true if should continue to the next consumer
     */
    public function objectAdded(BaseObject $object, BatchJob $raisedJob = null)
    {
        /** @var IElasticIndexable $object */
        $object->indexToElastic();
        return true;
    }

    /**
     * @param BaseObject $object
     * @return bool true if the consumer should handle the event
     */
    public function shouldConsumeAddedEvent(BaseObject $object)
    {
        //prevent indexing 2 times- if object is IIndexable we raise the event in kSphinxSearchManager
        if($object instanceof IElasticIndexable && !($object instanceof IIndexable))
            return true;

        return false;
    }

    /**
     * @param BaseObject $object
     * @param BatchJob $raisedJob
     * @return bool true if should continue to the next consumer
     */
    public function objectUpdated(BaseObject $object, BatchJob $raisedJob = null)
    {
        /** @var IElasticIndexable $object */
        $object->indexToElastic();
        return true;
    }

    /**
     * @param BaseObject $object
     * @return bool true if the consumer should handle the event
     */
    public function shouldConsumeUpdatedEvent(BaseObject $object)
    {
        //prevent indexing 2 times- if object is IIndexable we raise the event in kSphinxSearchManager
        if($object instanceof IElasticIndexable && !($object instanceof IIndexable))
            return true;

        return false;
    }

    /**
     * @param BaseObject $object
     * @param array $modifiedColumns
     * @return bool true if should continue to the next consumer
     */
    public function objectChanged(BaseObject $object, array $modifiedColumns)
    {
        $childEntries = entryPeer::retrieveChildEntriesByEntryIdAndPartnerId($object->getId(), $object->getPartnerId());
        foreach ($childEntries as $childEntry)
        {
            /**
             * @var entry $childEntry
             */
            $childEntry->indexToElastic();
        }
    }

    /**
     * @param BaseObject $object
     * @param array $modifiedColumns
     * @return bool true if the consumer should handle the event
     */
    public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns)
    {
        //fields we save on parent entry
        $fieldsToMonitor = array(entryPeer::STATUS, entryPeer::KUSER_ID, entryPeer::CATEGORIES_IDS);
        // custom data for entitled kusers edit/publish
        $customDataFieldsToMonitor = array('entitledUserPuserEdit', 'entitledUserPuserPublish', 'creatorKuserId');
        $namespace = '';

        if($object instanceof entry)
        {
            unset($modifiedColumns[kObjectChangedEvent::CUSTOM_DATA_OLD_VALUES]);

            if(count(array_intersect($fieldsToMonitor, $modifiedColumns)) > 0)
                return true;

            if(in_array(entryPeer::CUSTOM_DATA, $modifiedColumns))
            {
                $oldCustomData = $object->getCustomDataOldValues();
                $oldCustomDataKeys = array_keys($oldCustomData[$namespace]);
                if(count(array_intersect($customDataFieldsToMonitor, $oldCustomDataKeys)) > 0)
                    return true;
            }
        }
        return false;
    }

    /**
     * @param $tempParams
     * @return mixed
     */
    private function trimParamFields($tempParams)
    {
        $itemsToTrim = array('description', 'reference_id');

        $params = &$tempParams;
        // in case we are handling category we need to handle the 'doc' element.
        if(isset($tempParams['doc']))
            $params = &$tempParams['doc'];

        foreach ($itemsToTrim as $item)
        {
            if (array_key_exists($item, $params) && (strlen($params[$item]) > kElasticSearchManager::MAX_LENGTH))
                $params[$item] = substr($params[$item], 0, self::MAX_LENGTH);
        }
        return $tempParams;
    }

    public function deleteFromElastic(IElasticIndexable $object)
    {
        $this->execElastic(null, $object, 'delete');
    }

}
