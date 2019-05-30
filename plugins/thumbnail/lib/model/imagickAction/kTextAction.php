<?php
/**
 * @package plugins.thumbnail
 * @subpackage model.imagickAction
 */

class kTextAction extends kImagickAction
{
	protected $x;
	protected $y;
	protected $font;
	protected $font_size;
	protected $text;
	protected $angle;
	protected $strokeColor;
	protected $fillColor;
	protected $maxWidth;
	protected $maxHeight;

	protected function initParameterAlias()
	{
		$textParameterAlias = array(
			"f" => kThumbnailParameterName::FONT,
			"fs" => kThumbnailParameterName::FONT_SIZE,
			"fontsize" => kThumbnailParameterName::FONT_SIZE,
			"t" => kThumbnailParameterName::TEXT,
			"txt" => kThumbnailParameterName::TEXT,
			"a" => kThumbnailParameterName::ANGLE,
			"sc" => kThumbnailParameterName::STROKE_COLOR,
			"strokecolor" => kThumbnailParameterName::STROKE_COLOR,
			"fc" => kThumbnailParameterName::FILL_COLOR,
			"fillcolor" => kThumbnailParameterName::FILL_COLOR,
			"w" => kThumbnailParameterName::WIDTH,
			"maxwidth" => kThumbnailParameterName::WIDTH,
			"mw" => kThumbnailParameterName::WIDTH,
			"h" => kThumbnailParameterName::HEIGHT,
			"maxheight" => kThumbnailParameterName::HEIGHT,
			"mh" => kThumbnailParameterName::HEIGHT,
		);

		$this->parameterAlias = array_merge($this->parameterAlias, $textParameterAlias);
	}

	protected function extractActionParameters()
	{
		$this->x = $this->getIntActionParameter(kThumbnailParameterName::X, 0);
		$this->y = $this->getIntActionParameter(kThumbnailParameterName::Y, 10);
		$this->font_size = $this->getFloatActionParameter(kThumbnailParameterName::FONT_SIZE, 10);
		$this->text = $this->getActionParameter(kThumbnailParameterName::TEXT);
		$this->text = trim(urldecode($this->text));
		$this->font = $this->getActionParameter(kThumbnailParameterName::FONT, 'Courier');
		$this->angle = $this->getFloatActionParameter(kThumbnailParameterName::ANGLE, 0);
		$this->strokeColor = $this->getColorActionParameter(kThumbnailParameterName::STROKE_COLOR, "black");
		$this->fillColor = $this->getColorActionParameter(kThumbnailParameterName::FILL_COLOR, "black");
		$this->maxHeight = $this->getIntActionParameter(kThumbnailParameterName::HEIGHT);
		$this->maxWidth = $this->getIntActionParameter(kThumbnailParameterName::WIDTH);
	}

	protected function validateInput()
	{
		$this->validateColorParameter($this->strokeColor);
		$this->validateColorParameter($this->fillColor);
		if(!$this->text)
		{
			$data = array("errorString" => "You must supply a text for this action");
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::BAD_QUERY, $data);
		}
	}

	/**
	 * @return Imagick
	 */
	protected function doAction()
	{
		$draw = new ImagickDraw();
		$draw->setFont($this->font);
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
