<?php
class kReportExportFile
{

	/**
	 * @var string
	 */
	protected $fileId;

	/**
	 * @var string
	 */
	protected $fileName;

	/**
	 * @return string
	 */
	public function getFileId()
	{
		return $this->fileId;
	}

	/**
	 * @param string $fileId
	 */
	public function setFileId($fileId)
	{
		$this->fileId = $fileId;
	}

	/**
	 * @return string
	 */
	public function getFileName()
	{
		return $this->fileName;
	}

	/**
	 * @param string $fileName
	 */
	public function setFileName($fileName)
	{
		$this->fileName = $fileName;
	}

}
