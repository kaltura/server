<?php

class kCaptionPlaybackPluginData {

	/**
	 * @var string
	 */
	protected $label;

	/**
	 * @var string
	 */
	protected $format;

	/**
	 * @var string
	 */
	protected $language;

	/**
	 * @var string
	 */
	protected $url;

	/**
	 * @var string
	 */
	protected $webVttUrl;

	/**
	 * @var string
	 */
	protected $languageCode;


	/**
	 * @var bool
	 */
	protected $isDefault;

	public function __construct($label = null, $format  = null, $language = null , $isDefault = false, $webVttUrl = null, $url = null , $languageCode = null)
	{
		$this->label = $label;
		$this->format = $format;
		$this->language = $language;
		$this->isDefault = $isDefault;
		$this->webVttUrl = $webVttUrl;
		$this->url = $url;
		$this->languageCode = $languageCode;
	}

	/**
	 * @return string
	 */
	public function getLabel()
	{
		return $this->label;
	}

	/**
	 * @param string $label
	 */
	public function setLabel($label)
	{
		$this->label = $label;
	}

	/**
	 * @return string
	 */
	public function getFormat()
	{
		return $this->format;
	}

	/**
	 * @param string $format
	 */
	public function setFormat($format)
	{
		$this->format = $format;
	}

	/**
	 * @return string
	 */
	public function getLanguage()
	{
		return $this->language;
	}

	/**
	 * @param string $language
	 */
	public function setLanguage($language)
	{
		$this->language = $language;
	}

	/**
	 * @return string
	 */
	public function getWebVttUrl()
	{
		return $this->webVttUrl;
	}

	/**
	 * @param string $webVttUrl
	 */
	public function setWebVttUrl($webVttUrl)
	{
		$this->webVttUrl = webVttUrl;
	}

	/**
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * @param string $url
	 */
	public function setUrl($url)
	{
		$this->url = $url;
	}

	/**
	 * @return bool
	 */
	public function getIsDefault()
	{
		return $this->isDefault;
	}

	/**
	 * @param array $isDefault
	 */
	public function setIsDefault($isDefault)
	{
		$this->isDefault = $isDefault;
	}

	/**
	 * @return string
	 */
	public function getLanguageCode()
	{
		return $this->languageCode;
	}

	/**
	 * @param string
	 */
	public function setLanguageCode($languageCode)
	{
		$this->languageCode = $languageCode;
	}
}