<?php

class kFairPlayEntryPlayingPluginData extends kDrmEntryPlayingPluginData {

	/**
	 * @var string
	 */
	protected $certificate;

	/**
	 * @return string
	 */
	public function getCertificate()
	{
		return $this->certificate;
	}

	/**
	 * @param string $certificate
	 */
	public function setCertificate($certificate)
	{
		$this->certificate = $certificate;
	}

} // kFairPlayEntryPlayingPluginData
