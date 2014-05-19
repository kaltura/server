<?php
/**
 * @package plugins.tvinciDistribution
 * @subpackage lib
 */
class TvinciDistributionRemoteIdHandler
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
			$object = json_decode($str);
			if (isset($object->video))
				$idHandler->setVideoId($object->video);

			if (isset($object->asset))
				$idHandler->setAssetId($object->asset);

			if (isset($object->reference))
				$idHandler->setReferenceId($object->reference);
		}
		else
		{
			$idHandler->setVideoId($str);
		}

		return $idHandler;
	}

	public function getSerialized()
	{
		$object = new stdClass;
		$object->video = $this->getVideoId();
		$object->asset = $this->getAssetId();
		$object->reference = $this->getReferenceId();
		return json_encode($object);
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
		return is_object(json_decode($str));
	}
}