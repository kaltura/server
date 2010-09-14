<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaMediaEntryFilterForPlaylistArray extends KalturaTypedArray
{
	public function fromArray($arr)
	{
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaMediaEntryFilterForPlaylist();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaMediaEntryFilterForPlaylist" );
	}
}
?>