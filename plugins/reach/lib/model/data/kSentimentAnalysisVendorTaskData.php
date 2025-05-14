<?php

/**
 * @package plugins.reach
 * @subpackage model
 */
class kSentimentAnalysisVendorTaskData extends kVendorTaskData
{
	protected $language;

	public function __construct()
	{
		$this->language = null;
	}

	public function getLanguage()
	{
		return $this->language;
	}

	public function setLanguage($language): void
	{
		$this->language = $language;
	}
}
