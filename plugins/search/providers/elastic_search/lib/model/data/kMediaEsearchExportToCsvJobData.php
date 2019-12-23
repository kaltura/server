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
	private $options;
	/**
	 * @var ESearchParams
	 */
	protected $searchParams;
	/**
	 * @return bool
	 */
	public function getoptions()
	{
		return $this->options;
	}
	/**
	 * @param bool $options
	 */
	public function setOptions($options)
	{
		$this->options = $options;
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
