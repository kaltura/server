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
	 * Drop-shadow color as a hex code. Null means no shadow.
	 * @var string
	 */
	private $shadowColor;

	/**
	 * Drop-shadow opacity as a percentage (0–100). 0 or null means no shadow.
	 * @var int
	 */
	private $shadowOpacity;

	/**
	 * Horizontal shadow offset in pixels (positive = right).
	 * @var int
	 */
	private $shadowOffsetX;

	/**
	 * Vertical shadow offset in pixels (positive = down).
	 * @var int
	 */
	private $shadowOffsetY;

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

	/**
	 * @return string
	 */
	public function getShadowColor()
	{
		return $this->shadowColor;
	}

	/**
	 * @param string $shadowColor
	 */
	public function setShadowColor($shadowColor)
	{
		$this->shadowColor = $shadowColor;
	}

	/**
	 * @return int
	 */
	public function getShadowOpacity()
	{
		return $this->shadowOpacity;
	}

	/**
	 * @param int $shadowOpacity
	 */
	public function setShadowOpacity($shadowOpacity)
	{
		$this->shadowOpacity = $shadowOpacity;
	}

	/**
	 * @return int
	 */
	public function getShadowOffsetX()
	{
		return $this->shadowOffsetX;
	}

	/**
	 * @param int $shadowOffsetX
	 */
	public function setShadowOffsetX($shadowOffsetX)
	{
		$this->shadowOffsetX = $shadowOffsetX;
	}

	/**
	 * @return int
	 */
	public function getShadowOffsetY()
	{
		return $this->shadowOffsetY;
	}

	/**
	 * @param int $shadowOffsetY
	 */
	public function setShadowOffsetY($shadowOffsetY)
	{
		$this->shadowOffsetY = $shadowOffsetY;
	}

	public function hasBorder()
	{
		return $this->width > 0;
	}

	public function hasShadow()
	{
		return $this->shadowOpacity > 0 && $this->shadowColor !== null;
	}

	public function toArray()
	{
		return array(
			'color'         => $this->color,
			'width'         => $this->width,
			'opacity'       => $this->opacity,
			'shadowColor'   => $this->shadowColor,
			'shadowOpacity' => $this->shadowOpacity,
			'shadowOffsetX' => $this->shadowOffsetX,
			'shadowOffsetY' => $this->shadowOffsetY,
		);
	}

	public function getApiType()
	{
		return 'KalturaOverlayBorderAttributes';
	}
}
