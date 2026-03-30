<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kReplaceBackgroundAttributes extends kMediaCompositionAttributes
{
	/**
	 * @var kContentResource
	 */
	private $resource;

	/**
	 * @var string
	 */
	private $backgroundColorCode;

	/**
	 * @var float
	 */
	private $foregroundScalePercentage;

	/**
	 * @var kPosition
	 */
	private $foregroundPositionPercentage;

	/**
	 * @return kContentResource $resource
	 */
	public function getResource()
	{
		return $this->resource;
	}

	/**
	 * @return string
	 */
	public function getBackgroundColorCode()
	{
		return $this->backgroundColorCode;
	}

	/**
	 * @return float
	 */
	public function getForegroundScalePercentage()
	{
		return $this->foregroundScalePercentage;
	}

	/**
	 * @return kPosition
	 */
	public function getForegroundPositionPercentage()
	{
		return $this->foregroundPositionPercentage;
	}

	/**
	 * @param kContentResource $resource
	 */
	public function setResource(kContentResource $resource)
	{
		$this->resource = $resource;
	}

	/**
	 * @param string $backgroundColorCode
	 */
	public function setBackgroundColorCode($backgroundColorCode)
	{
		$this->backgroundColorCode = $backgroundColorCode;
	}

	/**
	 * @param float $foregroundScalePercentage
	 */
	public function setForegroundScalePercentage($foregroundScalePercentage)
	{
		$this->foregroundScalePercentage = $foregroundScalePercentage;
	}

	/**
	 * @param kPosition $foregroundPositionPercentage
	 */
	public function setForegroundPositionPercentage($foregroundPositionPercentage)
	{
		$this->foregroundPositionPercentage = $foregroundPositionPercentage;
	}

	public function toArray()
	{
		return array(
			'resource' => $this->resource,
			'backgroundColorCode' => $this->backgroundColorCode,
			'foregroundScalePercentage' => $this->foregroundScalePercentage,
			'foregroundPositionPercentage' => $this->foregroundPositionPercentage
		);
	}

	public function getApiType()
	{
		return 'KalturaReplaceBackgroundAttributes';
	}
}
