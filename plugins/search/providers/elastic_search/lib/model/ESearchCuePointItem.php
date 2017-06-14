<?php

class ESearchCuePointItem extends ESearchItem
{

	/**
	 * @var ESearchCuePointFieldName
	 */
	protected $fieldName;

	/**
	 * @var string
	 */
	protected $searchTerm;

	private static $exact_match_only_fields = array(
	);

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

	public function getType()
	{
		return 'cuepoint';
	}

	public function getQueryVerbs()
	{
		if (in_array($this->getFieldName(), self::$exact_match_only_fields))
			return array('must','term');
		return parent::getQueryVerbs();
	}


}