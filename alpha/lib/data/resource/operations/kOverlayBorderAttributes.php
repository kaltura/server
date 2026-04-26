<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kOverlayBorderAttributes
{
	/**
	 * Border color as a hex code (e.g. #FFFFFF or FFFFFF).
	 * @var string
	 */
	private $color;

	/**
	 * Border width in pixels (0–20).
	 * @var int
	 */
	private $width;

	/**
	 * Border opacity as a percentage (0–100).
	 * @var int
	 */
	private $opacity;

	/**
	 * @return string
	 */
	public function getColor()
	{
		return $this->color;
	}

	/**
	 * @param string $color
	 */
	public function setColor($color)
	{
		$this->color = $color;
	}

	/**
	 * @return int
	 */
	public function getWidth()
	{
		return $this->width;
	}

	/**
	 * @param int $width
	 */
	public function setWidth($width)
	{
		$this->width = $width;
	}

	/**
	 * @return int
	 */
	public function getOpacity()
	{
		return $this->opacity;
	}

	/**
	 * @param int $opacity
	 */
	public function setOpacity($opacity)
	{
		$this->opacity = $opacity;
	}

	public function hasBorder()
	{
		return $this->width > 0;
	}

	public function toArray()
	{
		return array(
			'color'   => $this->color,
			'width'   => $this->width,
			'opacity' => $this->opacity,
		);
	}

	public function getApiType()
	{
		return 'KalturaOverlayBorderAttributes';
	}
}
