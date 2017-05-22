<?php
/**
 * @package plugins.elasticSearch
 * @subpackage lib
 */
interface IElasticIndexable extends IBaseObject
{
    /**
     * elastic index prefix
     */
    const ELASTIC_INDEX_PREFIX = 'kaltura';

    /**
     * return the name of the elasticsearch index for this object
     */
    public function getElasticIndexName();

    /**
     * return the name of the elasticsearch type for this object
     */
    public function getElasticObjectType();

    /**
     * return the elasticsearch id for this object
     */
    public function getElasticId();

    /**
     * return the elasticsearch parent id or null if no parent
     */
    public function getElasticParentId();

    /**
     * get the params we index to elasticsearch for this object
     */
    public function getObjectParams($params = null);

    /**
     * return true if we index the doc using update to elasticsearch
     */
    public function shouldIndexWithUpdate();

    /**
     * Index the object into elasticsearch
     */
    public function indexToElasticIndex($params = null);

}