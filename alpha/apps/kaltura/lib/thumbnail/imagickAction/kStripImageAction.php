<?php
/**
 * @package core
 * @subpackage thumbnail.imagickAction
 */

class kStripImageAction extends kImagickAction
{
	/**
	 * @return Imagick
	 */
	protected function doAction()
	{
		$this->image->stripImage();
		return $this->image;
	}

	protected function initParameterAlias()
	{
	}

	protected function validateInput()
	{
	}

	protected function extractActionParameters()
	{
	}
}