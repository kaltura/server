<?php

/**
 * @package plugins.interactivity
 * @subpackage api.objects
 */

class KalturaInteractivityDataFieldsFilterArray extends KalturaTypedArray
{
	public function __construct()
	{
		return parent::__construct('KalturaInteractivityDataFieldsFilter');
	}

	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaInteractivityDataFieldsFilterArray();
		foreach ( $arr as $obj )
		{
			switch (get_class($obj))
			{
				case 'kRootFieldsFilter';
					$nObj = new KalturaRootFieldsFilter();
					break;
				case 'kNodeFieldsFilter';
					$nObj = new KalturaNodeFieldsFilter();
					break;
				case 'kInteractionFieldsFilter';
					$nObj = new KalturaInteractionFieldsFilter();
					break;
				default:
					throw new Exception('Not implemented');
					break;
			}

			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}

		return $newArr;
	}
}