<?php
/**
 * @package core
 * @subpackage thumbnail.imagickAction
 */

class kResizeAction extends kImagickAction
{
	protected $newWidth;
	protected $newHeight;
	protected $currentWidth;
	protected $currentHeight;
	protected $bestFit;
	protected $filterType;
	protected $blur;
	protected $shouldUseResize;
	protected $compositeFit;
	/* @var Imagick $compositeObject */
	protected $compositeObject;

	const BEST_FIT_MIN = 1;

	protected function initParameterAlias()
	{
		$resizeParameterAlias = array(
			'w' => kThumbnailParameterName::WIDTH,
			'h' => kThumbnailParameterName::HEIGHT,
			'ft' => kThumbnailParameterName::FILTER_TYPE,
			'b' => kThumbnailParameterName::BLUR,
			'bf' => kThumbnailParameterName::BEST_FIT,
			'cf' => kThumbnailParameterName::COMPOSITE_FIT,
		);
		$this->parameterAlias = array_merge($this->parameterAlias, $resizeParameterAlias);
	}

	protected function extractActionParameters()
	{
		$this->currentWidth = $this->image->getImageWidth();
		$this->currentHeight = $this->image->getImageHeight();
		$this->newWidth = $this->getIntActionParameter(kThumbnailParameterName::WIDTH);
		$this->newHeight = $this->getIntActionParameter(kThumbnailParameterName::HEIGHT);
		$this->filterType = $this->getActionParameter(kThumbnailParameterName::FILTER_TYPE, Imagick::FILTER_LANCZOS);
		$this->blur = $this->getFloatActionParameter(kThumbnailParameterName::BLUR, 1);
		$this->bestFit = $this->getBoolActionParameter(kThumbnailParameterName::BEST_FIT);
		$this->compositeFit = $this->getBoolActionParameter(kThumbnailParameterName::COMPOSITE_FIT);
		$this->compositeObject = $this->getActionParameter(kThumbnailParameterName::COMPOSITE_OBJECT);
		$this->shouldUseResize = true;
	}

	function validateInput()
	{
		if($this->compositeFit)
		{
			if(!$this->compositeObject)
			{
				$data = array(kThumbnailErrorMessages::ERROR_STRING => kThumbnailErrorMessages::MISSING_COMPOSITE);
				throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::BAD_QUERY, $data);
			}
		}
		else
		{
			$this->validateDimensions();
		}
	}

	protected function validateDimensions()
	{
		if($this->bestFit && $this->newWidth < self::BEST_FIT_MIN)
		{
			$data = array(kThumbnailErrorMessages::ERROR_STRING => kThumbnailErrorMessages::BEST_FIT_WIDTH);
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::BAD_QUERY, $data);
		}

		if($this->bestFit && $this->newHeight < self::BEST_FIT_MIN)
		{
			$data = array(kThumbnailErrorMessages::ERROR_STRING => kThumbnailErrorMessages::BEST_FIT_HEIGHT);
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::BAD_QUERY, $data);
		}

		if(!is_numeric($this->newWidth) || $this->newWidth < self::MIN_DIMENSION || $this->newWidth > self::MAX_DIMENSION)
		{
			$data = array(kThumbnailErrorMessages::ERROR_STRING => kThumbnailErrorMessages::WIDTH_DIMENSIONS);
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::BAD_QUERY, $data);
		}

		if(!is_numeric($this->newHeight) || $this->newHeight < self::MIN_DIMENSION || $this->newHeight > self::MAX_DIMENSION)
		{
			$data = array(kThumbnailErrorMessages::ERROR_STRING => kThumbnailErrorMessages::HEIGHT_DIMENSIONS);
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::BAD_QUERY, $data);
		}
	}

	protected function doAction()
	{
		if($this->compositeFit)
		{
			$this->newHeight = $this->compositeObject->getImageHeight();
			$this->newWidth = $this->compositeObject->getImageWidth();
		}

		if($this->newHeight > $this->currentHeight && $this->newWidth > $this->currentWidth)
		{
			$this->shouldUseResize = false;
		}

		if($this->shouldUseResize)
		{
			$this->image->resizeImage($this->newWidth, $this->newHeight, $this->filterType, $this->blur, $this->bestFit);
		}
		else
		{
			$this->image->scaleImage($this->newWidth, $this->newHeight, $this->bestFit);
		}

		return $this->image;
	}
}