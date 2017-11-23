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
		'caption_assets.lines' => ESearchItemDataType::CAPTION,
		'metadata' => ESearchItemDataType::METADATA,
		'cue_points' => ESearchItemDataType::CUE_POINTS
	);

	public static function transformElasticToCoreObject($elasticResults, $peerName)
	{
		list($objectData, $objectOrder, $objectCount, $objectHighlight) = self::getElasticResultAsArray($elasticResults);
		$objects = $peerName::retrieveByPKsNoFilter(array_keys($objectData));
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
				$objectHighlight[$elasticObject[self::ID_KEY]] = self::pruneHighlight($elasticObject[self::HIGHLIGHT_KEY]);
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
			foreach ($objectsData[$coreObject->getId()] as $objectType => $values)
			{
				$itemsDataResult = self::getItemsDataResult($objectType, $values);
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
			$objectType = self::getInnerHitsObjectType($innerHitsKey);
			$itemData[$objectType] = array();
			$itemData[$objectType][self::TOTAL_COUNT_KEY] = self::getInnerHitsTotalCountForObject($hits, $objectType);
			foreach ($hits[self::HITS_KEY][self::HITS_KEY] as $itemResult)
			{
				$currItemData = KalturaPluginManager::loadObject('ESearchItemData', $objectType);
				if ($currItemData)
				{
					if(array_key_exists(self::HIGHLIGHT_KEY, $itemResult))
						$itemResult[self::HIGHLIGHT_KEY] = self::pruneHighlight($itemResult[self::HIGHLIGHT_KEY]);

					$currItemData->loadFromElasticHits($itemResult);
					$itemData[$objectType][self::ITEMS_KEY][] = $currItemData;
				}
			}
		}
	}

	private static function getInnerHitsObjectType($innerHitsKey)
	{
		if(isset(self::$innerHitsObjectType[$innerHitsKey]))
			return self::$innerHitsObjectType[$innerHitsKey];

		KalturaLog::err('Unsupported inner object key in elastic results['.$innerHitsKey.']');
		return $innerHitsKey;
	}

	private static function getItemsDataResult($objectType, $values)
	{
		if(!isset($values[self::ITEMS_KEY]))
			return null;
		
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

	private static function pruneHighlight($highlight)
	{
		if(isset($highlight))
		{
			$keys = array_keys($highlight);
			return $highlight[$keys[0]][0];
		}

		return null;
	}
}
