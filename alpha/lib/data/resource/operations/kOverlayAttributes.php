<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kOverlayAttributes extends kMediaCompositionAttributes
{
	/**
	 * @var kContentResource
	 */
	private $resource;

	/**
	 * @var array<kMediaCompositionAttributes>
	 */
	private $resourceMediaCompositionAttributesArray;

	/**
	 * @var float
	 */
	private $marginsPercentage;

	/**
	 * @var float
	 */
	private $overlayScalePercentage;

	/**
	 * @var kMediaCompositionAlignment
	 */
	private $overlayPlacement;

	/**
	 * @var kAudioAttributes
	 */
	private $audioAttributes;

	/**
	 * @return kContentResource
	 */
	public function getResource()
	{
		return $this->resource;
	}

	/**
	 * @param kContentResource $resource
	 */
	public function setResource(kContentResource $resource)
	{
		$this->resource = $resource;
	}

	/**
	 * @return array<kMediaCompositionAttributes>
	 */
	public function getResourceMediaCompositionAttributesArray()
	{
		return $this->resourceMediaCompositionAttributesArray;
	}

	/**
	 * @param array<kMediaCompositionAttributes> $resourceMediaCompositionAttributesArray
	 */
	public function setResourceMediaCompositionAttributesArray($resourceMediaCompositionAttributesArray)
	{
		$this->resourceMediaCompositionAttributesArray = $resourceMediaCompositionAttributesArray;
	}

	/**
	 * @return kAudioAttributes
	 */
	public function getAudioAttributes()
	{
		return $this->audioAttributes;
	}

	/**
	 * @param kAudioAttributes $audioAttributes
	 */
	public function setAudioAttributes($audioAttributes)
	{
		$this->audioAttributes = $audioAttributes;
	}

	/**
	 * @return float
	 */
	public function getMarginsPercentage()
	{
		return $this->marginsPercentage;
	}

	/**
	 * @param float $marginsPercentage
	 */
	public function setMarginsPercentage($marginsPercentage)
	{
		$this->marginsPercentage = $marginsPercentage;
	}

	/**
	 * @return float
	 */
	public function getOverlayScalePercentage()
	{
		return $this->overlayScalePercentage;
	}

	/**
	 * @param float $overlayScalePercentage
	 */
	public function setOverlayScalePercentage($overlayScalePercentage)
	{
		$this->overlayScalePercentage = $overlayScalePercentage;
	}

	/**
	 * @return kMediaCompositionAlignment
	 */
	public function getOverlayPlacement()
	{
		return $this->overlayPlacement;
	}

	/**
	 * @param kMediaCompositionAlignment $overlayPlacement
	 */
	public function setOverlayPlacement($overlayPlacement)
	{
		$this->overlayPlacement = $overlayPlacement;
	}


	public function toArray()
	{
		return array(
			'resource' => $this->resource,
			'resourceMediaCompositionAttributesArray' => $this->resourceMediaCompositionAttributesArray,
			'marginsPercentage' => $this->marginsPercentage,
			'overlayScalePercentage' => $this->overlayScalePercentage,
			'overlayPlacement' => $this->overlayPlacement,
			'audioAttributes' => $this->audioAttributes
		);
	}

	public function getApiType()
	{
		return 'KalturaOverlayAttributes';
	}
}
