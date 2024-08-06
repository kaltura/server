<?php

/**
 * @package plugins.reach
 * @subpackage model
 */
class kClipsVendorTaskData extends kVendorTaskData
{
	public int $clipsDuration = 0;

	public string $instruction = "";

	public ?string $clipsOutputJson = null;

	public function getClipsDuration(): int
	{
		return $this->clipsDuration;
	}

	public function setClipsDuration(int $clipsDuration): void
	{
		$this->clipsDuration = $clipsDuration;
	}

	public function getInstruction(): string
	{
		return $this->instruction;
	}

	public function setInstruction(string $instruction): void
	{
		$this->instruction = $instruction;
	}

	public function getClipsOutputJson(): ?string
	{
		return $this->clipsOutputJson;
	}

	public function setClipsOutputJson(?string $clipsOutputJson): void
	{
		$this->clipsOutputJson = $clipsOutputJson;
	}
}
