<?php
/**
 * @package plugins.searchHistory
 */
class SearchHistoryPlugin extends KalturaPlugin implements IKalturaPending, IKalturaServices, IKalturaEventConsumers
{

    const PLUGIN_NAME = 'searchHistory';
    const SEARCH_HISTORY_MANAGER = 'kESearchHistoryManager';

    /**
     * @return string the name of the plugin
     */
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
            self::SEARCH_HISTORY_MANAGER,
        );
    }

    /* (non-PHPdoc)
    * @see IKalturaPending::dependsOn()
    */
    public static function dependsOn()
    {
        $rabbitMqDependency = new KalturaDependency(RabbitMQPlugin::getPluginName());
        $elasticSearchDependency = new KalturaDependency(ElasticSearchPlugin::getPluginName());
        return array($rabbitMqDependency, $elasticSearchDependency);
    }

    public static function getServicesMap()
    {
        $map = array(
            'SearchHistory' => 'ESearchHistoryService',
        );
        return $map;
    }

}
