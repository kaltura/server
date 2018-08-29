<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */
class kESearchCompletionSuggestQuery extends kESearchBaseSuggestQuery
{

	const COMPLETION_KEY = 'completion';
	const PREFIX_KEY = 'prefix';
	const FIELD_KEY = 'field';
	const SKIP_DUPLICATES = 'skip_duplicates';
	const SIZE_KEY = 'size';

	/**
	 * @var string
	 */
	protected $suggestName;

	/**
	 * @var string
	 */
	protected $prefix;

	/**
	 * @var string
	 */
	protected $field;

	/**
	 * @var int
	 */
	protected $size;

	/**
	 * @var bool
	 */
	protected $skipDuplicates;

	/**
	 * @return string
	 */
	public function getSuggestName()
	{
		return $this->suggestName;
	}

	/**
	 * @param string $suggestName
	 */
	public function setSuggestName($suggestName)
	{
		$this->suggestName = $suggestName;
	}

	/**
	 * @return string
	 */
	public function getPrefix()
	{
		return $this->prefix;
	}

	/**
	 * @param string $prefix
	 */
	public function setPrefix($prefix)
	{
		$this->prefix = $prefix;
	}

	/**
	 * @return string
	 */
	public function getField()
	{
		return $this->field;
	}

	/**
	 * @param string $field
	 */
	public function setField($field)
	{
		$this->field = $field;
	}

	/**
	 * @return int
	 */
	public function getSize()
	{
		return $this->size;
	}

	/**
	 * @param int $size
	 */
	public function setSize($size)
	{
		$this->size = $size;
	}

	/**
	 * @return boolean
	 */
	public function getSkipDuplicates()
	{
		return $this->skipDuplicates;
	}

	/**
	 * @param boolean $skipDuplicates
	 */
	public function setSkipDuplicates($skipDuplicates)
	{
		$this->skipDuplicates = $skipDuplicates;
	}

	public function getFinalQuery()
	{
		$query = array();
		if($this->getSuggestName())
		{
			$query[self::SUGGEST_KEY][$this->getSuggestName()][self::PREFIX_KEY] = $this->getPrefix();
			$query[self::SUGGEST_KEY][$this->getSuggestName()][self::COMPLETION_KEY][self::FIELD_KEY] = $this->getField();
			$query[self::SUGGEST_KEY][$this->getSuggestName()][self::COMPLETION_KEY][self::SKIP_DUPLICATES] = $this->getSkipDuplicates();
			$query[self::SUGGEST_KEY][$this->getSuggestName()][self::COMPLETION_KEY][self::SIZE_KEY] = $this->getSize();
		}
		return $query;
	}

}
