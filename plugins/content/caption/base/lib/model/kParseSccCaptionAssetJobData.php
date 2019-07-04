<?php
/**
 * @package plugins.caption
 * @subpackage model.data
 */
class kParseSccCaptionAssetJobData extends kJobData
{
	/**
     * @var string
	 */
	private $sccCaptionAssetId;

    /**
     * @var string
     */
	private $fileLocation;

	/**
	 * @var string
	 */
	private $fileEncryptionKey;

	/**
     * @return string $multiLanaguageCaptionAssetId
     */
	public function getSccCaptionAssetId()
	{
		return $this->sccCaptionAssetId;
	}

	/**
	 * @param string $captionAssetId
	 */
	public function setSccCaptionAssetId($captionAssetId)
	{
		$this->sccCaptionAssetId = $captionAssetId;
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

}