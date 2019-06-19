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

	/**
	 * creates a directory using the dirname of the specified path
	 *
	 * @param string $path path to create dir
	 * @param int $rights mode for the dir
	 * @param bool $recursive should we make the dir path recursively
	 * @return bool true on success or false on failure.
	 */
	abstract protected function doFullMkdir($path, $rights = 0755, $recursive = true);

	/**
	 * creates a directory using the specified path
	 * @param string $path path to create dir
	 * @param int $rights mode for the dir
	 * @param bool $recursive should we make the dir path recursively
	 * @return bool true on success or false on failure.
	 */
	abstract protected function doFullMkfileDir($path, $rights = 0777, $recursive = true);

	/**
	 * move path from one directory to another
	 *
	 * @param $from
	 * @param $to
	 * @param bool $override_if_exists
	 * @param bool $copy
	 * @return mixed
	 */
	abstract protected function doMoveFile($from, $to, $override_if_exists = false, $copy = false);

	/**
	 * check if path is dir
	 *
	 * @param $path
	 * @return mixed
	 */
	abstract protected function doIsDir($path);

	/**
	 * creates path directory
	 *
	 * @param $path
	 * @return mixed
	 */
	abstract protected function doMkdir($path);

	/**
	 * removes path directory
	 *
	 * @param $path
	 * @return mixed
	 */
	abstract protected function doRmdir($path);

	/**
	 * chmod path with given mode
	 *
	 * @param $path
	 * @param $mode
	 * @return mixed
	 */
	abstract public function doChmod($path, $mode);

	/**
	 * return the file size of given file
	 *
	 * @param $filename
	 * @return mixed
	 */
	abstract public function doFileSize($filename);

	/**
	 * copy single file from source to dest
	 *
	 * @param $src
	 * @param $dest
	 * @param $deleteSrc
	 * @return mixed
	 */
	abstract protected function copySingleFile($src, $dest, $deleteSrc);

	/**
	 * delete file
	 *
	 * @param $filename
	 * @return mixed
	 */
	abstract protected function doDeleteFile($filename);

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

	public function fullMkdir($path, $rights = 0755, $recursive = true)
	{
		return $this->doFullMkdir($path, $rights, $recursive);
	}

	public function moveFile($from, $to, $override_if_exists = false, $copy = false)
	{
		$from = str_replace("\\", "/", $from);
		$to = str_replace("\\", "/", $to);
		return $this->doMoveFile($from, $to, $override_if_exists, $copy);
	}

	public function isDir($path)
	{
		return $this->doIsDir($path);
	}

	public function mkdir($path)
	{
		return $this->doMkdir($path);
	}

	public function rmdir($path)
	{
		return $this->doRmdir($path);
	}

	public function chmod($path, $mode)
	{
		return $this->doChmod($path, $mode);
	}

	public function fileSize($filename)
	{
		return $this->doFileSize($filename);
	}

	public function deleteFile($filename)
	{
		return $this->doDeleteFile($filename);
	}
	public static function getInstance($type = null, array $options = null)
	{
		if(self::$kSharedFsMgr)
			return self::$kSharedFsMgr;

		$dc_config = kConf::getMap("dc_config");
		$options = isset($dc_config['storage']) ? $dc_config['storage'] : null;
		if(!$type)
		{
			$type = isset($dc_config['fileSystemType']) ? $dc_config['fileSystemType'] : kSharedFileSystemMgrType::LOCAL;
		}

		switch($type)
		{
			case kSharedFileSystemMgrType::LOCAL:
				self::$kSharedFsMgr = new kNfsSharedFileSystemMgr($options);

			case kSharedFileSystemMgrType::S3:
				self::$kSharedFsMgr = new kS3SharedFileSystemMgr($options);
		}

		return self::$kSharedFsMgr;
	}
	
}