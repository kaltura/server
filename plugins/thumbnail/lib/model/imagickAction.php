<?php
/**
 * @package plugins.thunmbnail
 * @subpackage model
 */

abstract class imagickAction
{
	abstract protected function extractActionParameters($actionParameters);
	abstract protected function validateInput();

	/**
	 * @param Imagick $image
	 * @return mixed
	 */
	abstract protected function doAction($image);

	/**
	 * @param Imagick $image
	 * @param array $actionParameters
	 * @return Imagick
	 */
	protected function execute($image, $actionParameters)
	{
		$this->extractInputParameter($actionParameters);
		$this->validateInput();
		return $this->doAction($image);
	}
}