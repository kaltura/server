<?php
/**
 * @package plugins.schedule
 * @subpackage model
 */
class LiveTranslationFeature extends LiveFeature
{
	/**
	 * Url for sending/getting the media
	 * @var string
	 */
	protected $mediaUrl;

	/**
	 * Identifier for the media url
	 * @var string
	 */
	protected $mediaKey;

	/**
	 * @var string
	 */
	protected $language;

	/**
	 * @param string $v
	 */
	public function setMediaUrl($v)
	{
		$this->mediaUrl = $v;
	}

	public function getMediaUrl()
	{
		return $this->mediaUrl;
	}

	/**
	 * @param string $v
	 */
	public function setMediaKey($v)
	{
		$this->mediaKey = $v;
	}

	public function getMediaKey()
	{
		return $this->mediaKey;
	}

	/**
	 * @param string $v
	 */
	public function setLanguage($v)
	{
		$this->language = $v;
	}

	public function getLanguage()
	{
		return $this->language;
	}

	public function getApiType()
	{
		return 'KalturaLiveTranslationFeature';
	}
}
