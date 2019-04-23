<?php
/**
* @package plugins.thumbnail
* @subpackage model.imagickAction
*/

class cropAction extends imagickAction
{
	protected $newWidth;
	protected $newHeight;
	protected $currentWidth;
	protected $currentHeight;
	protected $gravityPoint;
	protected $x;
	protected $y;

	protected $parameterAlias = array(
		"gp" => kThumbnailParameterName::GRAVITY_POINT,
		"w" => kThumbnailParameterName::WIDTH,
		"h" => kThumbnailParameterName::HEIGHT,
	);

	protected function extractActionParameters($transformationParameters)
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
			throw new KalturaAPIException(KalturaThumbnailErrors::BAD_QUERY, 'You cant define only crop x or crop y');
		}

		if($this->newWidth > $this->currentWidth)
		{
			throw new KalturaAPIException(KalturaThumbnailErrors::BAD_QUERY, 'crop width must be smaller or equal to the current width');
		}

		if($this->newHeight > $this->currentHeight)
		{
			throw new KalturaAPIException(KalturaThumbnailErrors::BAD_QUERY, 'crop height must be smaller or equal to the current height');
		}

		if($this->newWidth < 1)
		{
			throw new KalturaAPIException(KalturaThumbnailErrors::BAD_QUERY, 'width must be positive');
		}

		if($this->newHeight < 1)
		{
			throw new KalturaAPIException(KalturaThumbnailErrors::BAD_QUERY, 'height must be positive');
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
