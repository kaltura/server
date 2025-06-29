<?php

/**
 * @package plugins.reach
 * @subpackage model
 */
class kMetadataEnrichmentVendorTaskData extends kLocalizedVendorTaskData
{
	public string $detailLevel = "";

	public string $instruction = "";

	public bool $shouldApply = false;

	public string $applyMode = "";

	public array $overrideFields = [];

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

	public function getShouldApply(): bool
	{
		return $this->shouldApply;
	}

	public function setShouldApply(bool $shouldApply): void
	{
		$this->shouldApply = $shouldApply;
	}

	public function getApplyMode(): string
	{
		return $this->applyMode;
	}

	public function setApplyMode(string $applyMode): void
	{
		$this->applyMode = $applyMode;
	}

	public function getOverrideFields(): array
	{
		return $this->overrideFields;
	}

	public function setOverrideFields(array $overrideFields): void
	{
		$this->overrideFields = $overrideFields;
	}
}
