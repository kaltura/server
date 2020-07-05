<?php

/**
 * Created by IntelliJ IDEA.
 * User: David.Winder
 * Date: 10/22/2017
 * Time: 5:10 PM
 */
class kFileBase 
{
	protected static $storageTypeMap = null;
	
    /**
     * Lazy saving of file content to a temporary path, the file will exist in this location until the temp files are purged
     * @param string $fileContent
     * @param string $prefix
     * @param string $postfix
     * @param integer $permission
     * @return string path to temporary file location
     */
    public static function createTempFile($fileContent, $prefix = '' , $permission = null, $postfix = null)
    {
        $tempDirectory = sys_get_temp_dir();
        if ($postfix )
            $tempDirectory .= ".$postfix";
        $fileLocation = tempnam($tempDirectory, $prefix);
        if (self::safeFilePutContents($fileLocation, $fileContent, $permission))
            return $fileLocation;
    }

    public static function filePutContents($filename, $data, $flags = 0, $context = null)
	{
		if(kFile::isSharedPath($filename))
		{
			$kSharedFsMgr = kSharedFileSystemMgr::getInstanceFromPath($filename);
			return $kSharedFsMgr->putFileContent($filename, $data, $flags, $context);
		}
		
		return file_put_contents($filename, $data, $flags, $context);
	}

    public static function safeFilePutContents($filePath, $var, $mode=null)
    {
        // write to a temp file and then rename, so that the write will be atomic
        $tempFilePath = tempnam(dirname($filePath), basename($filePath));
        if (file_put_contents($tempFilePath, $var) === false)
            return false;
        if (rename($tempFilePath, $filePath) === false)
        {
            @unlink($tempFilePath);
            return false;
        }
        if($mode)
        {
            self::chmod($filePath, $mode);
        }
        return true;
    }

    public static function chmod($filePath, $mode)
    {
	    if(kFile::isSharedPath($filePath))
	    {
		    $kSharedFsMgr = kSharedFileSystemMgr::getInstanceFromPath($filePath);
		    return $kSharedFsMgr->chmod($filePath, $mode);
	    }
	    
        return chmod($filePath, $mode);
    }
	
	public static function chown($filePath, $user, $group)
	{
		if(kFile::isSharedPath($filePath))
		{
			$kSharedFsMgr = kSharedFileSystemMgr::getInstanceFromPath($filePath);
			return $kSharedFsMgr->chown($filePath,  $user, $group);
		}
		
		passthru("chown $user:$group $filePath", $ret);
		
		return $ret;
	}

    public static function readLastBytesFromFile($file_name, $bytes = 1024)
    {
        $fh = fopen($file_name, 'r');
        $data = "";
        if($fh)
        {
            fseek($fh, - $bytes, SEEK_END);
            $data = fread($fh, $bytes);
        }

        fclose($fh);

        return $data;
    }

    static public function getFileNameNoExtension($file_name, $include_file_path = false)
    {
        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $base_file_name = pathinfo($file_name, PATHINFO_BASENAME);
        $len = strlen($base_file_name) - strlen($ext);
        if(strlen($ext) > 0)
        {
            $len = $len - 1;
        }

        $res = substr($base_file_name, 0, $len);
        if($include_file_path)
        {
            $res = pathinfo($file_name, PATHINFO_DIRNAME) . "/" . $res;
        }
        return $res;
    }

    static public function replaceExt($file_name, $new_ext)
    {
        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $len = strlen($ext);
        return ($len ? substr($file_name, 0, - strlen($ext)) : $file_name) . $new_ext;
    }

    // make sure the file is closed , then remove it
    public static function deleteFile($file_name)
    {
	    if(kFile::isSharedPath($file_name))
	    {
		    $kSharedFsMgr = kSharedFileSystemMgr::getInstanceFromPath($file_name);
		    return $kSharedFsMgr->unlink($file_name);
	    }
    	
        $fh = fopen($file_name, 'w') or die("can't open file");
        fclose($fh);
        unlink($file_name);
    }

    /**
     * creates a dirctory using the specified path
     * @param string $path
     * @param int $rights
     * @param bool $recursive
     * @return bool true on success or false on failure.
     */
    public static function fullMkfileDir ($path, $rights = 0777, $recursive = true)
    {
	    if(kFile::checkFileExists($path))
		    return true;

        $oldUmask = umask(00);
        $result = @mkdir($path, $rights, $recursive);
        umask($oldUmask);
        return $result;
    }
	
