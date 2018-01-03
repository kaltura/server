<?php

abstract class KWowmeEngine {

	protected $highlightType;
	protected $outEntryId;

	/**
	 * KWowmeEngine constructor.
	 */
	public function __construct(KalturaWowmeJobData $jobData)
	{
		$this->highlightType = $jobData->highlightType;
		$this->outEntryId = $jobData->outEntryId;
	}

	abstract public function createWowFactor();
}