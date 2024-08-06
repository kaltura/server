<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kCropAspectRatio
{
	/**
	 * @var bool
	 */
	private $crop;

	/**
	 * @var float
	 */
	private $aspectRatio;

	/**
	 * @return bool
	 */
	public function getCrop()
	{
		return $this->crop;
	}

	/**
	 * @param bool $crop
	 */
	public function setCrop($crop)
	{
		$this->crop = $crop;
	}

	/**
	 * @return float
	 */
	public function getAspectRatio()
	{
		return $this->aspectRatio;
	}

	/**
	 * @param float $aspectRatio
	 */
	public function setAspectRatio($aspectRatio)
	{
		$this->aspectRatio = $aspectRatio;
	}
}
