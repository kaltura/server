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