<?php
/**
 * @package plugins.thumbnail
 * @subpackage model.imagickAction
 */

class kCropAction extends kImagickAction
{
	protected $newWidth;
	protected $newHeight;
	protected $currentWidth;
	protected $currentHeight;
	protected $gravityPoint;
	protected $x;
	protected $y;
	protected function initParameterAlias()
	{
		$cropParameterAlias = array(
			'gp' => kThumbnailParameterName::GRAVITY_POINT,
			'w' => kThumbnailParameterName::WIDTH,
			'h' => kThumbnailParameterName::HEIGHT,
		);
		$this->parameterAlias = array_merge($this->parameterAlias, $cropParameterAlias);
	}

	protected function extractActionParameters()
	{
		$this->newWidth = $this->getIntActionParameter(kThumbnailParameterName::WIDTH);
		$this->newHeight = $this->getIntActionParameter(kThumbnailParameterName::HEIGHT);
		$this->x = $this->getIntActionParameter(kThumbnailParameterName::X);
		$this->y = $this->getIntActionParameter(kThumbnailParameterName::Y);
		$this->gravityPoint = $this->getIntActionParameter(kThumbnailParameterName::GRAVITY_POINT, kCropGravityPoint::NORTHWEST);
		$this->currentWidth = $this->image->getImageWidth();
		$this->currentHeight = $this->image->getImageHeight();
	}

	function validateInput()
	{
		$this->validateDimensions();
		$this->validateGravityPoint();
	}

	protected function validateGravityPoint()
	{
		if($this->gravityPoint < Imagick::GRAVITY_NORTHWEST || $this->gravityPoint > Imagick::GRAVITY_SOUTHEAST)
		{
			$data = array(kThumbnailErrorMessages::ERROR_STRING => kThumbnailErrorMessages::ILLEGAL_GRAVITY);
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::BAD_QUERY, $data);
		}
	}

	protected function validateDimensions()
	{
		if(($this->x && !$this->y) || (!$this->x && $this->y))
		{
			$data = array(kThumbnailErrorMessages::ERROR_STRING => kThumbnailErrorMessages::MISSING_CORP_X_Y);
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::BAD_QUERY, $data);
		}

		if($this->newWidth > $this->currentWidth)
		{
			$data = array(kThumbnailErrorMessages::ERROR_STRING => kThumbnailErrorMessages::CROP_WIDTH);
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::BAD_QUERY, $data);
		}

		if($this->newHeight > $this->currentHeight)
		{
			$data = array(kThumbnailErrorMessages::ERROR_STRING => kThumbnailErrorMessages::CROP_HEIGHT);
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::BAD_QUERY, $data);
		}

		if($this->newWidth < 1)
		{
			$data = array(kThumbnailErrorMessages::ERROR_STRING => kThumbnailErrorMessages::WIDTH_POSITIVE);
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::BAD_QUERY, $data);
		}

		if($this->newHeight < 1)
		{
			$data = array(kThumbnailErrorMessages::ERROR_STRING => kThumbnailErrorMessages::HEIGHT_POSITIVE);
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::BAD_QUERY, $data);
		}
	}

	/**
	 * @return Imagick
	 */
	protected function doAction()
	{
		$this->calculateGravityOffSet();
		$this->image->cropImage($this->newWidth, $this->newHeight, $this->x, $this->y);
		return $this->image;
	}

	protected function calculateGravityOffSet()
	{
		switch ($this->gravityPoint) {
			case kCropGravityPoint::NORTHWEST:
				break;
			case kCropGravityPoint::NORTH:
				$this->x += ($this->currentWidth / 2) - ($this->newWidth / 2);
				break;
			case kCropGravityPoint::NORTHEAST:
				$this->x += ($this->currentWidth) - $this->newWidth;
				break;
			case kCropGravityPoint::WEST:
				$this->y += ($this->currentHeight / 2) - ($this->newHeight / 2);
				break;
			case kCropGravityPoint::EAST:
				$this->x += $this->currentWidth- $this->newWidth;
				$this->y += ($this->currentHeight / 2)  - ($this->newHeight / 2);
				break;
			case kCropGravityPoint::CENTER:
				$this->x += ($this->currentWidth / 2) - ($this->newWidth / 2);
				$this->y += $this->currentHeight / 2  - ($this->newHeight / 2);
				break;
			case kCropGravityPoint::SOUTHWEST:
				$this->y += $this->currentHeight - $this->newHeight;
				break;
			case kCropGravityPoint::SOUTH:
				$this->x += ($this->currentWidth / 2) - ($this->newWidth / 2);
				$this->y += $this->currentHeight - $this->newHeight;
				break;
			case kCropGravityPoint::SOUTHEAST:
				$this->x += $this->currentWidth - $this->newWidth;
				$this->y += $this->currentHeight - $this->newHeight;
				break;
			default:
				break;
		}
	}
}