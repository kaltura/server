<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
class ESearchCuePointItem extends ESearchItem
{

	/**
	 * @var ESearchCuePointFieldName
	 */
	protected $fieldName;

	/**
	 * @var string
	 */
	protected $searchTerm;

	private static $allowed_search_types_for_field = array(
		'cue_points.cue_point_type' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN),
		'cue_points.cue_point_id' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN, 'Unified'),
		'cue_points.cue_point_name' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN, 'Unified'),
		'cue_points.cue_point_text' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN, 'Unified'),
		'cue_points.cue_point_tags' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN, 'Unified'),
		'cue_points.cue_point_start_time' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN),
		'cue_points.cue_point_end_time' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN),
		'cue_points.cue_point_sub_type' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN),
		'cue_points.cue_point_answers' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN, 'Unified'),
		'cue_points.cue_point_hint' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN, 'Unified'),
		'cue_points.cue_point_explanation' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN, 'Unified'),
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

	public function getQueryVerbs()
	{
		$allowedSearchTypes = self::getAllowedSearchTypesForField();
		if (!in_array($this->getItemType() ,$allowedSearchTypes[$this->getFieldName()]))
			throw new kCoreException('Type of search ['.$this->getItemType().'] not allowed on specific field ['. $this->getFieldName().']', kCoreException::INTERNAL_SERVER_ERROR);
		return parent::getQueryVerbs();
	}

	public static function createSearchQuery(array $eSearchItemsArr, $boolOperator, $eSearchOperatorType = null)
	{
		$cuePointQuery['nested']['path'] = 'cue_points';
		$cuePointQuery['nested']['inner_hits'] = array('size' => 10, '_source' => true);
		$allowedSearchTypes = ESearchCuePointItem::getAllowedSearchTypesForField();
		foreach ($eSearchItemsArr as $cuePointSearchItem)
		{
			/**
			 * @var ESearchEntryItem $cuePointSearchItem
			 */
			$queryVerbs = $cuePointSearchItem->getQueryVerbs();
			self::createSingleItemSearchQuery($cuePointSearchItem, $boolOperator, $cuePointQuery, $allowedSearchTypes);
			if (in_array('Range', $allowedSearchTypes[$cuePointSearchItem->getFieldName()]))
			{
				foreach ($cuePointSearchItem->getRanges() as $range)
				{
					$queryOut[$queryVerbs[0]]['range'] = array($cuePointSearchItem->getFieldName() => array('gte' => $range[0], 'lte' => $range[1]));
				}
			}
		}
		return $cuePointQuery;
	}

	private static function createSingleItemSearchQuery($cuePointSearchItem, $boolOperator, &$cuePointQuery, $allowedSearchTypes)
	{
		switch ($cuePointSearchItem->getItemType())
		{
			case ESearchItemType::EXACT_MATCH:
				$cuePointQuery['nested']['query']['bool'][$boolOperator][] =
					kESearchQueryManager::getExactMatchQuery($cuePointSearchItem, $cuePointSearchItem->getFieldName(), $allowedSearchTypes);
				break;
			case ESearchItemType::PARTIAL:
				$cuePointQuery['nested']['query']['bool'][$boolOperator][] =
					kESearchQueryManager::getMultiMatchQuery($cuePointSearchItem->getSearchTerm(), $cuePointSearchItem->getFieldName(), false);
				break;
			case ESearchItemType::STARTS_WITH:
				$cuePointQuery['nested']['query']['bool'][$boolOperator][] =
					kESearchQueryManager::getPrefixQuery($cuePointSearchItem, $cuePointSearchItem->getFieldName(), $allowedSearchTypes);
				break;
			case ESearchItemType::DOESNT_CONTAIN:
				$cuePointQuery['nested']['query']['bool']['must_not'][] =
					kESearchQueryManager::getDoesntContainQuery($cuePointSearchItem, $cuePointSearchItem->getFieldName(), $allowedSearchTypes);
		}
	}


}