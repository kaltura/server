<?php
/**
 * @package plugins.thumbnail
 * @subpackage model
 */

abstract class kThumbnailAction
{
	abstract protected function extractActionParameters();
	abstract protected function validateInput();
	protected $actionParameters = array();
	protected $parameterAlias = array();

	protected function getActionParameter($actionParameterName, $default = null)
	{
		if(array_key_exists($actionParameterName, $this->actionParameters))
		{
			return $this->actionParameters[$actionParameterName];
		}

		if($this->transformationParameters && array_key_exists($actionParameterName, $this->transformationParameters))
		{
			return $this->transformationParameters[$actionParameterName];
		}

		return $default;
	}

	protected function getIntActionParameter($actionParameterName, $default = null)
	{
		$result = $this->getActionParameter($actionParameterName, $default);
		if($result)
		{
			return intval($result);
		}

		return $result;
	}

	protected function getFloatActionParameter($actionParameterName, $default = null)
	{
		$result = $this->getActionParameter($actionParameterName, $default);
		if($result)
		{
			return floatval($result);
		}

		return $result;
	}

	protected function getBoolActionParameter($actionParameterName, $default = null)
	{
		$result = $this->getActionParameter($actionParameterName, $default);
		if($result)
		{
			return true;
		}

		return false;
	}

	/**
	 * Try to get a color string from the action parameters since we cant pass # in the url if it match
	 * against a string of 6 or 3 consisting of numbers or a-f characters it will add a # to it
	 *
	 * @param $actionParameterName
	 * @param null $default
	 * @return mixed|null|string
	 */
	protected function getColorActionParameter($actionParameterName, $default = null)
	{
		$result = $this->getActionParameter($actionParameterName, $default);
		if(preg_match("([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})", $result))
		{
			$result = "#" . $result;
		}

		return $result;
	}

	/**
	 * @param string $parameterName
	 * @param string $parameterValue
	 */
	public function setActionParameter($parameterName, $parameterValue)
	{
		if(array_key_exists($parameterName, $this->parameterAlias))
		{
			$parameterName = $this->parameterAlias[$parameterName];
		}

		$this->actionParameters[$parameterName] = $parameterValue;
	}

	protected function validateColorParameter($color)
	{
		$image = new Imagick();
		$image->newPseudoImage(1,1, "plasma:fractal");
		try
		{
			$image->setBackgroundColor($color);
		}
		catch(Exception $e)
		{
			throw new KalturaAPIException(KalturaThumbnailErrors::BAD_QUERY, "Illegal value for color {$color}");
		}
	}
}