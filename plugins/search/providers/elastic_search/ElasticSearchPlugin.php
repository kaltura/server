<?php
/**
 * @package plugins.elasticSearch
 */
class ElasticSearchPlugin extends KalturaPlugin implements IKalturaEventConsumers, IKalturaPending, IKalturaServices, IKalturaObjectLoader
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

    /* (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
	 */
    public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
    {
        if ($baseClass == 'KalturaESearchItemData' && $enumValue == KalturaESearchItemDataType::CAPTION)
            return new KalturaESearchCaptionItemData();

        if ($baseClass == 'ESearchItemData' && $enumValue == ESearchItemDataType::CAPTION)
            return new ESearchCaptionItemData();

        if ($baseClass == 'KalturaESearchItemData' && $enumValue == KalturaESearchItemDataType::METADATA)
            return new KalturaESearchMetadataItemData();

        if ($baseClass == 'ESearchItemData' && $enumValue == ESearchItemDataType::METADATA)
            return new ESearchMetadataItemData();

        if ($baseClass == 'KalturaESearchItemData' && $enumValue == KalturaESearchItemDataType::CUE_POINTS)
            return new KalturaESearchCuePointItemData();

        if ($baseClass == 'ESearchItemData' && $enumValue == ESearchItemDataType::CUE_POINTS)
            return new ESearchCuePointItemData();

        return null;
    }

    /* (non-PHPdoc)
	* @see IKalturaObjectLoader::loadObject()
	*/
    public static function getObjectClass($baseClass, $enumValue)
    {
       return null;
    }

}
