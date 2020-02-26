<?php
/**
 * @package plugins.thumbnail
 * @subpackage api.objects
 */
class KalturaColorArray extends KalturaTypedArray
{
	public function __construct()
	{
		return parent::__construct("KalturaColor");
	}

	/**
	 * @param KalturaColorPriorityQueue $kalturaColorPriorityQueue
	 * @return KalturaColorArray
	 */
	public static function fromKalturaPriorityQueue($kalturaColorPriorityQueue)
	{
		$newArr = new KalturaColorArray();
		while(!$kalturaColorPriorityQueue->isEmpty())
		{
			$newArr[] = $kalturaColorPriorityQueue->extract();
		}

		return $newArr;
	}
}
