<?php
/**
 * @package plugins.thumbnail
 * @subpackage model.imagickAction
 */

class textAction extends imagickAction
{
	protected $x;
	protected $y;
	protected $font;
	protected $font_size;
	protected $text;
	protected $angle;
	protected $stroke_color;
	protected $fill_color;

	protected $parameterAlias = array(
		"f" => kThumbnailParameterName::FONT,
		"fs" => kThumbnailParameterName::FONT_SIZE,
		"t" => kThumbnailParameterName::TEXT,
		"txt" => kThumbnailParameterName::TEXT,
		"a" => kThumbnailParameterName::ANGLE,
		"sc" => kThumbnailParameterName::STROKE_COLOR,
		"fc" => kThumbnailParameterName::FILL_COLOR,
	);

	protected function extractActionParameters()
	{
		$this->x = $this->getIntActionParameter(kThumbnailParameterName::X, 0);
		$this->y = $this->getIntActionParameter(kThumbnailParameterName::Y, 10);
		$this->font_size = $this->getFloatActionParameter(kThumbnailParameterName::FONT_SIZE, 10);
		$this->text = $this->getActionParameter(kThumbnailParameterName::TEXT);
		$this->text = trim(urldecode($this->text));
		$this->font = $this->getActionParameter(kThumbnailParameterName::FONT, 'Courier');
		$this->angle = $this->getFloatActionParameter(kThumbnailParameterName::ANGLE, 0);
		$this->stroke_color = $this->getColorActionParameter(kThumbnailParameterName::STROKE_COLOR, "black");
		$this->fill_color = $this->getColorActionParameter(kThumbnailParameterName::FILL_COLOR, "black");
	}

	function validateInput()
	{
		$this->validateColorParameter($this->stroke_color);
		$this->validateColorParameter($this->fill_color);
		if(!$this->text)
		{
			throw new KalturaAPIException(KalturaThumbnailErrors::BAD_QUERY, "You must supply a text for this action");
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
		$draw->setStrokeColor($this->stroke_color);
		$draw->setFillColor($this->fill_color);
		$this->image->annotateImage($draw, $this->x, $this->y, $this->angle, $this->text);
		return $this->image;
	}

	function wordWrapAnnotation($image, $draw, $text, $maxWidth)
	{
		$words = preg_split('%\s%', $text, -1, PREG_SPLIT_NO_EMPTY);
		$lines = array();
		$i = 0;
		$lineHeight = 0;

		while (count($words) > 0)
		{
			$metrics = $image->queryFontMetrics($draw, implode(' ', array_slice($words, 0, ++$i)));
			$lineHeight = max($metrics['textHeight'], $lineHeight);

			// check if we have found the word that exceeds the line width
			if ($metrics['textWidth'] > $maxWidth or count($words) < $i)
			{
				// handle case where a single word is longer than the allowed line width (just add this as a word on its own line?)
				if ($i == 1)
					$i++;

				$lines[] = implode(' ', array_slice($words, 0, --$i));
				$words = array_slice($words, $i);
				$i = 0;
			}
		}

		return array($lines, $lineHeight);
	}
}
