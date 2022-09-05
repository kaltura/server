<?php

/**
 * @package plugins.schedule
 * @subpackage model
 */
class LiveRestreamFeature extends LiveFeature
{
	/**
	 * Primary URL to forward content to
	 * @var string
	 */
	protected $primaryUrl;

	/**
	 * Secondary URL to forward content to
	 * @var string
	 */
	protected $secondaryUrl;

	/**
	 * stream
	 * @var string
	 */
	protected $streamKey;

	/**
	 * @param string $v
	 */
	public function setPrimaryUrl($v)
	{
		$this->primaryUrl = $v;
	}

	public function getPrimaryUrl()
	{
		return $this->primaryUrl;
	}

	/**
	 * @param string $v
	 */
	public function setSecondaryUrl($v)
	{
		$this->secondaryUrl = $v;
	}

	public function getSecondaryUrl()
	{
		return $this->secondaryUrl;
	}

	/**
	 * @param string $v
	 */
	public function setStreamKey($v)
	{
		$this->streamKey = $v;
	}

	public function getStreamKey()
	{
		return $this->streamKey;
	}

	public function getApiType()
	{
		return 'KalturaLiveRestreamFeature';
	}
}