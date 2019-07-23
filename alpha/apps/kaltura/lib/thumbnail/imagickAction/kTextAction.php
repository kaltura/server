<?php
/**
 * @package core
 * @subpackage thumbnail.imagickAction
 */

class kTextAction extends kImageTextureTextAction
{
	protected $strokeColor;
	protected $fillColor;

	protected function initParameterAlias()
	{
		parent::initParameterAlias();
		$textParameterAlias = array(
			'sc' => kThumbnailParameterName::STROKE_COLOR,
			'fc' => kThumbnailParameterName::FILL_COLOR,
		);

		$this->parameterAlias = array_merge($this->parameterAlias, $textParameterAlias);
	}

	protected function extractActionParameters()
	{
		parent::extractActionParameters();
		$this->strokeColor = $this->getColorActionParameter(kThumbnailParameterName::STROKE_COLOR, self::DEFAULT_STROKE_COLOR);
		$this->fillColor = $this->getColorActionParameter(kThumbnailParameterName::FILL_COLOR, self::DEFAULT_STROKE_COLOR);
	}

	protected function validateInput()
	{
		parent::validateInput();
		$this->validateColorParameter($this->strokeColor);
		$this->validateColorParameter($this->fillColor);
	}

	/**
	 * @return Imagick
	 */
	protected function doAction()
	{
		$draw = new ImagickDraw();
		$draw->setFont($this->font);
		$draw->setStrokeWidth($this->strokeWidth);
		$draw->setFontSize($this->font_size);
		$draw->setStrokeColor($this->strokeColor);
		$draw->setFillColor($this->fillColor);
		if($this->maxWidth || $this->maxHeight)
		{
			$wordWrapHelper = new kWordWrapHelper($this->image, $draw, $this->text, $this->maxWidth, $this->maxHeight);
			$this->text = $wordWrapHelper->calculateWordWrap();
		}

		$this->image->annotateImage($draw, $this->x, $this->y, $this->angle, $this->text);
		return $this->image;
	}
}
