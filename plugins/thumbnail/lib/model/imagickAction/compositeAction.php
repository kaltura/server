<?php
/**
 * @package plugins.thunmbnail
 * @subpackage model.imagickAction
 */

class compositeAction extends imagickAction
{
	protected $compositeType = imagick::COMPOSITE_DEFAULT;
	protected $channel;
	protected $x;
	protected $y;
	protected $compositeObject;

	protected $parameterAlias = array(
		"ct" => kThumbnailParameterName::COMPOSITE_TYPE,
		"ch" => kThumbnailParameterName::CHANNEL,
	);

	protected function extractActionParameters($transformationParameters)
	{
		$this->x = $this->getIntActionParameter(kThumbnailParameterName::X, 0);
		$this->y = $this->getIntActionParameter(kThumbnailParameterName::Y, 0);
		$this->compositeType = $this->getIntActionParameter(kThumbnailParameterName::COMPOSITE_TYPE, imagick::COMPOSITE_DEFAULT);
		$this->channel = $this->getIntActionParameter(kThumbnailParameterName::CHANNEL, Imagick::CHANNEL_DEFAULT);
		$this->compositeObject = $this->getActionParameter(kThumbnailParameterName::COMPOSITE_OBJECT, $transformationParameters);
	}

	protected function validateInput()
	{
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
		if(!$this->image->compositeImage($this->compositeObject, $this->type, $this->x, $this->y, $this->channel))
		{
			throw new KalturaAPIException(KalturaThumbnailErrors::BAD_QUERY, 'Failed to compose image');
		}

		return $this->image;
	}

	public function canHandleCompositeObject()
	{
		return true;
	}
}