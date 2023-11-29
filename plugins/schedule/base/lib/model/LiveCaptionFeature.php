<?php
/**
 * @package plugins.schedule
 * @subpackage model
 */
class LiveCaptionFeature extends LiveFeature
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
	 * Url for retrieving the captions
	 * @var string
	 */
	protected $captionUrl;

	/**
	 * token for the caption url
	 * @var string
	 */
	protected $captionToken;

	/**
	 * Number of seconds stream should wait for caption data
	 * @var int
	 */
	protected $inputDelay;

	/**
	 * @var string
	 */
	protected $language;

	/**
	 * @var string
	 */
	protected $captionFormat;

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
	public function setCaptionUrl($v)
	{
		$this->captionUrl = $v;
	}

	public function getCaptionUrl()
	{
		return $this->captionUrl;
	}

	/**
	 * @param string $v
	 */
	public function setCaptionToken($v)
	{
		$this->captionToken = $v;
	}

	public function getCaptionToken()
	{
		return $this->captionToken;
	}

	public function setInputDelay($v)
	{
		$this->inputDelay = $v;
	}

	public function getInputDelay()
	{
		return $this->inputDelay;
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

	/**
	 * @param string $v
	 */
	public function setCaptionFormat($v)
	{
		$this->captionFormat = $v;
	}

	public function getCaptionFormat()
	{
		return $this->captionFormat;
	}

	public function getApiType()
	{
		return 'KalturaLiveCaptionFeature';
	}
}
