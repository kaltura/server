<?php

/**
 * @package plugins.reach
 * @subpackage model
 */
class kSummaryVendorTaskData extends kVendorTaskData
{
	protected $typeOfSummary;
	protected $writingStyle;
	protected $language;
	protected $summaryOutputJson = null;

	public function __construct()
	{
		$this->typeOfSummary = TypeOfSummaryTaskData::CONCISE;
		$this->writingStyle = SummaryWritingStyleTaskData::FORMAL;
		$this->language = null;
	}

	public function getTypeOfSummary()
	{
		return $this->typeOfSummary;
	}

	public function setTypeOfSummary( $typeOfSummary): void
	{
		$this->typeOfSummary = $typeOfSummary;
	}

	public function getWritingStyle()
	{
		return $this->writingStyle;
	}

	public function setWritingStyle( $writingStyle): void
	{
		$this->writingStyle = $writingStyle;
	}

	public function getLanguage()
	{
		return $this->language;
	}

	public function setLanguage($language): void
	{
		$this->language = $language;
	}

	public function getSummaryOutputJson(): ?string
	{
		return $this->summaryOutputJson;
	}

	public function setSummaryOutputJson(?string $summaryOutputJson): void
	{
		$this->summaryOutputJson = $summaryOutputJson;
	}
}
