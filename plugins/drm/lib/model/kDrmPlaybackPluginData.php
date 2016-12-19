<?php

class kDrmPlaybackPluginData extends PluginData {

	/**
	 * @var string
	 */
	protected $scheme;

	/**
	 * @var string
	 */
	protected $licenseURL;

	/**
	 * @return string
	 */
	public function getScheme()
	{
		return $this->scheme;
	}

	/**
	 * @param string $scheme
	 */
	public function setScheme($scheme)
	{
		$this->scheme = $scheme;
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
