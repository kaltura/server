<?php

class KalturaPlaylistArray extends KalturaTypedArray
{
	public static function fromPlaylistArray ( $arr )
	{
		$newArr = new KalturaPlaylistArray();
		if ( $arr == null ) return $newArr;
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaPlaylist();
			$nObj->fromObject(  $obj );
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaPlaylist");	
	}
}