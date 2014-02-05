<?php
/**
 * @package plugins.scheduledTask
 * @subpackage model.data
 */
class kScheduledTaskJobData extends kJobData
{
	/**
	 * @var int
	 */
	private $_maxResults;

	/**
	 * @var string
	 */
	private $_resultsFilePath;

	/**
	 * @param int $maxResults
	 */
	public function setMaxResults($maxResults)
	{
		$this->_maxResults = $maxResults;
	}

	/**
	 * @return int
	 */
	public function getMaxResults()
	{
		return $this->_maxResults;
	}

	/**
	 * @param string $results
	 */
	public function setResultsFilePath($results)
	{
		$this->_resultsFilePath = $results;
	}

	/**
	 * @return string
	 */
	public function getResultsFilePath()
	{
		return $this->_resultsFilePath;
	}
}