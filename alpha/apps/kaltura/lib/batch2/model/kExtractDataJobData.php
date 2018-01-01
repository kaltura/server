<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kExtractDataJobData extends kJobData
{
	/**
	 * @var FileContainer
	 */
	public $fileContainer;

	/**
	 * @var string
	 */
	public $entryId;

	/**
	 * @var string
	 */
	public $enginesType;
	
	
	/**
	 * @return FileContainer $fileContainer
	 */
	public function getFileContainer()
	{
		return $this->fileContainer;
	}

	/**
	 * @param $fileContainer FileContainer
	 */
	public function setFileContainer($fileContainer)
	{
		$this->fileContainer = $fileContainer;
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
	 * @return string $enginesType
	 */
	public function getEnginesType()
	{
		return $this->enginesType;
	}
	
	/**
	 * @param string $enginesType
	 */
	public function setEnginesType($enginesType)
	{
		$this->enginesType = $enginesType;
	}
	
}
