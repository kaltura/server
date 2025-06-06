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
    const MAX_SQL_LENGTH = 262144;// 256 * 1024
    const CACHE_PREFIX_STICKY_SESSIONS = 'elastic_large_sql_lock_';
    const REPETITIVE_UPDATES_CONFIG_KEY = 'skip_elastic_repetitive_updates';
    const METADATA_MAX_LENGTH = 256;
    const EVENT_PLATFORM_METADATA_MAX_LENGTH = 1024;

    private $skipObjects = null;

    /**
     * Add a multidimensional assoc array where key is plugin name and value is array of objects ids, like:
     * array(
     * 'captions' => array('entry_id', 'category_id', 'kuser_id'),
     * 'cuepoint' => array('entry_id')
     * )
     *
     * @param array $objectsToSkipPerPlugin
     * @return void
     */
    public function setSkipObjects(array $objectsToSkipPerPlugin = array())
    {
        $this->skipObjects = $objectsToSkipPerPlugin;
    }

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
        if ($object instanceof IElasticIndexable)
		{
			return true;
		}

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
	
        list($skipElasticRepetitiveUpdatesValue, $matchKey) = kSearchUtils::getSkipRepetitiveUpdatesValue(self::REPETITIVE_UPDATES_CONFIG_KEY, $className);
        $skipSave = isset($skipElasticRepetitiveUpdatesValue) && $saveCounter > $skipElasticRepetitiveUpdatesValue;

        if($skipSave)
        {
            KalturaLog::debug("Skipping save elastic for object [$className] [$objectId] count [$saveCounter] max allowed [$skipElasticRepetitiveUpdatesValue] with match key [$matchKey]");
            return true;
        }
	
		KalturaLog::debug("Updating elastic for object [$className] [$objectId] count [$saveCounter] service info [" . kCurrentContext::$service . ' ' . kCurrentContext::$action . "]");
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
            // used for elastic-builder to skip objects that their DB query hangs causing the process to get stuck
            if (isset($this->skipObjects[$pluginName]) && in_array($object->getId(), $this->skipObjects[$pluginName]))
            {
                $objectId = $object->getId();
                $objectClass = get_class($object);
                KalturaLog::debug("Skipping object [$objectClass] id [$objectId] for plugin [$pluginName]");
                continue;
            }

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

        $genericIndexName = kBaseESearch::getElasticIndexNamePerPartner($object->getElasticIndexName(),$object->getPartnerId(),false);
        $splitIndexName = kBaseESearch::getElasticIndexNamePerPartner($object->getElasticIndexName(),$object->getPartnerId());

        $params['index'] = $genericIndexName;
        $params['type'] = $object->getElasticObjectType();
        $params['id'] = $object->getElasticId();
        $params['action'] = $action;

        KalturaLog::debug('Using elastic Index:' . $splitIndexName . ' ,actual index name:' . $genericIndexName );


        try
        {
            if(kConf::get('disableElastic', 'elastic', true))
                return true;

            $shouldSyncElastic = $this->shouldSyncElastic($object);
            $this->saveToSphinxLog($object, $params, $shouldSyncElastic, $splitIndexName);

            if(!$shouldSyncElastic)
                return true;

            //Must be after the saveToSphinxLog in order to save the generic command
            $params['index'] = $splitIndexName;

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

    private function saveToSphinxLog($object, $params, $shouldSyncElastic, $splitIndexName = null)
    {
        $command = serialize($params);
        $skipSave = $this->shouldSkipSaveToSphinxLog($object, $command);
        if($skipSave)
            return;

        $elasticLog = new SphinxLog();
        $elasticLog->setSql($command);
        $clusterId = $shouldSyncElastic ? $this->retrieveElasticClusterId() : null;
        $elasticLog->setExecutedServerId($clusterId);
        $elasticLog->setObjectId($object->getId());
        $elasticLog->setObjectType($object->getElasticObjectName());
        $elasticLog->setEntryId($object->getElasticEntryId());
        $elasticLog->setPartnerId($object->getPartnerId());
        $elasticLog->setIndexName($splitIndexName);
        $elasticLog->setType(SphinxLogType::ELASTIC);
        $elasticLog->save(myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_SPHINX_LOG));
    }

    private function shouldSyncElastic($object)
    {
        $key = kCurrentContext::$ks_partner_id . "_" . kCurrentContext::$service . "_" . kCurrentContext::$action . "_" . kBaseESearch::getElasticIndexNamePerPartner($object->getElasticIndexName(),$object->getPartnerId());
        $map = kConf::get('partner_actions_to_skip_elastic_map', 'local', array());
        if (isset($map[$key]))
        {
            KalturaLog::log("Specific partner action to skip elastic detected $key. skipping elastic sync.");
            return false;
        }

        if (kConf::get('exec_elastic', 'local', 0))
            return true;

        if (kConf::hasParam('exec_elastic_client_tags'))
        {
            $execElasticTags = kConf::get('exec_elastic_client_tags');
            $clientTag = kCurrentContext::$client_lang;
            foreach ($execElasticTags as $execElasticTag)
            {
                if (strpos($clientTag, $execElasticTag) === 0)
                {
                    return true;
                }
            }
        }

        return false;
    }

    private function shouldSkipSaveToSphinxLog($object, &$command)
    {
        // limit the number of large SQLs to 1/min per object, since they load the sphinx log database and elastic servers
	$commandSize = strlen($command);
        if ($commandSize > self::MAX_SQL_LENGTH)
        {
            $lockKey = self::CACHE_PREFIX_STICKY_SESSIONS . $object->getElasticObjectName() . '_' . $object->getId();
            $cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_ELASTIC_STICKY_SESSIONS);
            if ($cache && !$cache->add($lockKey, true, 60))
            {
                KalturaLog::log('skipping saving elastic sphinxLog sql for key ' . $lockKey . ' size is - ' . $commandSize);
                return true;
            }
        }
        return false;
    }

    private function retrieveElasticClusterId()
    {
        $elasticClusterId = null;
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
		if ($object instanceof IElasticIndexable)
		{
			return true;
		}

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
		{
			return true;
		}

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
		{
			return true;
		}

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
                $oldCustomDataKeys = isset($oldCustomData[$namespace])
	                ? array_keys($oldCustomData[$namespace])
                    : array();
				
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
        $itemsToTrim = array('name', 'description', 'reference_id');

        $params = &$tempParams;
        // in case we are handling category we need to handle the 'doc' element.
        if(isset($tempParams['doc']))
            $params = &$tempParams['doc'];

        foreach ($itemsToTrim as $item)
        {
            if(!array_key_exists($item, $params) || is_null($params[$item]))
            {
                continue;
            }

            //Handle cases where item is array object, this can happen when multi language description is passed
            if(is_array($params[$item]))
            {
                foreach ($params[$item] as $key => $value)
                {
                    if(strlen($value) > kElasticSearchManager::MAX_LENGTH)
                    {
                        $value = substr($value, 0, self::MAX_LENGTH);
                        $params[$item][$key] = $value;
                    }
                }
                continue;
            }
            
            if (strlen($params[$item]) > kElasticSearchManager::MAX_LENGTH)
            {
                $params[$item] = substr($params[$item], 0, self::MAX_LENGTH);
            }
        }
        return $tempParams;
    }

    public function deleteFromElastic(IElasticIndexable $object)
    {
        $this->execElastic(null, $object, 'delete');
    }

}
