<?php

/**
 * @package plugins.reach
 * @subpackage model
 */
class kSummaryVendorTaskData extends kLocalizedVendorTaskData
{
	protected $typeOfSummary;
	protected $writingStyle;
	protected $summaryOutputJson = null;

	public function __construct()
	{
		$this->typeOfSummary = TypeOfSummaryTaskData::CONCISE;
		$this->writingStyle = SummaryWritingStyleTaskData::FORMAL;
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

	public function getSummaryOutputJson(): ?string
	{
		return $this->summaryOutputJson;
	}

	public function setSummaryOutputJson(?string $summaryOutputJson): void
	{
		$this->summaryOutputJson = $summaryOutputJson;
	}
}
