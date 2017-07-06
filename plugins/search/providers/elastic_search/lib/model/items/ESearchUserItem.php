<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
class ESearchUserItem extends ESearchItem
{

	/**
	 * @var ESearchUserFieldName
	 */
	protected $fieldName;

	/**
	 * @var string
	 */
	protected $searchTerm;

	private static $allowed_search_types_for_field = array(
		'last_name' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN),
		'first_name' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN),
		'screen_name' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN),
		'email' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN),
		'tags' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN),
		'group_ids' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN),
		'updated_at' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::RANGE'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN, 'ESearchItemType::RANGE'),
		'created_at' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN, 'ESearchItemType::RANGE'),
		'type' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN),
		'status' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, "ESearchItemType::DOESNT_CONTAIN"=> ESearchItemType::DOESNT_CONTAIN),
	);

	/**
	 * @return ESearchUserFieldName
	 */
	public function getFieldName()
	{
		return $this->fieldName;
	}

	/**
	 * @param ESearchUserFieldName $fieldName
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
		return 'user';
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
		$userQuery = array();
		$allowedSearchTypes = ESearchEntryItem::getAllowedSearchTypesForField();
		foreach ($eSearchItemsArr as $userSearchItem)
		{
			self::getSingleItemSearchQuery($userSearchItem, $userQuery, $allowedSearchTypes);
			//$entryQuery[] = $itemQuery;
		}
		return $userQuery;
	}

	private static function getSingleItemSearchQuery($userSearchItem, &$userQuery, $allowedSearchTypes)
	{
		/**
		 * @var ESearchUserItem $userSearchItem
		 */
		$queryVerbs = $userSearchItem->getQueryVerbs();
		$searchTerm = $userSearchItem->getSearchTerm();
		if (!empty($searchTerm))
		{
			switch ($userSearchItem->getItemType())
			{
				case ESearchItemType::EXACT_MATCH:
					$userQuery[] = kESearchQueryManager::getExactMatchQuery($userSearchItem, $userSearchItem->getFieldName(), $allowedSearchTypes);
					break;
				case ESearchItemType::PARTIAL:
					$userQuery[] = kESearchQueryManager::getMultiMatchQuery($userSearchItem, $userSearchItem->getFieldName(), false);
					break;
				case ESearchItemType::STARTS_WITH:
					$userQuery[] = kESearchQueryManager::getPrefixQuery($userSearchItem, $userSearchItem->getFieldName(), $allowedSearchTypes);
					break;
				case ESearchItemType::DOESNT_CONTAIN:
					$userQuery[] = kESearchQueryManager::getDoesntContainQuery($userSearchItem, $userSearchItem->getFieldName(), $allowedSearchTypes);
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