<?php
/**
 * @package Core
 * @subpackage model.interfaces
 */ 
interface ISyncableFile extends IBaseObject
{
	/**
	 * @param int $sub_type
	 * @param unknown_type $version
	 * @return FileSyncKey
	 */
	public function getSyncKey( $sub_type , $version=null);

	/**
	 * @param $sub_type
	 * @param unknown_type $version
	 * @param bool $externalPath
	 * @return mixed
	 */
	public function generateFilePathArr ( $sub_type , $version = null, $externalPath = false );

	/**
	 * will return a string of the base file name
	 *
	 * @param int $sub_type
	 * @param unknown_type $version
	 */	
	public function generateFileName( $sub_type, $version = null);

	/**
	 * @return FileSync
	 */
	public function getFileSync ( );
	
	/**
	 * @param FileSync $file_sync
	 */
	public function setFileSync ( FileSync $file_sync );
}
