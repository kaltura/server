<?php
/**
 * @package plugins.elasticSearch
 * @subpackage lib
 */
class kElasticSearchManager implements kObjectReadyForIndexEventConsumer, kObjectReadyForIndexContainerEventConsumer, kObjectUpdatedEventConsumer, kObjectAddedEventConsumer
{
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
        KalturaLog::debug('@nadav@ saving to elastic for object [' . get_class($object) . '] [' . $object->getId() . ']');
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

        $op = $object->shouldIndexWithUpdate() ? 'update' : 'index';
        $params['index'] = $object->getElasticIndexName();
        $params['type'] = $object->getElasticObjectType();
        $params['id'] = $object->getElasticId();

        KalturaLog::DEBUG("@nadav@ ".print_r($params,true));
        KalturaLog::DEBUG("@nadav@ op ".print_r($op,true));
        $client->$op($params);
        return true;
    }

    public function deleteFromElastic(IElasticIndexable $object)
    {
        $client = new elasticClient();
        if(!$object->getElasticId())
        {
            KalturaLog::debug("cannot delete an object without an elasticsearch id");
            return;
        }
        $client->delete($object->getElasticIndexName(),$object->getElasticObjectType(),$object->getElasticId());
        KalturaLog::debug("deleted object [".$object->getElasticId()."] from elasticsearch index .[".$object->getElasticIndexName()."] type [".$object->getElasticObjectType()."]");
    }

    /**
     * @param $object
     * @param $params
     * @return bool true if should continue to the next consumer
     */
    public function objectReadyForIndexContainer($object, $params = null)
    {
        KalturaLog::debug("@nadav@ acc ".print_r($params,true));
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
        $object->indexToElasticIndex();
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
        $object->indexToElasticIndex();
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