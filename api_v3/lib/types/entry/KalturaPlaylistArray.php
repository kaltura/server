<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaPlaylistArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr = null, KalturaResponseProfileBase $responseProfile = null)
	{
		$newArr = new KalturaPlaylistArray();
		if ( $arr == null ) return $newArr;
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaPlaylist();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaPlaylist");	
	}
}