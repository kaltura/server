<?php
/**
 * Unlike AdvancedSearchFilterComparableCondition, this class allows using a dynamic field name.
 * This class should not be exposed directly on the API.
 * Field names (attributes) should always be exposed using API enums.
 *
 * @package Core
 * @subpackage model.filters.advanced
 */ 
class AdvancedSearchFilterComparableAttributeCondition extends AdvancedSearchFilterComparableCondition
{
	protected function getFieldValue($field)
	{
		$fieldValue = parent::getFieldValue($field);
		if (!is_null($fieldValue))
			return $fieldValue;

		return $this->getField();
	}
}
