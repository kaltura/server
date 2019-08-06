<?php
/**
 * @package core
 * @subpackage thumbnail.imagickAction
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
		$this->gravityPoint = $this->getIntActionParameter(kThumbnailParameterName::GRAVITY_POINT, Imagick::GRAVITY_NORTHWEST);
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
	 * @throws kThumbnailException
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
			case Imagick::GRAVITY_NORTHWEST:
				break;
			case Imagick::GRAVITY_NORTH:
				$this->x += ($this->currentWidth / 2) - ($this->newWidth / 2);
				break;
			case Imagick::GRAVITY_NORTHEAST:
				$this->x += ($this->currentWidth) - $this->newWidth;
				break;
			case Imagick::GRAVITY_WEST:
				$this->y += ($this->currentHeight / 2) - ($this->newHeight / 2);
				break;
			case Imagick::GRAVITY_EAST:
				$this->x += $this->currentWidth- $this->newWidth;
				$this->y += ($this->currentHeight / 2)  - ($this->newHeight / 2);
				break;
			case Imagick::GRAVITY_CENTER:
				$this->x += ($this->currentWidth / 2) - ($this->newWidth / 2);
				$this->y += $this->currentHeight / 2  - ($this->newHeight / 2);
				break;
			case Imagick::GRAVITY_SOUTHWEST:
				$this->y += $this->currentHeight - $this->newHeight;
				break;
			case Imagick::GRAVITY_SOUTH:
				$this->x += ($this->currentWidth / 2) - ($this->newWidth / 2);
				$this->y += $this->currentHeight - $this->newHeight;
				break;
			case Imagick::GRAVITY_SOUTHEAST:
				$this->x += $this->currentWidth - $this->newWidth;
				$this->y += $this->currentHeight - $this->newHeight;
				break;

			default:
				break;
		}
	}
}
