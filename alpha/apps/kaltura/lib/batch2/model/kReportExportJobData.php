<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kReportExportJobData extends kJobData
{

	/**
	 * @var string
	 */
	protected $recipientEmail;

	/**
	 * @var array
	 */
	protected $reportItems;

	/**
	 * @var int
	 */
	protected $timeZoneOffset;

	/**
	 * @var bigint
	 */
	protected $timeReference;

	/**
	 * @var string
	 */
	protected $filePaths;

	/**
	 * @var array
	 */
	protected $files;

	/**
	 * @var string
	 */
	protected $reportsGroup;

	/**
	 * @return string
	 */
	public function getRecipientEmail()
	{
		return $this->recipientEmail;
	}

	/**
	 * @param string $recipientEmail
	 */
	public function setRecipientEmail($recipientEmail)
	{
		$this->recipientEmail = $recipientEmail;
	}

	/**
	 * @return array
	 */
	public function getReportItems()
	{
		return $this->reportItems;
	}

	/**
	 * @param array $reportItems
	 */
	public function setReportItems($reportItems)
	{
		$this->reportItems = $reportItems;
	}

	/**
	 * @return int
	 */
	public function getTimeZoneOffset()
	{
		return $this->timeZoneOffset;
	}

	/**
	 * @param int $timeZoneOffset
	 */
	public function setTimeZoneOffset($timeZoneOffset)
	{
		$this->timeZoneOffset = $timeZoneOffset;
	}

	/**
	 * @return bigint
	 */
	public function getTimeReference()
	{
		return $this->timeReference;
	}

	/**
	 * @param bigint $timeReference
	 */
	public function setTimeReference($timeReference)
	{
		$this->timeReference = $timeReference;
	}

	/**
	 * @return string
	 */
	public function getFilePaths()
	{
		return $this->filePaths;
	}

	/**
	 * @param string $filePaths
	 */
	public function setFilePaths($filePaths)
	{
		$this->filePaths = $filePaths;
	}

	/**
	 * @return array
	 */
	public function getFiles()
	{
		return $this->files;
	}

	/**
	 * @param array $files
	 */
	public function setFiles($files)
	{
		$this->files = $files;
	}

	/**
	 * @return string
	 */
	public function getReportsGroup()
	{
		return $this->reportsGroup ? $this->reportsGroup : ' ';
	}

	/**
	 * @param string $reportsGroup
	 */
	public function setReportsGroup($reportsGroup)
	{
		$this->reportsGroup = $reportsGroup;
	}

}
