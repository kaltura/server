<?php
/**
 * @package plugins.thunmbnail
 * @subpackage model
 */

class resizeAction extends imagickAction
{
	protected function extractActionParameters($actionParameters)
	{
		// TODO: Implement extractActionParameters() method.
	}

	protected function validateInput()
	{
		// TODO: Implement validateInput() method.
	}

	protected function doAction($image)
	{
		$image->resizeImage($this->width, $this->height, $this->filterType, $this->blur, $this->bestFit);
		return $image;
	}
}