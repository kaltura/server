<?php
/**
 * @package core
 * @subpackage thumbnail.imagickAction
 */

class kBorderImageAction extends kImagickAction
{
	protected $backgroundColor;
	protected $width;
	protected $height;

	/**
	 * @return Imagick
	 */
	protected function doAction()
	{
		$this->image->borderImage($this->backgroundColor, $this->width, $this->height);
		return $this->image;
	}

	protected function initParameterAlias()
	{
		$parameterAlias = array(
			'w' => kThumbnailParameterName::WIDTH,
			'h' => kThumbnailParameterName::HEIGHT,
			'bg' => kThumbnailParameterName::BACKGROUND_COLOR,
		);

		$this->parameterAlias = array_merge($this->parameterAlias, $parameterAlias);
	}

	/**
	 * @throws kThumbnailException
	 */
	protected function validateInput()
	{
		if($this->width < self::MIN_DIMENSION || $this->width > self::MAX_DIMENSION)
		{
			$data = array(kThumbnailErrorMessages::ERROR_STRING => kThumbnailErrorMessages::WIDTH_DIMENSIONS);
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::BAD_QUERY, $data);
		}

		if($this->height < self::MIN_DIMENSION || $this->height > self::MAX_DIMENSION)
		{
			$data = array(kThumbnailErrorMessages::ERROR_STRING => kThumbnailErrorMessages::HEIGHT_DIMENSIONS);
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::BAD_QUERY, $data);
		}

		if($this->backgroundColor)
		{
			$this->validateColorParameter($this->backgroundColor);
		}
	}

	protected function extractActionParameters()
	{
		$this->height = $this->getFloatActionParameter(kThumbnailParameterName::HEIGHT);
		$this->width = $this->getFloatActionParameter(kThumbnailParameterName::WIDTH);
		$this->backgroundColor = $this->getColorActionParameter(kThumbnailParameterName::BACKGROUND_COLOR);
	}
}