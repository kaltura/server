<?php
/**
 * @package plugins.thumbnail
 * @subpackage model.imagickAction
 */

class rotateAction extends imagickAction
{
	protected $degrees;
	protected $backgroundColor;

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
		$this->degrees = self::getIntActionParameter(kThumbnailParameterName::DEGREES, 0);
		$this->backgroundColor = self::getColorActionParameter(kThumbnailParameterName::BACKGROUND_COLOR, 'black');
	}

	protected function validateInput()
	{
		if($this->degrees < 1 || $this->degrees > 359)
		{
			throw new KalturaAPIException(KalturaThumbnailErrors::BAD_QUERY, "Degrees must be between 0 and 360, exclusive");
		}

		$this->validateColorParameter($this->backgroundColor);
	}
}