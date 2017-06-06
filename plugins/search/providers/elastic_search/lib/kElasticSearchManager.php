<?php
/**
 * @package plugins.elasticSearch
 * @subpackage lib
 */
class kElasticSearchManager implements kObjectReadyForIndexEventConsumer, kObjectReadyForIndexContainerEventConsumer, kObjectUpdatedEventConsumer, kObjectAddedEventConsumer
{

    const CACHE_PREFIX = 'executed_elastic_server_';

    /**
     * @param BaseObject $object
     * @param BatchJob $raisedJob
     * @return bool true if should continue to the next consumer
     */
    public function objectReadyForIndex(BaseObject $object, BatchJob $raisedJob = null)
    {
        $this->saveToElastic($object);
        return true;
    }

    /**
     * @param BaseObject $object
     * @return bool true if the consumer should handle the event
     */
    public function shouldConsumeReadyForIndexEvent(BaseObject $object)
    {
        if($object instanceof IElasticIndexable)
            return true;

        return false;
    }

    public function saveToElastic(IElasticIndexable $object ,$params = null)
    {
        KalturaLog::debug('Saving to elastic for object [' . get_class($object) . '] [' . $object->getId() . ']');
        //$cmd = $this->getElasticSaveParams($object, $params);
        $cmd['body'] = $object->getObjectParams($params);
        if(!$cmd)
            return true;

        return $this->execElastic($cmd, $object);
    }

    //exe the curl
    public function execElastic($params, IElasticIndexable $object)
    {
        $client = new elasticClient();
        if($object->getElasticParentId())
            $params['parent'] = $object->getElasticParentId();

        $op = $object->getElasticSaveMethod();
        $params['index'] = $object->getElasticIndexName();
        $params['type'] = $object->getElasticObjectType();
        $params['id'] = $object->getElasticId();

        $params['action'] = $op;
        $elasticLog = new SphinxLog();
        $command = serialize($params);
        $elasticLog->setSql($command);
        $elasticLog->setExecutedServerId($this->retrieveElasticServerId());
        $elasticLog->setObjectId($object->getId());
        $elasticLog->setObjectType($object->getElasticObjectType());
        //$sphinxLog->setEntryId($object->getEntryId());
        $elasticLog->setPartnerId($object->getPartnerId());
        $elasticLog->setType(SphinxLogType::ELASTIC);
        $elasticLog->save(myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_SPHINX_LOG));

        if(!kConf::get('exec_elastic', 'local', 0))
            return true;
        
        $ret = $client->$op($params);
        if(!$ret)
        {
            KalturaLog::err('Failed to Execute elasticSearch query: '.print_r($params,true));
            return false;
        }

        return true;
    }

    private function retrieveElasticServerId()
    {
        $elasticServerId = null;
        if(kConf::hasParam('exec_elastic') && kConf::get('exec_elastic'))
        {
            $elasticHostName = kConf::get('elasticHost', 'local');
            $elasticServerCacheStore = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_ELASTIC_EXECUTED_SERVER);
            if ($elasticServerCacheStore)
            {
                $elasticServerId = $elasticServerCacheStore->get(self::CACHE_PREFIX . $elasticHostName);
                if ($elasticServerId)
                    return $elasticServerId;
            }
            $elasticServer = SphinxLogServerPeer::retrieveByLocalServer($elasticHostName);
            if($elasticServer)
            {
                $elasticServerId = $elasticServer->getId();
                if ($elasticServerCacheStore)
                    $elasticServerCacheStore->set(self::CACHE_PREFIX . $elasticHostName, $elasticServerId);
            }
        }

        return $elasticServerId;
    }

    /**
     * @param $object
     * @param $params
     * @return bool true if should continue to the next consumer
     */
    public function objectReadyForIndexContainer($object, $params = null)
    {
        $this->saveToElastic($object, $params);
        return true;
    }

    /**
     * @param  $object
     * @param  $params
     * @return bool true if the consumer should handle the event
     */
    public function shouldConsumeReadyForIndexContainerEvent($object, $params = null)
    {
        if($object instanceof CaptionAsset)
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
        if($object instanceof CaptionAsset)
            return false;

        if($object instanceof IElasticIndexable)
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
        if($object instanceof CaptionAsset)
            return false;

        if($object instanceof IElasticIndexable)
            return true;

        return false;
    }
}
