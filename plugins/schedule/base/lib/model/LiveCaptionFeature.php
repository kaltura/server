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
	private $mediaUrl;

	/**
	 * Identifier for the media url
	 * @var string
	 */
	private $mediaKey;

	/**
	 * Url for retrieving the captions
	 * @var string
	 */
	private $captionUrl;

	/**
	 * token for the caption url
	 * @var string
	 */
	private $captionToken;

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

	public function getApiType()
	{
		return 'KalturaLiveCaptionFeature';
	}
}