	/**
	 * Truncate the given with the same contnet of the given size
	 * @param string $fileName
	 * @param int $newFileSize
	 * @return bool true on success or false on failure.
	 */
	public static function truncateFile($fileName, $newFileSize)
	{
		$fp = fopen($fileName, "r+");
		if($fp === false)
		{
			return false;
		}
		
		$res = ftruncate($fp, $newFileSize);
		fclose($fp);
		
		return $res;
	}
    
    /**
     *
     * creates a directory using the dirname of the specified path
     * @param string $path
     * @param int $rights
     * @param bool $recursive
     * @return bool true on success or false on failure.
     */
    public static function fullMkdir($path, $rights = 0755, $recursive = true)
    {
	    if(kFile::isSharedPath($path))
	    {
		    $kSharedFsMgr = kSharedFileSystemMgr::getInstanceFromPath($path);
		    return $kSharedFsMgr->fullMkdir($path, $rights, $recursive);
	    }
    	
        return self::fullMkfileDir(dirname($path), $rights, $recursive);
    }
    
    /**
     * @param string $filename - path to file
     * @return float
     */
    static public function fileSize($filename)
    {
	    if(kFile::isSharedPath($filename))
	    {
		    KalturaLog::debug("Check file size for shared file [$filename]");
		    $kSharedFsMgr = kSharedFileSystemMgr::getInstanceFromPath($filename);
		    return $kSharedFsMgr->fileSize($filename);
	    }
    	
        if(PHP_INT_SIZE >= 8)
            return filesize($filename);

        $filename = str_replace('\\', '/', $filename);

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

    static public function appendToFile($file_name , $str)
    {
        file_put_contents($file_name, $str, FILE_APPEND);
    }
	
	static public function fixPath($file_name)
	{
		return str_replace(array("//", "\\"), array("/", "/"), $file_name);
	}
    
    static public function setFileContent($file_name, $content)
    {
        $res = null;
        $file_name = self::fixPath($file_name);

        // TODO - this code should be written in fullMkdir
        if(! file_exists(dirname($file_name)))
            self::fullMkdir($file_name);

        $fh = fopen($file_name, 'w');
        try
        {
            $res = fwrite($fh, $content);
        }
        catch(Exception $ex)
        {
            // whatever happens - don't forget to close $fh
        }
        fclose($fh);
        return $res;
    }
    
    static public function getFileContent($file_name, $from_byte = 0, $to_byte = -1, $mode = 'r')
    {
	    if(kFile::isSharedPath($file_name))
	    {
		    $kSharedFsMgr = kSharedFileSystemMgr::getInstanceFromPath($file_name);
		    return $kSharedFsMgr->getFileContent($file_name, $from_byte, $to_byte);
	    }
    	
        $file_name = self::fixPath($file_name);

        try
        {
            if(! file_exists($file_name))
                return NULL;
            $fh = fopen($file_name, $mode);

            if($fh == NULL)
                return NULL;
            if($from_byte > 0)
            {
                fseek($fh, $from_byte);
            }

            if($to_byte > 0)
            {
                $to_byte = min($to_byte, self::fileSize($file_name));
                $length = $to_byte - $from_byte;
            }
            else
            {
                $length = self::fileSize($file_name);
            }

            $theData = fread($fh, $length);
            fclose($fh);
            return $theData;
        }
        catch(Exception $ex)
        {
            return NULL;
        }
    }
    
    public static function mimeType($file_name)
    {
        if (!file_exists($file_name))
            return false;

        if(! function_exists('mime_content_type'))
        {
            $type = null;
            exec('file -i -b ' . realpath($file_name), $type);

            $parts = @ explode(";", $type[0]); // can be of format text/plain;  charset=us-ascii 


            return trim($parts[0]);
        }
        else
        {
            return mime_content_type($file_name);
        }
    }

    public static function copyFileMetadata($srcFile, $destFile)
    {
        @chown($destFile, fileowner($srcFile));
        @chgrp($destFile, filegroup($srcFile));
        $mode = substr(decoct(fileperms($srcFile)), -4);
        self::chmod($destFile,intval($mode,8));
    }
	
	public static function checkIsFile($path)
	{
		return is_file($path);
	}
	
	public static function getDataFromFile($src, $destFilePath = null, $maxFileSize = null, $allowInternalUrl = false)
	{
		if(!$destFilePath)
		{
			$curlWrapper = new KCurlWrapper();
			$res = $curlWrapper->exec($src, null, null, $allowInternalUrl);
			$httpCode = $curlWrapper->getHttpCode();
			if (KCurlHeaderResponse::isError($httpCode))
			{
				KalturaLog::info("curl request [$src] return with http-code of [$httpCode]");
				if ($destFilePath && file_exists($destFilePath))
					unlink($destFilePath);
				$res = false;
			}
			
			$curlWrapper->close();
			return $res;
		}
		
		if(kFile::isSharedPath($destFilePath))
		{
			$kSharedFsMgr = kSharedFileSystemMgr::getInstanceFromPath($destFilePath);
			return $kSharedFsMgr->getFileFromResource($src, $destFilePath, $allowInternalUrl);
		}
		
		return KCurlWrapper::getDataFromFile($src, $destFilePath, $maxFileSize, $allowInternalUrl);
	}
	
	public static function checkFileExists($path)
	{
		KalturaLog::debug("Check file exists [$path]");
		if(kFile::isSharedPath($path))
		{
			$kSharedFsMgr = kSharedFileSystemMgr::getInstanceFromPath($path);
			return $kSharedFsMgr->checkFileExists($path);
		}
		
		return file_exists($path);
	}
	
	public static function isFile($filePath)
	{
		if(kFile::isSharedPath($filePath))
		{
			$kSharedFsMgr = kSharedFileSystemMgr::getInstanceFromPath($filePath);
			return $kSharedFsMgr->isFile($filePath);
		}
		
		return is_file($filePath);
	}
	
	public static function realPath($filePath, $getRemote = true)
	{
		if(filter_var($filePath, FILTER_VALIDATE_URL))
		{
			return $filePath;
		}
		
		if(kFile::isSharedPath($filePath))
		{
			$kSharedFsMgr = kSharedFileSystemMgr::getInstanceFromPath($filePath);
			return $kSharedFsMgr->realPath($filePath, $getRemote);
		}
		
		return realpath($filePath);
	}
	
	public static function dumpFilePart($file_name, $range_from, $range_length)
	{
		if(kFile::isSharedPath($file_name))
		{
			$kSharedFsMgr = kSharedFileSystemMgr::getInstanceFromPath($file_name);
			return $kSharedFsMgr->dumpFilePart($file_name, $range_from, $range_length);
		}
		
		return infraRequestUtils::dumpFilePart($file_name, $range_from, $range_length);
	}
	
	public static function isDir($path)
	{
		if(kFile::isSharedPath($path))
		{
			$kSharedFsMgr = kSharedFileSystemMgr::getInstanceFromPath($path);
			return $kSharedFsMgr->isDir($path);
		}
		
		return is_dir($path);
	}
	
	public static function chgrp($filePath, $contentGroup)
	{
		if(kFile::isSharedPath($filePath))
		{
			$kSharedFsMgr = kSharedFileSystemMgr::getInstanceFromPath($filePath);
			return $kSharedFsMgr->chgrp($filePath, $contentGroup);
		}
		
		return chgrp($filePath, $contentGroup);
	}
	
	public static function unlink($filePath)
	{
		if(kFile::isSharedPath($filePath))
		{
			$kSharedFsMgr = kSharedFileSystemMgr::getInstanceFromPath($filePath);
			return $kSharedFsMgr->unlink($filePath);
		}
		
		return @unlink($filePath);
	}
	
	public static function filemtime($filePath)
	{
		if(kFile::isSharedPath($filePath))
		{
			$kSharedFsMgr = kSharedFileSystemMgr::getInstanceFromPath($filePath);
			return $kSharedFsMgr->filemtime($filePath);
		}
		
		return filemtime($filePath);
	}
	
	public static function getFolderSize($path)
	{
		if(!kFile::checkFileExists($path))
		{
			return 0;
		}
		
		if(kFile::isFile($path))
		{
			return kFile::fileSize($path);
		}
		
		$ret = 0;
		foreach(glob($path."/*") as $fn)
		{
			$ret += self::getFolderSize($fn);
		}
		
		return $ret;
	}
	
	public static function rename($from, $to)
	{
		if(kFile::isSharedPath($to))
		{
			$kSharedFsMgr = kSharedFileSystemMgr::getInstanceFromPath($to);
			return $kSharedFsMgr->rename($from, $to);
		}
		
		return rename($from, $to);
	}
	
	public static function copy($from, $to)
	{
		if(kFile::isSharedPath($to) || kFile::isSharedPath($to))
		{
			$kSharedFsMgr = kSharedFileSystemMgr::getInstanceFromPath($to);
			return $kSharedFsMgr->copy($from, $to);
		}
		
		return copy($from, $to);
	}
	
	public static function mkdir($path, $mode = 0777, $recursive = false)
	{
		if(kFile::isSharedPath($path))
		{
			$kSharedFsMgr = kSharedFileSystemMgr::getInstanceFromPath($path);
			return $kSharedFsMgr->mkdir($path, $mode, $recursive);
		}
		
		return mkdir($path, $mode, $recursive);
	}
	
	public static function rmdir($path)
	{
		if(kFile::isSharedPath($path))
		{
			$kSharedFsMgr = kSharedFileSystemMgr::getInstanceFromPath($path);
			return $kSharedFsMgr->rmdir($path);
		}
		
		return rmdir($path);
	}
	
	public static function copyDir($src, $dest, $deleteSrc)
	{
		if(kFile::isSharedPath($src))
		{
			$kSharedFsMgr = kSharedFileSystemMgr::getInstanceFromPath($src);
			return $kSharedFsMgr->copyDir($src, $dest, $deleteSrc);
		}
		
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
	
	public static function getStorageTypeMap()
	{
		if(self::$storageTypeMap)
			return self::$storageTypeMap;
		
		$dc_config = kConf::getMap("dc_config");
		self::$storageTypeMap = isset($dc_config['storage_type_map']) ? $dc_config['storage_type_map'] : array();
		return self::$storageTypeMap;
	}
	
	public static function isSharedPath($path)
	{
		$storageTypeMap = self::getStorageTypeMap();
		if(!$storageTypeMap)
			return false;
		
		foreach ( array_keys($storageTypeMap) as $pathPrefix)
		{
			if(kString::beginsWith($path, $pathPrefix))
			{
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 *  the function returns a unique file path for a file
	 *
	 * @param string $prefix the prefix for the path
	 * @param bool $isDir should we create this path as dir
	 * @return string
	 * @throws Exception
	 */
	public static function createUniqueFilePath($prefix = '', $isDir = false)
	{
		$retiresNum = kConf::get('create_path_retries', 'local', 10);
		for ($i = 0; $i < $retiresNum; $i++)
		{
			$id = md5(microtime(true) . getmypid() . uniqid(rand(), true));
			$path = $prefix . substr($id, 0, 2) . '/' . substr($id, -2) . '/' . $id;
			if ($isDir)
			{
				$path .= '/';
			}
			
			$path = kFile::fixPath($path);
			$existingObject = kFile::checkFileExists($path);
			
			if (!$existingObject)
			{
				// create empty file to avoid generating the same path for a different file
				kFile::filePutContents($path, '');
				return $path;
			}
		}
		
		throw new Exception("Could not calculate unique path for file");
	}
	
	public static function resolveFilePath($filePath)
	{
		$isRemote = false;
		$realFilePath = kFile::realPath($filePath);
		
		if(strpos($realFilePath, "http") !== false)// && kFile::checkFileExists($filePath))
		{
			$isRemote = true;
		}
		
		return array($isRemote, $realFilePath);
	}
	
	public static function getExternalFile($externalUrl, $dirName = null, $baseName = null)
	{
		if(!$dirName)
		{
			$dirName = sys_get_temp_dir();
		}
		
		if(!$baseName)
		{
			$baseName = md5(microtime(true) . getmypid() . uniqid(rand(), true));;
		}
		
		$tmpFilePath = kFile::fixPath($dirName . DIRECTORY_SEPARATOR . $baseName);
		
		$res = null;
		try
		{
			$res = kFile::getDataFromFile($externalUrl, $tmpFilePath, null, true);
			if ($res)
			{
				$res = $tmpFilePath;
				KalturaLog::info("Succeeded to retrieve asset content from [$externalUrl] to [$tmpFilePath]");
			}
			else
			{
				KalturaLog::info("Failed to retrieve asset content from [$externalUrl] to [$tmpFilePath]");
			}
		}
		catch(Exception $e)
		{
			KalturaLog::info("Can't serve fetch from [$externalUrl] " . $e->getMessage());
		}
		
		return $res;
	}
}
