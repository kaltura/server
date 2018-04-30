<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */
class kESearchNestedQuery extends kESearchCompoundQuery
{
	const NESTED_KEY = 'nested';
	const PATH_KEY = 'path';
	const INNER_HITS_KEY = 'inner_hits';
	const SIZE_KEY = 'size';
	const SOURCE_KEY = '_source';
	const NAME_KEY = 'name';
	const HIGHLIGHT_KEY = 'highlight';
	const QUERY_KEY = 'query';
	const SORT_KEY = 'sort';

	/**
	 * @var string
	 */
	protected $path;

	/**
	 * @var string
	 */
	protected $innerHitsName;

	/**
	 * @var int
	 */
	protected $innerHitsSize;

	/**
	 * @var bool
	 */
	protected $innerHitsSource;

	/**
	 * @var array
	 */
	protected $highlight;
	

	protected $query;

	/**
	 * @var array
	 */
	protected $sort;

	/**
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * @param string $path
	 */
	public function setPath($path)
	{
		$this->path = $path;
	}

	/**
	 * @return array
	 */
	public function getHighlight()
	{
		return $this->highlight;
	}

	/**
	 * @param array $highlight
	 */
	public function setHighlight($highlight)
	{
		$this->highlight = $highlight;
	}

	/**
	 * @return boolean
	 */
	public function getInnerHitsSource()
	{
		return $this->innerHitsSource;
	}

	/**
	 * @param boolean $innerHitsSource
	 */
	public function setInnerHitsSource($innerHitsSource)
	{
		$this->innerHitsSource = $innerHitsSource;
	}

	/**
	 * @return int
	 */
	public function getInnerHitsSize()
	{
		return $this->innerHitsSize;
	}

	/**
	 * @param int $innerHitsSize
	 */
	public function setInnerHitsSize($innerHitsSize)
	{
		$this->innerHitsSize = $innerHitsSize;
	}

	/**
	 * @return string
	 */
	public function getInnerHitsName()
	{
		return $this->innerHitsName;
	}

	/**
	 * @param string $innerHitsName
	 */
	public function setInnerHitsName($innerHitsName)
	{
		$this->innerHitsName = $innerHitsName;
	}

	/**
	 * @return mixed
	 */
	public function getQuery()
	{
		return $this->query;
	}

	/**
	 * @param mixed $query
	 */
	public function setQuery($query)
	{
		$this->query = $query;
	}

	/**
	 * @return array
	 */
	public function getSort()
	{
		return $this->sort;
	}

	/**
	 * @param array $sort
	 */
	public function setSort($sort)
	{
		$this->sort = $sort;
	}

	public function getFinalQuery()
	{
		if(!$this->getQuery())
			return null;

		$query[self::NESTED_KEY][self::PATH_KEY] = $this->getPath();
		$query[self::NESTED_KEY][self::INNER_HITS_KEY][self::SIZE_KEY] = $this->getInnerHitsSize();
		$query[self::NESTED_KEY][self::INNER_HITS_KEY][self::SOURCE_KEY] = $this->getInnerHitsSource();
		$query[self::NESTED_KEY][self::QUERY_KEY] = $this->getQuery()->getFinalQuery();

		if($this->getInnerHitsName())
			$query[self::NESTED_KEY][self::INNER_HITS_KEY][self::NAME_KEY] = $this->getInnerHitsName();

		if($this->getHighlight())
			$query[self::NESTED_KEY][self::INNER_HITS_KEY][self::HIGHLIGHT_KEY] = $this->getHighlight();

		if($this->getSort())
			$query[self::NESTED_KEY][self::INNER_HITS_KEY][self::SORT_KEY] = $this->getSort();

		return $query;
	}
}
