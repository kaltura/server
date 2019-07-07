<?php
/**
 * @package plugins.thumbnail
 * @subpackage model.thumbStorage
 */

abstract class kThumbStorageBase
{
	protected static $configParams;
	protected static $init;
	protected static $type;
	protected $url;
	protected $content;
	protected $fileName;

	const DEFAULT_MIME_TYPE = 'image/jpeg';
	const MAX_AGE = 86400;
	const DEFAULT_PATH = 'thumb';
	const CONF_SECTION_NAME = 'thumb_storage';
	const CONF_PATH = 'path';
	const CONF_USER_NAME = 'user_name';
	const CONF_PASSWORD = 'password';
	const CONF_REGION = 'region';
	const CONF_URL = 'url';
	const CONF_TYPE = 'type';

	protected function getPrefix()
	{
		$prefix = self::DEFAULT_PATH;
		if(isset(self::$configParams[self::CONF_PATH]))
		{
			$prefix = self::$configParams[self::CONF_PATH];
		}

		return $prefix;
	}

	protected function getPath($md5)
	{
		$path = substr($md5, 0, 2). DIRECTORY_SEPARATOR .substr($md5, 2, 2);
		return $path;
	}

	protected function getFullPath($fileName)
	{
		$md5 = md5($fileName);
		$path = $this->getPrefix() . DIRECTORY_SEPARATOR . $this->getPath($md5) . DIRECTORY_SEPARATOR . $md5 . '.jpg';
		return $path;
	}

	protected static function init()
	{
		if (self::$init)
		{
			return;
		}

		self::$init = true;
		self::$configParams = kConf::get(self::CONF_SECTION_NAME, 'local', array());
		if(isset(self::$configParams[self::CONF_TYPE]))
		{
			self::$type = self::$configParams[self::CONF_TYPE];
		}
	}

	public static function getInstance()
	{
		if(kApiCache::isCacheEnabled())
		{
			self::init();
			$storage = kThumbStorageFactory::getInstance(self::$type);
		}
		else
		{
			$storage = kThumbStorageFactory::getInstance(kThumbStorageType::NONE);
		}

		return $storage;
	}

	/**
	 * @param string $type
	 * @param null|string $lastModified
	 * @return kRendererBase
	 */
	protected abstract function getRenderer($type = self::DEFAULT_MIME_TYPE, $lastModified = null);

	public function getType()
	{
		return self::DEFAULT_MIME_TYPE;
	}

	public function render($lastModified = null)
	{
		$renderer = $this->getRenderer($this->getType(), $lastModified);
		$renderer->output();
		KExternalErrors::dieGracefully();
	}
}
