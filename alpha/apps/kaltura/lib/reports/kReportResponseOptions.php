<?php

class kReportResponseOptions
{

	protected $delimiter;
	protected $skipEmptyDates;

	public function __construct()
	{
		$this->delimiter = ',';
		$this->skipEmptyDates = true;
	}

	/**
	 * @return string
	 */
	public function getDelimiter()
	{
		return $this->delimiter;
	}

	/**
	 * @param string $delimiter
	 */
	public function setDelimiter($delimiter)
	{
		$this->delimiter = $delimiter;
	}

	/**
	 * @return boolean
	 */
	public function getSkipEmptyDates()
	{
		return $this->skipEmptyDates;
	}

	/**
	 * @param boolean $skipEmptyDates
	 */
	public function setSkipEmptyDates($skipEmptyDates)
	{
		$this->skipEmptyDates = $skipEmptyDates;
	}

}