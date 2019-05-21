<?php
/**
 * @package plugins.thumbnail
 * @subpackage model
 */

abstract class kThumbStorageBase
{
	protected static $configParams;
	protected static $init;
	protected static $type;

	protected $url;
	protected $content;
	protected $fileName;

	const MIME_TYPE = 'image/jpeg';
	const MAX_AGE = 86400;
	const LOCAL_TMP = '/tmp/';

	const CONF_SECTION_NAME = 'storage_path';
	const CONF_PATH = 'path';
	const CONF_USER_NAME = 'user_name';
	const CONF_PASSWORD = 'password';
	const CONF_REGION = 'region';
	const CONF_URL = 'url';

	protected function getPrefix()
	{
		$prefix = 'thumb';
		if(isset(self::$configParams[self::CONF_PATH]))
		{
			$prefix = self::$configParams[self::CONF_PATH];
		}
		return $prefix;
	}

	protected function getPath($md5)
	{
		$path = substr($md5,0,3). '/' .substr($md5,3,3);
		return $path;
	}

	protected function getFullPath($fileName)
	{
		$md5 = md5($fileName);
		$path = '/' . $this->getPrefix() . '/' . $this->getPath($md5) . '/' .$md5. '.jpg';
		return $path;
	}

	protected static function init()
	{
		if (self::$init)
		{
			return;
		}
		self::$init = true;
		self::$configParams = kConf::get('thumb_storage','local',array());
		if(isset(self::$configParams['type']))
		{
			self::$type = self::$configParams['type'];
		}
	}
	public static function getInstance()
	{
		self::init();
		$storage = kThumbStorageFactory::getInstance(self::$type);
		return $storage;
	}

	protected abstract function getRenderer();

	public function render()
	{
		$renderer = $this->getRenderer();
		$renderer->output();
		KExternalErrors::dieGracefully();
	}
}