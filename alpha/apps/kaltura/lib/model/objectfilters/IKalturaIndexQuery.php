<?php

/**
 * @package Core
 * @subpackage model.filters
 */
interface IKalturaIndexQuery
{
	/**
	 * Add a new where clause condition to the query
	 * @param string $statement
	 */
	public function addWhere($statement);
	
	/**
	 * Add a new match condition clause to the query
	 * @param string $where
	 */
	public function addMatch($match);
	
	/**
	 * Add a new condition clause to the query
	 * @param string $condition
	 */
	public function addCondition($condition);
	
	/**
	 * Add a new column to order by
	 * @param string $orderBy
	 * @param string $orderByType Criteria::ASC | Criteria::DESC
	 */
	public function addOrderBy($column, $orderByType = Criteria::ASC);
}
