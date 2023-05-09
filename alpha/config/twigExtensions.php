<?php
require_once(realpath(__DIR__) . '/../../vendor/twig/autoload.php');

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class twigExtensions extends AbstractExtension
{
	public function getFunctions()
	{
		return [
			new TwigFunction('get_env', [$this, 'getEnvironmentVariable']),
			new TwigFunction('get_secret', [$this, 'getSecretFileContents']),
		];
	}

	/**
	 * Return the value of the requested environment variable.
	 *
	 * @param String $varName
	 * @return String
	 */
	public function getEnvironmentVariable($varName, $default = null)
	{
		$defaultValue = ($default === null) ? '' : $default;
		$varName = strtoupper($varName);

		if(empty($varName))
		{
			return $defaultValue;
		}

		$varValue = getenv($varName, true);
		if($varValue === false)
		{
			$varValue = $defaultValue;
		}

		return $varValue;
	}

	/**
	 * Return the content of a specific file in the secrets folder.
	 *
	 * @param String $fileName
	 * @return String
	 */
	public function getSecretFileContents($fileName, $default = null)
	{
		$defaultValue = ($default === null) ? '' : $default;
		if(empty($fileName))
		{
			return $defaultValue;
		}

		$dir = realpath(__DIR__) . '/../../../secrets';
		$fileName = realpath($dir . DIRECTORY_SEPARATOR . basename(strtoupper($fileName)));
		$content = @file_get_contents($fileName);
		if($content === false)
		{
			return $defaultValue;
		}
		return $content;
	}
}
