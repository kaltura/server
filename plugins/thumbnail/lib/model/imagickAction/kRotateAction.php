<?php
/**
 * @package plugins.thumbnail
 * @subpackage model.imagickAction
 */

class kRotateAction extends kImagickAction
{
	protected $degrees;
	protected $backgroundColor;

	const MAX_DEGREES = 359;
	const MIN_DEGREES = 1;

	protected function initParameterAlias()
	{
		$rotateParameterAlias = array(
			"d" => kThumbnailParameterName::DEGREES,
			"deg" => kThumbnailParameterName::DEGREES,
			"b" => kThumbnailParameterName::BACKGROUND_COLOR,
			"bg" => kThumbnailParameterName::BACKGROUND_COLOR,
			"backgroundColor" => kThumbnailParameterName::BACKGROUND_COLOR,
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
		$this->backgroundColor = self::getColorActionParameter(kThumbnailParameterName::BACKGROUND_COLOR, 'black');
	}

	protected function validateInput()
	{
		if($this->degrees < self::MIN_DEGREES || $this->degrees > self::MAX_DEGREES)
		{
			$data = array("errorString" => "Degrees must be between 0 and 360, exclusive");
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::BAD_QUERY, $data);
		}

		$this->validateColorParameter($this->backgroundColor);
	}
}