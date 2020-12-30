<?php
/**
 * @package Core
 * @subpackage model.data
 */

class kExportCsvJobData extends kJobData
{
	
	/**
	 * The file location
	 * @var string
	 */
	private $outputPath;
	
	/**
	 * The users name
	 * @var string
	 */
	private $userName;
	
	
	/**
	 * The users email
	 * @var string
	 */
	private $userMail;

	/**
	 * @var string
	 */
	public $storageDestinationFilePath;
	
	/**
	 * @return string
	 */
	public function getOutputPath()
	{
		return $this->outputPath;
	}
	
	/**
	 * @param string $outputPath
	 */
	public function setOutputPath($outputPath)
	{
		$this->outputPath = $outputPath;
	}
	
	/**
	 * @return string
	 */
	public function getUserMail()
	{
		return $this->userMail;
	}
	
	/**
	 * @param string $userMail
	 */
	public function setUserMail($userMail)
	{
		$this->userMail = $userMail;
	}

	/**
	 * @return string
	 */
	public function getUserName()
	{
		return $this->userName;
	}

	/**
	 * @param string $userName
	 */
	public function setUserName($userName)
	{
		$this->userName = $userName;
	}

	/**
	 * @return string
	 */
	public function getStorageDestinationFilePath()
	{
		return $this->storageDestinationFilePath;
	}

	/**
	 * @param string $storageDestinationFilePath
	 */
	public function setStorageDestinationFilePath($storageDestinationFilePath)
	{
		$this->storageDestinationFilePath = $storageDestinationFilePath;
	}
	
}