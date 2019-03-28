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

}
