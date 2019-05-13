<?php
/**
 * @package plugins.thumbnail
 * @subpackage model.imagickAction
 */

class rotateAction extends imagickAction
{
	protected $degrees;
	protected $backgroundColor;

	const MAX_DEGREES = 359;
	const MIN_DEGREES = 1;

	protected $parameterAlias = array(
		"d" => kThumbnailParameterName::DEGREES,
		"deg" => kThumbnailParameterName::DEGREES,
		"b" => kThumbnailParameterName::BACKGROUND_COLOR,
		"bg" => kThumbnailParameterName::BACKGROUND_COLOR,
	);

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
		$this->degrees = self::getIntActionParameter(kThumbnailParameterName::DEGREES, self::MIN_DEGREES);
		$this->backgroundColor = self::getColorActionParameter(kThumbnailParameterName::BACKGROUND_COLOR, 'black');
	}

	protected function validateInput()
	{
		if($this->degrees < self::MIN_DEGREES || $this->degrees > self::MAX_DEGREES)
		{
			throw new KalturaAPIException(KalturaThumbnailErrors::BAD_QUERY, "Degrees must be between 0 and 360, exclusive");
		}

		$this->validateColorParameter($this->backgroundColor);
	}
}