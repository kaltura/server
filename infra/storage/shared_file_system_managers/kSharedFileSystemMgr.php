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

require_once(dirname(__FILE__) . '/kS3SharedFileSystemMgr.php');
require_once(dirname(__FILE__) . '/kNfsSharedFileSystemMgr.php');

interface kSharedFileSystemMgrType
{
	const LOCAL = "NFS";
	const S3 = "S3";
}

abstract class kSharedFileSystemMgr
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
	abstract protected function doGetFileContent($filePath);
	
	
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
	 * @param $fileContent the content to put in the filePath
	 *
	 * @return true / false according to success
	 */
	abstract protected function doPutFileContentAtomic($filePath, $fileContent);
	
	/**
	 * Write a file to the given file path
	 *
	 * @param $filePath the file path
	 * @param $fileContent the content to put in the filePath
	 *
	 * @return true / false according to success
	 */
	abstract protected function doPutFileContent($filePath, $fileContent);
	
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
	
	
	/**
	 * Copy a file
	 *
	 * @param $url $remoteUrl
	 * @param $destFilePath file name to save remote content to
	 *
	 * @return true / false according to success
	 */
	abstract protected function doGetFileFromRemoteUrl($url, $destFilePath = null, $allowInternalUrl = false);
	
	
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
	
	public function getFileFromRemoteUrl($url, $destFilePath = null)
	{
		return $this->doGetFileFromRemoteUrl($filePath, $destFilePath);
	}
	
	public function unlink($filePath)
	{
		return $this->doUnlink($filePath);
	}
	
	public function putFileContentAtomic($filePath, $fileContent)
	{
		return $this->doPutFileContentAtomic($filePath, $fileContent);
	}
	
	public function putFileContent($filePath, $fileContent)
	{
		return $this->doPutFileContent($filePath, $fileContent);
	}
	
	public function rename($filePath, $newFilePath)
	{
		return $this->doRename($filePath, $newFilePath);
	}
	
	public function copy($fromFilePath, $toFilePath)
	{
		return $this->doCopy($fromFilePath, $toFilePath);
	}
	
	public function getFileContent($filePath)
	{
		return $this->doGetFileContent($filePath);
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
			case kSharedFileSystemMgrType::LOCAL:
				return new kNfsSharedFileSystemMgr($options);
			
			case kSharedFileSystemMgrType::S3:
				return new kS3SharedFileSystemMgr($options);
		}
		
		return null;
	}
}