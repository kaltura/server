<?php
/**
 * @package plugins.elasticSearch
 * @subpackage lib
 */

class elasticSearchUtils
{
    /**
     * return the analyzed language field name
     * @param $language
     * @param $fieldName
     * @return null|string
     */
    public static function getAnalyzedFieldName($language, $fieldName)
    {
        $fieldMap = array(
            'english' => 'english',
            'arabic' => 'arabic',
            'basque' => 'basque',
            'brazilian' => 'brazilian',
            'bulgarian' => 'bulgarian',
            'catalan' => 'catalan',
            'chinese' => 'cjk',
            'korean' => 'cjk',
            'japanese' => 'cjk',
            'czech' => 'czech',
            'danish' => 'danish',
            'dutch' => 'dutch',
            'finnish' => 'finnish',
            'french' => 'french',
            'galician' => 'galician',
            'german' => 'german',
            'greek' => 'greek',
            'hindi' => 'hindi',
            'hungarian' => 'hungarian',
            'indonesian' => 'indonesian',
            'irish' => 'irish',
            'italian' => 'italian',
            'latvian' => 'latvian',
            'lithuanian' => 'lithuanian',
            'norwegian' => 'norwegian',
            'persian' => 'persian',
            'portuguese' => 'portuguese',
            'romanian' => 'romanian',
            'russian' => 'russian',
            'sorani' => 'sorani',
            'spanish' => 'spanish',
            'swedish' => 'swedish',
            'turkish' => 'turkish',
            'thai' => 'thai',
        );

        $language = strtolower($language);
        if(isset($fieldMap[$language]))
            return $fieldName.'_'.$fieldMap[$language];

        return null;
    }

    public static function transformElasticToObject($elasticResults)
    {
		$coreObjs = array();
	    $entriesData = array();
	    foreach ($elasticResults['hits']['hits'] as $elasticEntry)
	    {
		    $itemData = array();
		    foreach ($elasticEntry['inner_hits']['caption']['hits']['hits'] as $captionAssetResult)
		    {
			    foreach ($captionAssetResult['inner_hits']['lines']['hits']['hits'] as $captionAssetItemResult)
			    {
				    $currItemData = KalturaPluginManager::loadObject('ESearchItemData', $captionAssetResult['_type']);
				    $currItemData->setLine($captionAssetItemResult['_source']['content']);
				    $currItemData->setStartsAt($captionAssetItemResult['_source']['start_time']);
				    $currItemData->setEndsAt($captionAssetItemResult['_source']['end_time']);
				    $itemData[] = $currItemData;
			    }
		    }
		    $entriesData[$elasticEntry['_id']] = $itemData;
	    }
	    $entries = entryPeer::retrieveByPKs(array_keys($entriesData));
	    foreach ($entries as $baseEntry)
	    {
		    $resultObj = new UltraSearchResult();
		    $resultObj->setEntry($baseEntry);
		    $resultObj->setItemData($entriesData[$baseEntry->getId()]);
		    $coreObjs[] = $resultObj;
	    }
	    return $coreObjs;
    }

    
}