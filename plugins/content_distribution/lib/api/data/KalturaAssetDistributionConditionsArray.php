<?php

/**
 * Array of asset distribution conditions
 *
 * @package plugins.contentDistribution
 * @subpackage api.objects
 */
class KalturaAssetDistributionConditionsArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr)
	{
		$newArr = new KalturaAssetDistributionConditionsArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
			switch(get_class($obj))
			{
				case 'kAssetDistributionPropertyCondition':
					$nObj = new KalturaAssetDistributionPropertyCondition();
					break;
			}

			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}

		return $newArr;
	}
	
	public function __construct()
	{
		parent::__construct("KalturaAssetDistributionCondition");
	}
}