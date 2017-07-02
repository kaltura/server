<?php

class ESearchCategoryItem extends ESearchItem
{

	/**
	 * @var ESearchCategoryFieldName
	 */
	protected $fieldName;

	/**
	 * @var string
	 */
	protected $searchTerm;

	private static $allowed_search_types_for_field = array(
		'name' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, 'ESearchItemType::DOESNT_CONTAIN'=> ESearchItemType::DOESNT_CONTAIN),
		'full_name' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, 'ESearchItemType::DOESNT_CONTAIN'=> ESearchItemType::DOESNT_CONTAIN),
		'description' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, 'ESearchItemType::DOESNT_CONTAIN'=> ESearchItemType::DOESNT_CONTAIN),

		'privacy' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH,'ESearchItemType::DOESNT_CONTAIN'=> ESearchItemType::DOESNT_CONTAIN),
		'privacy_context' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, 'ESearchItemType::DOESNT_CONTAIN'=> ESearchItemType::DOESNT_CONTAIN),
		'privacy_contexts' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, 'ESearchItemType::DOESNT_CONTAIN'=> ESearchItemType::DOESNT_CONTAIN),
		'kuser_ids' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, 'ESearchItemType::DOESNT_CONTAIN'=> ESearchItemType::DOESNT_CONTAIN),
		'parent_id' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH,'ESearchItemType::DOESNT_CONTAIN'=> ESearchItemType::DOESNT_CONTAIN),
		'depth' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, 'ESearchItemType::DOESNT_CONTAIN'=> ESearchItemType::DOESNT_CONTAIN),
		'full_ids' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, 'ESearchItemType::DOESNT_CONTAIN'=> ESearchItemType::DOESNT_CONTAIN),
		'tags' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, 'ESearchItemType::DOESNT_CONTAIN'=> ESearchItemType::DOESNT_CONTAIN),
		'display_in_search' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, 'ESearchItemType::DOESNT_CONTAIN'=> ESearchItemType::DOESNT_CONTAIN),
		'inheritance_type' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH,'ESearchItemType::DOESNT_CONTAIN'=> ESearchItemType::DOESNT_CONTAIN),
		'kuser_id' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH,'ESearchItemType::DOESNT_CONTAIN'=> ESearchItemType::DOESNT_CONTAIN),
		'reference_id' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, 'ESearchItemType::DOESNT_CONTAIN'=> ESearchItemType::DOESNT_CONTAIN),
		'inherited_parent_id' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, 'ESearchItemType::DOESNT_CONTAIN'=> ESearchItemType::DOESNT_CONTAIN),
		'moderation' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, 'ESearchItemType::DOESNT_CONTAIN'=> ESearchItemType::DOESNT_CONTAIN),
		'contribution_policy' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, 'ESearchItemType::DOESNT_CONTAIN'=> ESearchItemType::DOESNT_CONTAIN),
		'metadata' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::PARTIAL'=> ESearchItemType::PARTIAL, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH, 'ESearchItemType::DOESNT_CONTAIN'=> ESearchItemType::DOESNT_CONTAIN),

		'entries_count' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::RANGE' => 'Range'),
		'direct_entries_count' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::RANGE' => 'Range'),
		'direct_sub_categories_count' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::RANGE' => 'Range'),
		'members_count' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::RANGE' => 'Range'),
		'pending_members_count' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::RANGE' => 'Range'),
		'pending_entries_count' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::RANGE' => 'Range'),

		'created_at' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH,'ESearchItemType::DOESNT_CONTAIN'=> ESearchItemType::DOESNT_CONTAIN),
		'updated_at' => array('ESearchItemType::EXACT_MATCH'=> ESearchItemType::EXACT_MATCH, 'ESearchItemType::STARTS_WITH'=> ESearchItemType::STARTS_WITH,'ESearchItemType::DOESNT_CONTAIN'=> ESearchItemType::DOESNT_CONTAIN),
	);

	/**
	 * @return ESearchCategoryFieldName
	 */
	public function getFieldName()
	{
		return $this->fieldName;
	}

	/**
	 * @param ESearchCategoryFieldName $fieldName
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
		return 'category';
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

	public static function createSearchQuery(array $eSearchItemsArr, $boolOperator, $additionalParams = null)
	{
		$queryOut = array();

		$allowedSearchTypes = ESearchCategoryItem::getAllowedSearchTypesForField();
		foreach ($eSearchItemsArr as $categorySearchItem)
		{
			/**
			 * @var ESearchCategoryItem $categorySearchItem
			 */
			$queryVerbs = $categorySearchItem->getQueryVerbs();
			$searchTerm = $categorySearchItem->getSearchTerm();
			if (!empty($searchTerm))
			{
				if ($categorySearchItem->getItemType() == ESearchItemType::PARTIAL)
				{
					$queryOut[$queryVerbs[0]]['multi_match']['query'] = strtolower($categorySearchItem->getSearchTerm());
					$queryOut[$queryVerbs[0]]['multi_match']['fields'] = array($categorySearchItem->getFieldName() . "^2", $categorySearchItem->getFieldName() . ".raw^2", $categorySearchItem->getFieldName() . ".trigrams");
					$queryOut[$queryVerbs[0]]['multi_match']['type'] = 'most_fields';
				} else
				{
					$fieldNameAddition = '';
					if ($categorySearchItem->getItemType() == ESearchItemType::EXACT_MATCH && in_array(ESearchItemType::PARTIAL, $allowedSearchTypes[$categorySearchItem->getFieldName()]))
						$fieldNameAddition = '.raw';
					$queryOut[$queryVerbs[1]] = array($categorySearchItem->getFieldName().$fieldNameAddition => strtolower($searchTerm));
				}
			}
			if (in_array('Range', $allowedSearchTypes[$categorySearchItem->getFieldName()]))
			{
				foreach ($categorySearchItem->getRanges() as $range)
				{
					$queryOut[$queryVerbs[0]]['range'] = array($categorySearchItem->getFieldName() => array('gte' => $range[0], 'lte' => $range[1]));
				}
			}
		}




		return $queryOut;
	}
}