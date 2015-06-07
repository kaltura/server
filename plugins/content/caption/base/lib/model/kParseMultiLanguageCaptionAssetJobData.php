<?php
/**
 * @package plugins.caption
 * @subpackage model.data
 */
class kParseMultiLanguageCaptionAssetJobData extends kJobData
{
	/**
     * @var string
	 */
	private $multiLanaguageCaptionAssetId;

	/**
     * @var string
	 */
	private $entryId;

    /**
     * @var string
     */
	private $fileLocation;

	/**
     * @return string $multiLanaguageCaptionAssetId
     */
	public function getMultiLanaguageCaptionAssetId()
	{
		return $this->multiLanaguageCaptionAssetId;
	}

	/**
	 * @param string $captionAssetId
	 */
	public function setMultiLanaguageCaptionAssetId($captionAssetId)
	{
		$this->multiLanaguageCaptionAssetId = $captionAssetId;
	}

	/**
	 * @return string $entryId
	 */
	public function getEntryId()
	{
		return $this->entryId;
	}

	/**
	 * @param string $entryId
	 */
	public function setEntryId($entryId)
	{
		$this->entryId = $entryId;
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
}

	