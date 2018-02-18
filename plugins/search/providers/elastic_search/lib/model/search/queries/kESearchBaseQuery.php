<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */
abstract class kESearchBaseQuery
{

	abstract public function getFinalQuery();

	/**
	 * @return boolean
	 */
	public function getShouldMoveToFilterContext()
	{
		return false;
	}

}
