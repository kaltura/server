<?php
/**
 * @package plugins.elasticSearch
 * @subpackage lib
 */


function build_sorter($objectsOrder) {
	return function ($a, $b) use ($objectsOrder) {
		return ($objectsOrder[$a->getId()] > $objectsOrder[$b->getId()]) ? 1 : -1;
	};
}

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

	private static function getElasticResultAsArray($elasticResults)
	{
		$objectData = array();
		$objectOrder = array();
		$objectCount = 0;
		foreach ($elasticResults['hits']['hits'] as $key => $elasticObject)
		{
			$itemData = array();
			if (isset($elasticObject['inner_hits']))
			{
				foreach ($elasticObject['inner_hits'] as $objectType => $hits)
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
			}
			$objectData[$elasticObject['_id']] = $itemData;
			$objectOrder[$elasticObject['_id']] = $key;
		}
		if(isset($elasticResults['hits']['total']))
			$objectCount = $elasticResults['hits']['total'];
		return array($objectData, $objectOrder, $objectCount);
	}

	private static function getCoreESearchResults($coreObjects, $objectsData, $objectsOrder)
	{
		$resultsObjects = array();
		usort($coreObjects, build_sorter($objectsOrder));
		foreach ($coreObjects as $coreObject)
		{
			$resultObj = new ESearchResult();
			$resultObj->setObject($coreObject);
			$resultObj->setItemData($objectsData[$coreObject->getId()]);
			$resultsObjects[] = $resultObj;
		}
		return $resultsObjects;
	}

	public static function transformElasticToCoreObject($elasticResults, $peerName)
	{
		list($objectData, $objectOrder, $objectCount) = elasticSearchUtils::getElasticResultAsArray($elasticResults);
		$objects = $peerName::retrieveByPKs(array_keys($objectData));
		$coreResults = elasticSearchUtils::getCoreESearchResults($objects, $objectData, $objectOrder);
		return array($coreResults, $objectCount);
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

	public static function formatPartnerStatus($partnerId, $status)
	{
		return sprintf("p%ss%s", $partnerId, $status);
	}

	public static function formatSearchTerm($searchTerm)
	{
		//remove extra spaces
		$term = preg_replace('/\s+/', ' ', $searchTerm);
		//lowercase and trim
		$term = strtolower($term);
		$term = trim($term);
		return $term;
	}

}
