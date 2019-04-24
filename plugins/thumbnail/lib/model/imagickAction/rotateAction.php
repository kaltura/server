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
		$this->backgroundColor = self::getActionParameter(kThumbnailParameterName::BACKGROUND_COLOR, 'black');
	}

	protected function validateInput()
	{
		if($this->degrees < 1 || $this->degrees > 359)
		{
			throw new KalturaAPIException(KalturaThumbnailErrors::BAD_QUERY,"Degrees must be between 0 and 360, exclusive");
		}

		$image = new Imagick();
		$image->newPseudoImage(2,2);
		try
		{
			$image->setBackgroundColor($this->backgroundColor);
		}
		catch(Exception $e)
		{
			throw new KalturaAPIException(KalturaThumbnailErrors::BAD_QUERY, "Illegal value for rotate background color");
		}
	}
}