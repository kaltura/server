<?php

/**
 * @package plugins.reach
 * @subpackage model
 */
class kLocalizedVendorTaskData extends kVendorTaskData
{
	protected $outputLanguage;

	public function __construct()
	{
		$this->outputLanguage = null;
	}

	public function getOutputLanguage()
	{

		return $this->outputLanguage;
	}

	public function setOutputLanguage($language): void
	{
		$this->outputLanguage = $language;
	}
}
