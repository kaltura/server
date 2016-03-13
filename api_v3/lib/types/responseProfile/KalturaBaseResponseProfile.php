<?php
/**
 * @package api
 * @subpackage objects
 */
abstract class KalturaBaseResponseProfile extends KalturaObject implements IApiObjectFactory
{
	public static function getInstance($sourceObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$object = null;
		
		if($sourceObject instanceof ResponseProfile)
		{
			$object = new KalturaResponseProfile();
		}
		elseif($sourceObject instanceof kResponseProfile)
		{
			$object = new KalturaDetachedResponseProfile();
		}
		
		if($object)
		{
			$object->fromObject($sourceObject, $responseProfile);
		}
		
		return $object;
	}
}