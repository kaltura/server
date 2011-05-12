<?php
/**
 * @package plugins.contentDistribution
 * @subpackage model.data
 */
class kDistributionThumbDimensions
{
	/**
	 * @var int
	 */
	private $width;
	
	/**
	 * @var int
	 */
	private $height;
	
	/**
	 * @return string
	 */
	public function getKey()
	{
		return "{$this->width}x{$this->height}";
	}
	
	/**
	 * @return the $width
	 */
	public function getWidth()
	{
		return $this->width;
	}

	/**
	 * @return the $height
	 */
	public function getHeight()
	{
		return $this->height;
	}

	/**
	 * @param $width the $width to set
	 */
	public function setWidth($width)
	{
		$this->width = $width;
	}

	/**
	 * @param $height the $height to set
	 */
	public function setHeight($height)
	{
		$this->height = $height;
	}
}