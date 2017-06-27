<?php

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

	public static function getAallowedSearchTypesForField()
	{
		return array_merge(self::$allowed_search_types_for_field, parent::getAallowedSearchTypesForField());
	}

	public function getQueryVerbs()
	{
		$allowedSearchTypes = self::getAallowedSearchTypesForField();
		if (!in_array($this->getItemType() ,$allowedSearchTypes[$this->getFieldName()]))
			throw new kCoreException('Type of search ['.$this->getItemType().'] not allowed on specific field ['. $this->getFieldName().']', kCoreException::INTERNAL_SERVER_ERROR);
		return parent::getQueryVerbs();
	}

	public static function createSearchQuery(array $eSearchItemsArr, $boolOperator, $additionalParams = null)
	{
		$cuePointQuery['nested']['path'] = 'cue_points';
		$cuePointQuery['nested']['inner_hits'] = array('size' => 10, '_source' => true);
		$allowedSearchTypes = ESearchCuePointItem::getAallowedSearchTypesForField();
		foreach ($eSearchItemsArr as $cuePointSearchItem)
		{
			/**
			 * @var ESearchEntryItem $cuePointSearchItem
			 */
			$queryVerbs = $cuePointSearchItem->getQueryVerbs();
			$searchTerm = $cuePointSearchItem->getSearchTerm();
			if (!empty($searchTerm))
			{
				if ($cuePointSearchItem->getItemType() == ESearchItemType::PARTIAL)
				{
					$cuePointQuery['nested']['query']['bool'][$queryVerbs[0]]['multi_match']['query'] = strtolower($cuePointSearchItem->getSearchTerm());
					$cuePointQuery['nested']['query']['bool'][$queryVerbs[0]]['multi_match']['fields'] = array($cuePointSearchItem->getFieldName() . "^2", $cuePointSearchItem->getFieldName() . "raw^2", $cuePointSearchItem->getFieldName() . "trigrams");
					$queryOut[$queryVerbs[0]]['multi_match']['type'] = 'most_fields';
				} else
				{
					$fieldNameAddition = '';
					if ($cuePointSearchItem->getItemType() == ESearchItemType::EXACT_MATCH && in_array(ESearchItemType::PARTIAL, $allowedSearchTypes[$cuePointSearchItem->getFieldName()]))
					{
						$fieldNameAddition = '.raw';
					}
					$cuePointQuery['nested']['query']['bool'][$queryVerbs[0]][$queryVerbs[1]] = array($cuePointSearchItem->getFieldName() . $fieldNameAddition => strtolower($cuePointSearchItem->getSearchTerm()));
				}
			}
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


}