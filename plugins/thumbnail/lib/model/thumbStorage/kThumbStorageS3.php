<?php
/**
 * @package plugins.thumbnail
 * @subpackage model
 */

class kThumbStorageS3 extends kThumbStorageBase implements kThumbStorageInterface
{
	protected $s3Mgr;
	function __construct()
	{
		$options =  $this->setS3Options();
		$this->s3Mgr = kFileTransferMgr::getInstance(StorageProfileProtocol::S3 ,$options);
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


	public function saveFile($fileName , $content)
	{
		$this->login();
		$path = $this->getFullPath($fileName);
		$this->s3Mgr->mkDir(dirname($path));
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
		$this->url = self::$configParams[self::CONF_URL].$path;
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