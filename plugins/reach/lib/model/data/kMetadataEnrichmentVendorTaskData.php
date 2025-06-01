<?php

/**
 * @package plugins.reach
 * @subpackage model
 */
class kMetadataEnrichmentVendorTaskData extends kLocalizedVendorTaskData
{
	public string $detailLevel = "";

	public string $instruction = "";

	public ?string $outputJson = null;

	public function getDetailLevel(): string
	{
		return $this->detailLevel;
	}

	public function setDetailLevel(string $detailLevel): void
	{
		$this->detailLevel = $detailLevel;
	}

	public function getInstruction(): string
	{
		return $this->instruction;
	}

	public function setInstruction(string $instruction): void
	{
		$this->instruction = $instruction;
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
