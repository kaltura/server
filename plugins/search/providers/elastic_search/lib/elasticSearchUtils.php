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
		return array($objectData, $objectOrder);
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

	public static function transformElasticToCategory($elasticResults)
	{
		list($categoryData, $categoryOrder) = elasticSearchUtils::getElasticResultAsArray($elasticResults);
		$categories = categoryPeer::retrieveByPKs(array_keys($categoryData));
		return elasticSearchUtils::getCoreESearchResults($categories, $categoryData, $categoryOrder);
	}

	public static function transformElasticToEntry($elasticResults)
	{
		list($entriesData, $entriesOrder) = elasticSearchUtils::getElasticResultAsArray($elasticResults);
		$entries = entryPeer::retrieveByPKs(array_keys($entriesData));
		return elasticSearchUtils::getCoreESearchResults($entries, $entriesData, $entriesOrder);
	}

	public static function transformElasticToUser($elasticResults)
	{
		list($usersData, $usersOrder) = elasticSearchUtils::getElasticResultAsArray($elasticResults);
		$users = kuserPeer::retrieveByPKs(array_keys($usersData));
		return elasticSearchUtils::getCoreESearchResults($users, $usersData, $usersOrder);
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
