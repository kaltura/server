<?php
/**
 * @package plugins.elasticSearch
 */
class ElasticSearchPlugin extends KalturaPlugin implements IKalturaEventConsumers
{
    const PLUGIN_NAME = 'elasticSearch';
    const ELASTIC_SEARCH_MANAGER = 'kElasticSearchManager';

    public static function getPluginName()
    {
        return self::PLUGIN_NAME;
    }

    /**
     * @return array
     */
    public static function getEventConsumers()
    {
        return array(
            self::ELASTIC_SEARCH_MANAGER,
        );
    }
}