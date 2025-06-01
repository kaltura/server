<?php

/**
 * @package plugins.reach
 * @subpackage model
 */
class kLocalizedVendorTaskData extends kVendorTaskData
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
