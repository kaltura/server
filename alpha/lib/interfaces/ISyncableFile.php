<?php
interface ISyncableFile 
{
	public function getSyncKey( $sub_type , $version=null);
	
	/**
	 * will return a pair of file_root and file_path
	 *
	 * @param int $sub_type
	 * @param unknown_type $version
	 */
	public function generateFilePathArr ( $sub_type , $version=null ); 

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
	
	/**
	 * @return int
	 */
	public function getPartnerId();
}
