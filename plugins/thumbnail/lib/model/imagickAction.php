<?php
/**
 * @package plugins.thunmbnail
 * @subpackage model
 */

abstract class imagickAction
{
	abstract protected function extractActionParameters();
	abstract protected function validateInput();

	/* @var array $actionParameters */
	protected $actionParameters;
	/* @var Imagick $image */
	protected $image;

	/**
	 * @return Imagick
	 */
	abstract protected function doAction();

	/**
	 * @param Imagick $image
	 * @param array $actionParameters
	 * @return Imagick
	 */
	public function execute($image, $actionParameters)
	{
		$this->actionParameters = $actionParameters;
		$this->image = $image;
		$this->extractActionParameters();
		$this->validateInput();
		return $this->doAction();
	}

	protected function getActionParameter($actionParameterName, $default = null)
	{
		if(array_key_exists($actionParameterName, $this->actionParameters))
		{
			return $this->actionParameters[$actionParameterName];
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
}