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
	 * @return kContentResource $resource
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

	public function toArray()
	{
		return array();
	}

	public function getApiType()
	{
		return 'KalturaOverlayAttributes';
	}
}
