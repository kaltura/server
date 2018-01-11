<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */
class kESearchExistsQuery extends kESearchBaseFieldQuery
{
	const EXISTS_KEY = 'exists';
	const FIELD_KEY = 'field';

	/**
	 * @var string
	 */
	protected $searchTerm;

	public function __construct($fieldName)
	{
		$this->fieldName = $fieldName;
	}

	public function getFinalQuery()
	{
		$query = array();
		$query[self::EXISTS_KEY][self::FIELD_KEY] = $this->fieldName;
		return $query;
	}
}
