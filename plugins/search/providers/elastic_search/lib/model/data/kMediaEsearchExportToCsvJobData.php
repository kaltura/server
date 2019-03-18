<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model
 */

class kMediaEsearchExportToCsvJobData extends kExportCsvJobData
{
	/**
	 * @var ESearchParams
	 */
	protected $searchParams;
	
	/**
	 * @return ESearchParams
	 */
	public function getSearchParams()
	{
		return $this->searchParams;
	}
	
	/**
	 * @param ESearchParams $searchParams
	 */
	public function setSearchParams($searchParams)
	{
		$this->searchParams = $searchParams;
	}
}