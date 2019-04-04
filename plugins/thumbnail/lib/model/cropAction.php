<?php
/**
* @package plugins.thunmbnail
* @subpackage model
*/

class cropAction extends imagickAction
{
	protected $newWidth;
	protected $newHeight;
	protected $currentWidth;
	protected $currentHeight;
	protected $gravityPoint = kCropGravityPoint::CENTER;
	protected $x;
	protected $y;

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
			KExternalErrors::dieError(KExternalErrors::BAD_QUERY, 'You cant define only crop x or crop y');
		}

		if($this->newWidth > $this->currentWidth)
		{
			KExternalErrors::dieError(KExternalErrors::BAD_QUERY, 'crop width must be smaller or equal to the current width');
		}

		if($this->newHeight > $this->currentHeight)
		{
			KExternalErrors::dieError(KExternalErrors::BAD_QUERY, 'crop height must be smaller or equal to the current height');
		}

		if($this->newWidth < 1)
		{
			KExternalErrors::dieError(KExternalErrors::BAD_QUERY, 'width must be positive');
		}

		if($this->newHeight < 1)
		{
			KExternalErrors::dieError(KExternalErrors::BAD_QUERY, 'height must be positive');
		}
	}

	/**
	 * @return Imagick
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
			}
		}

		return $this->image;
	}
}
