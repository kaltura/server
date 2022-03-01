<?php
/**
 * @package plugins.game
 * @subpackage api.objects
 */
class KalturaUserScorePropertiesArray extends KalturaTypedArray
{
	public static function fromDbSingleValue($userRank, $userId, $userScore)
	{
		$newArr = new KalturaUserScorePropertiesArray();
		
		$nObj = new KalturaUserScoreProperties();
		$nObj->rank = $userRank + 1;
		$nObj->userId = $userId;
		$nObj->score = floor($userScore);
		$newArr[] = $nObj;
		
		return $newArr;
	}
	
	public static function fromDbArray(array $arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaUserScorePropertiesArray();
		
		foreach ($arr as $userScoreProperties)
		{
			$nObj = new KalturaUserScoreProperties();
			$nObj->rank = $userScoreProperties['rank'] + 1;
			$nObj->userId = $userScoreProperties['userId'];
			$nObj->score = floor($userScoreProperties['score']);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaUserScoreProperties" );
	}
}
