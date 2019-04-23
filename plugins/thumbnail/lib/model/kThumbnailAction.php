<?php
/**
 * @package plugins.thumbnail
 * @subpackage model
 */

abstract class kThumbnailAction
{
	abstract protected function extractActionParameters($transformationParameters);
	abstract protected function validateInput();
	protected $actionParameters = array();
	protected $parameterAlias = array();

	protected function getActionParameter($actionParameterName, $default = null, $transformationParameters = null)
	{
		if($transformationParameters)
		{
			if(array_key_exists($actionParameterName, $transformationParameters))
			{
				return $transformationParameters[$actionParameterName];
			}
		}
		else if(array_key_exists($actionParameterName, $this->actionParameters))
		{
			return $this->actionParameters[$actionParameterName];
		}

		return $default;
	}

	protected function getIntActionParameter($actionParameterName, $default = null, $transformationParameters = null)
	{
		$result = $this->getActionParameter($actionParameterName, $default, $transformationParameters);
		if($result)
		{
			return intval($result);
		}

		return $result;
	}

	protected function getFloatActionParameter($actionParameterName, $default = null, $transformationParameters = null)
	{
		$result = $this->getActionParameter($actionParameterName, $default, $transformationParameters);
		if($result)
		{
			return floatval($result);
		}

		return $result;
	}

	protected function getBoolActionParameter($actionParameterName, $default = null, $transformationParameters = null)
	{
		$result = $this->getActionParameter($actionParameterName, $default, $transformationParameters);
		if($result)
		{
			return true;
		}

		return false;
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
}