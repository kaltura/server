<?php
/**
 * Created by IntelliJ IDEA.
 * User: yossi.papiashvili
 * Date: 5/26/19
 * Time: 4:19 PM
 */

require_once(dirname(__FILE__) . '/kSharedFileSystemMgr.php');

class kNfsSharedFileSystemMgr extends kSharedFileSystemMgr
{
	// instances of this class should be created using the 'getInstance' of the 'kFileTransferMgr' class
	public function __construct(array $options = null)
	{
		return;
	}
	
	protected function doCreateDirForPath($filePath)
	{
		$dirname = dirname($filePath);
		if (!is_dir($dirname))
		{
			mkdir($dirname, 0777, true);
		}
	}
	
	protected function doCheckFileExists($filePath)
	{
		return file_exists($filePath);
	}
	
	protected function doGetFileContent($filePath, $from_byte = 0, $to_byte = -1)
	{
		return file_get_contents($filePath);
	}
	
	protected function doUnlink($filePath)
	{
		return @unlink($filePath);
	}
	
	protected function doPutFileContentAtomic($filePath, $fileContent)
	{
		// write to a temp file and then rename, so that the write will be atomic
		$tempFilePath = tempnam(dirname($filePath), basename($filePath));
		
		if(!$this->doPutFileContent($tempFilePath, $fileContent))
			return false;
		
		if(!$this->doRename($tempFilePath, $filePath))
		{
			$this->doUnlink($tempFilePath);
			return false;
		}
		
		return true;
	}
	
	protected function doPutFileContent($filePath, $fileContent, $flags = 0, $context = null)
	{
		return file_put_contents($filePath, $fileContent);
	}
	
	protected function doRename($filePath, $newFilePath)
	{
		return rename($filePath, $newFilePath);
	}
	
	protected function doCopy($fromFilePath, $toFilePath)
	{
		return copy($fromFilePath, $toFilePath);
	}
	
	protected function doGetFileFromResource($resource, $destFilePath = null, $allowInternalUrl = false)
	{
		$curlWrapper = new KCurlWrapper();
		$res = $curlWrapper->exec($resource, $destFilePath, null, $allowInternalUrl);
		
		$httpCode = $curlWrapper->getHttpCode();
		if (KCurlHeaderResponse::isError($httpCode))
		{
			KalturaLog::info("curl request [$resource] return with http-code of [$httpCode]");
			if ($destFilePath && file_exists($destFilePath))
				unlink($destFilePath);
			$res = false;
		}
		
		$curlWrapper->close();
		return $res;
	}
	
	protected function doFullMkdir($path, $rights = 0755, $recursive = true)
	{
		return $this->doFullMkfileDir(dirname($path), $rights, $recursive);
	}
	
	protected function doFullMkfileDir($path, $rights = 0777, $recursive = true)
	{
		if(file_exists($path))
			return true;
		$oldUmask = umask(00);
		$result = @mkdir($path, $rights, $recursive);
		umask($oldUmask);
		return $result;
	}
	
	protected function doMoveFile($from, $to, $override_if_exists = false, $copy = false)
	{
		$from = kFileBase::fixPath($from);
		$to = kFileBase::fixPath($to);
		
		if(!file_exists($from))
		{
			KalturaLog::err("Source doesn't exist [$from]");
			return false;
		}
		if(strpos($to,'\"') !== false)
		{
			KalturaLog::err("Illegal destination file [$to]");
			return false;
		}
		if($override_if_exists && is_file($to))
		{
			$this->deleteFile($to);
		}
		if(!is_dir(dirname($to)))
		{
			$this->fullMkdir($to);
		}
		return $this->copyRecursively($from,$to, !$copy);
	}
	
	protected function doDeleteFile($file_name)
	{
		$fh = fopen($file_name, 'w') or die("can't open file");
		fclose($fh);
		unlink($file_name);
		return true;
	}
	
	protected function doCopySingleFile($src, $dest, $deleteSrc)
	{
		if($deleteSrc)
		{
			// In case of move, first try to move the file before copy & unlink.
			$startTime = microtime(true);
			if(rename($src, $dest))
			{
				KalturaLog::log("rename took : ".(microtime(true) - $startTime)." [$src] to [$dest] size: ".filesize($dest));
				return true;
			}
			KalturaLog::err("Failed to rename file : [$src] to [$dest]");
		}
		if (!copy($src,$dest))
		{
			KalturaLog::err("Failed to copy file : [$src] to [$dest]");
			return false;
		}
		if ($deleteSrc && (!unlink($src)))
		{
			KalturaLog::err("Failed to delete source file : [$src]");
			return false;
		}
		return true;
	}
	
