<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kIndexJobData extends kJobData
{
	/**
	 * @var baseObjectFilter
	 */
	private $filter;
	
	/**
	 * @return baseObjectFilter $filter
	 */
	public function getFilter()
	{
		return $this->filter;
	}

	/**
	 * @param baseObjectFilter $filter
	 */
	public function setFilter(baseObjectFilter $filter)
	{
		$this->filter = $filter;
	}
}
