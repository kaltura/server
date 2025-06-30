<?php

/**
 * @package plugins.reach
 * @subpackage model
 */
class kLocalizedVendorTaskData extends kVendorTaskData
{
	protected $outputLanguage;

	protected $outputJson = null;

	public function __construct()
	{
		$this->outputLanguage = null;
		$this->outputJson = null;
	}

	public function getOutputLanguage()
	{

		return $this->outputLanguage;
	}

	public function setOutputLanguage($language): void
	{
		$this->outputLanguage = $language;
	}

	public function getOutputJson(): ?string
	{
		return $this->outputJson;
	}

	public function setOutputJson(?string $outputJson): void
	{
		$this->outputJson = $outputJson;
	}
}
