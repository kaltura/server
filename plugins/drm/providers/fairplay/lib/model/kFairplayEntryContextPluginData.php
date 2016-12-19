<?php

class kFairplayEntryContextPluginData extends PluginData {

	/**
    * @var string
    */
	public $publicCertificate;

	/**
	 * @return string
	 */
	public function getPublicCertificate()
	{
		return $this->publicCertificate;
	}

	/**
	 * @param string $publicCertificate
	 */
	public function setPublicCertificate($publicCertificate)
	{
		$this->publicCertificate = $publicCertificate;
	}
} // kFairplayEntryContextPluginData
