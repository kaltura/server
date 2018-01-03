<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kWowmeJobData extends kJobData
{
	/**
	 * @var string
	 */
	private $outEntryId;

	/**
	 * @var HighlightType
	 */
	private $highlightType;

	/**
	 * @var string
	 */
	private $fileSyncPath;

	/**
	 * @return string
	 */
	public function getFileSyncPath()
	{
		return $this->fileSyncPath;
	}

	/**
	 * @param string $fileSyncPath
	 */
	public function setFileSyncPath($fileSyncPath)
	{
		$this->fileSyncPath = $fileSyncPath;
	}

	/**
	 * @return string
	 */
	public function getHighlightType()
	{
		return $this->highlightType;
	}

	/**
	 * @param string $highlightType
	 */
	public function setHighlightType($highlightType)
	{
		$this->highlightType = $highlightType;
	}

	/**
	 * @return string
	 */
	public function getOutEntryId()
	{
		return $this->outEntryId;
	}

	/**
	 * @param string $outEntryId
	 */
	public function setOutEntryId($outEntryId)
	{
		$this->outEntryId = $outEntryId;
	}

}
