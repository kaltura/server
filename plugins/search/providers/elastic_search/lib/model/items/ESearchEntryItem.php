<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
class ESearchEntryItem extends ESearchItem
{

	/**
	 * @var ESearchEntryFieldName
	 */
	protected $fieldName;

	/**
	 * @var string
	 */
	protected $searchTerm;

	private static $allowed_search_types_for_field = array(
		'_id' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'Unified'),
		'name' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN, 'Unified'),
		'description' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN, 'Unified'),
		'tags' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN, 'Unified'),
		'category_ids' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN),
		'puser_id' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN, 'Unified'),
		'creator_kuser_id' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN, 'Unified'),
		'start_time' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN, 'ESearchItemType::RANGE'),
		'end_time' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN, 'ESearchItemType::RANGE'),
		'reference_id' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN, 'Unified'),
		'conversion_profile_id' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN),
		'redirect_entry_id' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'Unified'),
		'entitled_kusers_edit' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN, 'Unified'),
		'entitled_kusers_publish' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN, 'Unified'),
		'template_entry_id' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'Unified'),
		'display_in_search' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN),
		'parent_id' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN, 'Unified'),
		'media_type' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN),
		'source_type' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN),
		'recorded_entry_id' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'Unified'),
		'push_publish' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN),
		'length_in_msecs' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN, 'ESearchItemType::RANGE'),
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
		return 'entry';
	}

	public static function getAllowedSearchTypesForField()
	{
		return array_merge(self::$allowed_search_types_for_field, parent::getAllowedSearchTypesForField());
	}

	public function getQueryVerbs()
	{
		$allowedSearchTypes = self::getAllowedSearchTypesForField();
		if (!in_array($this->getItemType(),  $allowedSearchTypes[$this->getFieldName()]))
			throw new kCoreException('Type of search ['.$this->getItemType().'] not allowed on specific field ['. $this->getFieldName().']', kCoreException::INTERNAL_SERVER_ERROR);
		return parent::getQueryVerbs();
	}

	public static function createSearchQuery(array $eSearchItemsArr, $boolOperator, $eSearchOperatorType = null)
	{
		$entryQuery = array();
		$allowedSearchTypes = ESearchEntryItem::getAllowedSearchTypesForField();
		foreach ($eSearchItemsArr as $entrySearchItem)
		{
			self::getSingleItemSearchQuery($entrySearchItem, $entryQuery, $allowedSearchTypes);
			//$entryQuery[] = $itemQuery;
		}
		return $entryQuery;
	}

	private static function getSingleItemSearchQuery($entrySearchItem, &$entryQuery, $allowedSearchTypes)
	{
		/**
		 * @var ESearchEntryItem $entrySearchItem
		 */
		$queryVerbs = $entrySearchItem->getQueryVerbs();
		$searchTerm = $entrySearchItem->getSearchTerm();
		if (!empty($searchTerm))
		{
			switch ($entrySearchItem->getItemType())
			{
				case ESearchItemType::EXACT_MATCH:
					$entryQuery[] = kESearchQueryManager::getExactMatchQuery($entrySearchItem, $entrySearchItem->getFieldName(), $allowedSearchTypes);
					break;
				case ESearchItemType::PARTIAL:
					$entryQuery[] = kESearchQueryManager::getMultiMatchQuery($entrySearchItem, $entrySearchItem->getFieldName(), false);
					break;
				case ESearchItemType::STARTS_WITH:
					$entryQuery[] = kESearchQueryManager::getPrefixQuery($entrySearchItem, $entrySearchItem->getFieldName(), $allowedSearchTypes);
					break;
				case ESearchItemType::DOESNT_CONTAIN:
					$entryQuery[] = kESearchQueryManager::getDoesntContainQuery($entrySearchItem, $entrySearchItem->getFieldName(), $allowedSearchTypes);
			}
		}
//		if (in_array('Range', $allowedSearchTypes[$entrySearchItem->getFieldName()]))
//		{
//			foreach ($entrySearchItem->getRanges() as $range)
//			{
//				$queryOut[$queryVerbs[0]]['range'] = array($entrySearchItem->getFieldName() => array('gte' => $range[0], 'lte' => $range[1]));
//			}
//		}
		
		//return $queryOut;
	}

}
