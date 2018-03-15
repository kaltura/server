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
	private $_totalCount;

	/**
	 * @var int
	 */
	private $_maxResults;

	/**
	 * @var bool
	 */
	private $_isNewFormat;

	/**
	 * @var string
	 */
	private $_resultsFilePath;

	/**
	 * @var int
	 */
	private $_referenceTime;

	/**
	 * kScheduledTaskJobData constructor.
	 */
	public function __construct()
	{
		$this->_isNewFormat = false;
		$this->_maxResults = 0;
	}

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

	/**
	 * @param int $referenceTime
	 */
	public function setReferenceTime($referenceTime)
	{
		$this->_referenceTime = (int)$referenceTime;
	}

	/**
	 * @return int
	 */
	public function getReferenceTime()
	{
		return $this->_referenceTime;
	}

	/**
	 * @return bool
	 */
	public function getIsNewFormat()
	{
		return $this->_isNewFormat;
	}

	/**
	 * @param bool $bool
	 */
	public function setIsNewFormat($bool)
	{
		$this->_isNewFormat = $bool;
	}

	/**
	 * @return int
	 */
	public function getTotalCount()
	{
		return $this->_totalCount;
	}

	/**
	 * @param int $count
	 */
	public function setTotalCount($count)
	{
		$this->_totalCount = $count;
	}
}