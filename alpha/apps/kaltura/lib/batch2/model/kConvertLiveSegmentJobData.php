<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kConvertLiveSegmentJobData extends kJobData
{
	/**
	 * @var string
	 */
	private $entryId;
	
	/**
	 * @var string
	 */
	private $assetId;
	
	/**
	 * @var int
	 */
	private $mediaServerIndex;
	
	/**
	 * @var int
	 */
	private $fileIndex;
	
	/**
	 * @var string
	 */
	private $srcFilePath;
	
	/**
	 * @var string
	 */
	private $destFilePath;
	
	/**
	 * @var float
	 */
	private $endTime;

	/**
	 * @var string
	 */
	private $destDataFilePath;

	/**
	 * @return string $entryId
	 */
	public function getEntryId()
	{
		return $this->entryId;
	}

	/**
	 * @return int $mediaServerIndex
	 */
	public function getMediaServerIndex()
	{
		return $this->mediaServerIndex;
	}

	/**
	 * @return string $srcFilePath
	 */
	public function getSrcFilePath()
	{
		return $this->srcFilePath;
	}

	/**
	 * @return string $destFilePath
	 */
	public function getDestFilePath()
	{
		return $this->destFilePath;
	}

	/**
	 * @return string $destDataFilePath
	 */
	public function getDestDataFilePath()
	{
		return $this->destDataFilePath;
	}

	/**
	 * @return float $endTime
	 */
	public function getEndTime()
	{
		return $this->endTime;
	}

	/**
	 * @param string $entryId
	 */
	public function setEntryId($entryId)
	{
		$this->entryId = $entryId;
	}

	/**
	 * @param int $mediaServerIndex
	 */
	public function setMediaServerIndex($mediaServerIndex)
	{
		$this->mediaServerIndex = $mediaServerIndex;
	}

	/**
	 * @param string $srcFilePath
	 */
	public function setSrcFilePath($srcFilePath)
	{
		$this->srcFilePath = $srcFilePath;
	}

	/**
	 * @param string $destFilePath
	 */
	public function setDestFilePath($destFilePath)
	{
		$this->destFilePath = $destFilePath;
	}

	/**
	 * @param string $destDataFilePath
	 */
	public function setDestDataFilePath($destDataFilePath)
	{
		$this->destDataFilePath = $destDataFilePath;
	}

	/**
	 * @param float $endTime
	 */
	public function setEndTime($endTime)
	{
		$this->endTime = $endTime;
	}
	
	/**
	 * @return int $fileIndex
	 */
	public function getFileIndex()
	{
		return $this->fileIndex;
	}

	/**
	 * @param int $fileIndex
	 */
	public function setFileIndex($fileIndex)
	{
		$this->fileIndex = $fileIndex;
	}
	
	/**
	 * @return the $assetId
	 */
	public function getAssetId()
	{
		return $this->assetId;
	}

	/**
	 * @param string $assetId
	 */
	public function setAssetId($assetId)
	{
		$this->assetId = $assetId;
	}
}
