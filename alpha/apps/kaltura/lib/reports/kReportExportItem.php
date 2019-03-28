<?php

class kReportExportItem
{
	/**
	 * @var string
	 */
	protected $reportTitle;

	/**
	 * @var ReportExportItemType
	 */
	protected $action;

	/**
	 * @var ReportType
	 */
	protected $reportType;

	protected $filter;

	/**
	 * @var string
	 */
	protected $order;

	/**
	 * @var string
	 */
	protected $objectIds;

	/**
	 * @var kReportResponseOptions
	 */
	protected $responseOptions;

	/**
	 * @return string
	 */
	public function getReportTitle()
	{
		return $this->reportTitle;
	}

	/**
	 * @param string $reportTitle
	 */
	public function setReportTitle($reportTitle)
	{
		$this->reportTitle = $reportTitle;
	}

	/**
	 * @return ReportExportItemType
	 */
	public function getAction()
	{
		return $this->action;
	}

	/**
	 * @param ReportExportItemType $action
	 */
	public function setAction($action)
	{
		$this->action = $action;
	}

	/**
	 * @return ReportType
	 */
	public function getReportType()
	{
		return $this->reportType;
	}

	/**
	 * @param ReportType $reportType
	 */
	public function setReportType($reportType)
	{
		$this->reportType = $reportType;
	}

	/**
	 * @return reportsInputFilter
	 */
	public function getFilter()
	{
		return $this->filter;
	}

	/**
	 * @param reportsInputFilter $filter
	 */
	public function setFilter($filter)
	{
		$this->filter = $filter;
	}

	/**
	 * @return string
	 */
	public function getOrder()
	{
		return $this->order;
	}

	/**
	 * @param string $order
	 */
	public function setOrder($order)
	{
		$this->order = $order;
	}

	/**
	 * @return string
	 */
	public function getObjectIds()
	{
		return $this->objectIds;
	}

	/**
	 * @param string $objectIds
	 */
	public function setObjectIds($objectIds)
	{
		$this->objectIds = $objectIds;
	}

	/**
	 * @return kReportResponseOptions
	 */
	public function getResponseOptions()
	{
		return $this->responseOptions;
	}

	/**
	 * @param kReportResponseOptions $responseOptions
	 */
	public function setResponseOptions($responseOptions)
	{
		$this->responseOptions = $responseOptions;
	}

}
