<?php
/**
 * @package core
 * @subpackage thumbnail.imagickAction
 */

class kFilterAction extends kImagickAction
{
	protected $filterType;

	/**
	 * @return Imagick
	 */
	protected function doAction()
	{
		switch ($this->filterType)
		{
			case kFilterType::BLUE_SHIFT:
				$this->image->blueShiftImage();
				break;
			case kFilterType::CHARCOAL:
				$this->image->charcoalImage(5,1);
				break;
			case kFilterType::CONTRAST:
				$this->image->contrastImage(1);
				break;
			case kFilterType::EDGE:
				$this->image->edgeImage(0);
				break;
			case kFilterType::OIL:
				$this->image->oilPaintImage(5);
				break;
			case kFilterType::POLAROID:
				$this->image->polaroidImage(new ImagickDraw(), 20);
				break;
			case kFilterType::RAISE:
				$this->image->raiseImage(15,15, 10, 10, 1);
				break;
			case kFilterType::SEPIA:
				$this->image->sepiaToneImage(80);
				break;
			case kFilterType::SHADE:
				$this->image->shadeImage(true, 45, 20);
				break;
			case kFilterType::SOLARIZE:
				$this->image->solarizeImage(0.2 * \Imagick::getQuantum());
				break;
			case kFilterType::VIGNETTE:
				$this->image->vignetteImage(10, 10, 10, 10);
				break;
			case kFilterType::WAVE:
				$this->image->waveImage(5, 20);
				break;
		}

		return $this->image;
	}

	protected function initParameterAlias()
	{
		$filterParameterAlias = array(
			'f' => kThumbnailParameterName::FILTER_TYPE,
			'ft' => kThumbnailParameterName::FILTER_TYPE,
		);

		$this->parameterAlias = array_merge($this->parameterAlias, $filterParameterAlias);
	}

	protected function validateInput()
	{
		if(!kEnumHelper::isValidValue("kFilterType", $this->filterType))
		{
			$data = array(kThumbnailErrorMessages::ERROR_STRING => kThumbnailErrorMessages::ILLEGAL_ENUM_VALUE);
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::BAD_QUERY, $data);
		}
	}

	protected function extractActionParameters()
	{
		$this->filterType = strtolower($this->getActionParameter(kThumbnailParameterName::FILTER_TYPE));
	}
}