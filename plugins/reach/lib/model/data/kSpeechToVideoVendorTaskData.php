<?php

/**
 * @package plugins.reach
 * @subpackage model
 */
class kSpeechToVideoVendorTaskData extends kVendorTaskData
{
	public string $avatarId = "";

	public ?string $conversionProfileId = null;

	public function getAvatarId(): string
	{
		return $this->avatarId;
	}

	public function setAvatarId(string $avatarId): void
	{
		$this->avatarId = $avatarId;
	}

	public function getConversionProfileId(): ?string
	{
		return $this->conversionProfileId;
	}

	public function setConversionProfileId(?string $conversionProfileId): void
	{
		$this->conversionProfileId = $conversionProfileId;
	}
}
