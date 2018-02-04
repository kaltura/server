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
	 * @var bool
	 */
	protected $addHighlight;

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

	/**
	 * @return boolean
	 */
	public function getAddHighlight()
	{
		return $this->addHighlight;
	}

	/**
	 * @param boolean $addHighlight
	 */
	public function setAddHighlight($addHighlight)
	{
		$this->addHighlight = $addHighlight;
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
		if ($this->isSearchTermEmpty($searchTerm) && !in_array($this->getItemType(), array(ESearchItemType::RANGE, ESearchItemType::EXISTS)))
		{
			$data = array();
			$data['itemType'] = $this->getItemType();
			$data['fieldName'] = $fieldName;
			throw new kESearchException('Empty search term is not allowed on Field ['. $fieldName.'] and search type ['.$this->getItemType().']', kESearchException::EMPTY_SEARCH_TERM_NOT_ALLOWED, $data);
		}
	}

	private function isSearchTermEmpty($searchTerm)
	{
		if(is_null($searchTerm) || $searchTerm == '')
			return true;

		return false;
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

	protected function shouldTryToOptimizeToFilterContext(&$subQuery, $fieldName)
	{
		if($subQuery && $subQuery instanceof kESearchBaseFieldQuery && self::getFieldBoostFactor($fieldName) == 1)
			return true;
		return false;
	}

}
