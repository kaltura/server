<?php

require_once(dirname(__FILE__) . '/kBaseCacheWrapper.php');

/**
 * @package infra
 * @subpackage cache
 */
class kFileSystemCacheWrapper extends kBaseCacheWrapper
{
	const EXPIRY_SUFFIX = '__expiry';

	protected $baseFolder;
	protected $keyFolderChars;
	protected $defaultExpiry;
	protected $supportExpiry;

	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::init()
	 */
	protected function doInit($config)
	{		
		$this->baseFolder = rtrim($config['rootFolder'], '/') . '/' . rtrim($config['baseFolder'], '/') . '/';
		$this->keyFolderChars = $config['keyFolderChars'];
		$this->defaultExpiry = $config['defaultExpiry'];
		$this->supportExpiry = isset($config['supportExpiry']) ? $config['supportExpiry'] : false;
		return true;
	}
	
	/**
	 * @param string $key
	 * @return string
	 */
	protected function getFilePath($key)
	{
		$filePath = $this->baseFolder;
		$keyFileName = basename($key);
		$keyDirName = dirname($key);
		if ($keyDirName != '.')
			$filePath .= $keyDirName . '/';
		if ($this->keyFolderChars)
		{
			$dashPos = strrpos($keyFileName, '-');
			$startPos = 0;
			if ($dashPos !== false)
			{
				$startPos = $dashPos + 1;
			}
			$foldersPart = substr($keyFileName, $startPos, $this->keyFolderChars);
			for ($curPos = 0; $curPos < strlen($foldersPart); $curPos += 2)
			{
				$filePath .= substr($foldersPart, $curPos, 2) . '/';
			}
		}
		return $filePath . $keyFileName;
	}

	/**
	 * @param string $filePath
	 */
	protected static function createDirForPath($filePath)
	{
		$dirname = dirname($filePath);
		if (!is_dir($dirname))
		{
			mkdir($dirname, 0777, true);
		}
	}
		
	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::get()
	 */
	protected function doGet($key)
	{
		$filePath = $this->getFilePath($key);
		if (!file_exists($filePath))
			return false;
		
		if ($this->supportExpiry)
		{
			$cacheExpiry = self::safeFileGetContents($filePath . self::EXPIRY_SUFFIX);
			if ($cacheExpiry === false && $this->defaultExpiry)
			{
				$cacheExpiry = filemtime($filePath) + $this->defaultExpiry;		
			}
			
			if ($cacheExpiry && $cacheExpiry <= time())
			{
				self::safeUnlink($filePath);
				self::safeUnlink($filePath . self::EXPIRY_SUFFIX);
				return false;
			}
		}
		
		$result = self::safeFileGetContents($filePath);

		return $result;
	}
		
	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::set()
	 */
	protected function doSet($key, $var, $expiry = 0)
	{
		$filePath = $this->getFilePath($key);
		
		self::createDirForPath($filePath);
		
		// write the expiry if non default
		if ($this->supportExpiry && $this->defaultExpiry != $expiry)
		{
			if (self::safeFilePutContents($filePath . self::EXPIRY_SUFFIX, $expiry ? time() + $expiry : 0) === false)
				return false;
		}
		
		return self::safeFilePutContents($filePath, $var);
	}
	
	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::add()
	 */
	protected function doAdd($key, $var, $expiry = 0)
	{
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::delete()
	 */
	protected function doDelete($key)
	{
		$filePath = $this->getFilePath($key);
		if ($this->supportExpiry)
		{
			self::safeUnlink($filePath . self::EXPIRY_SUFFIX);
		}
		return self::safeUnlink($filePath);
	}

	/**
	 * @param string $filePath
	 * @param string $var
	 * @return bool
	 */
	protected static function safeFilePutContents($filePath, $var)
	{
		// write to a temp file and then rename, so that the write will be atomic
		$tempFilePath = tempnam(dirname($filePath), basename($filePath));
		if (file_put_contents($tempFilePath, $var) === false)
			return false;
		if (rename($tempFilePath, $filePath) === false)
		{
			self::safeUnlink($tempFilePath);
			return false;
		}
		return true;
	}
		
	/**
	 * @param string $filePath
	 * @return string
	 */
	protected static function safeFileGetContents($filePath)
	{
		// This function avoids the 'file does not exist' warning
		if (!file_exists($filePath))
		{
			return false;
		}
		return @file_get_contents($filePath);
	}

	/**
	 * @param string $filePath
	 * @return bool false on error
	 */
	protected static function safeUnlink($filePath)
	{
		if (!file_exists($filePath))
		{
			return false;
		}
		return @unlink($filePath);
	}
}
