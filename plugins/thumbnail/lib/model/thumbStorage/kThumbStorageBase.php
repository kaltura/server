<?php
/**
 * @package plugins.thumbnail
<<<<<<< HEAD
 * @subpackage model
=======
 * @subpackage model.thumbStorage
>>>>>>> bc2267b517dd08ee9a78c282f90b0796fa25ad58
 */

abstract class kThumbStorageBase
{
	protected static $configParams;
	protected static $init;
	protected static $type;
<<<<<<< HEAD

=======
>>>>>>> bc2267b517dd08ee9a78c282f90b0796fa25ad58
	protected $url;
	protected $content;
	protected $fileName;

	const MIME_TYPE = 'image/jpeg';
	const MAX_AGE = 86400;
<<<<<<< HEAD
	const LOCAL_TMP = '/tmp/';
	const DEFAULT_PATH = 'thumb';


	const CONF_SECTION_NAME = 'storage_path';
=======
	const DEFAULT_PATH = 'thumb';
	const CONF_SECTION_NAME = 'thumb_storage';
>>>>>>> bc2267b517dd08ee9a78c282f90b0796fa25ad58
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
<<<<<<< HEAD
=======

>>>>>>> bc2267b517dd08ee9a78c282f90b0796fa25ad58
		return $prefix;
	}

	protected function getPath($md5)
	{
<<<<<<< HEAD
		$path = substr($md5, 0, 3). '/' .substr($md5, 3, 3);
=======
		$path = substr($md5, 0, 3). DIRECTORY_SEPARATOR .substr($md5, 3, 3);
>>>>>>> bc2267b517dd08ee9a78c282f90b0796fa25ad58
		return $path;
	}

	protected function getFullPath($fileName)
	{
		$md5 = md5($fileName);
<<<<<<< HEAD
		$path = '/' . $this->getPrefix() . '/' . $this->getPath($md5) . '/' .$md5. '.jpg';
=======
		$path = $this->getPrefix() . DIRECTORY_SEPARATOR . $this->getPath($md5) . DIRECTORY_SEPARATOR .$md5. '.jpg';
>>>>>>> bc2267b517dd08ee9a78c282f90b0796fa25ad58
		return $path;
	}

	protected static function init()
	{
		if (self::$init)
		{
			return;
		}
<<<<<<< HEAD
=======

>>>>>>> bc2267b517dd08ee9a78c282f90b0796fa25ad58
		self::$init = true;
		self::$configParams = kConf::get(self::CONF_SECTION_NAME, 'local', array());
		if(isset(self::$configParams[self::CONF_TYPE]))
		{
			self::$type = self::$configParams[self::CONF_TYPE];
		}
	}

	public static function getInstance()
	{
<<<<<<< HEAD
		self::init();
		$storage = kThumbStorageFactory::getInstance(self::$type);
		return $storage;
	}

	protected abstract function getRenderer();

	public function render()
	{
		$renderer = $this->getRenderer();
=======
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

	protected abstract function getRenderer($lastModified = null);

	public function render($lastModified = null)
	{
		$renderer = $this->getRenderer($lastModified);
>>>>>>> bc2267b517dd08ee9a78c282f90b0796fa25ad58
		$renderer->output();
		KExternalErrors::dieGracefully();
	}
}