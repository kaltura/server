<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model
 */
class ESearchParams extends BaseObject
{

	/**
	 * @var ESearchOperator
	 */
	protected $searchOperator;

	/**
	 * @var string
	 */
	protected $objectStatuses;

	/**
	 * @var ESearchOrderBy
	 */
	protected $orderBy;

	/**
	 * @return ESearchOperator
	 */
	public function getSearchOperator()
	{
		return $this->searchOperator;
	}

	/**
	 * @param ESearchOperator $searchOperator
	 */
	public function setSearchOperator($searchOperator)
	{
		$this->searchOperator = $searchOperator;
	}

	/**
	 * @return string
	 */
	public function getObjectStatuses()
	{
		return $this->objectStatuses;
	}

	/**
	 * @param string $objectStatuses
	 */
	public function setObjectStatuses($objectStatuses)
	{
		$this->objectStatuses = $objectStatuses;
	}

	/**
	 * @return ESearchOrderBy
	 */
	public function getOrderBy()
	{
		return $this->orderBy;
	}

	/**
	 * @param ESearchOrderBy $orderBy
	 */
	public function setOrderBy($orderBy)
	{
		$this->orderBy = $orderBy;
	}

}
