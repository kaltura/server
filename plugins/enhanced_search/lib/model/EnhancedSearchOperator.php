<?php

class EnhancedSearchOperator extends EnhancedSearchItem
{

	/**
	 * @var EnhancedSearchOperatorType
	 */
	protected $operator;

	/**
	 * @var boolean
	 */
	protected $not;

	/**
	 * @var array
	 */
	protected $searchItems;

	/**
	 * @return EnhancedSearchOperatorType
	 */
	public function getOperator()
	{
		return $this->operator;
	}

	/**
	 * @param EnhancedSearchOperatorType $operator
	 */
	public function setOperator($operator)
	{
		$this->operator = $operator;
	}

	/**
	 * @return boolean
	 */
	public function isNot()
	{
		return $this->not;
	}

	/**
	 * @param boolean $not
	 */
	public function setNot($not)
	{
		$this->not = $not;
	}

	/**
	 * @return array
	 */
	public function getSearchItems()
	{
		return $this->searchItems;
	}

	/**
	 * @param array $searchItems
	 */
	public function setSearchItems($searchItems)
	{
		$this->searchItems = $searchItems;
	}

	public function getSearchQuery()
	{
		
		$captionQuery = array(
			'has_child' => array(
				'type' => self::ELASTIC_CAPTION_TYPE,
				'query' => array(
					'nested' => array(
						'path' => 'lines',
						'query' => array(
							'bool' => array(
								//here you enter the query on the fields
							)
						),
						'inner_hits' => array( //to see the lines that contributed to the answer
							'size' => self::INNER_HITS_SIZE
						)
					)
				),
				'inner_hits' => array( //to see the caption asset id
					'size' => self::INNER_HITS_SIZE,
					'_source' => false,
				)
			)
		);
	}


}