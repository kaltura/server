<?php
/**
 * Created by IntelliJ IDEA.
 * User: yossi.papiashvili
 * Date: 5/26/19
 * Time: 4:19 PM
 */

/**
 * List of classes that extend 'kFileTransferMgr'.
 * Instances of these classes can be created using the 'getInstance($type)' function.
 *
 * @package infra
 * @subpackage Storage
 */
interface kFileSystemMgrType
{
	const LOCAL = 1;
	const S3 = 2;
}

abstract class kFileSystemMgr
{
	public function __construct(array $options = null)
	{
		return;
	}
	
	/**
	 * Should create the directory tree of the given file path
	 *
	 * @param $filePath the file path
	 *
	 * @return true / false according to success
	 */
	abstract protected function doCreateDirForPath($filePath);
	
	/**
	 * Check if the given file path exists in the destination storage
	 *
	 * @param $filePath the file path
	 *
	 * @return true / false according to success
	 */
	abstract protected function doCheckFileExists($filePath);
	
	/**
	 * Get the content of the given file path
	 *
	 * @param $filePath the file path
	 *
	 * @return content | error on failure
	 */
	abstract protected function doGetFile($filePath);
	
	
	/**
	 * Remove the symlink of the given file path
	 *
	 * @param $filePath the file path
	 *
	 * @return true / false according to success
	 */
	abstract protected function doUnlink($filePath);
	
	
	/**
	 * Write a file in atomic way
	 *
	 * @param $filePath the file path
	 * @param $fileContent the contnet to put in the filePath
	 *
	 * @return true / false according to success
	 */
	abstract protected function doPutFileAtomic($filePath, $fileContent);
	
	/**
	 * Write a file to the given file path
	 *
	 * @param $filePath the file path
	 * @param $fileContent the content to put in the filePath
	 *
	 * @return true / false according to success
	 */
	abstract protected function doPutFile($filePath, $fileContent);
	
	/**
	 * Rename a file
	 *
	 * @param $filePath the current file path
	 * @param $newFilePath the new file path
	 *
	 * @return true / false according to success
	 */
	abstract protected function doRename($filePath, $newFilePath);
	
	/**
	 * Copy a file
	 *
	 * @param $fromFilePath the current file path
	 * @param $toFilePath the new file path
	 *
	 * @return true / false according to success
	 */
	abstract protected function doCopy($fromFilePath, $toFilePath);
	
	
	public function createDirForPath($filePath)
	{
		return $this->doCreateDirForPath($filePath);
	}
	
	public function checkFileExists($filePath)
	{
		return $this->doCheckFileExists($filePath);
	}
	
	public function getFile($filePath)
	{
		return $this->doGetFile($filePath);
	}
	
	public function unlink($filePath)
	{
		return $this->doUnlink($filePath);
	}
	
	public function putFileAtomic($filePath, $fileContent)
	{
		return $this->doPutFileAtomic($filePath, $fileContent);
	}
	
	public function putFile($filePath, $fileContent)
	{
		return $this->doPutFile($filePath, $fileContent);
	}
	
	public function rename($filePath, $newFilePath)
	{
		return $this->doRename($filePath, $newFilePath);
	}
	
	public function copy($fromFilePath, $toFilePath)
	{
		return $this->doCopy($fromFilePath, $toFilePath);
	}
	
	/**
	 * Create a new class instance according to the given type.
	 *
	 * @param fileTransferMgrTypes $type Class type from the list under 'kFileTransferMgrType' class.
	 * @param array $options
	 *
	 * @return kFileTransferMgr a new instance
	 */
	public static function getInstance($type, array $options = null)
	{
		switch($type)
		{
			case kFileSystemMgrType::LOCAL:
				return new kLocalFileSystemMgr($options);
			
			case kFileSystemMgrType::S3:
				return new kS3lFileSystemMgr($options);
		}
		
		return null;
	}
}