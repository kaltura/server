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