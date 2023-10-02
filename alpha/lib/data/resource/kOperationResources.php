<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kOperationResources extends kContentResource
{
	/**
	 * Array of resources associated with operation resource
	 * @var array<kOperationResource>
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
	 * @param array<kOperationResource> $resources
	 */
	public function setResources(array $resources)
	{
		$this->resources = $resources;
	}
}