<?php

class EnhancedSearchEntryItem extends EnhancedSearchItem
{

	/**
	 * @var EnhancedSearchEntryFieldName
	 */
	protected $fieldName;

	/**
	 * @var string
	 */
	protected $searchTerm;

	/**
	 * @return EnhancedSearchEntryFieldName
	 */
	public function getFieldName()
	{
		return $this->fieldName;
	}

	/**
	 * @param EnhancedSearchEntryFieldName $fieldName
	 */
	public function setFieldName($fieldName)
	{
		switch ($fieldName)
		{
			case EnhancedSearchEntryFieldName::ENTRY_DESCRIPTION:
				$fieldName = 'ENTRY_DESCRIPTION';
				break;
			case EnhancedSearchEntryFieldName::ENTRY_NAME:
				$fieldName = 'ENTRY_NAME';
				break;
		}
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

	public function getSearchQuery()
	{
		$queryVerb = $this->getQueryVerb();
		$queryVal = array($this->getFieldName() => strtolower($this->getSearchTerm()));
		return array($queryVerb => $queryVal);
	}


}