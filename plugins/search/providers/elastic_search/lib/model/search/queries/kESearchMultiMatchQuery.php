<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */
class kESearchMultiMatchQuery extends kESearchBaseFieldQuery
{
	const MULTI_MATCH_KEY = 'multi_match';
	const QUERY_KEY = 'query';
	const MOST_FIELDS = 'most_fields';
	const FIELDS_KEY = 'fields';
	const TYPE_KEY = 'type';

	public function __construct()
	{
		$this->fields = array();
		$this->type = self::MOST_FIELDS;
	}

	/**
	 * @var array
	 */
	protected $fields;

	/**
	 * @var string
	 */
	protected $query;

	/**
	 * @var string
	 */
	protected $type;

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param string $type
	 */
	public function setType($type)
	{
		$this->type = $type;
	}

	/**
	 * @return string
	 */
	public function getQuery()
	{
		return $this->query;
	}

	/**
	 * @param string $query
	 */
	public function setQuery($query)
	{
		$this->query = $query;
	}

	public function addToFields($fieldName)
	{
		$this->fields[] = $fieldName;
	}

	public function getFinalQuery()
	{
		if(!count($this->fields))
			return null;

		$query[self::MULTI_MATCH_KEY][self::QUERY_KEY] = $this->getQuery();
		foreach ($this->fields as $field)
		{
			$query[self::MULTI_MATCH_KEY][self::FIELDS_KEY][] = $field;
		}
		$query[self::MULTI_MATCH_KEY][self::TYPE_KEY] = $this->getType();

		return $query;
	}
}
