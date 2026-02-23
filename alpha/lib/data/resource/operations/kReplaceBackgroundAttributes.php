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

	public function toArray()
	{
		return array('resource' => $this->resource);
	}

	public function getApiType()
	{
		return 'KalturaReplaceBackgroundAttributes';
	}
}
