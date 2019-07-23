<?php
/**
 * @package core
 * @subpackage thumbnail.imagickAction
 */

abstract class kImagickAction extends kThumbnailAction
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
	public function execute($image, &$transformationParameters)
	{
		$this->image = $image;
		$this->transformationParameters = $transformationParameters;
		$this->extractActionParameters();
		$this->validateInput();
		return $this->doAction();
	}

	public function canHandleCompositeObject()
	{
		return false;
	}

	public function getActionType()
	{
		return kActionType::IMAGICK;
	}

}