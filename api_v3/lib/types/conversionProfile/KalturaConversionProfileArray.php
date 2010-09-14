<?php
class KalturaConversionProfileArray extends KalturaTypedArray
{
	public static function fromDbArray($arr)
	{
		$newArr = new KalturaConversionProfileArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
    		$nObj = new KalturaConversionProfile();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaConversionProfile");	
	}
	
	public function loadFlavorParamsIds()
	{
		$conversionProfileIds = array();
		
		// find all profile ids
		foreach($this as $conversionProfile)
		{
			$conversionProfileIds[] = $conversionProfile->id;
		}
		// get all params relations by the profile ids list
		$c = new Criteria();
		$c->add(flavorParamsConversionProfilePeer::CONVERSION_PROFILE_ID, $conversionProfileIds, Criteria::IN);
		$allParams = flavorParamsConversionProfilePeer::doSelect($c);
		$paramsIdsPerProfile = array();
		
		// group the params by profile id
		foreach($allParams as $item)
		{
			if (!isset($paramsIdsPerProfile[$item->getConversionProfileId()]))
				$paramsIdsPerProfile[$item->getConversionProfileId()] = array();
			$paramsIdsPerProfile[$item->getConversionProfileId()][] = $item->getFlavorParamsId();
		}
		
		// assign the params ids to the profiles
		foreach($this as $conversionProfile)
		{
			if (isset($paramsIdsPerProfile[$conversionProfile->id]))
				$conversionProfile->flavorParamsIds =  implode(",", $paramsIdsPerProfile[$conversionProfile->id]);
		}
	}
}