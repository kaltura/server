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
	 * @var string
	 */
	protected $syncKey;
	/**
	 * @param $localFileSyncPath the $localFileSyncPath to set
	 */
	public function setLocalFileSyncPath ($localFileSyncPath)
	{
		$this->localFileSyncPath = $localFileSyncPath;
	}
	/**
	 * @param $syncKey the $syncKey to set
	 */
	public function setSyncKey ($syncKey)
	{
		$this->syncKey = $syncKey;
	}
	/**
	 * @return the $localFileSyncPath
	 */
	public function getLocalFileSyncPath()
	{
		return $this->localFileSyncPath;
	}
	/**
	 * @return the $syncKey
	 */
	public function getSyncKey()
	{
		return $this->syncKey;
	}
}