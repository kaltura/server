<?php

require_once(dirname(__FILE__) . '/kInfraBaseCacheWrapper.php');
require_once(dirname(__FILE__) . '/../storage/shared_file_system_managers/kSharedFileSystemMgr.php');


/**
 * @package infra
 * @subpackage cache
 */
class kInfraFileSystemCacheWrapper extends kInfraBaseCacheWrapper
{
	const EXPIRY_SUFFIX = '__expiry.txt';

	protected $baseFolder;
	protected $keyFolderChars;
	protected $defaultExpiry;
	protected $supportExpiry;
	protected $sharedFileSystemType;
	
	/* @var $kSharedFSManager kSharedFileSystemMgr */
	protected $kSharedFSManager;

	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::init()
	 */
	protected function doInit($config)
	{		
		$this->baseFolder = rtrim($config['rootFolder'], '/') . '/' . rtrim($config['baseFolder'], '/') . '/';
		$this->keyFolderChars = $config['keyFolderChars'];
		$this->defaultExpiry = $config['defaultExpiry'];
		$this->supportExpiry = isset($config['supportExpiry']) ? $config['supportExpiry'] : false;
		$this->sharedFileSystemType = isset($config['sharedFSType']) ? $config['sharedFSType'] : kSharedFileSystemMgrType::LOCAL;
		
		$this->kSharedFSManager = kSharedFileSystemMgr::getInstance($this->sharedFileSystemType, $config);
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
		{
			$filePath .= $keyDirName . '/';
		}
		
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
	protected function createDirForPath($filePath)
	{
		$this->kSharedFSManager->createDirForPath($filePath);
	}
		
	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::get()
	 */
	protected function doGet($key)
	{
		$filePath = $this->getFilePath($key);
		if (!$this->kSharedFSManager->checkFileExists($filePath))
			return false;
		
		if ($this->supportExpiry)
		{
			$cacheExpiry = $this->safeFileGetContents($filePath . self::EXPIRY_SUFFIX);
			if ($cacheExpiry === false && $this->defaultExpiry)
			{
				$cacheExpiry = filemtime($filePath) + $this->defaultExpiry;		
			}
			
			if ($cacheExpiry && $cacheExpiry <= time())
			{
				$this->safeUnlink($filePath);
				$this->safeUnlink($filePath . self::EXPIRY_SUFFIX);
				return false;
			}
		}
		
		$result = $this->safeFileGetContents($filePath);

		return $result;
	}
		
	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::set()
	 */
	protected function doSet($key, $var, $expiry = 0)
	{
		$filePath = $this->getFilePath($key);
		
		$this->createDirForPath($filePath);
		
		// write the expiry if non default
		if ($this->supportExpiry && $this->defaultExpiry != $expiry)
		{
			if ($this->safeFilePutContents($filePath . self::EXPIRY_SUFFIX, $expiry ? time() + $expiry : 0) === false)
				return false;
		}
		
		return $this->safeFilePutContents($filePath, $var);
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
			$this->safeUnlink($filePath . self::EXPIRY_SUFFIX);
		}
		return $this->safeUnlink($filePath);
	}

	/**
	 * @param string $filePath
	 * @param string $var
	 * @return bool
	 */
	protected function safeFilePutContents($filePath, $var)
	{
		return $this->kSharedFSManager->putFileContentAtomic($filePath, $var);
	}
		
	/**
	 * @param string $filePath
	 * @return string
	 */
	protected function safeFileGetContents($filePath)
	{
		// This function avoids the 'file does not exist' warning
		if (!$this->kSharedFSManager->checkFileExists($filePath))
		{
			return false;
		}
		
		return $this->kSharedFSManager->getFileContent($filePath);
	}

	/**
	 * @param string $filePath
	 * @return bool false on error
	 */
	protected function safeUnlink($filePath)
	{
		if (!$this->kSharedFSManager->checkFileExists($filePath))
		{
			return false;
		}
		return $this->kSharedFSManager->unlink($filePath);
	}
}
