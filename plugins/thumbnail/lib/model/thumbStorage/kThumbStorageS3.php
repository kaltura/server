<?php
/**
 * @package plugins.thumbnail
 * @subpackage model.thumbStorage
 */

class kThumbStorageS3 extends kThumbStorageBase implements kThumbStorageInterface
{
	protected $s3Mgr;

	function __construct()
	{
		$options = $this->setS3Options();
		if(!isset(self::$configParams[self::CONF_URL]) ||
			!isset(self::$configParams[self::CONF_USER_NAME]) ||
			!isset(self::$configParams[self::CONF_PASSWORD]))
		{
			throw new kThumbnailException(kThumbnailException::MISSING_S3_CONFIGURATION, kThumbnailException::MISSING_S3_CONFIGURATION);
		}

		$this->s3Mgr = kFileTransferMgr::getInstance(StorageProfileProtocol::S3, $options);
	}

	protected function setS3Options()
	{
		$s3Options = array();
		if (isset(self::$configParams[self::CONF_REGION]))
		{
			$s3Options['s3Region'] = self::$configParams[self::CONF_REGION];
		}

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
		$this->s3Mgr->registerStreamWrapper();
		$path = $this->getFullPath($fileName);
		$this->url = 's3://' . $path;
		if(file_put_contents($this->url, $content))
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
		$this->login();
		$this->s3Mgr->registerStreamWrapper();
		$path = $this->getFullPath($url);
		$this->url = 's3://' . $path;
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
}