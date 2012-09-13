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
	protected $serializeData;
	protected $defaultExpiry;
	protected $supportExpiry;

	/**
	 * @param string $rootFolder
	 * @param string $baseFolder
	 * @param int $keyFolderChars
	 * @param bool $serializeData
	 * @param int $defaultExpiry
	 * @param bool $supportExpiry
	 * @return bool false on error
	 */
	public function init($rootFolder, $baseFolder, $keyFolderChars, $serializeData, $defaultExpiry, $supportExpiry)
	{
		$this->baseFolder = rtrim($rootFolder, '/') . '/' . rtrim($baseFolder, '/') . '/';
		$this->keyFolderChars = $keyFolderChars;
		$this->serializeData = $serializeData;
		$this->defaultExpiry = $defaultExpiry;
		$this->supportExpiry = $supportExpiry;
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
	public function get($key)
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
		if ($result === false)
			return false;
		if ($this->serializeData)
			$result = @unserialize($result);
		return $result;
	}
		
	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::set()
	 */
	public function set($key, $var, $expiry = 0)
	{
		$filePath = $this->getFilePath($key);
		if ($this->serializeData)
			$var = serialize($var);
		
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
	 * @see kBaseCacheWrapper::delete()
	 */
	public function delete($key)
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
