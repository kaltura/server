<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model
 */

class kMediaEsearchExportToCsvJobData extends kExportCsvJobData
{
	/**
	 * @var bool
	 */
	private $humanReadable;
	/**
	 * @var ESearchParams
	 */
	protected $searchParams;
	/**
	 * @return bool
	 */
	public function getHumanReadable() {
		return $this->humanReadable;
	}
	/**
	 * @param bool $humanReadable
	 */
	public function setHumanReadable($humanReadable) {
		$this->humanReadable = $humanReadable;
	}
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
