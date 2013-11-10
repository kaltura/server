<?php
/**
 * Concat operation attributes
 *
 * @package Core
 * @subpackage model.data
 */
class kConcatAttributes extends kOperationAttributes 
{
	const SYSTEM_DEFAULT_FLAVOR_PARAMS_ID = -2;
	
	/**
	 * The resource to be concatenated
	 * @var string
	 */
	private $filePath;
	
	/* (non-PHPdoc)
	 * @see kOperationAttributes::toArray()
	 */
	public function toArray()
	{
		return array(
			'ConcatFilePath' => $this->filePath,
		);
	}
	
	/* (non-PHPdoc)
	 * @see kOperationAttributes::getApiType()
	 */
	public function getApiType()
	{
		return 'KalturaConcatAttributes';
	}

	/* (non-PHPdoc)
	 * @see kOperationAttributes::getAssetParamsId()
	 */
	public function getAssetParamsId()
	{
		return self::SYSTEM_DEFAULT_FLAVOR_PARAMS_ID;
	}
	
	/**
	 * @return string $filePath
	 */
	public function getFilePath()
	{
		return $this->filePath;
	}

	/**
	 * @param string $filePath
	 */
	public function setFilePath($filePath)
	{
		$this->filePath = $filePath;
	}
}