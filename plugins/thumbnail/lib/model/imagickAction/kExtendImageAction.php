<?php
/**
 * @package core
 * @subpackage thumbnail.imagickAction
 */

class kExtendImageAction extends kImagickAction
{
	protected $extendVector;
	protected $x;

	/**
	 * @return Imagick
	 */
	protected function doAction()
	{
		$newHeight = $this->image->getImageHeight();
		$newWidth = $this->image->getImageWidth();
		if($this->extendVector ===  kThumbnailParameterName::HEIGHT)
		{
			$newHeight = $newHeight * $this->x;
		}
		else
		{
			$newWidth = $newWidth * $this->x;
		}

		$this->image->extentImage($newWidth, $newHeight, 0 ,0);
		return $this->image;
	}

	protected function initParameterAlias()
	{
		$parameterAlias = array(
			'ev' => kThumbnailParameterName::EXTEND_VECTOR,
		);

		$this->parameterAlias = array_merge($this->parameterAlias, $parameterAlias);
	}

	/**
	 * @throws kThumbnailException
	 */
	protected function validateInput()
	{
		if ($this->x <= 0)
		{
			$data = array(kThumbnailErrorMessages::ERROR_STRING => kThumbnailErrorMessages::X_MUST_BE_NATURAL);
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::BAD_QUERY, $data);
		}

		if($this->extendVector != kThumbnailParameterName::HEIGHT && $this->extendVector != kThumbnailParameterName::WIDTH)
		{
			$data = array(kThumbnailErrorMessages::ERROR_STRING => kThumbnailErrorMessages::HEIGHT_DIMENSIONS);
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::BAD_QUERY, $data);
		}

	}

	protected function extractActionParameters()
	{
		$this->x = $this->getIntActionParameter(kThumbnailParameterName::X);
		$this->extendVector = $this->getActionParameter(kThumbnailParameterName::EXTEND_VECTOR);
	}
}