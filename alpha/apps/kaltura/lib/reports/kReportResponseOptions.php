<?php

class kReportResponseOptions
{
	protected $delimiter;

	public function __construct()
	{
		$this->delimiter = ',';
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

}