<?php
/**
 * @package plugins.searchHistory
 * @subpackage batch
 */
class kSearchHistoryCsvJobData extends kExportCsvJobData
{
	/**
	 * @var ESearchHistoryFilter
	 */
	private $filter;

	/**
	 *
	 * @return ESearchHistoryFilter $filter
	 */
	public function getFilter()
	{
		return $this->filter;
	}

	/**
	 * @param ESearchHistoryFilter $filter
	 */
	public function setFilter($filter)
	{
		$this->filter = $filter;
	}
}