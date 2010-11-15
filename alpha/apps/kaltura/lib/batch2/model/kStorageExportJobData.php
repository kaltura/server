<?php

/**
 * @package Core
 * @subpackage Batch
 *
 */
class kStorageExportJobData extends kStorageJobData
{
	/**
	 * @var string
	 */   	
    private $destFileSyncStoredPath;
    
	/**
	 * @var bool
	 */   	
    private $force;
    
    
	/**
	 * @return the $destFileSyncStoredPath
	 */
	public function getDestFileSyncStoredPath()
	{
		return $this->destFileSyncStoredPath;
	}

	/**
	 * @param $destFileSyncStoredPath the $destFileSyncStoredPath to set
	 */
	public function setDestFileSyncStoredPath($destFileSyncStoredPath)
	{
		$this->destFileSyncStoredPath = $destFileSyncStoredPath;
	}
	
	/**
	 * @return the $force
	 */
	public function getForce()
	{
		return $this->force;
	}

	/**
	 * @param $force the $force to set
	 */
	public function setForce($force)
	{
		$this->force = $force;
	}

}
