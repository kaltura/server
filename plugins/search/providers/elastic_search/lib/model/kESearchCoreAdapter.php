<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model
 */

function build_sorter($objectsOrder) {
	return function ($a, $b) use ($objectsOrder) {
		return ($objectsOrder[$a->getId()] > $objectsOrder[$b->getId()]) ? 1 : -1;
	};
}

class kESearchCoreAdapter
{

	const HITS_KEY = 'hits';
	const INNER_HITS_KEY = 'inner_hits';
	const ID_KEY = '_id';
	const TOTAL_KEY = 'total';
	const TOTAL_COUNT_KEY = 'totalCount';
	const ITEMS_KEY = 'items';
	const HIGHLIGHT_KEY = 'highlight';

	private static $innerHitsObjectType = array(
		'caption' => ESearchItemDataType::CAPTION,
		'metadata' => ESearchItemDataType::METADATA,
		'cue_points' => ESearchItemDataType::CUE_POINTS
	);

	public static function transformElasticToCoreObject($elasticResults, $peerName, $peerRetrieveFunctionName)
	{
		list($objectData, $objectOrder, $objectCount, $objectHighlight) = self::getElasticResultAsArray($elasticResults);
		$objects = $peerName::$peerRetrieveFunctionName(array_keys($objectData));
		$coreResults = self::getCoreESearchResults($objects, $objectData, $objectOrder, $objectHighlight);
		return array($coreResults, $objectCount);
	}

	private static function getElasticResultAsArray($elasticResults)
	{
		$objectData = array();
		$objectOrder = array();
		$objectHighlight = array();
		$objectCount = 0;
		foreach ($elasticResults[self::HITS_KEY][self::HITS_KEY] as $key => $elasticObject)
		{
			$itemData = array();
			if (isset($elasticObject[self::INNER_HITS_KEY]))
				self::getItemDataFromInnerHits($elasticObject, $itemData);
			
			$objectData[$elasticObject[self::ID_KEY]] = $itemData;
			$objectOrder[$elasticObject[self::ID_KEY]] = $key;
			if(array_key_exists(self::HIGHLIGHT_KEY, $elasticObject))
				$objectHighlight[$elasticObject[self::ID_KEY]] = self::elasticHighlightToCoreHighlight($elasticObject[self::HIGHLIGHT_KEY]);
		}
		
		if(isset($elasticResults[self::HITS_KEY][self::TOTAL_KEY]))
			$objectCount = $elasticResults[self::HITS_KEY][self::TOTAL_KEY];
		
		return array($objectData, $objectOrder, $objectCount, $objectHighlight);
	}

	private static function getCoreESearchResults($coreObjects, $objectsData, $objectsOrder, $objectHighlight)
	{
		$resultsObjects = array();
		usort($coreObjects, build_sorter($objectsOrder));
		foreach ($coreObjects as $coreObject)
		{
			$resultObj = new ESearchResult();
			$resultObj->setObject($coreObject);
			$itemsData = array();
			foreach ($objectsData[$coreObject->getId()] as $innerHitKey => $values)
			{
				$itemsDataResult = self::getItemsDataResult($innerHitKey, $values);
				if($itemsDataResult)
					$itemsData[] = $itemsDataResult;
			}

			$resultObj->setItemsData($itemsData);
			if(array_key_exists($coreObject->getId(), $objectHighlight))
				$resultObj->setHighlight($objectHighlight[$coreObject->getId()]);

			$resultsObjects[] = $resultObj;
		}

		return $resultsObjects;
	}

	private static function getItemDataFromInnerHits($elasticObject, &$itemData)
	{
		foreach ($elasticObject[self::INNER_HITS_KEY] as $innerHitsKey => $hits)
		{
			list($objectType, $queryName, $queryIndex) = self::getDataFromInnerHitsKey($innerHitsKey);

			$itemData[$innerHitsKey] = array();
			$itemData[$innerHitsKey][self::TOTAL_COUNT_KEY] = self::getInnerHitsTotalCountForObject($hits, $objectType);
			foreach ($hits[self::HITS_KEY][self::HITS_KEY] as $itemResult)
			{
				$currItemData = KalturaPluginManager::loadObject('ESearchItemData', $objectType);
				if ($currItemData)
				{
					if(array_key_exists(self::HIGHLIGHT_KEY, $itemResult))
						$itemResult[self::HIGHLIGHT_KEY] = self::elasticHighlightToCoreHighlight($itemResult[self::HIGHLIGHT_KEY]);

					$currItemData->loadFromElasticHits($itemResult);
					$itemData[$innerHitsKey][self::ITEMS_KEY][] = $currItemData;
				}
			}
		}
	}

	private static function getDataFromInnerHitsKey($innerHitsKey)
	{
		$queryNames = explode(ESearchNestedObjectItem::QUERY_NAME_DELIMITER, $innerHitsKey);
		$objectType = $queryNames[0];
		$queryName = null;
		if ($queryNames[1] != ESearchNestedObjectItem::DEFAULT_GROUP_NAME)
			$queryName = $queryNames[1];

		$queryIndex =  $queryNames[2];

		if(isset(self::$innerHitsObjectType[$objectType]))
			return array(self::$innerHitsObjectType[$objectType], $queryName, $queryIndex);

		KalturaLog::err('Unsupported inner object key in elastic results['.$innerHitsKey.']');
		return array($objectType, $queryName, $queryIndex);
	}

	private static function getItemsDataResult($innerHitsKey, $values)
	{
		if(!isset($values[self::ITEMS_KEY]))
			return null;

		list($objectType, $queryName, $queryIndex) = self::getDataFromInnerHitsKey($innerHitsKey);

		$result = new ESearchItemsDataResult();
		$result->setTotalCount($values[self::TOTAL_COUNT_KEY]);
		$result->setItems($values[self::ITEMS_KEY]);
		$result->setItemsType($objectType);

		return $result;
	}

	private static function getInnerHitsTotalCountForObject($objectResult, $objectType)
	{
		switch ($objectType)
		{
			case ESearchItemDataType::CAPTION:
			case ESearchItemDataType::METADATA:
			case ESearchItemDataType::CUE_POINTS:
				return $objectResult[self::HITS_KEY][self::TOTAL_KEY];
			default:
			{
				KalturaLog::err('Unsupported inner object type in elastic results['.$objectType.']');
				return 0;
			}
		}
	}

	private static function elasticHighlightToCoreHighlight($eHighlight)
	{
		if(isset($eHighlight))
		{
			$result = array();
			foreach ($eHighlight as $key => $value)
			{
				$resultType = new ESearchHighlight();
				$resultType->setFieldName($key);
				$resultType->setHits($value);
				$result[] = $resultType;
			}

			return $result;
		}

		return null;
	}
}
