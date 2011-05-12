<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kAssetsParamsResourceContainers extends kResource 
{
	/**
	 * Array of resources associated with asset params ids
	 * @var array
	 */
	private $resources;
	
	/**
	 * @return array
	 */
	public function getResources()
	{
		return $this->resources;
	}

	/**
	 * @param array $resources
	 */
	public function setResources(array $resources)
	{
		$this->resources = $resources;
	}

	
	
}