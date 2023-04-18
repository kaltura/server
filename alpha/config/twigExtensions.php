<?php
require_once('/opt/kaltura/app/vendor/twig/autoload.php');

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class twigExtensions extends AbstractExtension
{
	public function getFunctions()
	{
		return [
			new TwigFunction('get_env', [$this, 'getEnvironmentVariable']),
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
}
