<?php
/**
 * @package plugins.thunmbnail
 * @subpackage model.imagickAction
 */

class compositeAction extends imagickAction
{
	protected $compositeType;
	protected $channel;
	protected $x;
	protected $y;
	protected $compositeObject;
	protected $opacity;

	const MAX_OPACITY = "100";
	const MIN_OPACITY = "1";

	protected $parameterAlias = array(
		"ct" => kThumbnailParameterName::COMPOSITE_TYPE,
		"ch" => kThumbnailParameterName::CHANNEL,
		"op" => kThumbnailParameterName::OPACITY,
	);

	protected function extractActionParameters()
	{
		$this->x = $this->getIntActionParameter(kThumbnailParameterName::X, 0);
		$this->y = $this->getIntActionParameter(kThumbnailParameterName::Y, 0);
		$this->compositeType = $this->getIntActionParameter(kThumbnailParameterName::COMPOSITE_TYPE, imagick::COMPOSITE_DEFAULT);
		$this->channel = $this->getIntActionParameter(kThumbnailParameterName::CHANNEL, Imagick::CHANNEL_ALL);
		$this->compositeObject = $this->getActionParameter(kThumbnailParameterName::COMPOSITE_OBJECT);
		$this->opacity = $this->getIntActionParameter(kThumbnailParameterName::OPACITY);
	}

	protected function validateInput()
	{
		if($this->opacity && ($this->opacity < self::MIN_OPACITY || $this->opacity > SELF::MAX_OPACITY))
		{
			throw new KalturaAPIException(KalturaThumbnailErrors::BAD_QUERY, 'opacity must be between 1-100');
		}

		if(!$this->compositeObject)
		{
			throw new KalturaAPIException(KalturaThumbnailErrors::BAD_QUERY, 'Missing composite object');
		}
	}

	/**
	 * @return Imagick
	 * @throws KalturaAPIException
	 */
	protected function doAction()
	{
		if($this->opacity)
		{
			$opacity = new \Imagick();
			$pseudoString = "gradient:gray({$this->opacity}%)-gray({$this->opacity}%)";
			$opacity->newPseudoImage($this->image->getImageWidth(), $this->image->getImageHeight(), $pseudoString);
			$this->image->compositeImage($opacity, \Imagick::COMPOSITE_COPYOPACITY, 0, 0);
		}

		if(!$this->compositeObject->compositeImage($this->image, $this->compositeType, $this->x, $this->y, $this->channel))
		{
			throw new KalturaAPIException(KalturaThumbnailErrors::BAD_QUERY, 'Failed to compose image');
		}

		return $this->compositeObject;
	}

	public function canHandleCompositeObject()
	{
		return true;
	}
}