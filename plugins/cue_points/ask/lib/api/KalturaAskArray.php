<?php
/**
 * @package plugins.ask
 * @subpackage api.objects
 */
class KalturaAskArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaAskArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
			$kAsk = AskPlugin::getAskData($obj);
			if ( !is_null($kAsk) ) {
				$ask = new KalturaAsk();
				$ask->fromObject( $kAsk, $responseProfile );
				$newArr[] = $ask;
			}
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaAsk");
	}
}