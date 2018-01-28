<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model
 */

class ESearchHighlightHelper
{
	public static function cmpHighlightFieldsByPriority($field1, $field2)
	{
		return (ESearchHighlightHelper::getFieldTypePriority($field1) - ESearchHighlightHelper::getFieldTypePriority($field2));
	}

	private static function getFieldTypePriority($fieldName)
	{
		$priority =  10;
		if(kString::endsWith($fieldName, kESearchQueryManager::RAW_FIELD_SUFFIX))
			$priority =  1;
		else if(kString::endsWith($fieldName, kESearchQueryManager::NGRAMS_FIELD_SUFFIX))
			$priority =  20;

		return $priority;
	}
}