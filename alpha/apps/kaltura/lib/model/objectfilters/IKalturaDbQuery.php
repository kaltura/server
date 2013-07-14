<?php
/**
 * @package Core
 * @subpackage model.filters
 */
interface IKalturaDbQuery
{
	/**
	 * Add a new where clause condition to the query on specific column
	 * 
	 * @param string $column
	 * @param mixed $value
	 * @param string $comparison
	 */
	public function addColumnWhere($column, $value, $comparison);
	
	/**
	 * Add a new column to order by
	 * @param string $orderBy
	 * @param string $orderByType Criteria::ASC | Criteria::DESC
	 */
	public function addOrderBy($column, $orderByType = Criteria::ASC);
}
