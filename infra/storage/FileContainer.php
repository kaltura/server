<?php
/**
 * @package infra
 * @subpackage Storage
 */
class FileContainer
{
	/**
	 * @var string
	 */
	private $filePath;

	/**
	 * @var string
	 */
	private $encryptionKey;

	/**
	 * @var int
	 */
	private $fileSize;

	/**
	 * @return string filePath
	 */
	public function getFilePath()
	{
		return $this->filePath;
	}

	/**
	 * @return string $encryptionKey
	 */
	public function getEncryptionKey()
	{
		return $this->encryptionKey;
	}

	/**
	 * @return int $fileData
	 */
	public function getFileSize()
	{
		return $this->fileSize;
	}

	/**
	 * @param string $filePath
	 */
	public function setFilePath($filePath)
	{
		$this->filePath = $filePath;
	}

	/**
	 * @param string $encryptionKey
	 */
	public function setEncryptionKey($encryptionKey)
	{
		$this->encryptionKey = $encryptionKey;
	}

	/**
	 * @param int $fileSize
	 */
	public function setFileSize($fileSize)
	{
		$this->fileSize = $fileSize;
	}
}
?>