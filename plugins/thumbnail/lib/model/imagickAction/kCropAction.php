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
		$this->gravityPoint = $this->getIntActionParameter(kThumbnailParameterName::GRAVITY_POINT, kCropGravityPoint::CENTER);
		$this->currentWidth = $this->image->getImageWidth();
		$this->currentHeight = $this->image->getImageHeight();
	}

	function validateInput()
	{
		$this->validateDimensions();
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
		if($this->x)
		{
			$this->image->cropImage($this->newWidth, $this->newHeight, $this->x, $this->y);
		}
		else
		{
			switch ($this->gravityPoint) {
				case kCropGravityPoint::TOP:
					$this->image->cropImage($this->newWidth, $this->newHeight, 0, 0);
					break;
				case kCropGravityPoint::CENTER:
					$this->image->cropImage($this->newWidth, $this->newHeight, $this->currentWidth / 2, $this->currentHeight / 2);
					break;
				default:
					$data = array(kThumbnailErrorMessages::ERROR_STRING => kThumbnailErrorMessages::ILLEGAL_GRAVITY);
					throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::BAD_QUERY, $data);
			}
		}

		return $this->image;
	}
}
