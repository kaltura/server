<?php
/**
 * @package plugins.caption
 * @subpackage model.data
 */
class kConvertCaptionAssetJobData extends kJobData
{
	/**
     * @var string
	 */
	private $captionAssetId;

    /**
     * @var string
     */
	private $fileLocation;

	/**
	 * @var string
	 */
	private $fileEncryptionKey;

	/**
	 * @var string
	 */
	private $fromType;

	/**
	 * @var string
	 */
	private $toType;

	/**
     * @return string $multiLanaguageCaptionAssetId
     */
	public function getCaptionAssetId()
	{
		return $this->captionAssetId;
	}

	/**
	 * @param string $captionAssetId
	 */
	public function setCaptionAssetId($captionAssetId)
	{
		$this->captionAssetId = $captionAssetId;
	}

	/**
	 * @return string $fileLocation
	 */
	public function getFileLocation()
	{
		return $this->fileLocation;
	}

	/**
	 * @param string $fileLocation
	 */
	public function setFileLocation($fileLocation)
	{
		$this->fileLocation = $fileLocation;
	}

	/**
	 * @return string $fileEncryptionKey
	 */
	public function getFileEncryptionKey()
	{
		return $this->fileEncryptionKey;
	}

	/**
	 * @param string $fileEncryptionKey
	 */
	public function setFileEncryptionKey($fileEncryptionKey)
	{
		$this->fileEncryptionKey = $fileEncryptionKey;
	}

	/**
	 * @return string $fromType
	 */
	public function getFromType()
	{
		return $this->fromType;
	}

	/**
	 * @param string $fromType
	 */
	public function setFromType($fromType)
	{
		$this->fromType= $fromType;
	}

	/**
	 * @return string $toType
	 */
	public function getToType()
	{
		return $this->toType;
	}

	/**
	 * @param string $toType
	 */
	public function setToType($toType)
	{
		$this->toType= $toType;
	}

}