<?php

class kReportExportParams
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
	 * @var string
	 */
	protected $reportsItemsGroup;
	
	/**
	 * @var string
	 */
	protected $baseUrl;

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
	 * @return string
	 */
	public function getReportsItemsGroup()
	{
		return $this->reportsItemsGroup;
	}

	/**
	 * @param string $reportsItemsGroup
	 */
	public function setReportsItemsGroup($reportsItemsGroup)
	{
		$this->reportsItemsGroup = $reportsItemsGroup;
	}
	
	/**
	 * @return string
	 */
	public function getBaseUrl()
	{
		return $this->baseUrl;
	}
	
	/**
	 * @param string $baseUrl
	 */
	public function setBaseUrl($baseUrl)
	{
		$this->baseUrl = $baseUrl;
	}
}
