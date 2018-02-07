<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */
class ESearchQueryHighlightsAttributes
{
	const GLOBAL_SCOPE = "global";
	const INNER_SCOPE = "inner";
	const HIGHLIGHT_HIGHEST_PRIORITY = 1;
	const HIGHLIGHT_MEDIUM_PRIORITY = 10;
	const HIGHLIGHT_DEFUALT_PRIORITY = 50;
	const HIGHLIGHT_LOWEST_PRIORITY = 100;

	/**
	 * @var array
	 */
	private $fieldsToHighlight = array(self::GLOBAL_SCOPE => array(), self::INNER_SCOPE => array());

	/**
	 * @var array
	 */
	private $baseFieldNameMapping = array();

	/**
	 * @var string
	 */
	private $scope = self::GLOBAL_SCOPE;

	public function setScopeToInner()
	{
		$this->scope = self::INNER_SCOPE;
		$this->fieldsToHighlight[self::INNER_SCOPE] = array();
	}

	public function setScopeToGlobal()
	{
		$this->scope = self::GLOBAL_SCOPE;
	}

	/**
	 * @return array
	 */
	public function getFieldsToHighlight()
	{
		return $this->fieldsToHighlight[$this->scope];
	}

	/**
	 * @param string $baseFieldName
	 * @param string $field
	 */
	public function addFieldToHighlight($baseFieldName, $field)
	{
		if(!array_key_exists($field ,$this->fieldsToHighlight[$this->scope]))
		{
			$this->fieldsToHighlight[$this->scope][$field] = new stdClass();
		}

		if(!array_key_exists($field ,$this->baseFieldNameMapping))
		{
			$this->baseFieldNameMapping[$field]=$baseFieldName;
		}
	}

	/**
	 * @param array $eHighlight
	 * @return array
	 */
	public function removeDuplicateHits($eHighlight)
	{
		uksort($eHighlight, array($this, 'cmpHighlightFieldsByPriority'));
		$uniqueValuesPerBaseFieldName = array();
		foreach ($eHighlight as $fieldName => $hits)
		{
			$baseFieldName = $this->baseFieldNameMapping[$fieldName];
			$filteredHits = array();
			if(!array_key_exists($baseFieldName ,$uniqueValuesPerBaseFieldName))
			{
				$uniqueValuesPerBaseFieldName[$baseFieldName] = array();
			}

			foreach ($hits as $hit)
			{
				$uniqueValue =  strip_tags($hit);
				$uniqueValue = trim($uniqueValue);
				if(!in_array ($uniqueValue, $uniqueValuesPerBaseFieldName[$baseFieldName]))
				{
					$uniqueValuesPerBaseFieldName[$baseFieldName][] = $uniqueValue;
					$filteredHits[] = $hit;
				}
			}

			if(empty($filteredHits))
				unset($eHighlight[$fieldName]);
			else
				$eHighlight[$fieldName] = $filteredHits;
		}

		return $eHighlight;
	}

	public function cmpHighlightFieldsByPriority($field1, $field2)
	{
		return ($this->getFieldTypePriority($field1) - $this->getFieldTypePriority($field2));
	}

	private function getFieldTypePriority($fieldName)
	{
		$priority = self::HIGHLIGHT_DEFUALT_PRIORITY;
		if(kString::endsWith($fieldName, kESearchQueryManager::RAW_FIELD_SUFFIX))
			$priority =  self::HIGHLIGHT_HIGHEST_PRIORITY;
		else if(kString::endsWith($fieldName, kESearchQueryManager::NGRAMS_FIELD_SUFFIX))
			$priority = self::HIGHLIGHT_LOWEST_PRIORITY;
		else if(kString::endsWith($fieldName, kESearchQueryManager::SYNONYM_FIELD_SUFFIX))
			$priority = self::HIGHLIGHT_LOWEST_PRIORITY;
		else if($this->baseFieldNameMapping[$fieldName] == $fieldName)
			$priority = self::HIGHLIGHT_MEDIUM_PRIORITY;

		return $priority;
	}
}