<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model
 */

class ESearchHighlightHelper
{
	public static function removeDuplicateHits(&$eHighlight)
	{
		uksort($eHighlight, 'ESearchHighlightHelper::cmpHighlightFieldsByPriority');
		$uniqueValues = array();
		foreach ($eHighlight as $key => $hits)
		{
			$filteredHits = array();
			foreach ($hits as $hit)
			{
				$uniqueValue =  strip_tags($hit);
				$uniqueValue = trim($uniqueValue);
				if(!in_array ($uniqueValue , $uniqueValues))
				{
					$uniqueValues[] = $uniqueValue;
					$filteredHits[] = $hit;
				}
			}

			if(empty($filteredHits))
				unset($eHighlight[$key]);
			else
				$eHighlight[$key] = $filteredHits;
		}
	}

	private static function cmpHighlightFieldsByPriority($field1, $field2)
	{
		return (ESearchHighlightHelper::getFieldTypePriority($field1) - ESearchHighlightHelper::getFieldTypePriority($field2));
	}

	private static function getFieldTypePriority($fieldName)
	{
		$priority =  10;
		if(kString::endsWith($fieldName,"raw"))
			$priority =  1;
		else if(kString::endsWith($fieldName,"ngrams"))
			$priority =  20;

		return $priority;
	}
}