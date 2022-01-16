<?php
/**
 * @package plugins.leaderboard
 * @subpackage api.objects
 */
class KalturaUserScorePropertiesArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaUserScorePropertiesArray();
		
		foreach ($arr as $key => $value)
		{
			$nObj = new KalturaUserScoreProperties();
			$nObj->rank = $key;
			$nObj->userId = $value['userId'];
			$nObj->score = $value['score'];
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaUserScoreProperties" );
	}
}
