<?php

/**
 * @package plugins.reach
 * @subpackage model
 */
class kClipsVendorTaskData extends kLocalizedVendorTaskData
{
	public $clipsDuration = 0;
	public $eventSessionContextId = null;
	public $instruction = "";
	public $clipsOutputJson = null;

	public function getClipsDuration(): int
	{
		return $this->clipsDuration;
	}

	public function getEventSessionContextId(): ?string
	{
		return $this->eventSessionContextId;
	}

	public function setEventSessionContextId(?string $eventSessionContextId): void
	{
		$this->eventSessionContextId = $eventSessionContextId;
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
