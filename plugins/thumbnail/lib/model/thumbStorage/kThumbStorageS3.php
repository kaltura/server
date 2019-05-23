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
		$this->login();
		$path = $this->getFullPath($fileName);
		kFile::fullMkdir(sys_get_temp_dir() . $path);
		kFile::safeFilePutContents(sys_get_temp_dir() . $path, $content);
		try
		{
			$this->s3Mgr->putFile($path, sys_get_temp_dir() . $path);
		}
		catch (Exception $e)
		{
			KalturaLog::debug($e->getMessage());
		}

		kFile::deleteFile(sys_get_temp_dir() . $path);
		$this->content = $content;
	}

	protected function getRenderer($lastModified = null)
	{
		$renderer = new kRendererString($this->content ,self::MIME_TYPE, $lastModified);
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
	}
}