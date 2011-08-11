<?php
/**
 * Common interface to retrieve all remote resources
 *
 * @package Core
 * @subpackage model.data
 */
interface IRemoteStorageResource 
{
	/**
	 * @return array<kRemoteStorageResource>
	 */
	public function getResources();
}