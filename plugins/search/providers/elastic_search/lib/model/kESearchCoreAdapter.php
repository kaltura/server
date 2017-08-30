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

	private static $innerHitsObjectType = array(
		'caption_assets.lines' => ESearchItemDataType::CAPTION,
		'metadata' => ESearchItemDataType::METADATA,
		'cue_points' => ESearchItemDataType::CUE_POINTS
	);

	public static function transformElasticToCoreObject($elasticResults, $peerName)
	{
		list($objectData, $objectOrder, $objectCount) = self::getElasticResultAsArray($elasticResults);
		$objects = $peerName::retrieveByPKs(array_keys($objectData));
		$coreResults = self::getCoreESearchResults($objects, $objectData, $objectOrder);
		return array($coreResults, $objectCount);
	}

	private static function getElasticResultAsArray($elasticResults)
	{
		$objectData = array();
		$objectOrder = array();
		$objectCount = 0;
		foreach ($elasticResults[self::HITS_KEY][self::HITS_KEY] as $key => $elasticObject)
		{
			$itemData = array();
			if (isset($elasticObject[self::INNER_HITS_KEY]))
				self::getItemDataFromInnerHits($elasticObject, $itemData);
			
			$objectData[$elasticObject[self::ID_KEY]] = $itemData;
			$objectOrder[$elasticObject[self::ID_KEY]] = $key;
		}
		
		if(isset($elasticResults[self::HITS_KEY][self::TOTAL_KEY]))
			$objectCount = $elasticResults[self::HITS_KEY][self::TOTAL_KEY];
		
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

	private static function getItemDataFromInnerHits($elasticObject, &$itemData)
	{
		foreach ($elasticObject[self::INNER_HITS_KEY] as $innerHitsKey => $hits)
		{
			$objectType = self::getInnerHitsObjectType($innerHitsKey);
			$itemData[$objectType] = array();
			$itemData[$objectType]['totalCount'] = self::getInnerHitsTotalCountForObject($hits, $objectType);
			foreach ($hits[self::HITS_KEY][self::HITS_KEY] as $itemResult)
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
		if(isset(self::$innerHitsObjectType[$innerHitsKey]))
			return self::$innerHitsObjectType[$innerHitsKey];

		KalturaLog::err('Unsupported inner object key in elastic results['.$innerHitsKey.']');
		return $innerHitsKey;
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

}
