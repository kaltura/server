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
		    foreach ($elasticEntry['inner_hits'] as $objectType => $hits)
		    {
			    foreach ($hits['hits']['hits'] as $objectResult)
			    {
				    $itemResults = self::getItemResults($objectResult, $objectType);
				    foreach ($itemResults as $itemResult)
				    {
					    $currItemData = KalturaPluginManager::loadObject('ESearchItemData', $objectType);
					    if ($currItemData)
					    {
						    $currItemData->loadFromElasticHits($itemResult);
						    $itemData[] = $currItemData;
					    }
				    }
			    }
		    }
		    $entriesData[$elasticEntry['_id']] = $itemData;
	    }
	    $entries = entryPeer::retrieveByPKs(array_keys($entriesData));
	    foreach ($entries as $baseEntry)
	    {
		    $resultObj = new ESearchResult();
		    $resultObj->setEntry($baseEntry);
		    $resultObj->setItemData($entriesData[$baseEntry->getId()]);
		    $coreObjs[] = $resultObj;
	    }
	    return $coreObjs;
    }

	protected static function getItemResults($objectResult, $objectType)
	{
		switch ($objectType)
		{
			case 'caption_assets':
				return $objectResult['inner_hits']['caption_assets.lines']['hits']['hits'];
			case 'metadata':
			case 'cue_points':
				return array($objectResult);
		}
	}

    
}