<?php

namespace data;
use kVendorTaskData;

/**
 * @package plugins.reach
 * @subpackage model
 */
class kSentimentAnalysisVendorTaskData extends kVendorTaskData
{
	protected $language;
	protected $sentimentAnalysisOutputJson = null;

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

	public function getSentimentAnalysisOutputJson(): ?string
	{
		return $this->sentimentAnalysisOutputJson;
	}

	public function setSentimentAnalysisOutputJson(?string $sentimentAnalysisOutputJson): void
	{
		$this->sentimentAnalysisOutputJson = $sentimentAnalysisOutputJson;
	}
}
