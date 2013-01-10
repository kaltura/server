<?php
/**
 * @package plugins.youTubeDistribution
 * @subpackage lib
 */
class YouTubeDistributionRemoteIdHandler
{
	/**
	 * @var string
	 */
	protected $_videoId;

	/**
	 * @var string
	 */
	protected $_assetId;

	/**
	 * @var string
	 */
	protected $_referenceId;

	public function __construct()
	{

	}

	/**
	 * @param string $assetId
	 */
	public function setAssetId($assetId)
	{
		$this->_assetId = $assetId;
	}

	/**
	 * @return string
	 */
	public function getAssetId()
	{
		return $this->_assetId;
	}

	/**
	 * @param string $referenceId
	 */
	public function setReferenceId($referenceId)
	{
		$this->_referenceId = $referenceId;
	}

	/**
	 * @return string
	 */
	public function getReferenceId()
	{
		return $this->_referenceId;
	}

	/**
	 * @param string $videoId
	 */
	public function setVideoId($videoId)
	{
		$this->_videoId = $videoId;
	}

	/**
	 * @return string
	 */
	public function getVideoId()
	{
		return $this->_videoId;
	}

	public static function initialize($str)
	{
		$idHandler = new self;
		if (self::isSerialized($str))
		{
			$array = unserialize($str);
			if (isset($array['video']))
				$idHandler->setVideoId($array['video']);

			if (isset($array['asset']))
				$idHandler->setAssetId($array['asset']);

			if (isset($array['reference']))
				$idHandler->setReferenceId($array['reference']);
		}
		else
		{
			$idHandler->setVideoId($str);
		}

		return $idHandler;
	}

	public function getSerialized()
	{
		$array = array();
		$array['video'] = $this->getVideoId();
		$array['asset'] = $this->getAssetId();
		$array['reference'] = $this->getReferenceId();
		return serialize($array);
	}

	/**
	 * Legacy connector stored only the youtube video id, without asset id and reference id
	 */
	public function isLegacyRemoteId()
	{
		return !$this->getAssetId() && !$this->getReferenceId();
	}

	protected static function isSerialized($str)
	{
		// if string is not serialized, it would be the video id, so this validation (while being hacky) is good enough
		return strpos('{s:', $str) !== false;
	}
}