<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kResolutionCropAttributes extends kDimensionsAttributes
{
	/**
	 * @var int
	 */
	private $targetWidth;

	/**
	 * @var int
	 */
	private $targetHeight;

	/**
	 * @return int
	 */
	public function getTargetWidth()
	{
		return $this->targetWidth;
	}

	/**
	 * @return int
	 */
	public function getTargetHeight()
	{
		return $this->targetHeight;
	}

	/**
	 * @param $targetWidth int
	 */
	public function setTargetWidth($targetWidth)
	{
		$this->targetWidth = $targetWidth;
	}

	/**
	 * @param $targetHeight int
	 */
	public function setTargetHeight($targetHeight)
	{
		$this->targetHeight = $targetHeight;
	}

	public function toArray()
	{
		return array(
			'targetWidth' => $this->targetWidth,
			'targetHeight' => $this->targetHeight,
		);
	}

	public function getApiType()
	{
		return 'KalturaResolutionCropAttributes';
	}
}
