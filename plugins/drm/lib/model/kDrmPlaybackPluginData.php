<?php

class kDrmPlaybackPluginData extends PluginData {

	/**
	 * @var int
	 */
	protected $scheme;

	/**
	 * @var string
	 */
	protected $licenseURL;

	/**
	 * @return int
	 */
	public function getScheme()
	{
		return $this->scheme;
	}

	/**
	 * @param int $scheme
	 */
	public function setScheme($scheme)
	{
		if ($scheme !== null) {
			$this->scheme  = (int) $scheme;
		}
	}

	/**
	 * @return string
	 */
	public function getLicenseURL()
	{
		return $this->licenseURL;
	}

	/**
	 * @param string $licenseURL
	 */
	public function setLicenseURL($licenseURL)
	{
		$this->licenseURL = $licenseURL;
	}

} // kDrmPlaybackPluginData
