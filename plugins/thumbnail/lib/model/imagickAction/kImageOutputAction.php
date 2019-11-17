<?php
/**
 * @package core
 * @subpackage thumbnail.imagickAction
 */

class kImageOutputAction extends kImagickAction
{
	const DEFAULT_FORMAT = 'JPEG';
	const DEFAULT_QUALITY = 75;
	const MIN_QUALITY = 20;
	const MAX_QUALITY = 100;
	protected $format;
	protected $quality;
	protected $density;

	protected function validateFormat()
	{
		$validFormats = $this->image->queryFormats();
		if (!in_array($this->format, $validFormats))
		{
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailErrorMessages::NOT_VALID_IMAGE_FORMAT);
		}
	}

	protected function validateDensity()
	{
		if ($this->density && !($this->density > 0))
		{
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailErrorMessages::DENSITY_POSITIVE);
		}
	}

	protected function validateQuality()
	{
		if (($this->quality < self::MIN_QUALITY) || $this->quality > self::MAX_QUALITY)
		{
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailErrorMessages::QUALITY_NOT_IN_RANGE);
		}
	}

	protected function initParameterAlias()
	{
		$cropParameterAlias = array(
			'f' => kThumbnailParameterName::IMAGE_FORMAT,
			'q' => kThumbnailParameterName::QUALITY,
			'd'=> kThumbnailParameterName::DENSITY,
		);
		$this->parameterAlias = array_merge($this->parameterAlias, $cropParameterAlias);
	}

	protected function extractActionParameters()
	{
		$this->format = $this->getActionParameter(kThumbnailParameterName::IMAGE_FORMAT, self::DEFAULT_FORMAT);
		$this->density = $this->getFloatActionParameter(kThumbnailParameterName::DENSITY);
		$this->quality = $this->getIntActionParameter(kThumbnailParameterName::QUALITY, self::DEFAULT_QUALITY);
	}

	protected function validateInput()
	{
		$this->validateFormat();
		$this->validateQuality();
		$this->validateDensity();
	}

	/**
	 * @return Imagick
	 */
	protected function doAction()
	{
		$this->image->setFormat($this->format);
		$this->image->setImageCompressionQuality($this->quality);
		if($this->density)
		{
			$this->image->setImageResolution($this->density, $this->density);
		}

		return $this->image;
	}
}