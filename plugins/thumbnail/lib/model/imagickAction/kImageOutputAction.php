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

	/**
	 * @throws kThumbnailException
	 */
	protected function validateFormat()
	{
		$validFormats = $this->image->queryFormats();
		$validFormats = array_map('strtolower', $validFormats);
		if (!in_array(strtolower($this->format), $validFormats))
		{
			$data = array(kThumbnailErrorMessages::ERROR_STRING => kThumbnailErrorMessages::NOT_VALID_IMAGE_FORMAT);
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::BAD_QUERY, $data);
		}
	}

	/**
	 * @throws kThumbnailException
	 */
	protected function validateQuality()
	{
		if (($this->quality < self::MIN_QUALITY) || $this->quality > self::MAX_QUALITY)
		{
			$data = array(kThumbnailErrorMessages::ERROR_STRING => kThumbnailErrorMessages::QUALITY_NOT_IN_RANGE);
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::BAD_QUERY, $data);
		}
	}

	protected function initParameterAlias()
	{
		$cropParameterAlias = array(
			'f' => kThumbnailParameterName::IMAGE_FORMAT,
			'q' => kThumbnailParameterName::QUALITY,
		);

		$this->parameterAlias = array_merge($this->parameterAlias, $cropParameterAlias);
	}

	protected function extractActionParameters()
	{
		$this->format = $this->getActionParameter(kThumbnailParameterName::IMAGE_FORMAT, self::DEFAULT_FORMAT);
		$this->quality = $this->getIntActionParameter(kThumbnailParameterName::QUALITY, self::DEFAULT_QUALITY);
	}

	/**
	 * @throws kThumbnailException
	 */
	protected function validateInput()
	{
		$this->validateFormat();
		$this->validateQuality();
	}

	/**
	 * @return Imagick
	 */
	protected function doAction()
	{
		$this->image->setFormat($this->format);
		$this->image->setImageCompressionQuality($this->quality);
		return $this->image;
	}
}