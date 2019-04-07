<?php
/**
 * @package plugins.reach
 * @subpackage batch
 */
class kEntryVendorTaskCsvJobData extends kExportCsvJobData
{
	/**
	 * The filter should return the list of users that need to be specified in the csv.
	 * @var EntryVendorTaskFilter
	 */
	private $filter;
	
	/**
	 *
	 * @return EntryVendorTaskFilter $filter
	 */
	public function getFilter()
	{
		return $this->filter;
	}
	
	/**
	 * @param EntryVendorTaskFilter $filter
	 */
	public function setFilter($filter)
	{
		$this->filter = $filter;
	}
}