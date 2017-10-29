<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
class ESearchCuePointItem extends ESearchItem
{
	const DEFAULT_INNER_HITS_SIZE = 10;

	/**
	 * @var ESearchCuePointFieldName
	 */
	protected $fieldName;

	/**
	 * @var string
	 */
	protected $searchTerm;

	private static $allowed_search_types_for_field = array(
		'cue_points.cue_point_type' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH),
		'cue_points.cue_point_id' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, ESearchUnifiedItem::UNIFIED),
		'cue_points.cue_point_name' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::EXISTS"=> ESearchItemType::EXISTS, ESearchUnifiedItem::UNIFIED),
		'cue_points.cue_point_text' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::EXISTS"=> ESearchItemType::EXISTS, ESearchUnifiedItem::UNIFIED),
		'cue_points.cue_point_tags' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::EXISTS"=> ESearchItemType::EXISTS, ESearchUnifiedItem::UNIFIED),
		'cue_points.cue_point_start_time' => array("ESearchItemType::EXISTS"=> ESearchItemType::EXISTS, 'ESearchItemType::RANGE'=>ESearchItemType::RANGE),
		'cue_points.cue_point_end_time' => array("ESearchItemType::EXISTS"=> ESearchItemType::EXISTS, 'ESearchItemType::RANGE'=>ESearchItemType::RANGE),
		'cue_points.cue_point_sub_type' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, "ESearchItemType::EXISTS"=> ESearchItemType::EXISTS),
		'cue_points.cue_point_question' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::EXISTS"=> ESearchItemType::EXISTS, ESearchUnifiedItem::UNIFIED),
		'cue_points.cue_point_answers' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::EXISTS"=> ESearchItemType::EXISTS, ESearchUnifiedItem::UNIFIED),
		'cue_points.cue_point_hint' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::EXISTS"=> ESearchItemType::EXISTS, ESearchUnifiedItem::UNIFIED),
		'cue_points.cue_point_explanation' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::EXISTS"=> ESearchItemType::EXISTS, ESearchUnifiedItem::UNIFIED),
	);

	/**
	 * @return ESearchEntryFieldName
	 */
	public function getFieldName()
	{
		return $this->fieldName;
	}

	/**
	 * @param ESearchEntryFieldName $fieldName
	 */
	public function setFieldName($fieldName)
	{
		$this->fieldName = $fieldName;
	}

	/**
	 * @return string
	 */
	public function getSearchTerm()
	{
		return $this->searchTerm;
	}

	/**
	 * @param string $searchTerm
	 */
	public function setSearchTerm($searchTerm)
	{
		$this->searchTerm = $searchTerm;
	}

	public function getType()
	{
		return 'cuepoint';
	}

	public static function getAllowedSearchTypesForField()
	{
		return array_merge(self::$allowed_search_types_for_field, parent::getAllowedSearchTypesForField());
	}

	public static function createSearchQuery($eSearchItemsArr, $boolOperator, $eSearchOperatorType = null)
	{
		$innerHitsConfig = kConf::get('innerHits', 'elastic');
		$innerHitsSize = isset($innerHitsConfig['cuePointsInnerHitsSize']) ? $innerHitsConfig['cuePointsInnerHitsSize'] : self::DEFAULT_INNER_HITS_SIZE;
		$cuePointQuery['nested']['path'] = 'cue_points';
		$cuePointQuery['nested']['inner_hits'] = array('size' => $innerHitsSize, '_source' => true);
		$allowedSearchTypes = ESearchCuePointItem::getAllowedSearchTypesForField();
		foreach ($eSearchItemsArr as $cuePointSearchItem)
		{
			self::createSingleItemSearchQuery($cuePointSearchItem, $boolOperator, $cuePointQuery, $allowedSearchTypes);
		}
		return array($cuePointQuery);
	}

	public static function createSingleItemSearchQuery($cuePointSearchItem, $boolOperator, &$cuePointQuery, $allowedSearchTypes)
	{
		$cuePointSearchItem->validateItemInput();
		switch ($cuePointSearchItem->getItemType())
		{
			case ESearchItemType::EXACT_MATCH:
				$cuePointQuery['nested']['query']['bool'][$boolOperator][] =
					kESearchQueryManager::getExactMatchQuery($cuePointSearchItem, $cuePointSearchItem->getFieldName(), $allowedSearchTypes);
				break;
			case ESearchItemType::PARTIAL:
				$cuePointQuery['nested']['query']['bool'][$boolOperator][] =
					kESearchQueryManager::getMultiMatchQuery($cuePointSearchItem, $cuePointSearchItem->getFieldName(), false);
				break;
			case ESearchItemType::STARTS_WITH:
				$cuePointQuery['nested']['query']['bool'][$boolOperator][] =
					kESearchQueryManager::getPrefixQuery($cuePointSearchItem, $cuePointSearchItem->getFieldName(), $allowedSearchTypes);
				break;
			case ESearchItemType::EXISTS:
				$cuePointQuery['nested']['query']['bool'][$boolOperator][] =
					kESearchQueryManager::getExistsQuery($cuePointSearchItem, $cuePointSearchItem->getFieldName(), $allowedSearchTypes);
				break;
			case ESearchItemType::RANGE:
				$cuePointQuery['nested']['query']['bool'][$boolOperator][] =
					kESearchQueryManager::getRangeQuery($cuePointSearchItem, $cuePointSearchItem->getFieldName(), $allowedSearchTypes);
				break;
			default:
				KalturaLog::log("Undefined item type[".$cuePointSearchItem->getItemType()."]");
		}

		if($boolOperator == 'should')
			$cuePointQuery['nested']['query']['bool']['minimum_should_match'] = 1;
	}

	protected function validateItemInput()
	{
		$allowedSearchTypes = self::getAllowedSearchTypesForField();
		$this->validateAllowedSearchTypes($allowedSearchTypes, $this->getFieldName());
		$this->validateEmptySearchTerm($this->getFieldName(), $this->getSearchTerm());
	}
}
