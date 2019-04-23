<?php
/**
 * @package plugins.thumbnail
 * @subpackage model
 */

abstract class imagickAction extends kThumbnailAction
{
	/* @var Imagick $image */
	protected $image;

	/**
	 * @return Imagick
	 */
	abstract protected function doAction();

	/**
	 * @param Imagick $image
	 * @param array $transformationParameters
	 * @return Imagick
	 */
	public function execute($image, $transformationParameters)
	{
		$this->image = $image;
		$this->extractActionParameters($transformationParameters);
		$this->validateInput();
		return $this->doAction();
	}

	public function canHandleCompositeObject()
	{
		return false;
	}
}