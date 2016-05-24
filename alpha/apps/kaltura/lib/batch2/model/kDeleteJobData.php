<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kDeleteJobData extends kJobData
{
	/**
	 * The filter should return the list of objects that need to be deleted.
	 * @var baseObjectFilter
	 */
	private $filter;
	
	/**
	 * 
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
