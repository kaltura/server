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
	 * @var kRecordingFileType
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
	 * @var string
	 */
	protected $downloadToken;
	
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
	 * @return kRecordingFileType
	 */
	public function getFileType()
	{
		return $this->fileType;
	}

	/**
	 * @param kRecordingFileType $fileType
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
	
	/**
	 * @return string
	 */
	public function getDownloadToken()
	{
		return $this->downloadToken;
	}
	
	/**
	 * @param string $downloadToken
	 */
	public function setDownloadToken($downloadToken)
	{
		$this->downloadToken = $downloadToken;
	}

}