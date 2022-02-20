<?php
/**
 * @package plugins.game
 * @subpackage api.objects
 */
class KalturaUserScorePropertiesArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaUserScorePropertiesArray();
		
		foreach ($arr as $userScoreProperties)
		{
			$nObj = new KalturaUserScoreProperties();
			$nObj->rank = $userScoreProperties['rank'];
			$nObj->userId = $userScoreProperties['userId'];
			$nObj->score = $userScoreProperties['score'];
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaUserScoreProperties" );
	}
}
