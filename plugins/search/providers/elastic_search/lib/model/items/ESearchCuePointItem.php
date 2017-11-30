<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
class ESearchCuePointItem extends ESearchNestedObjectItem
{

	const INNER_HITS_CONFIG_KEY = 'cuePointsInnerHitsSize';
	const NESTED_QUERY_PATH = 'cue_points';
	const HIGHLIGHT_CONFIG_KEY = 'cuepointMaxNumberOfFragments';

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

	protected static $field_boost_values = array(
		'cue_points.cue_point_id' => 50,
		'cue_points.cue_point_name' => 50,
		'cue_points.cue_point_text' => 50,
		'cue_points.cue_point_tags' => 50,
		'cue_points.cue_point_question' => 50,
		'cue_points.cue_point_answers' => 50,
	);

	private static $multiLanguageFields = array();

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

	public static function getAllowedSearchTypesForField()
	{
		return array_merge(self::$allowed_search_types_for_field, parent::getAllowedSearchTypesForField());
	}

	/**
	 * @param $eSearchItemsArr
	 * @param $boolOperator
	 * @param ESearchQueryAttributes $queryAttributes
	 * @param null $eSearchOperatorType
	 * @return array
	 */
	public static function createSearchQuery($eSearchItemsArr, $boolOperator, &$queryAttributes, $eSearchOperatorType = null)
	{
		return self::createNestedQueryForItems($eSearchItemsArr, $boolOperator, $queryAttributes);
	}

	public static function createSingleItemSearchQuery($cuePointSearchItem, $boolOperator, &$cuePointQuery, $allowedSearchTypes, &$queryAttributes)
	{
		$cuePointSearchItem->validateItemInput();
		switch ($cuePointSearchItem->getItemType())
		{
			case ESearchItemType::EXACT_MATCH:
				$cuePointQuery['nested']['query']['bool'][$boolOperator][] =
					kESearchQueryManager::getExactMatchQuery($cuePointSearchItem, $cuePointSearchItem->getFieldName(), $allowedSearchTypes, $queryAttributes);
				break;
			case ESearchItemType::PARTIAL:
				$cuePointQuery['nested']['query']['bool'][$boolOperator][] =
					kESearchQueryManager::getMultiMatchQuery($cuePointSearchItem, $cuePointSearchItem->getFieldName(), $queryAttributes);
				break;
			case ESearchItemType::STARTS_WITH:
				$cuePointQuery['nested']['query']['bool'][$boolOperator][] =
					kESearchQueryManager::getPrefixQuery($cuePointSearchItem, $cuePointSearchItem->getFieldName(), $allowedSearchTypes, $queryAttributes);
				break;
			case ESearchItemType::EXISTS:
				$cuePointQuery['nested']['query']['bool'][$boolOperator][] =
					kESearchQueryManager::getExistsQuery($cuePointSearchItem, $cuePointSearchItem->getFieldName(), $allowedSearchTypes, $queryAttributes);
				break;
			case ESearchItemType::RANGE:
				$cuePointQuery['nested']['query']['bool'][$boolOperator][] =
					kESearchQueryManager::getRangeQuery($cuePointSearchItem, $cuePointSearchItem->getFieldName(), $allowedSearchTypes, $queryAttributes);
				break;
			default:
				KalturaLog::log("Undefined item type[".$cuePointSearchItem->getItemType()."]");
		}

		if($boolOperator == 'should')
			$cuePointQuery['nested']['query']['bool']['minimum_should_match'] = 1;
	}

	public function shouldAddLanguageSearch()
	{
		if(in_array($this->getFieldName(), self::$multiLanguageFields))
			return true;

		return false;
	}

	public function getItemMappingFieldsDelimiter()
	{

	}

}
