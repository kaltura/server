<?php
/**
 * @package core
 * @subpackage thumbnail.imagickAction
 */

class kRotateAction extends kImagickAction
{
	protected $degrees;
	protected $backgroundColor;

	const MAX_DEGREES = 359;
	const MIN_DEGREES = 1;
	const DEFAULT_BG = 'black';

	protected function initParameterAlias()
	{
		$rotateParameterAlias = array(
			'd' => kThumbnailParameterName::DEGREES,
			'deg' => kThumbnailParameterName::DEGREES,
			'b' => kThumbnailParameterName::BACKGROUND_COLOR,
			'bg' => kThumbnailParameterName::BACKGROUND_COLOR,
		);
		$this->parameterAlias = array_merge($this->parameterAlias, $rotateParameterAlias);
	}

	/**
	 * @return Imagick
	 */
	protected function doAction()
	{
		$this->image->rotateImage($this->backgroundColor, $this->degrees);
		return $this->image;
	}

	protected function extractActionParameters()
	{
		$this->degrees = self::getFloatActionParameter(kThumbnailParameterName::DEGREES, self::MIN_DEGREES);
		$this->backgroundColor = self::getColorActionParameter(kThumbnailParameterName::BACKGROUND_COLOR, self::DEFAULT_BG);
	}

	protected function validateInput()
	{
		if($this->degrees < self::MIN_DEGREES || $this->degrees > self::MAX_DEGREES)
		{
			$data = array(kThumbnailErrorMessages::ERROR_STRING => kThumbnailErrorMessages::DEGREES);
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::BAD_QUERY, $data);
		}

		$this->validateColorParameter($this->backgroundColor);
	}
}