<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kAspectRatioScaleAttributes extends kDimensionsAttributes
{
	/**
	 * @var float
	 */
	private $aspectRatio;

	/**
	 * @return float
	 */
	public function getAspectRatio()
	{
		return $this->aspectRatio;
	}

	/**
	 * @param $aspectRatio float
	 */
	public function setAspectRatio($aspectRatio)
	{
		$this->aspectRatio = $aspectRatio;
	}

	public function toArray()
	{
		return array('aspectRatio' => $this->aspectRatio);
	}

	public function getApiType()
	{
		return 'KalturaAspectRatioScaleAttributes';
	}
}
