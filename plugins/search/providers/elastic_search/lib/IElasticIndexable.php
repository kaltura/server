<?php
/**
 * @package plugins.elasticSearch
 * @subpackage lib
 */
interface IElasticIndexable extends IBaseObject
{
 
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
     * return the save method to elastic: ElasticMethodType::INDEX or ElasticMethodType::UPDATE
     */
    public function getElasticSaveMethod();

    /**
     * Index the object into elasticsearch
     */
    public function indexToElastic($params = null);

    /**
     * return true if the object needs to be deleted from elastic
     */
    public function shouldDeleteFromElastic();

    /**
     * return the name of the object we are indexing
     */
    public function getElasticObjectName();

}
