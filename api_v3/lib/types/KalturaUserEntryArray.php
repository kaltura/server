<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUserEntryArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaUserEntryArray();
		foreach($arr as $obj)
		{
			/* @var $obj UserEntry */
			$nObj = KalturaUserEntry::getInstanceByType($obj->getType());
			if (!$nObj)
			{
				throw new KalturaAPIException("There is an error in the DB, object type '".$obj->getType()."' of UserEntry id '".$obj->getId()."' is unknown");
			}
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}

		return $newArr;
	}

	public function __construct( )
	{
		return parent::__construct ( "KalturaUserEntry" );
	}
}
