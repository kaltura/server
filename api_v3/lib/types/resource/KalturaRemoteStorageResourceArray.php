<?php
/**
 * Used to ingest media that is available on remote server and accessible using the supplied URL, the media file won't be downloaded but a file sync object of URL type will point to the media URL.
 *
 * @package api
 * @subpackage objects
 */
class KalturaRemoteStorageResourceArray extends KalturaTypedArray
{
	/**
	 * @param array<kRemoteStorageResource> $arr
	 * @return KalturaRemoteStorageResourceArray
	 */
	public static function fromDbArray(array $arr, KalturaResponseProfileBase $responseProfile = null)
	{
		$newArr = new KalturaRemoteStorageResourceArray();
		foreach($arr as $obj)
		{
			$nObj = new KalturaRemoteStorageResource();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}

		return $newArr;
	}
	
	public function __construct()
	{
		parent::__construct("KalturaRemoteStorageResource");
	}
}