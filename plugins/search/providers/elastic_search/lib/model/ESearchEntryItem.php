<?php

class ESearchEntryItem extends ESearchItem
{

	/**
	 * @var ESearchEntryFieldName
	 */
	protected $fieldName;

	/**
	 * @var string
	 */
	protected $searchTerm;

	/**
	 * @return ESearchEntryFieldName
	 */
	public function getFieldName()
	{
		return $this->fieldName;
	}

	/**
	 * @param ESearchEntryFieldName $fieldName
	 */
	public function setFieldName($fieldName)
	{
		switch ($fieldName)
		{
			case ESearchEntryFieldName::ENTRY_DESCRIPTION:
				$fieldName = 'ENTRY_DESCRIPTION';
				break;
			case ESearchEntryFieldName::ENTRY_NAME:
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

	public function createSearchQuery()
	{
		return kEQueryManager::createEntrySearchQuery($this);
	}


}