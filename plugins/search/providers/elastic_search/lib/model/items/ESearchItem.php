<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
abstract class ESearchItem extends BaseObject
{
	/**
	 * @var array
	 */
	protected static $field_boost_values = array();

	/**
	 * @var ESearchItemType
	 */
	protected $itemType;

	/**
	 * @var ESearchRange
	 */
	protected $range;

	/**
	 * @return ESearchItemType
	 */
	public function getItemType()
	{
		return $this->itemType;
	}

	/**
	 * @param ESearchItemType $itemType
	 */
	public function setItemType($itemType)
	{
		$this->itemType = $itemType;
	}

	/**
	 * @return ESearchRange
	 */
	public function getRange()
	{
		return $this->range;
	}

	/**
	 * @param ESearchRange $range
	 */
	public function setRange($range)
	{
		$this->range = $range;
	}

	protected function validateAllowedSearchTypes($allowedSearchTypes, $fieldName)
	{
		if (!in_array($this->getItemType(),  $allowedSearchTypes[$fieldName]))
		{
			$data = array();
			$data['itemType'] = $this->getItemType();
			$data['fieldName'] = $fieldName;
			throw new kESearchException('Type of search ['.$this->getItemType().'] not allowed on specific field ['. $fieldName.']', kESearchException::SEARCH_TYPE_NOT_ALLOWED_ON_FIELD, $data);
		}
	}

	protected function validateEmptySearchTerm($fieldName, $searchTerm)
	{
		if (empty($searchTerm) && !in_array($this->getItemType(), array(ESearchItemType::RANGE, ESearchItemType::EXISTS)))
		{
			$data = array();
			$data['itemType'] = $this->getItemType();
			$data['fieldName'] = $fieldName;
			throw new kESearchException('Empty search term is not allowed on Field ['. $fieldName.'] and search type ['.$this->getItemType().']', kESearchException::EMPTY_SEARCH_TERM_NOT_ALLOWED, $data);
		}
	}

	protected function validateItemInput()
	{
		$allowedSearchTypes = static::getAllowedSearchTypesForField();
		$this->validateAllowedSearchTypes($allowedSearchTypes, $this->getFieldName());
		$this->validateEmptySearchTerm($this->getFieldName(), $this->getSearchTerm());
	}

	public static function getAllowedSearchTypesForField()
	{
		return array();
	}

	abstract public static function createSearchQuery($eSearchItemsArr, $boolOperator, &$queryAttributes, $eSearchOperatorType = null);

	abstract public function shouldAddLanguageSearch();

	abstract public function getItemMappingFieldsDelimiter();

	/**
	 * @param $fieldName
	 * @return int
	 */
	public static function getFieldBoostFactor($fieldName)
	{
		$result = 1;
		if(array_key_exists($fieldName, static::$field_boost_values))
		{
			$result = static::$field_boost_values[$fieldName];
		}

		return $result;
	}

	protected static function initializeInnerHitsSize($queryAttributes)
	{
		$overrideInnerHitsSize = $queryAttributes->getOverrideInnerHitsSize();
		if($overrideInnerHitsSize)
			return $overrideInnerHitsSize;

		$innerHitsConfig = kConf::get('innerHits', 'elastic');
		$innerHitsConfigKey = static::INNER_HITS_CONFIG_KEY;
		$innerHitsSize = isset($innerHitsConfig[$innerHitsConfigKey]) ? $innerHitsConfig[$innerHitsConfigKey] : static::DEFAULT_INNER_HITS_SIZE;

		return $innerHitsSize;
	}

}
