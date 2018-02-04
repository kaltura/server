<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */
abstract class kESearchBaseQuery
{

	/**
	 * @var bool
	 */
	protected $shouldMoveToFilterContext;

	abstract public function getFinalQuery();

	/**
	 * @return boolean
	 */
	public function getShouldMoveToFilterContext()
	{
		return $this->shouldMoveToFilterContext;
	}

	/**
	 * @param boolean $shouldMoveToFilterContext
	 */
	public function setShouldMoveToFilterContext($shouldMoveToFilterContext)
	{
		$this->shouldMoveToFilterContext = $shouldMoveToFilterContext;
	}

}
