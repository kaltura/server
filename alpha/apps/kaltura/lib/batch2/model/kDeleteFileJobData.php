<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kDeleteFileJobData extends kJobData
{
	/**
	 * @var string
	 */
	protected $localFileSyncPath;
	
	
	/**
	 * @param $localFileSyncPath the $localFileSyncPath to set
	 */
	public function setLocalFileSyncPath ($localFileSyncPath)
	{
		$this->localFileSyncPath = $localFileSyncPath;
	}

	/**
	 * @return the $localFileSyncPath
	 */
	public function getLocalFileSyncPath()
	{
		return $this->localFileSyncPath;
	}

}