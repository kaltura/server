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
	 * @var DryRunType
	 */
	private $_fileFormat;

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
		$this->_fileFormat = DryRunFileType::LIST_RESPONSE;
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
	public function getFileFormat()
	{
		return $this->_fileFormat;
	}

	/**
	 * @param DryRunFileType $fileFormat
	 */
	public function setFileFormat($fileFormat)
	{
		$this->_fileFormat = $fileFormat;
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