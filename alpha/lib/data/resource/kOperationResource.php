<?php
/**
 * A resource that perform operation (transcoding, clipping, cropping) before the flavor is ready.
 *
 * @package Core
 * @subpackage model.data
 */
class kOperationResource extends kContentResource 
{
	/**
	 * @var kContentResource
	 */
	private $resource;
	
	/**
	 * @var array<kOperationAttributes>
	 */
	private $operationAttributes;
	
	/**
	 * ID of alternative asset params to be used instead of the system default flavor params 
	 * @var int
	 */
	private $assetParamsId;
	
	/**
	 * @return kContentResource $resource
	 */
	public function getResource()
	{
		return $this->resource;
	}

	/**
	 * @return array $operationAttributes
	 */
	public function getOperationAttributes()
	{
		return $this->operationAttributes;
	}
	
	/**
	 * Return enum value from EntrySourceType
	 */
	public function getSourceType()
	{
		foreach($this->operationAttributes as $operationAttributes)
		{
			/* @var $operationAttributes kOperationAttributes */
			$sourceType = $operationAttributes->getSourceType();
			if($sourceType)
				return $sourceType;
		}
		
		return null;
	}

	/**
	 * @return int $assetParamsId
	 */
	public function getAssetParamsId()
	{
		if($this->assetParamsId)
			return $this->assetParamsId;
	
		foreach($this->operationAttributes as $operationAttributes)
		{
			/* @var $operationAttributes kOperationAttributes */
			$assetParamsId = $operationAttributes->getAssetParamsId();
			if($assetParamsId)
				return $assetParamsId;
		}
		
		return null;
	}

	/**
	 * @param kContentResource $resource
	 */
	public function setResource(kContentResource $resource)
	{
		$this->resource = $resource;
	}

	/**
	 * @param array $operationAttributes
	 */
	public function setOperationAttributes(array $operationAttributes)
	{
		$this->operationAttributes = $operationAttributes;
	}

	/**
	 * @param int $assetParamsId
	 */
	public function setAssetParamsId($assetParamsId)
	{
		$this->assetParamsId = $assetParamsId;
	}
}