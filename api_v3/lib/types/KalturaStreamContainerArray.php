<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaStreamContainerArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaStreamContainerArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
			$stream = new KalturaStreamContainer();
			$stream->fromObject( $obj, $responseProfile );
			$newArr[] = $stream;
		}

		return $newArr;
	}

	public function __construct()
	{
		parent::__construct("KalturaStreamContainer");
	}
}