<?php
/**
 * @package plugins.thunmbnail
 * @subpackage model
 */

class compositeAction extends imagickAction
{
	protected $compositeType = imagick::COMPOSITE_DEFAULT;
	protected $channel;
	protected $x;
	protected $y;
	protected $compositeObject;

	protected function extractActionParameters()
	{
		$this->x = $this->getIntActionParameter(kThumbnailParameterName::X, 0);
		$this->y = $this->getIntActionParameter(kThumbnailParameterName::Y, 0);
		$this->compositeType = $this->getIntActionParameter(kThumbnailParameterName::COMPOSITE_TYPE, imagick::COMPOSITE_DEFAULT);
		$this->channel = $this->getIntActionParameter(kThumbnailParameterName::CHANNEL, Imagick::CHANNEL_DEFAULT);
		$this->compositeObject = $this->getActionParameter(kThumbnailParameterName::COMPOSITE_OBJECT);
	}

	protected function validateInput()
	{
		if(!$this->compositeObject)
		{
			KExternalErrors::dieError(KExternalErrors::BAD_QUERY, 'Missing composite object');
		}
	}

	/**
	 * @return Imagick
	 */
	protected function doAction()
	{
		if(!$this->image->compositeImage($this->compositeObject, $this->type, $this->x, $this->y))
		{
			KExternalErrors::dieError(KExternalErrors::BAD_QUERY, 'Failed to compose image');
		}

		return $this->image;
	}
}