<?php

class kFairPlayPlaybackPluginData extends kDrmPlaybackPluginData {

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

} // kFairPlayPlaybackPluginData
