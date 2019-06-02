<?php
/**
 * @package plugins.thumbnail
<<<<<<< HEAD
 * @subpackage model
=======
 * @subpackage model.thumbStorage
>>>>>>> bc2267b517dd08ee9a78c282f90b0796fa25ad58
 */

class kThumbStorageS3 extends kThumbStorageBase implements kThumbStorageInterface
{
<<<<<<< HEAD
=======
	/** @var s3Mgr $s3Mgr*/
>>>>>>> bc2267b517dd08ee9a78c282f90b0796fa25ad58
	protected $s3Mgr;

	function __construct()
	{
<<<<<<< HEAD
		$options =  $this->setS3Options();
		$this->s3Mgr = kFileTransferMgr::getInstance(StorageProfileProtocol::S3 ,$options);
=======
		$options = $this->setS3Options();
		if(!isset(self::$configParams[self::CONF_URL]) ||
			!isset(self::$configParams[self::CONF_USER_NAME]) ||
			!isset(self::$configParams[self::CONF_PASSWORD]))
		{
			throw new kThumbnailException(kThumbnailException::MISSING_S3_CONFIGURATION, kThumbnailException::MISSING_S3_CONFIGURATION);
		}

		$this->s3Mgr = kFileTransferMgr::getInstance(StorageProfileProtocol::S3, $options);
		$this->login();
		$this->s3Mgr->registerStreamWrapper();
>>>>>>> bc2267b517dd08ee9a78c282f90b0796fa25ad58
	}

	protected function setS3Options()
	{
		$s3Options = array();
		if (isset(self::$configParams[self::CONF_REGION]))
		{
			$s3Options['s3Region'] = self::$configParams[self::CONF_REGION];
		}
<<<<<<< HEAD
=======

>>>>>>> bc2267b517dd08ee9a78c282f90b0796fa25ad58
		return $s3Options;
	}

	protected function login()
	{
		$this->s3Mgr->login(self::$configParams[self::CONF_URL],
							self::$configParams[self::CONF_USER_NAME],
							self::$configParams[self::CONF_PASSWORD]);
	}

	public function saveFile($fileName, $content)
	{
<<<<<<< HEAD
		$this->login();
		$path = $this->getFullPath($fileName);
		kFile::fullMkdir(self::LOCAL_TMP.$path);
		kFile::safeFilePutContents(self::LOCAL_TMP.$path,$content);
		try
		{
			$this->s3Mgr->putFile($path, self::LOCAL_TMP . $path);
		}
		catch (Exception $e)
		{
			KalturaLog::debug($e->getMessage());
		}
		kFile::deleteFile(self::LOCAL_TMP.$path);
		$this->content = $content;
	}
	protected function getRenderer()
	{
		$renderer = new kRendererString($this->content ,self::MIME_TYPE );
		return $renderer;
	}

	public function loadFile($url)
	{
		$this->login();
		$path = $this->getFullPath($url);
		$this->url = self::$configParams[self::CONF_URL] . $path;
		try
		{
			$this->content = $this->s3Mgr->getFile($path);
		}
		catch (Exception $e)
		{
			return false;
		}
		return !empty($this->content);
=======
		$path = $this->getFullPath($fileName);
		$this->url = self::getUrl($path);
		if(kFile::filePutContents($this->url, $content))
		{
			$this->content = $content;
		}
		else
		{
			KalturaLog::err("Failed to save thumbnail file");
			throw new kThumbnailException(kThumbnailException::CACHE_ERROR, kThumbnailException::CACHE_ERROR);
		}
	}

	protected function getRenderer($lastModified = null)
	{
		$renderer = new kRendererString($this->content ,self::MIME_TYPE, $lastModified);
		return $renderer;
	}

	public function loadFile($url, $lastModified = null)
	{
		KalturaLog::debug("loading file from S3 " . $url);
		$path = $this->getFullPath($url);
		$this->url = self::getUrl($path);
		try
		{
			if(file_exists($this->url))
			{
				if($lastModified)
				{
					$s3lastModified = filemtime($this->url);
					if($lastModified > $s3lastModified)
					{
						KalturaLog::debug("file was created before entry changed" . $s3lastModified);
						return false;
					}
				}

				$this->content = $this->s3Mgr->getFile($path);
				return true;
			}
		}
		catch (Exception $e)
		{
			KalturaLog::debug("failed to load file " . $e->getMessage());
			throw new kThumbnailException(kThumbnailException::CACHE_ERROR, kThumbnailException::CACHE_ERROR);
		}

		return false;
	}

	public function deleteFile($url)
	{
		KalturaLog::debug("deleting file from s3:" . $url);
		return $this->s3Mgr->delFile($url);
	}

	protected static function getUrl($path)
	{
		return 's3://' . $path;
>>>>>>> bc2267b517dd08ee9a78c282f90b0796fa25ad58
	}
}