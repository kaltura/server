<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */
class kESearchHighlightQuery extends kESearchBaseQuery
{

	const UNIFIED = 'unified';
	const SCORE = 'score';
	const ORDER = 'order';
	const NUMBER_OF_FRAGMENTS = 'number_of_fragments';
	const FIELDS = 'fields';
	const TYPE = 'type';

	/**
	 * @var string
	 */
	private $type;

	/**
	 * @var string
	 */
	private $order;

	/**
	 * @var int
	 */
	private $numberOfFragments;

	/**
	 * @var array
	 */
	private $fields;

	public function __construct($fields, $numberOfFragments = null)
	{
		$this->type = self::UNIFIED;
		$this->order = self::SCORE;
		if($numberOfFragments)
			$this->numberOfFragments = $numberOfFragments;
		$this->fields = $fields;
	}

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
	public function getOrder()
	{
		return $this->order;
	}

	/**
	 * @param string $order
	 */
	public function setOrder($order)
	{
		$this->order = $order;
	}

	/**
	 * @return int
	 */
	public function getNumberOfFragments()
	{
		return $this->numberOfFragments;
	}

	/**
	 * @param int $numberOfFragments
	 */
	public function setNumberOfFragments($numberOfFragments)
	{
		$this->numberOfFragments = $numberOfFragments;
	}

	/**
	 * @return array
	 */
	public function getFields()
	{
		return $this->fields;
	}

	/**
	 * @param array $fields
	 */
	public function setFields($fields)
	{
		$this->fields = $fields;
	}

	public function getFinalQuery()
	{
		if(!$this->getFields())
			return null;

		$highlightQuery = array();
		$highlightQuery[self::TYPE] = $this->getType();
		$highlightQuery[self::ORDER] = $this->getOrder();
		if($this->getNumberOfFragments())
			$highlightQuery[self::NUMBER_OF_FRAGMENTS] = $this->getNumberOfFragments();
		$highlightQuery[self::FIELDS] = $this->getFields();

		return $highlightQuery;
	}

}
