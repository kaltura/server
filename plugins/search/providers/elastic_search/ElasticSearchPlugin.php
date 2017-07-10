<?php
/**
 * @package plugins.elasticSearch
 */
class ElasticSearchPlugin extends KalturaPlugin implements IKalturaEventConsumers, IKalturaPending, IKalturaServices
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

    /**
     * Returns a Kaltura dependency object that defines the relationship between two plugins.
     *
     * @return array<KalturaDependency> The Kaltura dependency object
     */
    public static function dependsOn()
    {
        $searchDependency = new KalturaDependency(SearchPlugin::getPluginName());
        return array($searchDependency);
    }

    public static function getServicesMap()
    {
        $map = array(
            'ESearch' => 'ESearchService',
        );
        return $map;
    }
}
