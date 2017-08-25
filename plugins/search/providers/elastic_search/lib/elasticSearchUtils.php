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
				self::getItemDataFromInnerHits($elasticObject, $itemData);

			$objectData[$elasticObject['_id']] = $itemData;
			$objectOrder[$elasticObject['_id']] = $key;
		}

		if(isset($elasticResults['hits']['total']))
			$objectCount = $elasticResults['hits']['total'];

		return array($objectData, $objectOrder, $objectCount);
	}

	private static function getItemDataFromInnerHits($elasticObject, &$itemData)
	{
		foreach ($elasticObject['inner_hits'] as $innerHitsKey => $hits)
		{
			$objectType = self::getInnerHitsObjectType($innerHitsKey);
			$itemData[$objectType] = array();
			$itemData[$objectType]['totalCount'] = self::getInnerHitsTotalCountForObject($hits, $objectType);
			foreach ($hits['hits']['hits'] as $itemResult)
			{
				$currItemData = KalturaPluginManager::loadObject('ESearchItemData', $objectType);
				if ($currItemData)
				{
					$currItemData->loadFromElasticHits($itemResult);
					$itemData[$objectType]['items'][] = $currItemData;
				}
			}
		}
	}

	private static function getInnerHitsObjectType($innerHitsKey)
	{
		switch ($innerHitsKey)
		{
			case 'caption_assets.lines':
				return ESearchItemDataType::CAPTION;
			case 'metadata':
				return ESearchItemDataType::METADATA;
			case 'cue_points':
				return ESearchItemDataType::CUE_POINTS;
			default:
			{
				KalturaLog::err('Unsupported inner object key in elastic results['.$innerHitsKey.']');
				return $innerHitsKey;
			}
		}
	}

	private static function getCoreESearchResults($coreObjects, $objectsData, $objectsOrder)
	{
		$resultsObjects = array();
		usort($coreObjects, build_sorter($objectsOrder));
		foreach ($coreObjects as $coreObject)
		{
			$resultObj = new ESearchResult();
			$resultObj->setObject($coreObject);
			$itemsData = array();
			foreach ($objectsData[$coreObject->getId()] as $objectType => $values)
			{
				$itemsDataResult = self::getItemsDataResult($objectType, $values);
				if($itemsDataResult)
					$itemsData[] = $itemsDataResult;
			}
			$resultObj->setItemsData($itemsData);
			$resultsObjects[] = $resultObj;
		}
		return $resultsObjects;
	}

	private static function getItemsDataResult($objectType, $values)
	{
		if(!isset($values['items']))
			return null;

		$result = new ESearchItemsDataResult();
		$result->setTotalCount($values['totalCount']);
		$result->setItems($values['items']);
		$result->setItemsType($objectType);

		return $result;
	}

	public static function transformElasticToCoreObject($elasticResults, $peerName)
	{
		list($objectData, $objectOrder, $objectCount) = elasticSearchUtils::getElasticResultAsArray($elasticResults);
		$objects = $peerName::retrieveByPKs(array_keys($objectData));
		$coreResults = elasticSearchUtils::getCoreESearchResults($objects, $objectData, $objectOrder);
		return array($coreResults, $objectCount);
	}

	protected static function getInnerHitsTotalCountForObject($objectResult, $objectType)
	{
		switch ($objectType)
		{
			case ESearchItemDataType::CAPTION:
			case ESearchItemDataType::METADATA:
			case ESearchItemDataType::CUE_POINTS:
				 return $objectResult['hits']['total'];
			default:
			{
				KalturaLog::err('Unsupported inner object type in elastic results['.$objectType.']');
				return 0;
			}
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

	public static function isMaster($elasticClient, $elasticHostName)
	{
		$masterInfo = $elasticClient->getMasterInfo();
		if(isset($masterInfo[0]['node']) && $masterInfo[0]['node'] == $elasticHostName)
			return true;

		return false;
	}

}
