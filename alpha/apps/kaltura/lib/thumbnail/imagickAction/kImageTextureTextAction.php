<?php
/**
 * @package core
 * @subpackage thumbnail.imagickAction
 */

class kImageTextureTextAction extends kImagickAction
{
	const TRANSPARENT_COLOR = 'none';
	const DEFAULT_STROKE_COLOR = 'black';
	const FORMAT_TYPE = 'png';
	const DEFAULT_ANGLE = 0;
	const DEFAULT_STROKE_WIDTH = 1;
	const DEFAULT_FONT_SIZE = 10;
	const DEFAULT_FONT_TYPE = 'Courier';

	protected $x;
	protected $y;
	protected $font;
	protected $font_size;
	protected $text;
	protected $angle;
	protected $maxWidth;
	protected $maxHeight;
	protected $strokeWidth;

	protected function extractActionParameters()
	{
		$this->x = $this->getIntActionParameter(kThumbnailParameterName::X, 0);
		$this->y = $this->getIntActionParameter(kThumbnailParameterName::Y, self::DEFAULT_FONT_SIZE);
		$this->font_size = $this->getFloatActionParameter(kThumbnailParameterName::FONT_SIZE, self::DEFAULT_FONT_SIZE);
		$this->text = $this->getActionParameter(kThumbnailParameterName::TEXT);
		$this->text = trim(urldecode($this->text));
		$this->font = $this->getActionParameter(kThumbnailParameterName::FONT, self::DEFAULT_FONT_TYPE);
		$this->angle = $this->getFloatActionParameter(kThumbnailParameterName::ANGLE, self::DEFAULT_ANGLE);
		$this->maxHeight = $this->getIntActionParameter(kThumbnailParameterName::HEIGHT);
		$this->maxWidth = $this->getIntActionParameter(kThumbnailParameterName::WIDTH);
		$this->strokeWidth = $this->getFloatActionParameter(kThumbnailParameterName::STROKE_WIDTH, self::DEFAULT_STROKE_WIDTH);
	}

	protected function initParameterAlias()
	{
		$textureTextParameterAlias = array(
			'f' => kThumbnailParameterName::FONT,
			'fs' => kThumbnailParameterName::FONT_SIZE,
			't' => kThumbnailParameterName::TEXT,
			'txt' => kThumbnailParameterName::TEXT,
			'a' => kThumbnailParameterName::ANGLE,
			'w' => kThumbnailParameterName::WIDTH,
			'mw' => kThumbnailParameterName::WIDTH,
			'h' => kThumbnailParameterName::HEIGHT,
			'mh' => kThumbnailParameterName::HEIGHT,
			'sw' => kThumbnailParameterName::STROKE_WIDTH,
		);

		$this->parameterAlias = array_merge($this->parameterAlias, $textureTextParameterAlias);
	}

	/**
	 * @return Imagick
	 */
	protected function doAction()
	{
		$draw = new ImagickDraw();
		$draw->setFont($this->font);
		$draw->setFontSize($this->font_size);
		$draw->setStrokeColor(self::DEFAULT_STROKE_COLOR);
		$draw->setStrokeWidth($this->strokeWidth);
		$image = $this->createTransparentImage();
		$image->annotateImage($draw, $this->x, $this->y, $this->angle, $this->text);
		$this->image->setImageAlphaChannel(Imagick::ALPHACHANNEL_ACTIVATE);
		$this->image->compositeImage($image, Imagick::COMPOSITE_DSTIN,0 , 0);
		$this->image->setImageFormat(self::FORMAT_TYPE);
		return $this->image;
	}

	protected function createTransparentImage()
	{
		$transparentPixel = new ImagickPixel(self::TRANSPARENT_COLOR);
		$image = new Imagick();
		$image->newImage($this->image->getImageWidth(), $this->image->getImageHeight(), $transparentPixel);
		return $image;
	}

	protected function validateInput()
	{
		if(!$this->text)
		{
			$data = array(kThumbnailErrorMessages::ERROR_STRING => kThumbnailErrorMessages::MISSING_TEXT);
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::BAD_QUERY, $data);
		}
	}
}