<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaMediaEntryFilterForPlaylistArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr = null, IResponseProfile $responseProfile = null)
	{
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaMediaEntryFilterForPlaylist();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaMediaEntryFilterForPlaylist" );
	}
}
