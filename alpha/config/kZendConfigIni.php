<?php

require_once(realpath(__DIR__) . '/../../vendor/twig/autoload.php');
require_once(realpath(__DIR__) . '/twigExtensions.php');

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

class kZendConfigIni extends Zend_Config_Ini
{
	private $fileSystemLoader;

	public function __construct($filename, $section = null, $options = false)
	{
		if(!$this->fileSystemLoader)
		{
			$this->fileSystemLoader = new FilesystemLoader();
		}

		$dirName = dirname($filename);
		$baseName = basename($filename);
		$this->fileSystemLoader->setPaths($dirName);

		$twig = new Environment($this->fileSystemLoader);
		$twig->addExtension(new twigExtensions());

		$tmpFileName = tempnam(sys_get_temp_dir(), $baseName);
		$renderedOutput = $twig->render($baseName);

		if (file_put_contents($tmpFileName, $renderedOutput) === false)
		{
			throw new Zend_Config_Exception("Unable to write to $tmpFileName");
		}

		try
		{
			parent::__construct($tmpFileName, $section, $options);
		}
		catch(Zend_Config_Exception $e)
		{
			unlink($tmpFileName);
			throw $e;
		}

		unlink($tmpFileName);
	}

}
