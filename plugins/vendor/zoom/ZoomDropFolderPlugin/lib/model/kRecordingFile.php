<?php
/**
 * Recording File
 *
 * @package plugins.ZoomDropFolder
 * @subpackage model
 *
 */
class kRecordingFile
{
	/**
	 * @var string
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $recordingStart;
	
	/**
	 * @var string
	 */
	protected $fileType;

	/**
	 * @var string
	 */
	protected $downloadUrl;
	
	/**
	 * @var string
	 */
	protected $fileExtension;
	
	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param string $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
    * @return string
    */
	public function getRecordingStart()
	{
		return $this->recordingStart;
	}

	/**
	 * @param string $recordingStart
	 */
	public function setRecordingStart($recordingStart)
	{
		$this->recordingStart = $recordingStart;
	}
	
	/**
	 * @return string
	 */
	public function getFileType()
	{
		return $this->fileType;
	}

	/**
	 * @param string $fileType
	 */
	public function setFileType($fileType)
	{
		$this->fileType = $fileType;
	}

	/**
	 * @return string
	 */
	public function getDownloadUrl()
	{
		return $this->downloadUrl;
	}

	/**
	 * @param string $downloadUrl
	 */
	public function setDownloadUrl($downloadUrl)
	{
		$this->downloadUrl = $downloadUrl;
	}
	
	/**
	 * @return string
	 */
	public function getFileExtension()
	{
		return $this->fileExtension;
	}
	
	/**
	 * @param string $fileExtension
	 */
	public function setFileExtension($fileExtension)
	{
		$this->fileExtension = $fileExtension;
	}

}