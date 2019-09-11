<?php
/**
 * @package plugins.thumbnail
 * @subpackage model.imagickAction
 */

class kRoundCornersAction extends kImagickAction
{
	const MINIMUM_ROUNDING = 0;

	protected $xRounding;
	protected $yRounding;
	protected $strokeWidth;
	protected $displace;
	protected $sizeCorrection;
	protected $backgroundColor;

	/**
	 * @return Imagick
	 */
	protected function doAction()
	{
		$this->image->roundCorners($this->xRounding, $this->yRounding, $this->strokeWidth, $this->displace, $this->sizeCorrection);
		if($this->backgroundColor)
		{
			$background = new Imagick();
			$background->newImage($this->image->getImageWidth(), $this->image->getImageHeight(), new ImagickPixel($this->backgroundColor));
			$this->image->compositeImage($background, Imagick::COMPOSITE_DSTATOP, 0, 0);
		}

		return $this->image;
	}

	protected function initParameterAlias()
	{
		$roundCornersParameterAlias = array(
			'x' => kThumbnailParameterName::X_ROUNDING,
			'xr' => kThumbnailParameterName::X_ROUNDING,
			'y' => kThumbnailParameterName::Y_ROUNDING,
			'yr' => kThumbnailParameterName::Y_ROUNDING,
			'sw' => kThumbnailParameterName::STROKE_WIDTH,
			'd' => kThumbnailParameterName::DISPLACE,
			'sc' => kThumbnailParameterName::SIZE_CORRECTION,
			'bg' => kThumbnailParameterName::BACKGROUND_COLOR,
		);

		$this->parameterAlias = array_merge($this->parameterAlias, $roundCornersParameterAlias);
	}

	protected function validateInput()
	{
		if(!$this->xRounding > self::MINIMUM_ROUNDING)
		{
			$data = array(kThumbnailErrorMessages::ERROR_STRING => kThumbnailErrorMessages::X_ROUNDING);
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::BAD_QUERY, $data);
		}

		if(!$this->yRounding > self::MINIMUM_ROUNDING)
		{
			$data = array(kThumbnailErrorMessages::ERROR_STRING => kThumbnailErrorMessages::Y_ROUNDING);
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::BAD_QUERY, $data);
		}

		if($this->backgroundColor)
		{
			$this->validateColorParameter($this->backgroundColor);
		}
	}

	protected function extractActionParameters()
	{
		$this->xRounding = $this->getFloatActionParameter(kThumbnailParameterName::X_ROUNDING);
		$this->yRounding = $this->getFloatActionParameter(kThumbnailParameterName::Y_ROUNDING);
		$this->strokeWidth = $this->getFloatActionParameter(kThumbnailParameterName::STROKE_WIDTH, 10);
		$this->displace = $this->getFloatActionParameter(kThumbnailParameterName::DISPLACE, 5);
		$this->sizeCorrection = $this->getFloatActionParameter(kThumbnailParameterName::SIZE_CORRECTION, -6);
		$this->backgroundColor = $this->getColorActionParameter(kThumbnailParameterName::BACKGROUND_COLOR);
	}
}