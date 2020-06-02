<?php
/**
 * @package plugins.thunmbnail
 * @subpackage model.imagickAction
 */

class kCompositeAction extends kImagickAction
{
	protected $compositeType;
	protected $channel;
	protected $x;
	protected $y;
	/* @var Imagick $compositeObject */
	protected $compositeObject;
	protected $opacity;

	const MAX_OPACITY = '100';
	const MIN_OPACITY = '1';

	protected function initParameterAlias()
	{
		$compositeParameterAlias = array(
			'ct' => kThumbnailParameterName::COMPOSITE_TYPE,
			'ch' => kThumbnailParameterName::CHANNEL,
			'op' => kThumbnailParameterName::OPACITY,
			);
		$this->parameterAlias = array_merge($this->parameterAlias, $compositeParameterAlias);
	}

	protected function extractActionParameters()
	{
		$this->x = $this->getIntActionParameter(kThumbnailParameterName::X, 0);
		$this->y = $this->getIntActionParameter(kThumbnailParameterName::Y, 0);
		$this->compositeType = $this->getIntActionParameter(kThumbnailParameterName::COMPOSITE_TYPE, Imagick::COMPOSITE_DEFAULT);
		$this->channel = $this->getIntActionParameter(kThumbnailParameterName::CHANNEL, Imagick::CHANNEL_ALL);
		$this->compositeObject = $this->getActionParameter(kThumbnailParameterName::COMPOSITE_OBJECT);
		$this->opacity = $this->getIntActionParameter(kThumbnailParameterName::OPACITY);
	}

	/**
	 * @return void
	 * @throws kThumbnailException
	 */
	protected function validateInput()
	{
		if($this->opacity && ($this->opacity < self::MIN_OPACITY || $this->opacity > self::MAX_OPACITY))
		{
			$data = array(kThumbnailErrorMessages::ERROR_STRING => kThumbnailErrorMessages::OPACITY);
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::BAD_QUERY, $data);
		}

		if(!$this->compositeObject)
		{
			$data = array(kThumbnailErrorMessages::ERROR_STRING => kThumbnailErrorMessages::MISSING_COMPOSITE);
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::BAD_QUERY, $data);
		}
	}

	/**
	 * @return Imagick
	 * @throws kThumbnailException
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
			$data = array(kThumbnailErrorMessages::ERROR_STRING => kThumbnailErrorMessages::COMPOSE_FAILED);
			throw new kThumbnailException(kThumbnailException::ACTION_FAILED, kThumbnailException::ACTION_FAILED, $data);
		}

		return $this->compositeObject;
	}

	public function canHandleCompositeObject()
	{
		return true;
	}
}