<?php

/**
 * @package plugins.reach
 * @subpackage model
 */
class kSummaryVendorTaskData extends kVendorTaskData
{
	public TypeOfSummaryTaskData $typeOfSummary;

	public SummaryWritingStyleTaskData $writingStyle;

	public ?string $summaryOutputJson = null;

	public function getTypeOfSummary(): TypeOfSummaryTaskData
	{
		return $this->typeOfSummary;
	}

	public function setTypeOfSummary(TypeOfSummaryTaskData $typeOfSummary): void
	{
		$this->typeOfSummary = $typeOfSummary;
	}

	public function getWritingStyle(): SummaryWritingStyleTaskData
	{
		return $this->writingStyle;
	}

	public function setWritingStyle(SummaryWritingStyleTaskData $writingStyle): void
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