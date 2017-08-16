<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
abstract class ESearchItem extends BaseObject
{

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
			throw new kCoreException('Type of search ['.$this->getItemType().'] not allowed on specific field ['. $fieldName.']', kCoreException::INTERNAL_SERVER_ERROR);
	}

	protected function validateEmptySearchTerm($fieldName, $searchTerm)
	{
		if (empty($searchTerm) && !in_array($this->getItemType(), array(ESearchItemType::RANGE)))
			throw new kCoreException('Type of search ['.$this->getItemType().'] not allowed on empty search term on field ['. $fieldName.']', kCoreException::INTERNAL_SERVER_ERROR);
	}

	abstract public function getType();

	public static function getAllowedSearchTypesForField()
	{
		return array();
	}

	abstract public static function createSearchQuery(array $eSearchItemsArr, $boolOperator, $eSearchOperatorType = null);

}
