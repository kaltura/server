<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
class ESearchAdvancedSearchItem extends ESearchEntryItem
{
	protected $value;

	protected $fieldName;

	protected $itemType;

	/**
	 * @return kIntegerValue
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @param KalturaESearchItemType
	 */
	public function setItemType($itemType)
	{
		$this->itemType = $itemType;
	}


	/**
	 * @param string $value
	 */
	public function setValue(string $value)
	{
		$this->value = $value;
	}

	/**
	 * @param string $field
	 */
	public function setFieldName($fieldName)
	{
		$this->fieldName = $fieldName;
	}


	/**
	 * @return string $field
	 */
	public function getFieldName()
	{
		return $this->fieldName;
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


	public function handleComparisonType($advancedSearchFilterItem)
	{
		$range = new ESearchRange();
		$value = $advancedSearchFilterItem->getValue();
		switch ($advancedSearchFilterItem->getComparison()) {
			case searchConditionComparison::LESS_THAN:
				$range->setLessThan($value);
				break;
			case searchConditionComparison::LESS_THAN_OR_EQUAL:
				$range->setLessThanOrEqual($value);
				break;
			case searchConditionComparison::GREATER_THAN:
				$range->setGreaterThan($value);
				break;
			case searchConditionComparison::GREATER_THAN_OR_EQUAL:
				$range->setGreaterThanOrEqual($value);
				break;
			default:
				$data = array();
				$data['itemType'] = $advancedSearchFilterItem->getComparison();
				$data['fieldName'] = $this->getFieldName();

				throw new kESearchException(
					'Type of search [' . $data['itemType'] . '] not allowed on specific field [' . $data['fieldName'] . ']',
					kESearchException::SEARCH_TYPE_NOT_ALLOWED_ON_FIELD,
					$data
				);
		}

		$this->setRange($range);

	}
}