	protected function doIsDir($path)
	{
		return is_dir($path);
	}
	
	protected function doMkdir($path, $mode, $recursive)
	{
		return mkdir($path, $mode, $recursive);
	}
	
	protected function doRmdir($path)
	{
		return rmdir($path);
	}
	
	protected function doChmod($path, $mode)
	{
		return chmod($path, $mode);
	}
	
	protected function doFileSize($filename)
	{
		if(PHP_INT_SIZE >= 8)
			return filesize($filename);
		$filename = kFile::fixPath($filename);
		$url = "file://localhost/$filename";
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, true);
		$headers = curl_exec($ch);
		if(!$headers)
			KalturaLog::err('Curl error: ' . curl_error($ch));
		curl_close($ch);
		if(!$headers)
			return false;
		if (preg_match('/Content-Length: (\d+)/', $headers, $matches))
			return floatval($matches[1]);
		return false;
	}
	
	protected function doGetMaximumPartsNum()
	{
		return 2000000000;
	}
	
	protected function doGetUploadMinimumSize()
	{
		return 0;
	}
	
	protected function doGetUploadMaxSize()
	{
		return 2000000000;
	}
	
	protected function doListFiles($filePath, $pathPrefix = '')
	{
		$fileList = array();
		$path = str_ireplace(DIRECTORY_SEPARATOR, '/', $filePath);
		$handle = opendir($path);
		if ($handle)
		{
			while (false !== ($file = readdir($handle)))
			{
				if ($file != '.' && $file != '..')
				{
					$fullPath = $path.'/'.$file;
					$tmpPrefix = $pathPrefix.$file;
					
					if (is_dir($fullPath))
					{
						$tmpPrefix = $tmpPrefix.'/';
						$fileList[] = array($tmpPrefix, 'dir', self::fileSize($fullPath));
						$fileList = array_merge($fileList, self::listDir($fullPath, $tmpPrefix));
					}
					else
					{
						$fileList[] = array($tmpPrefix, 'file', self::fileSize($fullPath));
					}
				}
			}
			closedir($handle);
		}
		return $fileList;
	}
	
	protected function doIsFile($filePath)
	{
		return is_file($filePath);
	}
	
	protected function doRealPath($filePath, $getRemote = true)
	{
		return realpath($filePath);
	}

	protected function doMimeType($filePath)
	{
		if(!function_exists('mime_content_type'))
		{
			$type = null;
			exec('file -i -b ' . realpath($filePath), $type);
			
			$parts = @ explode(";", $type[0]); // can be of format text/plain;  charset=us-ascii
			
			
			return trim($parts[0]);
		}
		else
		{
			return mime_content_type($filePath);
		}
	}
	
	protected function doDumpFilePart($filePath, $range_from, $range_length)
	{
		return infraRequestUtils::dumpFilePart($filePath, $range_from, $range_length);
	}
	
	protected function doChgrp($filePath, $contentGroup)
	{
		return chgrp($filePath, $contentGroup);
	}
	
	protected function doDir($filePath)
	{
		return dir($filePath);
	}
	
	protected function doChown($path, $user, $group)
	{
		passthru("chown $user:$group $path", $ret);
		return $ret;
	}
	
	protected function doFilemtime($filePath)
	{
		return filemtime($filePath);
	}
	
	protected function doMoveLocalToShared($from, $to, $copy = false)
	{
		if($copy)
		{
			return copy($from, $to);
		}
		
		return rename($from, $to);
	}
	
	protected function doCopyDir($src, $dest, $deleteSrc)
	{
		$dir = dir($src);
		while ( false !== $entry = $dir->read () )
		{
			if ($entry == '.' || $entry == '..')
			{
				continue;
			}
			
			$newSrc = $src . DIRECTORY_SEPARATOR . $entry;
			if(kFile::isDir($newSrc))
			{
				KalturaLog::err("Copying of non-flat directories is illegal");
				return false;
			}
			
			$res = kFile::copySingleFile ($newSrc, $dest . DIRECTORY_SEPARATOR . $entry , $deleteSrc);
			if (! $res)
			{
				return false;
			}
		}
		return true;
	}
	
	protected function doCopySharedToSharedAllowed()
	{
		return true;
	}
	
}